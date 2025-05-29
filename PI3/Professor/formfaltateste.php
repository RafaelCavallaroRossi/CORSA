<?php
session_start();
include '../conexao.php';
include '../menu.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_falta = $_POST['tipo_falta'];
    $especifica_falta_medica = $_POST['especifica_falta_medica'] ?? null;
    $especifica_falta_justificada = $_POST['especifica_falta_justificada'] ?? null;
    $motivo_justificado = $_POST['motivo_justificado'] ?? null;
    $especifica_falta_legislacao = $_POST['especifica_falta_legislacao'] ?? null;
    $data_falta_inicio = $_POST['data_falta_inicio'];
    $data_falta_fim = $_POST['data_falta_fim'];
    $periodo_dias = $_POST['periodo_dias'] ?? null;
    $curso_id = $_POST['curso_id'];
    $turno_id = $_POST['turno_id'];
    $horario_inicio = $_POST['horario_inicio'];
    $horario_fim = $_POST['horario_fim'];
    

    
    $anexo = $_FILES['anexo'];
    $anexo_nome = uniqid() . $anexo['name'];
    $anexo_tmp = $anexo['tmp_name'];
    $anexo_destino = '../uploads/' . $anexo_nome;

    if (move_uploaded_file($anexo_tmp, $anexo_destino)) {
        $sql = "INSERT INTO justificativas (
                    id_usuario, tipo_falta, especifica_falta_medica, especifica_falta_justificada,
                    motivo_justificado, especifica_falta_legislacao, data_falta_inicio, data_falta_fim, periodo_dias,
                    id_curso, turno_id, horario_inicio, horario_fim, anexo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $stmt->execute([
            $_SESSION['id_usuario'], $tipo_falta, $especifica_falta_medica, $especifica_falta_justificada,
            $motivo_justificado, $especifica_falta_legislacao, $data_falta_inicio, $data_falta_fim, $periodo_dias,
            $curso_id, $turno_id, $horario_inicio, $horario_fim, $anexo_destino
        ]);

        $_SESSION['mensagem_sucesso'] = "Justificativa enviada com sucesso!";
        
        header("Location: menuprofessor.php");
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificativa de Faltas</title>
    <link rel="stylesheet" href="../geral.css">
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


        function setMaxDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const maxDate = `${year}-${month}-${day}`;

            document.getElementById('data_falta').setAttribute('max', maxDate);
        }

     
        window.onload = setMaxDate;
    </script>
</head>
<body>
    <div class="container-reposicao">
        <h1 id="Subtitulo">Justificativa de Faltas</h1>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="tipoFalta">Selecione o tipo de falta:</label>
            <select id="tipoFalta" name="tipo_falta" onchange="mostrarOpcoes()" required>
                <option value="">-- Selecione --</option>
                <option value="medica">Licença e Falta Médica</option>
                <option value="injustificada">Falta Injustificada</option>
                <option value="justificada">Falta Justificada</option>
                <option value="legislacao">Faltas Previstas na Legislação</option>
            </select>

            <div id="faltaMedica" class="opcoes-especificas hidden">
                <label for="especificaFaltaMedica">Selecione a opção específica:</label>
                <select id="especificaFaltaMedica" name="especifica_falta_medica">
                    <option value="">-- Selecione --</option>
                    <option value="falta_medica">Falta Médica</option>
                    <option value="comparecimento_medico">Comparecimento ao Médico</option>
                    <option value="licenca_saude">Licença Saúde</option>
                    <option value="licenca_maternidade">Licença Maternidade</option>
                </select>
            </div>

            <div id="faltaJustificada" class="opcoes-especificas hidden">
                <label for="especificaFaltaJustificada">Selecione a opção específica:</label>
                <select id="especificaFaltaJustificada" name="especifica_falta_justificada">
                    <option value="">-- Selecione --</option>
                    <option value="motivo">Falta por motivo específico</option>
                    <option value="saida_antecipada">Atraso ou Saída Antecipada</option>
                </select>
                <input type="text" name="motivo_justificado" placeholder="Descreva o motivo" />
            </div>

            <div id="faltaLegislacao" class="opcoes-especificas hidden">
                <label for="especificaFaltaLegislacao">Selecione a opção específica:</label>
                <select id="especificaFaltaLegislacao" name="especifica_falta_legislacao">
                    <option value="">-- Selecione --</option>
                    <option value="falecimento">Falecimento de Cônjuge, Pai, Mãe, Filho</option>
                    <option value="falecimento_outros">Falecimento de Ascendentes/Descendentes</option>
                    <option value="casamento">Casamento</option>
                    <option value="nascimento_filho">Nascimento de Filho</option>
                    <option value="acompanhamento_consulta">Acompanhamento em Consulta Médica</option>
                    <option value="doacao_sangue">Doação de Sangue</option>
                    <option value="alistamento_eleitor">Alistamento Eleitoral</option>
                    <option value="depoimento_judicial">Convocação para Depoimento Judicial</option>
                    <option value="participacao_juri">Participação em Júri</option>
                    <option value="servicos_eleitorais">Serviços Eleitorais</option>
                    <option value="prova_vestibular">Prova de Vestibular</option>
                    <option value="chamada_justica">Chamada pela Justiça do Trabalho</option>
                    <option value="acidente_transporte">Acidente de Transporte</option>
                </select>
            </div>

            
            
            <label for="periodo_dias">Período de (dias):</label>
            <input type="date" id="periodo_inicio" name="data_falta_inicio" required> Até (selecionar a mesma data em ambos caso for o período de 1 dia)
            <input type="date" id="periodo_fim" name="data_falta_fim" required>


            <label for="curso">Selecione o curso:</label>
            <select id="curso_id" name="curso_id" required>
                <option value="">-- Selecione --</option>
                <option value="4">DSM</option>
                <option value="3">GE</option>
                <option value="2">GPI</option>
                <option value="1">GTI</option>
            </select>

            <label for="turno_id">Turno:</label>
            <select id="turno_id" name="turno_id" required>
                <option value="">-- Selecione --</option>
                <option value="manha">Manhã</option>
                <option value="tarde">Tarde</option>
                <option value="noite">Noite</option>
            </select>

            <label for="horario_inicio">Horário da Ausência:</label>
<center>
            <input type="time" id="horario_inicio" name="horario_inicio" required step="600" required> Até
            <input type="time" id="horario_fim" name="horario_fim" required step="600" required>
</center>
            <label for="anexo" required>Anexe um documento (PDF):</label>
            <input type="file" id="anexo" name="anexo" accept=".pdf" required>

            <button type="submit" class="button">Enviar Formulário</button>
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