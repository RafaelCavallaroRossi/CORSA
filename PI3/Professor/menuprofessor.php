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

$id_usuario = $_SESSION['id_usuario'];

$mensagem_sucesso = '';
if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem_sucesso = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']); 
}

$sql_justificativas = "SELECT j.*, u.nome_usuario, u.rm
                       FROM justificativas j 
                       JOIN usuarios u ON j.id_usuario = u.id_usuario
                       WHERE j.id_usuario = ? AND j.status = 'pendente'";
$stmt_justificativas = $conn->prepare($sql_justificativas);
$stmt_justificativas->execute([$id_usuario]);
$justificativas = $stmt_justificativas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Professor</title>
    <link rel="stylesheet" href="../geral.css">
    <style>
        .container {
            width: 90%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .centralizar-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            margin: 0;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .button-group a {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            color: #900;
            border: 2px solid #900;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s;
        }

        .button-group a:hover {
            background-color: #f5f5f5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        table th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
        }

        .action-button {
            display: inline-block;
            padding: 5px 10px;
            background-color: #900;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .action-button:hover {
            background-color: #700;
        }
    </style>
</head>
<body>

<?php
$status = $_GET['status'] ?? null;

if ($status === 'reenviado') {
    $mensagem_reenvio = 'Formulário reenviado com sucesso!';
}
?>

<?php if ($mensagem_sucesso): ?>
    <div class="alerta-balao sucesso"><?= htmlspecialchars($mensagem_sucesso) ?></div>
<?php endif; ?>

<?php if (isset($mensagem_reenvio)): ?>
    <div class="alerta-balao sucesso"><?= htmlspecialchars($mensagem_reenvio) ?></div>
<?php endif; ?>

<div class="centralizar-container">
    <div class="container">
        <div class="button-group">
            <a href="formfaltateste.php">Nova Justificativa</a>
            <a href="acompanhamento.php">Acompanhar Justificativas</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tipo de Falta</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Fazer Reposição</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($justificativas as $justificativa): ?>
                <tr>
                    <td><?= htmlspecialchars(formatarTexto($justificativa['tipo_falta'])) ?></td>
                    <td><?= htmlspecialchars(formatarDataParaBR($justificativa['data_envio'])) ?></td>
                    <td><?= htmlspecialchars($justificativa['horario_inicio']) ?></td>
                    <td><center><a href="formreposicaoteste.php?id=<?= $justificativa['id_justificativa'] ?>" class="action-button">Preencher</a></td></center>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const alerts = document.querySelectorAll('.alerta-balao');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease-in-out';
                setTimeout(() => alert.remove(), 500); 
            }, 5000); 
        });
    });
</script>

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