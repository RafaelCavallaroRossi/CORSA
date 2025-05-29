<?php
session_start();
include 'conexao.php';
include 'menu.php';
// Supondo que o ID do usuário esteja armazenado na sessão
$user_id = $_SESSION['id_usuario'];

// Consulta para obter o RN e ID do professor a partir da tabela usuarios
$sql_user = "SELECT rm, id_usuario FROM usuarios WHERE id_usuario = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rm_professor = $user['rm_professor'];
    $id_professor = $user['id_professor'];
    $curso_id = $_POST['curso_id'];
    $turno_id = $_POST['turno_id'];
    $motivo_reposicao_id = $_POST['motivo_reposicao_id'];
    $data_aula_nao_ministrada = $_POST['data_aula_nao_ministrada'];
    $data_reposicao = $_POST['data_reposicao'];
    $horario_inicio = $_POST['horario_inicio'];
    $horario_fim = $_POST['horario_fim'];

    
    $disciplina = $_POST['disciplina'];

    $sql = "INSERT INTO reposicoes (rm_professor, id_professor, id_curso, turno, motivo_reposicao, data_aula_nao_ministrada, data_reposicao, horario_inicio, horario_fim, id_disciplina) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$rm_professor, $id_professor, $curso_id, $turno_id, $motivo_reposicao_id, $data_aula_nao_ministrada, $data_reposicao, $horario_inicio, $horario_fim, $disciplina]);

    echo "<div class='alert alert-success'>Solicitação de reposição enviada com sucesso!</div>";
    header('Location: admin_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plano de Reposição de Aulas</title>
    <link rel="stylesheet" href="geral.css"> <!-- Certifique-se de ajustar o caminho correto -->
</head>
<body>
    <div class="container-reposicao">
        <h1 id="Subtitulo">Plano de Reposição de Aulas</h1>
        <form action="" method="POST">

            <label for="rm">Registro de Matrícula do Professor:</label>
            <input type="text" id="rm_professor" name="rm_professor" value="<?= htmlspecialchars($user['rm']) ?>" readonly>

            <input type="hidden" id="id_professor" name="id_professor" value="<?= htmlspecialchars($user['id_usuario']) ?>">

            <label for="curso_id">Curso:</label>
            <select id="curso_id" name="curso_id" required>
                <option value="" disabled selected>Escolha um curso</option>
                <option value="1">Gestão da Tecnologia da Informação</option>
                <option value="2">Gestão da Produção Industrial</option>
                <option value="3">Gestão Empresarial</option>
                <option value="4">Desenvolvimento de Software Multiplataforma</option>
            </select>

            <label for="turno_id">Turno:</label>
            <select id="turno_id" name="turno_id" required>
                <option value="" disabled selected>Escolha o turno</option>
                <option value="1">Manhã</option>
                <option value="2">Tarde</option>
                <option value="3">Noite</option>
            </select>

            <label for="motivo_reposicao_id">Motivo da Reposição:</label>
            <select id="motivo_reposicao_id" name="motivo_reposicao_id" required>
                <option value="" disabled selected>Escolha o motivo</option>
                <option value="1">Falta</option>
                <option value="2">Substituição</option>
                <option value="3">Claro Docente</option>
            </select>

            <label for="data_aula_nao_ministrada">Data da Aula Não Ministrada:</label>
            <input type="date" id="data_aula_nao_ministrada" name="data_aula_nao_ministrada" required>

            <label for="data_reposicao">Data da Reposição:</label>
            <input type="date" id="data_reposicao" name="data_reposicao" required>

            <label for="horario_inicio">Horário de Início da Reposição:</label>
            <input type="time" id="horario_inicio" name="horario_inicio" required>

            <label for="horario_fim">Horário de Fim da Reposição:</label>
            <input type="time" id="horario_fim" name="horario_fim" required>

            <label for="disciplina">Disciplina:</label>
            <select id="disciplina" name="disciplina" required>
                <option value="" disabled selected>Escolha a disciplina</option>
                <option value="1">Engenharia de Sofware</option>
                <option value="2">Técnicas de Programação</option>
                <option value="3">Estrutura de Dados</option>
            </select>

            <button type="submit" class="button">Enviar Solicitacao</button>
        </form>
    </div>

    <footer class="rodape">
    <div class="container-rodape">
        <div class="logo-rodape">
        <img src="IMG/sp.png" alt="Logo Governo de São Paulo">
        </div>
        <div class="info-rodape">
            <p>© 2024 Governo do Estado de São Paulo</p>
            <p>Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>