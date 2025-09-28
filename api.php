<?php
// endpoints: listar, criar, alternar, excluir, editar
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';

$acao = $_REQUEST['acao'] ?? '';

try {
    if ($acao === 'listar') {
        $stmt = $pdo->query("SELECT id, titulo, descricao, concluida, criado_em FROM tarefas ORDER BY criado_em DESC");
        $tarefas = $stmt->fetchAll();
        echo json_encode(['sucesso' => true, 'tarefas' => $tarefas]);
        exit;
    }

    if ($acao === 'criar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulo = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');

        if ($titulo === '') {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Título é obrigatório.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO tarefas (titulo, descricao) VALUES (:titulo, :descricao)");
        $stmt->execute([':titulo' => $titulo, ':descricao' => $descricao]);
        $id = $pdo->lastInsertId();

        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa criada.', 'id' => $id]);
        exit;
    }

    if ($acao === 'alternar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE tarefas SET concluida = 1 - concluida WHERE id = :id");
        $stmt->execute([':id' => $id]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Status atualizado.']);
        exit;
    }

    if ($acao === 'excluir' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM tarefas WHERE id = :id");
        $stmt->execute([':id' => $id]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa removida.']);
        exit;
    }

    if ($acao === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');

        if ($id <= 0) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
            exit;
        }
        if ($titulo === '') {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Título é obrigatório.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE tarefas SET titulo = :titulo, descricao = :descricao WHERE id = :id");
        $stmt->execute([':titulo' => $titulo, ':descricao' => $descricao, ':id' => $id]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Tarefa atualizada.']);
        exit;
    }

    // ação inválida
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
