<?php
session_start();
include '../conexao.php';
include '../menu.php';

$id = $_GET['id_reposicao'];
$reposicao = $conn->prepare("SELECT r.*, u.nome_usuario, u.rm, j.*, a.*
            FROM reposicoes r
            JOIN usuarios u ON r.id_usuario = u.id_usuario
            JOIN justificativas j ON r.id_justificativa = j.id_justificativa
            JOIN aulas_reposicao a ON r.id_reposicao = a.reposicao_id
            WHERE r.id_reposicao = ?");
$reposicao->execute([$id]);
$reposicao = $reposicao->fetch(PDO::FETCH_ASSOC);


$sql_datas = "SELECT data_reposicao, horario_inicio, horario_fim FROM aulas_reposicao WHERE reposicao_id = ?";
$stmt_datas = $conn->prepare($sql_datas);
$stmt_datas->execute([$id]);
$datas_reposicao = $stmt_datas->fetchAll(PDO::FETCH_ASSOC);




if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $datas_reposicao = $_POST['data_reposicao'];
    $horarios_inicio = $_POST['horario_inicio'];
    $horarios_fim = $_POST['horario_fim'];
    $quantidade_aulas = $reposicao['quantidade_aulas'];
    $id_justificativa = $reposicao['id_justificativa'];

    $stmt_delete = $conn->prepare("DELETE FROM aulas_reposicao WHERE reposicao_id = ?");
    $stmt_delete->execute([$id]);

for ($i = 0; $i < $quantidade_aulas; $i++) {
    if (!empty($datas_reposicao[$i]) && !empty($horarios_inicio[$i]) && !empty($horarios_fim[$i])) {
        $sql_aula = "INSERT INTO aulas_reposicao (
                        reposicao_id, data_reposicao, horario_inicio, horario_fim
                     ) VALUES (?, ?, ?, ?)";
        $stmt_aula = $conn->prepare($sql_aula);
        $stmt_aula->execute([
            $id, $datas_reposicao[$i], $horarios_inicio[$i], $horarios_fim[$i]
        ]);
    }
}
    // Verifique se um novo arquivo foi enviado
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
        $arquivoNovo = $_FILES['anexo'];
        $nomeArquivoNovo = uniqid() . "-" . $arquivoNovo['name'];
        
        // Mover o novo arquivo para a pasta de uploads
        move_uploaded_file($arquivoNovo['tmp_name'], "../uploads/$nomeArquivoNovo");

        $arquvionovomesmo = "../uploads/$nomeArquivoNovo";

        // Exclua o arquivo anterior, se existir
        if (file_exists("../" . $reposicao['anexo'])) {
            unlink("../" . $reposicao['anexo']);
        }

        // Atualize o banco de dados com o novo arquivo
        $stmt_justificativas = $conn->prepare("UPDATE justificativas SET anexo = ? WHERE id_justificativa = ?");
        $stmt_justificativas->execute([$nomeArquivoNovo, $id_justificativa]);
        
        // Atualiza a tabela reposicoes
        $stmt_reposicoes = $conn->prepare("UPDATE reposicoes SET status = 'pendente' WHERE id_reposicao = ?");
        $stmt_reposicoes->execute([$id]);
    }

    // Redirecionar após a atualização
    header("Location: acompanhamento.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="../geral.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reposição de Aula</title>
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
            overflow-y: auto; /* Ativa a rolagem vertical */
        }
        .container-editarform {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 600px;
            overflow-y: auto; /* Ativa a rolagem vertical */
        }
        label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            color: #333;
        }
        input[type="date"],
        input[type="time"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
            background-color: #f9f9f9;
        }
        .btn-enviar,
        .btn-adicionar {
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
        .btn-enviar:hover,
        .btn-adicionar:hover {
            background-color: #b71c1c;
        }
        .reposicao-item {
            margin-top: 20px;
        }
        .aula-reposicao {
            display: block;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="container-editarform">
            <h2>Editar Solicitação de Reposição</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="anexo">Anexar novo documento:</label>
                <input type="file" id="anexo" name="anexo" accept=".pdf,.jpg,.jpeg,.png">

                <label>Datas e Horários da Reposição:</label>
                <div class="aulas-reposicao" id="datas-container">
                    <div class="aula-reposicao">
                        <?php if (!empty($datas_reposicao)): $cont=1;?>
                        <?php foreach ($datas_reposicao as $data):?>
                        <label for="data_reposicao_">Data da Reposição <?= $cont ?> :</label>
                        <input type="date" name="data_reposicao[]" value="<?= htmlspecialchars($data['data_reposicao']) ?>">
                        <label for="horario_resposicao_">Horario da Reposição <?= $cont ?> :</label>
                        <input type="time" name="horario_inicio[]" value="<?= htmlspecialchars($data['horario_inicio']) ?>"> Até 
                        <input type="time" name="horario_fim[]" value="<?= htmlspecialchars($data['horario_fim']) ?>"> 
                        <?php 
                    $cont++;
                    endforeach; ?>
                    <?php endif; ?>
                    </div>
                </div>
                <button type="submit" class="btn-enviar">Enviar para o Coordenador</button>
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