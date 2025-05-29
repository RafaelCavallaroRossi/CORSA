<?php
session_start();
include '../conexao.php';
include '../menu.php';

$id = $_GET['id_usuario'];
$usuario = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$usuario->execute([$id]);
$usuario = $usuario->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $tipo_usuario = $_POST['nivel'];

    $stmt = $conn->prepare("UPDATE usuarios SET nome_usuario = ?, email = ?, tipo_usuario = ? WHERE id_usuario = ?");
    $stmt->execute([$nome, $email, $tipo_usuario, $id]);

    $_SESSION['mensagem_sucesso'] = "Usuário editado com sucesso!";
    header("Location: lista_usuarios.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="../geral.css">
</head>
<body>
    <?php
    if (isset($_SESSION['mensagem_sucesso'])) {
        echo "<div class='alerta-balao sucesso'>{$_SESSION['mensagem_sucesso']}</div>";
        unset($_SESSION['mensagem_sucesso']);
    }
    ?>

    <div class="container-editar">
        <h1 class="mt-4">Editar Usuário</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form_group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($usuario['nome_usuario']) ?>" required>
            </div>
            <div class="form_group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>
            <div class="form_group">
                <label for="nivel">Nível de Acesso:</label>
                <select name="nivel" id="nivel" required>
                    <option value="">Selecione...</option>
                    <option value="professor" <?= $usuario['tipo_usuario'] === 'professor' ? 'selected' : '' ?>>Professor</option>
                    <option value="coordenador" <?= $usuario['tipo_usuario'] === 'coordenador' ? 'selected' : '' ?>>Coordenador</option>
                    <option value="administrador" <?= $usuario['tipo_usuario'] === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>
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