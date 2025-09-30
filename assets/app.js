// Funções AJAX e inicialização para pages com Bootstrap
async function api(acao, data = null) {
  let opts = { method: 'POST' };
  const fd = new FormData();
  fd.append('acao', acao);
  if (data instanceof FormData) {
    for (const [k,v] of data.entries()) fd.append(k, v);
    opts.body = fd;
  } else if (data && typeof data === 'object') {
    for (const k in data) {
      if (Array.isArray(data[k])) data[k].forEach(v=> fd.append(k+'[]', v));
      else fd.append(k, data[k]);
    }
    opts.body = fd;
  } else {
    opts.body = fd;
  }
  const res = await fetch('api.php', opts);
  return res.json();
}

async function carregarFornecedores() {
  const res = await fetch('api.php?acao=listar_fornecedores');
  const lista = await res.json();
  const sel = document.getElementById('selectFornecedor');
  if (!sel) return;
  sel.innerHTML = '<option value="">--Fornecedor--</option>';
  lista.forEach(s => {
    const o = document.createElement('option');
    o.value = s.id; o.textContent = s.nome;
    sel.appendChild(o);
  });
}

async function carregarProdutos() {
  const res = await fetch('api.php?acao=listar_produtos');
  const lista = await res.json();
  const cont = document.getElementById('listaProdutos');
  if (!cont) return;
  if (!Array.isArray(lista)) { cont.innerHTML = '<div class="text-danger">Erro ao carregar produtos</div>'; return; }

  // montar HTML com botão remover
  cont.innerHTML = lista.map(p =>
    `<div class="list-group-item d-flex justify-content-between align-items-center">
      <div>
        <input type="checkbox" class="form-check-input me-2 chkProd" value="${p.id}">
        <strong>${p.nome}</strong>
        <div class="small text-muted">R$ ${parseFloat(p.preco).toFixed(2)} • ${p.fornecedor_nome || '-'}</div>
      </div>
      <div class="btn-group btn-group-sm" role="group" aria-label="Ações">
        <button class="btn btn-sm btn-outline-danger btn-remover-prod" data-id="${p.id}">Remover</button>
      </div>
    </div>`
  ).join('');

  // anexar handlers aos botões remover
  document.querySelectorAll('.btn-remover-prod').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = btn.dataset.id;
      // confirmação
      if (!confirm('Deseja realmente remover este produto? A ação não pode ser desfeita.')) return;
      // chamar API
      const fd = new FormData();
      fd.append('produto_id', id);
      const r = await api('remover_produto', fd);
      if (r.sucesso) {
        if (window.showAppAlert) window.showAppAlert('Produto removido.', 'success');
        await carregarProdutos(); // atualizar lista
      } else {
        if (window.showAppAlert) window.showAppAlert(r.msg || 'Erro ao remover produto', 'danger');
      }
    });
  });
}

document.addEventListener('DOMContentLoaded', function(){
  // forms presence check
  const formFornecedor = document.getElementById('formFornecedor');
  if (formFornecedor) {
    formFornecedor.addEventListener('submit', async e=>{
      e.preventDefault();
      const fd = new FormData(e.target);
      const r = await api('criar_fornecedor', fd);
      if (window.showAppAlert) window.showAppAlert(r.sucesso ? 'Fornecedor criado' : r.msg, r.sucesso ? 'success' : 'danger');
      await carregarFornecedores();
    });
  }

  const formProduto = document.getElementById('formProduto');
  if (formProduto) {
    formProduto.addEventListener('submit', async e=>{
      e.preventDefault();
      const fd = new FormData(e.target);
      const r = await api('criar_produto', fd);
      if (window.showAppAlert) window.showAppAlert(r.sucesso ? 'Produto criado' : r.msg, r.sucesso ? 'success' : 'danger');
      await carregarProdutos();
    });
  }

  const btnAdicionar = document.getElementById('adicionarCesta');
  if (btnAdicionar) {
    btnAdicionar.addEventListener('click', async ()=>{
      const checked = Array.from(document.querySelectorAll('.chkProd:checked')).map(i=>i.value);
      if (checked.length === 0) {
        if (window.showAppAlert) window.showAppAlert('Selecione ao menos 1 produto.', 'warning');
        return;
      }
      const fd = new FormData();
      checked.forEach(id => fd.append('produto_ids[]', id));
      const r = await api('adicionar_cesta', fd);
      if (window.showAppAlert) window.showAppAlert(r.sucesso ? `Adicionados: ${r.adicionados}. Total: R$ ${parseFloat(r.resumo.total).toFixed(2)} (${r.resumo.qtd} itens)` : r.msg, r.sucesso ? 'success' : 'danger');
    });
  }

  // carregar dados ao abrir página
  carregarFornecedores();
  carregarProdutos();
});
