<?php
session_start();
include '../conexao.php';
include '../menu.php';

function formatarDataParaBR($data) {
    if (preg_match('/^\d{4}[-\/]\d{2}[-\/]\d{2}$/', $data)) {
        $timestamp = strtotime($data);
        return date("d/m/Y", $timestamp);
    } else {
        return "Formato de data inválido!";
    }
}

$sql_reposicoes = "SELECT r.id_reposicao, u.nome_usuario, u.rm, r.data_envio 
                   FROM reposicoes r 
                   JOIN usuarios u ON r.id_usuario = u.id_usuario
                   WHERE r.status = 'pendente'";

$stmt_reposicoes = $conn->prepare($sql_reposicoes);
$stmt_reposicoes->execute();
$reposicoes = $stmt_reposicoes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="../geral.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulários Reposição</title>
    <style>
        .btn-visualizar {
            display: inline-block;
            padding: 10px 20px;
            background-color: #900; 
            color: #fff; 
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .btn-visualizar:hover {
            background-color: #700; 
        }

        .btn-visualizar:active {
            background-color: #500; 
        }
    </style>
</head>

<body>
<?php if (isset($_SESSION['mensagem_sucesso'])): ?>
    <div class="alerta-balao sucesso"><?= htmlspecialchars($_SESSION['mensagem_sucesso']) ?></div>
    <?php unset($_SESSION['mensagem_sucesso']); ?>
<?php endif; ?>

    <div class="container">
        <div class="container-justificativa">
            <h1 id="Subtitulo">Formulários Reposição</h1>
        
        <div id="visuReposicao">
            <table class="tabela-reposicao">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>RM</th>
                        <th>Data Envio</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reposicoes)): ?>
                        <?php foreach ($reposicoes as $reposicao): ?>
                            <tr>
                                <td><?= htmlspecialchars($reposicao['nome_usuario']) ?></td>
                                <td><?= htmlspecialchars($reposicao['rm']) ?></td>
                                <td><?= htmlspecialchars(formatarDataParaBR($reposicao['data_envio'])) ?></td>
                                <td>
                                    <a href="visualizarreposCoord.php?id=<?= $reposicao['id_reposicao'] ?>" class="btn-visualizar">Visualizar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Nenhuma reposição de aula encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const mensagemSucesso = document.querySelector('.alerta-balao.sucesso');
        if (mensagemSucesso) {
            mensagemSucesso.style.opacity = 1; 
            setTimeout(() => {
                mensagemSucesso.style.transition = "opacity 0.5s";
                mensagemSucesso.style.opacity = 0; 
            }, 3000); 

            setTimeout(() => {
                mensagemSucesso.remove(); 
            }, 3500);
        }
    });
</script>
</body>
</html>