<?php
// api.php
session_start();
require_once __DIR__ . '/db.php';

$acao = $_REQUEST['acao'] ?? '';

header('Content-Type: application/json');

try {
  // ---------- REGISTRO ----------
  if ($acao === 'registrar') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if ($usuario === '' || $senha === '') throw new Exception('Preencha usuário e senha.');
    // verificar se existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    if ($stmt->fetch()) throw new Exception('Usuário já existe.');

    $salt = gerar_salt();
    $hash = sha256_salt($salt, $senha);
    $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, salt, senha_hash) VALUES (?, ?, ?)");
    $stmt->execute([$usuario, $salt, $hash]);

    echo json_encode(['sucesso' => true]);
    exit;
  }

  // ---------- LOGIN ----------
  if ($acao === 'entrar') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';
    if ($usuario === '' || $senha === '') throw new Exception('Preencha usuário e senha.');

    $stmt = $pdo->prepare("SELECT id, salt, senha_hash FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $u = $stmt->fetch();
    if (!$u) throw new Exception('Credenciais inválidas.');

    $calc = sha256_salt($u['salt'], $senha);
    if (!hash_equals($u['senha_hash'], $calc)) throw new Exception('Credenciais inválidas.');

    $_SESSION['usuario_id'] = $u['id'];
    echo json_encode(['sucesso' => true]);
    exit;
  }

  // ---------- LOGOUT ----------
  if ($acao === 'sair') {
    session_destroy();
    echo json_encode(['sucesso' => true]);
    exit;
  }

  // ---------- FORNECEDORES ----------
  if ($acao === 'listar_fornecedores') {
    $rows = $pdo->query("SELECT * FROM fornecedores ORDER BY nome")->fetchAll();
    echo json_encode($rows); exit;
  }
  if ($acao === 'criar_fornecedor') {
    $nome = trim($_POST['nome'] ?? '');
    if ($nome === '') throw new Exception('Nome obrigatório.');
    $stmt = $pdo->prepare("INSERT INTO fornecedores (nome) VALUES (?)");
    $stmt->execute([$nome]);
    echo json_encode(['sucesso' => true, 'id' => $pdo->lastInsertId()]); exit;
  }

  // ---------- PRODUTOS ----------
  if ($acao === 'listar_produtos') {
    $rows = $pdo->query("SELECT p.*, f.nome as fornecedor_nome FROM produtos p LEFT JOIN fornecedores f ON p.fornecedor_id = f.id ORDER BY p.nome")->fetchAll();
    echo json_encode($rows); exit;
  }
  if ($acao === 'criar_produto') {
    $nome = trim($_POST['nome'] ?? '');
    $preco = (float)($_POST['preco'] ?? 0);
    $fornecedor_id = empty($_POST['fornecedor_id']) ? null : (int)$_POST['fornecedor_id'];
    if ($nome === '') throw new Exception('Nome do produto obrigatório.');
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, fornecedor_id) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $preco, $fornecedor_id]);
    echo json_encode(['sucesso' => true, 'id' => $pdo->lastInsertId()]); exit;
  }

  // ---------- AÇÕES DA CESTA (requer autenticação) ----------
  if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'msg' => 'Autentique-se.']); exit;
  }
  $usuario_id = $_SESSION['usuario_id'];

  // obter ou criar cesta do usuário
  $stmt = $pdo->prepare("SELECT id FROM cestas WHERE usuario_id = ? LIMIT 1");
  $stmt->execute([$usuario_id]);
  $c = $stmt->fetch();
  if ($c) $cesta_id = $c['id'];
  else {
    $pdo->prepare("INSERT INTO cestas (usuario_id) VALUES (?)")->execute([$usuario_id]);
    $cesta_id = $pdo->lastInsertId();
  }

  // adicionar produtos à cesta
  if ($acao === 'adicionar_cesta') {
    // espera product_ids[] via POST
    $ids = $_POST['produto_ids'] ?? $_POST['produto_ids[]'] ?? [];
    if (!is_array($ids) || count($ids) === 0) throw new Exception('Nenhum produto selecionado.');
    $adicionados = 0;
    $stmt = $pdo->prepare("INSERT IGNORE INTO itens_cesta (cesta_id, produto_id) VALUES (?, ?)");
    foreach ($ids as $pid) {
      $stmt->execute([$cesta_id, (int)$pid]);
      $adicionados += $stmt->rowCount();
    }
    // resumo
    $s = $pdo->prepare("SELECT COUNT(*) as qtd, COALESCE(SUM(p.preco),0) as total FROM itens_cesta ic JOIN produtos p ON ic.produto_id = p.id WHERE ic.cesta_id = ?");
    $s->execute([$cesta_id]);
    $resumo = $s->fetch();
    echo json_encode(['sucesso'=>true,'adicionados'=>$adicionados,'resumo'=>$resumo]); exit;
  }

  // obter itens da cesta
  if ($acao === 'obter_cesta') {
    $stmt = $pdo->prepare("SELECT p.id, p.nome, p.preco FROM itens_cesta ic JOIN produtos p ON ic.produto_id = p.id WHERE ic.cesta_id = ?");
    $stmt->execute([$cesta_id]);
    $itens = $stmt->fetchAll();
    $s = $pdo->prepare("SELECT COUNT(*) as qtd, COALESCE(SUM(p.preco),0) as total FROM itens_cesta ic JOIN produtos p ON ic.produto_id = p.id WHERE ic.cesta_id = ?");
    $s->execute([$cesta_id]);
    $resumo = $s->fetch();
    echo json_encode(['sucesso'=>true,'itens'=>$itens,'resumo'=>$resumo]); exit;
  }

  // remover item da cesta
  if ($acao === 'remover_cesta') {
    $pid = (int)($_POST['produto_id'] ?? 0);
    if ($pid <= 0) throw new Exception('Produto inválido.');
    $stmt = $pdo->prepare("DELETE FROM itens_cesta WHERE cesta_id = ? AND produto_id = ?");
    $stmt->execute([$cesta_id, $pid]);
    echo json_encode(['sucesso' => true]); exit;
  }

    // ---------- REMOVER PRODUTO ----------
  if ($acao === 'remover_produto') {
    // exige autenticação (a página de produtos já exige session)
    if (!isset($_SESSION['usuario_id'])) {
      echo json_encode(['sucesso' => false, 'msg' => 'Autentique-se.']); exit;
    }
    $pid = (int)($_POST['produto_id'] ?? 0);
    if ($pid <= 0) throw new Exception('Produto inválido.');

    // opcional: verificar se produto existe (não obrigatório)
    $check = $pdo->prepare("SELECT id FROM produtos WHERE id = ?");
    $check->execute([$pid]);
    if (!$check->fetch()) throw new Exception('Produto não encontrado.');

    // remover produto
    $del = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $del->execute([$pid]);

    echo json_encode(['sucesso' => true]);
    exit;
  }


  throw new Exception('Ação inválida');

} catch (Exception $e) {
  echo json_encode(['sucesso' => false, 'msg' => $e->getMessage()]);
  exit;
}
