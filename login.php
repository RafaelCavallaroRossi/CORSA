<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['tipo'] = $usuario['tipo'];
        
        // Redirecionar para o painel apropriado
        if ($usuario['tipo'] == 'Secretaria') {
            header("Location: painel_secretaria.php");
        } else if ($usuario['tipo'] == 'Professor') {
            header("Location: painel_professor.php");
        }
        exit;
    } else {
        $erro = "Email ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="POST">
        Email: <input type="email" name="email" required><br>
        Senha: <input type="password" name="senha" required><br>
        <input type="submit" value="Entrar">
    </form>
    <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
</body>
</html>