<?php
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Lista de Tarefas — Painel</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome para ícones (AdminLTE-like) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Estilos personalizados (tema AdminLTE-like) -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="hold-transition sidebar-mini">

  <div class="wrapper d-flex"> <!-- wrapper inicio -->

    <!-- Barra lateral -->
    <nav id="sidebar" class="bg-dark text-white vh-100 p-3" style="width:250px;">
      <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
        <i class="fa-solid fa-tasks fa-lg me-2"></i>
        <span class="fs-5 fw-bold">Meu Painel</span>
      </a>
      <hr class="text-secondary">
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
          <a href="#" class="nav-link active text-white" aria-current="page"><i class="fa-solid fa-list-check me-2"></i> Lista de Tarefas</a>
        </li>
      </ul>
      <hr class="text-secondary">
      <div >Versão 1.0</div>
    </nav>

    <div class="content flex-grow-1"> <!-- content inicio -->

      <!-- Barra superior -->
      <nav class="navbar navbar-expand navbar-light bg-white shadow-sm px-4">
        <div class="container-fluid">
          <button class="btn btn-sm btn-outline-secondary me-3 d-md-none" id="toggleSidebar"><i class="fa-solid fa-bars"></i></button>
          <form class="d-flex ms-0 ms-md-3" role="search" onsubmit="return false;">
            <input class="form-control form-control-sm" type="search" placeholder="Pesquisar tarefas..." id="search-input">
          </form>

          <div class="ms-auto d-flex align-items-center">
            <div class="me-3 text-muted small d-none d-md-block">Olá, Seja Bem-Vindo</div>
          </div>
        </div>
      </nav>

      <!-- Conteiner principal -->
      <main class="p-4">
        <div class="container-fluid"> <!-- container-fluid inicio -->
          <div class="row g-4"> <!-- row inicio -->

            <!-- Formulário lado esquerdo -->
            <div class="col-lg-4">
              <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                  <i class="fa-solid fa-plus me-2"></i> Nova Tarefa
                </div>
                <div class="card-body">
                  <form id="form-tarefa">
                    <div class="mb-3">
                      <label for="titulo" class="form-label">Título</label>
                      <input type="text" id="titulo" name="titulo" class="form-control" required maxlength="255">
                    </div>
                    <div class="mb-3">
                      <label for="descricao" class="form-label">Descrição</label>
                      <textarea id="descricao" name="descricao" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="d-flex align-items-center">
                      <button class="btn btn-primary me-2" type="submit"><i class="fa-solid fa-plus me-1"></i> Adicionar</button>
                      <button class="btn btn-outline-secondary" type="button" id="btn-atualizar"><i class="fa-solid fa-rotate-right me-1"></i> Atualizar</button>
                      <div id="mensagem-form" class="ms-3 text-success"></div>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Estatística Rápida -->
              <div class="card mt-3 shadow-sm">
                <div class="card-body">
                  <h6 class="card-title">Resumo</h6>
                  <div class="d-flex justify-content-between">
                    <div>Total</div>
                    <div id="res-total">0</div>
                  </div>
                  <div class="d-flex justify-content-between">
                    <div>Pendentes</div>
                    <div id="res-pendentes">0</div>
                  </div>
                  <div class="d-flex justify-content-between">
                    <div>Concluídas</div>
                    <div id="res-concluidas">0</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Lista lado direito -->
            <div class="col-lg-8">
              <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <div><i class="fa-solid fa-list"></i> Tarefas</div>
                  <div>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Filtros">
                      <button class="btn btn-outline-primary active" id="filtro-todas">Todas</button>
                      <button class="btn btn-outline-success" id="filtro-pendentes">Pendentes</button>
                      <button class="btn btn-outline-secondary" id="filtro-concluidas">Concluídas</button>
                    </div>
                  </div>
                </div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tabela-tarefas">
                      <thead class="table-light">
                        <tr>
                          <th style="width:40px;"></th>
                          <th>Título</th>
                          <th>Descrição</th>
                          <th style="width:160px;">Criado em</th>
                          <th style="width:150px;">Ações</th>
                        </tr>
                      </thead>
                      <tbody id="corpo-tarefas">
                        <!-- preenchido por JS -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

          </div> <!-- row fim -->
        </div> <!-- container-fluid fim -->
      </main>

      <footer class="bg-white border-top text-center py-3 small">
        <div class="container">Desenvolvimento de Aplicações para WEB I </div>
      </footer>

    </div> <!-- content fim -->

  </div> <!-- wrapper fim -->

  <!-- Modal de edição -->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="form-editar" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditarLabel"><i class="fa-solid fa-edit me-2"></i> Editar tarefa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editar-id" name="id">
          <div class="mb-3">
            <label for="editar-titulo" class="form-label">Título</label>
            <input type="text" id="editar-titulo" name="titulo" class="form-control" required maxlength="255">
          </div>
          <div class="mb-3">
            <label for="editar-descricao" class="form-label">Descrição</label>
            <textarea id="editar-descricao" name="descricao" class="form-control" rows="4"></textarea>
          </div>
          <div id="mensagem-editar" class="text-danger"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar alterações</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/app.js" defer></script>
</body>
</html>
