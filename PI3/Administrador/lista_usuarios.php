<?php
session_start();
require_once '../conexao.php';
include '../menu.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit;
}

$mensagem_sucesso = '';
$mensagem_erro = '';

if (isset($_SESSION['mensagem_sucesso'])) {
    $mensagem_sucesso = $_SESSION['mensagem_sucesso'];
    unset($_SESSION['mensagem_sucesso']);
}

if (isset($_SESSION['mensagem_erro'])) {
    $mensagem_erro = $_SESSION['mensagem_erro'];
    unset($_SESSION['mensagem_erro']);
}

$sql = 'SELECT * FROM usuarios';
$stmt = $conn->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../geral.css">
    <title>Lista de Usuários</title>
</head>
<body>
<?php if ($mensagem_sucesso): ?>
    <div class="alerta-balao sucesso"><?= htmlspecialchars($mensagem_sucesso) ?></div>
<?php elseif ($mensagem_erro): ?>
    <div class="alerta-balao erro"><?= htmlspecialchars($mensagem_erro) ?></div>
<?php endif; ?>

<div class="lista-user">
    <h2>Lista de Usuários</h2>
    <table class="lista-user">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Nível de Acesso</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= $usuario['id_usuario'] ?></td>
                <td><?= $usuario['nome_usuario'] ?></td>
                <td><?= $usuario['email'] ?></td>
                <td><?= $usuario['tipo_usuario'] ?></td>
                <td>
                    <a href="editar.php?id_usuario=<?= $usuario['id_usuario'] ?>" class="btn btn-warning">Editar</a>
                    <a href="#" class="btn btn-danger delete-btn" data-url="excluir.php?id_usuario=<?= $usuario['id_usuario'] ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    </table>
</div>

<div id="confirm-modal" class="modal">
    <div class="modal-content">
        <h2>Confirmação</h2>
        <p>Tem certeza que deseja excluir este usuário?</p>
        <div class="modal-actions">
            <button id="cancel-btn" class="btn btn-secondary">Cancelar</button>
            <a id="confirm-btn" class="btn btn-danger">Excluir</a>
        </div>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    width: 300px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-actions {
    display: flex;
    justify-content: space-around;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-size: 14px;
}

.btn-secondary {
    background-color: #ccc;
    color: #000;
}

.btn-danger {
    background-color: #f44336;
    color: #fff;
}

.btn-warning {
    background-color: #FFC107;
    color: #000;
}
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const modal = document.getElementById("confirm-modal");
        const confirmBtn = document.getElementById("confirm-btn");
        const cancelBtn = document.getElementById("cancel-btn");

        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", (e) => {
                e.preventDefault();
                const url = button.getAttribute("data-url");
                confirmBtn.href = url;
                modal.style.display = "flex";
            });
        });

        cancelBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });

        window.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });

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
    </footer>
</body>
</html>