<?php
session_start();
include '../conexao.php';
include '../menu.php';

function formatarTexto($texto) {
 
    $texto = str_replace('_', ' ', $texto);
    
    
    $texto = ucwords($texto);
    
    return $texto;
}
$nomeProfessor = isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : '';
$rm = isset($_SESSION['rm']) ? $_SESSION['rm'] : '';
$id_solicitacao = $_GET['id'] ?? null;
if ($id_solicitacao) {
    $sql = "SELECT j.id_justificativa, u.nome_usuario, u.rm, c.nome_curso,
    j.data_falta_inicio, j.data_falta_fim, j.horario_inicio, j.horario_fim, j.tipo_falta,
    j.especificacao, j.anexo
FROM justificativas j
JOIN usuarios u ON j.id_usuario = u.id_usuario
JOIN cursos c ON j.id_curso = c.id_curso
WHERE j.id_justificativa = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_solicitacao]);
    $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$solicitacao) {
        echo "<p>Solicitação não encontrada.</p>";
        exit;
    }
} else {
    echo "<p>ID da solicitação não fornecido.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="../geral.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano de Reposição de Aulas</title>
    <script>
        function atualizarCampos() {
            const quantidade = document.getElementById('quantidade_aulas').value;
            const aulasReposicao = document.querySelectorAll('.aulas-reposicao .aula-reposicao');
            aulasReposicao.forEach((campo, index) => {
                campo.style.display = index < quantidade ? 'block' : 'none';
            });
        }

       
        window.onload = setCurrentDate;
    </script>
</head>
<body>
    <div class="container-reposicao">
        <h2>Plano de Reposição de Aulas</h2>
        <form action="processarPlanoReposicao.php" method="post">

            <input type="hidden" id="id_justificativa" name="id_justificativa" value="<?= htmlspecialchars($solicitacao['id_justificativa']) ?>" >

            <label for="nomeProfessor">Nome do Professor:</label>
            <input type="text" id="nomeProfessor" name="nomeProfessor" value="<?= htmlspecialchars($nomeProfessor) ?>" readonly>

            <label for="rm">Registro de Matrícula (RM):</label>
            <input type="text" id="rm" name="rm" value="<?= htmlspecialchars($rm) ?>" readonly>

            <label for="curso">Selecione o curso:</label>
            <input type="text" id="curso_id" name="curso_id" value="<?= htmlspecialchars($solicitacao['nome_curso']) ?>" readonly>

            <label for="turno">Turno:</label>
            <select id="turno" name="turno" required>
                <option value="" disabled selected>Escolha o turno</option>
                <option value="Manhã">Manhã</option>
                <option value="Tarde">Tarde</option>
                <option value="Noite">Noite</option>
            </select>

            <label for="motivo">Motivo da Reposição:</label>
            <input type="text" id="tipo_falta" name="motivo" value="<?= htmlspecialchars(formatarTexto($solicitacao['tipo_falta'])) ?>" readonly>

            <h3>Dados da aula não ministrada</h3>
            <center><label for="data_falta">Data:</label>
            <input type="date" id="data_falta" name="data_nao_ministrada" value="<?= htmlspecialchars($solicitacao['data_falta_inicio']) ?>" readonly> Até <input type="date" id="data_falta" name="data_nao_ministrada" value="<?= htmlspecialchars($solicitacao['data_falta_fim']) ?>" readonly></center>

            <label for="disciplina_nao_ministrada">Disciplina:</label>
            <select id="disciplina_nao_ministrada" name="disciplina_nao_ministrada" onchange="atualizarCampos()" required>
                <option value="" disabled selected>Escolha a Disciplina</option>
                <option value="1">Engenharia de Software II</option>
                <option value="2">Banco de Dados Relacional</option>
                <option value="3">Técnicas em Programação</option>
            </select>

            <label for="quantidade_aulas">Quantidade de Aulas a Repor:</label>
            <select id="quantidade_aulas" name="quantidade_aulas" onchange="atualizarCampos()" required>
                <option value="" disabled selected>Escolha a quantidade</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>

            <h3>Dados da(s) aulas de reposição</h3>
            <div class="aulas-reposicao">
                <div class="aula-reposicao" style="display: none;">
                    <label for="reposicao1">1ª Data:</label>
                    <input type="date" id="reposicao1" name="datas_reposicao[]" min="<?= date('Y-m-d') ?>">
                    <label for="horario1">Horário de Início e Término</label>
                    <center><input type="time" id="horario_inicio1" name="horarios_inicio[]" step="600" required> Até 
                    <input type="time" id="horario_fim1" name="horarios_fim[]" step="600" required></center>
                </div>

                <div class="aula-reposicao" style="display: none;">
                    <label for="reposicao2">2ª Data:</label>
                    <input type="date" id="reposicao2" name="datas_reposicao[]" min="<?= date('Y-m-d') ?>">
                    <label for="horario2">Horário de Início e Término</label>
                    <center><input type="time" id="horario_inicio2" name="horarios_inicio[]" step="600"> Até <input type="time" id="horario_fim2" name="horarios_fim[]" step="600"></center>
                </div>

                <div class="aula-reposicao" style="display: none;">
                    <label for="reposicao3">3ª Data:</label>
                    <input type="date" id="reposicao3" name="datas_reposicao[]" min="<?= date('Y-m-d') ?>">
                    <label for="horario3">Horário de Início e Término</label>
                    <center><input type="time" id="horario_inicio3" name="horarios_inicio[]" step="600"> Até <input type="time" id="horario_fim3" name="horarios_fim[]" step="600"></center>
                </div>

                <div class="aula-reposicao" style="display: none;">
                    <label for="reposicao4">4ª Data:</label>
                    <input type="date" id="reposicao4" name="datas_reposicao[]" min="<?= date('Y-m-d') ?>">
                    <label for="horario4">Horário de Início e Término</label>
                    <center><input type="time" id="horario_inicio4" name="horarios_inicio[]" step="600"> Até <input type="time" id="horario_fim4" name="horarios_fim[]" step="600"></center>
                </div>
            </div>

            <button type="submit" class="btn-enviar">Enviar Plano de Reposição</button>
        </form>
    </div>
    <footer class="rodape">
        <div class="container-rodape">
            <div class="logo-rodape">
            <img src="../IMG/sp.png" alt="Logo Governo de São Paulo">
            </div>
            <div class="info-rodape">
                <p>© 2024 Governo do Estado de São Paulo</p>
                <p>Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>