<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Produtos - Mini Gestão</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">Mini Gestão</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="products.php">Produtos</a></li>
        <li class="nav-item"><a class="nav-link" href="cesta.php">Cesta</a></li>
      </ul>
      <div class="d-flex">
        <button id="sair" class="btn btn-outline-light me-2">Sair</button>
      </div>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div id="alertPlaceholder"></div>

  <div class="row">
    <div class="col-lg-4">
      <div class="card mb-3 shadow-sm">
        <div class="card-body">
          <h5>Cadastrar Fornecedor</h5>
          <form id="formFornecedor" class="row g-2">
            <div class="col-12">
              <input name="nome" class="form-control" placeholder="Nome do fornecedor" required>
            </div>
            <div class="col-12">
              <button class="btn btn-primary w-100">Salvar Fornecedor</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Cadastrar Produto</h5>
          <form id="formProduto" class="row g-2">
            <div class="col-12">
              <input name="nome" class="form-control" placeholder="Nome do produto" required>
            </div>
            <div class="col-6">
              <input name="preco" class="form-control" placeholder="Preço (ex: 99.99)" required>
            </div>
            <div class="col-6">
              <select name="fornecedor_id" id="selectFornecedor" class="form-select">
                <option value="">--Fornecedor--</option>
              </select>
            </div>
            <div class="col-12">
              <button class="btn btn-success w-100">Salvar Produto</button>
            </div>
          </form>
        </div>
      </div>

    </div>

    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Lista de Produtos</h5>
            <div>
              <a href="cesta.php" class="btn btn-outline-primary me-2">Ver Cesta</a>
              <button id="adicionarCesta" class="btn btn-primary">Adicionar selecionados à cesta</button>
            </div>
          </div>

          <div id="listaProdutos" class="list-group">
            <!-- produtos carregados via JS -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="mt-4 text-muted small">Desenvolvimento de Aplicações para WEB I</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/app.js"></script>
<script>
function showAlert(msg, type='info') {
  const p = document.getElementById('alertPlaceholder');
  p.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
    ${msg}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>`;
}

// adaptações simples: exibir mensagens de retorno do app.js
window.showAppAlert = showAlert;

// inicialização feita no app.js (carregar fornecedores e produtos)
// logout
document.getElementById('sair').addEventListener('click', async ()=>{
  const fd = new FormData(); fd.append('acao','sair');
  await fetch('api.php', {method:'POST', body: fd});
  location = 'index.php';
});
</script>
</body>
</html>
