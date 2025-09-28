document.addEventListener('DOMContentLoaded', () => {
  const formTarefa = document.getElementById('form-tarefa');
  const corpoTarefas = document.getElementById('corpo-tarefas');
  const mensagemForm = document.getElementById('mensagem-form');
  const btnAtualizar = document.getElementById('btn-atualizar');

  const totalEl = document.getElementById('res-total');
  const pendentesEl = document.getElementById('res-pendentes');
  const concluidasEl = document.getElementById('res-concluidas');

  const searchInput = document.getElementById('search-input');

  // ajustar barra lateral (mobile)
  const toggleSidebarBtn = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');
  if (toggleSidebarBtn) {
    toggleSidebarBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }

  let filtroAtual = 'todas'; // 'todas' | 'pendentes' | 'concluidas'
  let todasTarefasCache = []; // cache local das tarefas carregadas

  // modal bootstrap para editar
  const modalEditarEl = document.getElementById('modalEditar');
  const modalEditar = new bootstrap.Modal(modalEditarEl);
  const formEditar = document.getElementById('form-editar');
  const mensagemEditar = document.getElementById('mensagem-editar');

  async function carregarTarefas() {
    try {
      const res = await fetch('api.php?acao=listar');
      const data = await res.json();
      if (!data.sucesso) throw new Error(data.mensagem || 'Erro ao carregar tarefas');
      todasTarefasCache = data.tarefas || [];
      renderizarTarefas();
      atualizarResumo();
    } catch (err) {
      corpoTarefas.innerHTML = `<tr><td colspan="5" class="text-danger">Erro: ${escapeHtml(err.message)}</td></tr>`;
    }
  }

  function renderizarTarefas() {
    const termo = (searchInput.value || '').trim().toLowerCase();
    let filtradas = todasTarefasCache.filter(t => {
      if (filtroAtual === 'pendentes') if (Number(t.concluida) === 1) return false;
      if (filtroAtual === 'concluidas') if (Number(t.concluida) === 0) return false;
      return true;
    });

    if (termo) {
      filtradas = filtradas.filter(t => {
        return (t.titulo || '').toLowerCase().includes(termo) || (t.descricao || '').toLowerCase().includes(termo);
      });
    }

    if (!Array.isArray(filtradas) || filtradas.length === 0) {
      corpoTarefas.innerHTML = '<tr><td colspan="5" class="text-muted">Nenhuma tarefa encontrada.</td></tr>';
      return;
    }

    corpoTarefas.innerHTML = filtradas.map(t => {
      const concluida = Number(t.concluida) === 1;
      const titulo = escapeHtml(t.titulo);
      const descricao = escapeHtml(t.descricao || '');
      return `
        <tr data-id="${t.id}">
          <td class="align-middle text-center"><input class="form-check-input toggle-concluida" type="checkbox" ${concluida ? 'checked' : ''}></td>
          <td class="align-middle ${concluida ? 'text-decoration-line-through text-muted' : ''}">${titulo}</td>
          <td class="align-middle ${concluida ? 'text-muted' : ''}">${descricao}</td>
          <td class="align-middle">${t.criado_em}</td>
          <td class="align-middle">
            <button class="btn btn-sm btn-outline-secondary editar-btn"><i class="fa-solid fa-pen-to-square"></i> Editar</button>
            <button class="btn btn-sm btn-danger excluir-btn"><i class="fa-solid fa-trash"></i> Excluir</button>
          </td>
        </tr>
      `;
    }).join('');
  }

  function atualizarResumo() {
    const total = todasTarefasCache.length;
    const concluidas = todasTarefasCache.filter(t => Number(t.concluida) === 1).length;
    const pendentes = total - concluidas;
    totalEl.textContent = total;
    concluidasEl.textContent = concluidas;
    pendentesEl.textContent = pendentes;
  }

  function escapeHtml(s) {
    return String(s || '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  // criar tarefa
  formTarefa.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(formTarefa);
    try {
      const res = await fetch('api.php?acao=criar', { method: 'POST', body: fd });
      const data = await res.json();
      if (!data.sucesso) {
        mensagemForm.classList.remove('text-success');
        mensagemForm.classList.add('text-danger');
        mensagemForm.textContent = data.mensagem || 'Erro';
        return;
      }
      mensagemForm.classList.remove('text-danger');
      mensagemForm.classList.add('text-success');
      mensagemForm.textContent = data.mensagem || 'Tarefa criada.';
      formTarefa.reset();
      carregarTarefas();
      setTimeout(() => mensagemForm.textContent = '', 2500);
    } catch (err) {
      mensagemForm.classList.remove('text-success');
      mensagemForm.classList.add('text-danger');
      mensagemForm.textContent = 'Erro: ' + err.message;
    }
  });

  // delegação para alternar e excluir e abrir editar
  corpoTarefas.addEventListener('click', async (e) => {
    const tr = e.target.closest('tr');
    if (!tr) return;
    const id = tr.dataset.id;

    if (e.target.classList.contains('toggle-concluida')) {
      try {
        const fd = new FormData();
        fd.append('id', id);
        const res = await fetch('api.php?acao=alternar', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.sucesso) {
          carregarTarefas();
        } else {
          alert(data.mensagem || 'Não foi possível atualizar.');
        }
      } catch (err) {
        alert('Erro: ' + err.message);
      }
    }

    if (e.target.closest('.excluir-btn')) {
      if (!confirm('Deseja realmente excluir esta tarefa?')) return;
      try {
        const fd = new FormData();
        fd.append('id', id);
        const res = await fetch('api.php?acao=excluir', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.sucesso) {
          carregarTarefas();
        } else {
          alert(data.mensagem || 'Não foi possível excluir.');
        }
      } catch (err) {
        alert('Erro: ' + err.message);
      }
    }

    if (e.target.closest('.editar-btn')) {
      // preencher modal com dados da linha (usando cache para obter descricao completa)
      const tarefa = todasTarefasCache.find(t => String(t.id) === String(id));
      if (!tarefa) return alert('Tarefa não encontrada.');
      document.getElementById('editar-id').value = tarefa.id;
      document.getElementById('editar-titulo').value = tarefa.titulo;
      document.getElementById('editar-descricao').value = tarefa.descricao || '';
      mensagemEditar.textContent = '';
      modalEditar.show();
    }
  });

  // submit edição
  formEditar.addEventListener('submit', async (e) => {
    e.preventDefault();
    mensagemEditar.textContent = '';
    const fd = new FormData(formEditar);
    try {
      const res = await fetch('api.php?acao=editar', { method: 'POST', body: fd });
      const data = await res.json();
      if (!data.sucesso) {
        mensagemEditar.textContent = data.mensagem || 'Erro ao atualizar.';
        return;
      }
      modalEditar.hide();
      carregarTarefas();
    } catch (err) {
      mensagemEditar.textContent = 'Erro: ' + err.message;
    }
  });

  // filtros
  document.getElementById('filtro-todas').addEventListener('click', (e) => { mudarFiltro('todas', e.target); });
  document.getElementById('filtro-pendentes').addEventListener('click', (e) => { mudarFiltro('pendentes', e.target); });
  document.getElementById('filtro-concluidas').addEventListener('click', (e) => { mudarFiltro('concluidas', e.target); });

  function mudarFiltro(novoFiltro, botaoEl) {
    filtroAtual = novoFiltro;
    // atualizar classes de botões
    document.querySelectorAll('[id^="filtro-"]').forEach(b => b.classList.remove('active'));
    if (botaoEl) botaoEl.classList.add('active');
    renderizarTarefas();
  }

  btnAtualizar.addEventListener('click', carregarTarefas);

  // pesquisa ao digitar
  let dp;
  searchInput.addEventListener('input', () => {
    clearTimeout(dp);
    dp = setTimeout(() => renderizarTarefas(), 250);
  });

  // carga inicial
  carregarTarefas();
});
