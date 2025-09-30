<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header('Location: index.php'); exit; }
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Cesta - Mini Gestão</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">Mini Gestão</a>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="produtos.php">Produtos</a></li>
        <li class="nav-item"><a class="nav-link active" href="cesta.php">Cesta</a></li>
      </ul>
      <div class="d-flex">
        <button id="sair" class="btn btn-outline-light">Sair</button>
      </div>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div id="alertPlaceholder"></div>

  <div class="card shadow-sm">
    <div class="card-body">
      <h5>Sua Cesta</h5>
      <div id="itens" class="mb-3">
        <!-- itens carregados via JS -->
      </div>
      <div id="resumo" class="fw-bold"></div>
      <div class="mt-3">
        <a href="produtos.php" class="btn btn-secondary">Voltar aos produtos</a>
      </div>
    </div>
  </div>

  <footer class="mt-4 text-muted small">Desenvolvimento de Aplicações para WEB I</footer>
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

async function carregarCesta() {
  const res = await fetch('api.php?acao=obter_cesta');
  const j = await res.json();
  if (!j.sucesso) { showAlert(j.msg, 'danger'); return; }
  const itens = j.itens;
  const cont = document.getElementById('itens');
  if (itens.length === 0) cont.innerHTML = '<p class="text-muted">Cesta vazia</p>';
  else {
    cont.innerHTML = itens.map(it => `
      <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2">
        <div><strong>${it.nome}</strong><div class="text-muted">R$ ${parseFloat(it.preco).toFixed(2)}</div></div>
        <div>
          <button class="btn btn-sm btn-outline-danger remover" data-id="${it.id}">Remover</button>
        </div>
      </div>
    `).join('');
  }
  document.getElementById('resumo').textContent = `Total: R$ ${parseFloat(j.resumo.total).toFixed(2)} — Itens: ${j.resumo.qtd}`;
  document.querySelectorAll('.remover').forEach(b=>{
    b.addEventListener('click', async ()=>{
      const id = b.dataset.id;
      const fd = new FormData(); fd.append('acao','remover_cesta'); fd.append('produto_id', id);
      const res = await fetch('api.php', {method:'POST', body: fd});
      const rr = await res.json();
      if (rr.sucesso) {
        showAlert('Item removido', 'success');
        carregarCesta();
      } else showAlert(rr.msg || 'Erro', 'danger');
    });
  });
}

document.getElementById('sair').addEventListener('click', async ()=>{
  const fd = new FormData(); fd.append('acao','sair');
  await fetch('api.php', {method:'POST', body: fd});
  location = 'index.php';
});

carregarCesta();
</script>
</body>
</html>
