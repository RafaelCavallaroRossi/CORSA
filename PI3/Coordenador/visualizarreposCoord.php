<?php
session_start();
include '../conexao.php';
include '../menu.php';

function formatarTexto($texto) {
    $texto = str_replace('_', ' ', $texto);
    $texto = ucwords($texto);
    return $texto;
}
function formatarDataParaBR($data) {
    if (preg_match('/^\d{4}[-\/]\d{2}[-\/]\d{2}$/', $data)) {
        $timestamp = strtotime($data);
        return date("d/m/Y", $timestamp);
    } else {
        return "Formato de data inválido!";
    }
}

$id_solicitacao = $_GET['id'] ?? null;

if ($id_solicitacao) {
    $sql = "SELECT r.*, u.nome_usuario, u.rm,j.*, a.*, c. nome_curso
            FROM reposicoes r
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            JOIN justificativas j ON r.id_justificativa = j.id_justificativa
            JOIN aulas_reposicao a ON r.id_reposicao = a.reposicao_id
            JOIN cursos c ON j.id_curso = c.id_curso
            WHERE r.id_reposicao = ?";
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

$sql_datas = "SELECT data_reposicao, horario_inicio, horario_fim FROM aulas_reposicao WHERE reposicao_id = ?";
$stmt_datas = $conn->prepare($sql_datas);
$stmt_datas->execute([$id_solicitacao]);
$datas_reposicao = $stmt_datas->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'];
    $comentario = $_POST['comentario'];
    $data_atual = $_POST['data_atual'];
    
    $sql_update = "UPDATE reposicoes SET status = ?, comentario = ?, data_resposta = ? WHERE id_reposicao = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->execute([$acao, $comentario, $data_atual, $id_solicitacao]);

    $_SESSION['mensagem_sucesso'] = 'Resposta enviada com sucesso!';
header('Location: menucoordenador.php');
exit;

}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="../geral.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Solicitação de Reposição</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .page-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            overflow-y: auto; 
        }
        .container-visualizarform {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
            overflow-y: auto; 
        }
        #mensagemvj {
            color: #d32f2f;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .detalhes-solicitacao p {
            font-size: 16px;
            margin: 8px 0;
            color: #333;
        }
        .detalhes-solicitacao a {
            color: #d32f2f;
            text-decoration: none;
        }
        .detalhes-solicitacao a:hover {
            text-decoration: underline;
        }
        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            color: #333;
        }
        .radio-group {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .radio-group input {
            margin-right: 5px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
            font-size: 16px;
            background-color: #f9f9f9;
            box-sizing: border-box;
            resize: vertical;
        }
        .btn-enviar {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            color: #fff;
            background-color: #d32f2f;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        .btn-enviar:hover {
            background-color: #b71c1c;
        }
    </style>
    <script>
        function setCurrentDate() {
            const currentDate = new Date().toISOString().split('T')[0];
            document.getElementById('data_atual').value = currentDate;
        }

        window.onload = setCurrentDate;
        
    </script>
</head>
<body>
    
    <div class="page-container">
        <div class="container-visualizarform">
            <h2 id="mensagemvj">Detalhes da Solicitação de Reposição</h2>

            <?php if (!empty($solicitacao)): ?>
    <div class="detalhes-solicitacao">
        <p><strong>Nome do Professor:</strong> <?= htmlspecialchars($solicitacao['nome_usuario']) ?></p>
        <p><strong>Matrícula:</strong> <?= htmlspecialchars($solicitacao['rm']) ?></p>
        <p><strong>Curso:</strong> <?= htmlspecialchars(formatarTexto($solicitacao['nome_curso'])) ?></p>
        <p><strong>Tipo de Falta:</strong> <?= htmlspecialchars(formatarTexto($solicitacao['tipo_falta'])) ?></p>
        <p><strong>Data da Falta:</strong> <?= htmlspecialchars(formatarDataParaBR($solicitacao['data_falta_inicio'])) ?> Até <?= htmlspecialchars(formatarDataParaBR($solicitacao['data_falta_fim'])) ?> </p>
        <p><strong>Horário de Ausência:</strong> <?= htmlspecialchars($solicitacao['horario_inicio']) ?> até <?= htmlspecialchars($solicitacao['horario_fim']) ?></p>

         <?php endif; ?>
        <?php if (!empty($solicitacao['especifica_falta_medica'])): ?>
            <p><strong>Especificação:</strong> <?= htmlspecialchars(formatarTexto($solicitacao['especifica_falta_medica'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($solicitacao['especifica_falta_justificada'])): ?>
            <p><strong>Especificação:</strong> <?= htmlspecialchars(formatarTexto($solicitacao['especifica_falta_justificada'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($solicitacao['especifica_falta_legislacao'])): ?>
            <p><strong>Especificação:</strong> <?= htmlspecialchars(formatarTexto($solicitacao['especifica_falta_legislacao'])) ?></p>
        <?php endif; ?>
   
        <?php if (!empty($datas_reposicao)): $cont=1;?>
            <?php foreach ($datas_reposicao as $data):?>
                        <label for="data_reposicao_">Data da Reposição <?= $cont ?> :</label>
                        <input type="date" id="data_reposicao_" name="data_reposicao" value="<?= htmlspecialchars($data['data_reposicao']) ?>" readonly>
                        <label for="horario_resposicao_">Horario da Reposição <?= $cont ?> :</label>
                        <input type="time" id="horario_inicio" name="horario_inicio" value="<?= htmlspecialchars($data['horario_inicio']) ?>" readonly> Até 
                        <input type="time" id="horario_fim" name="horario_fim" value="<?= htmlspecialchars($data['horario_fim']) ?>" readonly>
            <?php 
        $cont++;
        endforeach; ?>
       
        <p><strong>Anexo:</strong> <a href="<?= htmlspecialchars($solicitacao['anexo']) ?>" target="_blank">Visualizar Documento</a></p>
    </div>
<?php endif; ?>

            <form action="" method="post">
                <input type="hidden" name="id_solicitacao" value="<?= htmlspecialchars($solicitacao['id_reposicao']) ?>">
                <input type="hidden" id="data_atual" name="data_atual">
                <label for="acao">Ação do Coordenador:</label>
                <div class="radio-group">
                    <input type="radio" id="aceitar" name="acao" value="aceito" required>
                    <label for="aceitar">Aceitar</label>

                    <input type="radio" id="recusar" name="acao" value="recusado" required>
                    <label for="recusar">Recusar</label>
                </div>

                <label for="comentario">Comentário do Coordenador:</label>
                <textarea id="comentario" name="comentario" rows="4" placeholder="Adicione um comentário (opcional)"></textarea>

                <button type="submit" class="btn-enviar">Enviar Resposta</button>
            </form>
        </div>
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