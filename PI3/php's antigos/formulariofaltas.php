<?php
session_start();
include 'conexao.php';
include 'menu.php';

// Supondo que o ID do usuário esteja armazenado na sessão
$user_id = $_SESSION['id_usuario'];

// Consulta para obter o RN e ID do professor a partir da tabela usuarios
$sql_user = "SELECT id_usuario,rm FROM usuarios WHERE id_usuario = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_professor = $user['id_usuario']; 

    $tipo_falta = $_POST['tipo_falta'];
    $curso_id = $_POST['curso_id'];
    $turno_id = $_POST['turno_id'];
    $especifica_falta_medica = $_POST['especifica_falta_medica'];
    $especifica_falta_justificada = $_POST['especifica_falta_justificada'];
    $especifica_falta_legislacao = $_POST['especifica_falta_legislacao'];
    $data_falta = $_POST['data_falta'];
    $horario_inicio = $_POST['horario_inicio'];
    $horario_fim = $_POST['horario_fim'];
    $anexo = $_FILES['anexo'];

    // Verifique se houve erro no upload
    if ($anexo['error'] !== UPLOAD_ERR_OK) {
        die('Erro no upload do arquivo. Código de erro: ' . $anexo['error']);
    }

    $nomeArquivo = uniqid() . "-" . basename($anexo['name']);
    
    // Mova o arquivo para a pasta uploads
    if (move_uploaded_file($anexo['tmp_name'], "uploads/$nomeArquivo")) {
        // Sucesso ao mover o arquivo, insira no banco de dados
        $sql = "INSERT INTO justificativas (id_usuario, tipo_falta, id_curso, turno, especifica_falta_medica , especifica_falta_justificada, especifica_falta_legislacao, data_falta, horario_inicio, horario_fim, anexo) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_professor, $tipo_falta, $curso_id, $turno_id, $especifica_falta_medica, $especifica_falta_justificada, $especifica_falta_legislacao, $data_falta, $horario_inicio, $horario_fim, $nomeArquivo]);

        echo "<div class='alert alert-success'>Solicitação de reposição enviada com sucesso!</div>";
        header('Location: menuprofessor.php');
        exit();
    } else {
        echo "<div class='alert alert-danger'>Falha ao mover o arquivo para a pasta uploads.</div>";
    }
}


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificativa de Faltas</title>
    <link rel="stylesheet" href="geral.css"> <!-- Link para o CSS externo -->
    <script>
        function mostrarOpcoes() {
            const tipoFalta = document.getElementById('tipoFalta').value;
            document.getElementById('faltaMedica').classList.add('hidden');
            document.getElementById('faltaJustificada').classList.add('hidden');
            document.getElementById('faltaLegislacao').classList.add('hidden');

            if (tipoFalta === 'medica') {
                document.getElementById('faltaMedica').classList.remove('hidden');
            } else if (tipoFalta === 'justificada') {
                document.getElementById('faltaJustificada').classList.remove('hidden');
            } else if (tipoFalta === 'legislacao') {
                document.getElementById('faltaLegislacao').classList.remove('hidden');
            }
        }
    </script>
</head>
<body>

    <input type="hidden" id="id_professor" name="id_professor" value="<?= htmlspecialchars($user['id_usuario']) ?>">
    
    <div class="container-reposicao">
        <h1 id="Subtitulo"> Justificativa de Faltas</h1>

        <!-- Tipo de Falta -->
        <form action="" method="POST" enctype ="multipart/form-data">
        <label for="tipoFalta">Selecione o tipo de falta:</label>
        <select id="tipoFalta" name="tipo_falta" onchange="mostrarOpcoes()">
            <option value="">-- Selecione --</option>
            <option value="medica">Falta Médica</option>
            <option value="justificada">Falta Justificada</option>
            <option value="legislacao">Faltas Previstas na Legislação</option>
        </select>

        <div id="faltaMedica" class="opcoes-especificas hidden">
            <label for="especificaFaltaMedica">Selecione a opção específica:</label>
            <select id="especificaFaltaMedica" name="especifica_falta_medica">
                <option value="">-- Selecione --</option>
                <option value="comparecimento">Comparecimento ao Médico</option>
                <option value="licencaMaternidade">Licença Maternidade</option>
                <option value="licencaSaude">Licença Saúde</option>
            </select>
        </div>

        <div id="faltaJustificada" class="opcoes-especificas hidden">
            <label for="especificaFaltaJustificada">Selecione a opção específica:</label>
            <select id="especificaFaltaJustificada" name="especifica_falta_justificada">
                <option value="">-- Selecione --</option>
                <option value="falta">Falta</option>
                <option value="saidaAntecipada">Saída Antecipada</option>
            </select>
        </div>

        <div id="faltaLegislacao" class="opcoes-especificas hidden">
            <label for="especificaFaltaLegislacao">Selecione a opção específica:</label>
            <select id="especificaFaltaLegislacao" name="especifica_falta_legislacao">
                <option value="">-- Selecione --</option>
                <option value="falecimentoFamiliar">Falecimento de Familiar (Licença-Nojo)</option>
                <option value="casamento">Casamento</option>
                <option value="nascimentoFilho">Nascimento de Filho</option>
                <option value="acompanhamentoConsulta">Acompanhamento em consulta médica</option>
                <option value="doacaoSangue">Doação de Sangue</option>
                <option value="alistamentoEleitor">Alistamento como Eleitor</option>
                <option value="depoimentoJudicial">Convocação para Depoimento Judicial</option>
                <option value="participacaoJuri">Convocação para Participação em Júri</option>
                <option value="servicosEleitorais">Convocação para Serviços Eleitorais</option>
                <option value="chamadaJusticaTrabalho">Atendimento de Chamado da Justiça do Trabalho</option>
                <option value="acidenteTransporte">Acidente de Transporte</option>
            </select>
        </div>

        <!-- Campo para data -->
        <label for="data_falta">Selecione a data da falta:</label>
        <input type="date" id="data_falta" name="data_falta" required>

        <label for="curso">Selecione o curso: </label>
        <select id="curso_id" name="curso_id">
            <option value="">-- Selecione --</option>
            <option value="1">Gestão de Tecnologia da Informação</option>
            <option value="2">Gestão da Produção Industrial</option>
            <option value="3">Gestão de Empresarial</option>
            <option value="4">Desenvolvimento de Software Multiplataforma</option>
        </select>

        <label for="turno_id">Turno:</label>
            <select id="turno_id" name="turno_id" required>
                <option value="" disabled selected>Escolha o turno</option>
                <option value="1">Manhã</option>
                <option value="2">Tarde</option>
                <option value="3">Noite</option>
            </select>

        <label for="horario_inicio">Horário da Ausência:</label>
        <input type="time" id="horario_inicio" name="horario_inicio" required> Até

        <input type="time" id="horario_fim" name="horario_fim" required>



        <!-- Campo para anexar arquivo -->
        <label for="anexo">Anexe um documento:</label>
        <input type="file" id="anexo" name="anexo" accept=".pdf">

        <button type="submit" class="button">Enviar Formulario</button>

        </form>
    </div>
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
