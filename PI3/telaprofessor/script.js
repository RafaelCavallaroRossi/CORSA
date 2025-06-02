let turmas = [];

// Alternar seções
function showSection(sectionId) {
  document.querySelectorAll('main > section').forEach(sec => sec.classList.add('hidden'));
  document.getElementById(sectionId).classList.remove('hidden');
  if (sectionId === 'dashboard') renderizarDashboard();
  if (sectionId === 'turmas') renderizarTurmas();
  if (sectionId === 'alunos') carregarTurmasNoSelect();
  if (sectionId === 'frequencia') carregarTurmasNaFrequencia();
}

// Função para criar turma
document.getElementById('formTurma').addEventListener('submit', (e) => {
  e.preventDefault();
  const nome = document.getElementById('nomeTurma').value.trim();
  if (nome === '') return;

  turmas.push({ nome, alunos: [] });
  document.getElementById('nomeTurma').value = '';
  renderizarTurmas();
  renderizarDashboard();
});

// Renderizar lista de turmas
function renderizarTurmas() {
  const lista = document.getElementById('listaTurmas');
  lista.innerHTML = '';

  turmas.forEach((turma, index) => {
    const item = document.createElement('li');
    item.className = 'flex justify-between items-center border p-2 rounded';
    item.innerHTML = `
      <span>${turma.nome}</span>
      <button onclick="removerTurma(${index})" class="bg-red-500 text-white px-2 py-1 rounded">Remover</button>
    `;
    lista.appendChild(item);
  });
}

// Remover turma
function removerTurma(index) {
  turmas.splice(index, 1);
  renderizarTurmas();
  renderizarDashboard();
}

// Cadastrar aluno
document.getElementById('formAluno').addEventListener('submit', (e) => {
  e.preventDefault();
  const nome = document.getElementById('nomeAluno').value.trim();
  const turmaIndex = document.getElementById('turmaAluno').value;

  if (nome === '' || turmaIndex === '') return;

  turmas[turmaIndex].alunos.push({ nome, presencas: 0, faltas: 0 });
  document.getElementById('nomeAluno').value = '';
  renderizarAlunos();
});

// Carregar turmas no select (cadastro de alunos)
function carregarTurmasNoSelect() {
  const select = document.getElementById('turmaAluno');
  select.innerHTML = '';

  turmas.forEach((turma, index) => {
    const opt = document.createElement('option');
    opt.value = index;
    opt.innerText = turma.nome;
    select.appendChild(opt);
  });

  renderizarAlunos();
}

// Renderizar alunos cadastrados
function renderizarAlunos() {
  const lista = document.getElementById('listaAlunos');
  lista.innerHTML = '';

  turmas.forEach((turma) => {
    turma.alunos.forEach((aluno) => {
      const item = document.createElement('li');
      item.className = 'border p-2 rounded';
      item.innerText = `${aluno.nome} - Turma: ${turma.nome}`;
      lista.appendChild(item);
    });
  });
}

// Dashboard
function renderizarDashboard() {
  const container = document.getElementById('dashboardTurmas');
  container.innerHTML = '';

  turmas.forEach((turma, index) => {
    const card = document.createElement('div');
    card.className = 'bg-white p-6 rounded shadow cursor-pointer hover:shadow-lg';
    card.onclick = () => abrirModal(index);

    card.innerHTML = `
      <h3 class="text-xl font-bold mb-2">${turma.nome}</h3>
      <p>Total de Alunos: ${turma.alunos.length}</p>
    `;

    container.appendChild(card);
  });
}

// Modal com lista de alunos
function abrirModal(index) {
  const turma = turmas[index];
  document.getElementById('modalTitulo').innerText = `Turma: ${turma.nome}`;

  const lista = document.getElementById('listaAlunosModal');
  lista.innerHTML = '';

  if (turma.alunos.length === 0) {
    lista.innerHTML = `<li class="text-center text-gray-500">Nenhum aluno cadastrado.</li>`;
  } else {
    turma.alunos.forEach(aluno => {
      const item = document.createElement('li');
      item.className = 'border p-2 rounded flex justify-between';
      item.innerHTML = `
        <span>${aluno.nome}</span>
        <span>✅ ${aluno.presencas} | ❌ ${aluno.faltas}</span>
      `;
      lista.appendChild(item);
    });
  }

  document.getElementById('modalTurma').classList.remove('hidden');
}

// Fechar Modal
function fecharModal() {
  document.getElementById('modalTurma').classList.add('hidden');
}

// Frequência
function carregarTurmasNaFrequencia() {
  const select = document.getElementById('turmaFrequencia');
  select.innerHTML = '';

  turmas.forEach((turma, index) => {
    const opt = document.createElement('option');
    opt.value = index;
    opt.innerText = turma.nome;
    select.appendChild(opt);
  });

  document.getElementById('listaFrequencia').innerHTML = '';
}

function gerarListaFrequencia() {
  const turmaIndex = document.getElementById('turmaFrequencia').value;
  const data = document.getElementById('dataFrequencia').value;

  if (turmaIndex === '' || data === '') {
    alert('Selecione a turma e a data!');
    return;
  }

  const turma = turmas[turmaIndex];
  const lista = document.getElementById('listaFrequencia');
  lista.innerHTML = '';

  turma.alunos.forEach((aluno, idx) => {
    const item = document.createElement('li');
    item.className = 'border p-2 rounded flex justify-between items-center';

    item.innerHTML = `
      <span>${aluno.nome}</span>
      <div class="flex gap-2">
        <button onclick="marcarPresenca(${turmaIndex}, ${idx})" class="bg-green-500 text-white px-2 py-1 rounded">Presente</button>
        <button onclick="marcarFalta(${turmaIndex}, ${idx})" class="bg-red-500 text-white px-2 py-1 rounded">Falta</button>
      </div>
    `;

    lista.appendChild(item);
  });
}

function marcarPresenca(turmaIndex, alunoIndex) {
  turmas[turmaIndex].alunos[alunoIndex].presencas++;
  gerarListaFrequencia();
  renderizarDashboard();
}

function marcarFalta(turmaIndex, alunoIndex) {
  turmas[turmaIndex].alunos[alunoIndex].faltas++;
  gerarListaFrequencia();
  renderizarDashboard();
}

// Inicialização
showSection('dashboard');
renderizarDashboard();
