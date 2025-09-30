<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
  header('Location: produtos.php'); exit;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mini Gestão - Login</title>
  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">Mini Gestão de Produtos</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Início</a></li>
      </ul>
      <span class="navbar-text text-white">Crud de Produtos</span>
    </div>
  </div>
</nav>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div id="alertPlaceholder"></div>

      <div class="card mb-4 shadow-sm">
        <div class="card-body">
          <h4 class="card-title">Entrar</h4>
          <form id="formLogin" class="row g-3">
            <div class="col-md-6">
              <input name="usuario" class="form-control" placeholder="Usuário" required>
            </div>
            <div class="col-md-6">
              <input name="senha" type="password" class="form-control" placeholder="Senha" required>
            </div>
            <div class="col-12">
              <button class="btn btn-primary">Entrar</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title">Registrar (novo usuário)</h4>
          <form id="formRegistrar" class="row g-3">
            <div class="col-md-6">
              <input name="usuario" class="form-control" placeholder="Usuário" required>
            </div>
            <div class="col-md-6">
              <input name="senha" type="password" class="form-control" placeholder="Senha (min 6)" required>
            </div>
            <div class="col-12">
              <button class="btn btn-success">Cadastrar</button>
            </div>
          </form>
        </div>
      </div>

      <footer class="mt-4 text-muted small">Desenvolvimento de Aplicações para WEB I</footer>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showAlert(msg, type='info') {
  const p = document.getElementById('alertPlaceholder');
  p.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
    ${msg}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>`;
}

async function postar(acao, form) {
  const data = new FormData(form);
  data.append('acao', acao);
  const res = await fetch('api.php', {method:'POST', body: data});
  return res.json();
}

document.getElementById('formLogin').addEventListener('submit', async e=>{
  e.preventDefault();
  const r = await postar('entrar', e.target);
  if (r.sucesso) {
    showAlert('Logado! Redirecionando...', 'success');
    setTimeout(()=>location = 'produtos.php', 700);
  } else showAlert(r.msg || 'Erro ao autenticar', 'danger');
});

document.getElementById('formRegistrar').addEventListener('submit', async e=>{
  e.preventDefault();
  const r = await postar('registrar', e.target);
  if (r.sucesso) showAlert('Registrado! Faça login.', 'success');
  else showAlert(r.msg || 'Erro ao registrar', 'danger');
});
</script>
</body>
</html>
