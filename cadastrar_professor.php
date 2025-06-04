<?php
session_start();
include 'config.php';

// Verificação de sessão
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'Secretaria') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $matricula = $_POST['matricula'];
    $cpf = $_POST['cpf'];
    $rg = $_POST['rg'];
    $data_nascimento = $_POST['data_nascimento'];
    $telefone = $_POST['telefone'];
    $tipo = 'Professor';

    $stmt = $conn->prepare("INSERT INTO Usuarios (nome, email, senha, matricula, cpf, rg, data_nascimento, telefone, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$nome, $email, $senha, $matricula, $cpf, $rg, $data_nascimento, $telefone, $tipo])) {
        echo "Professor cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar professor.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Professor</title>
</head>
<body>
    <h1>Cadastrar Professor</h1>
    <form method="POST">
        Nome: <input type="text" name="nome" required><br>
        Email: <input type="email" name="email" required><br>
        Senha: <input type="password" name="senha" required><br>
        Matrícula: <input type="text" name="matricula" required><br>
        CPF: <input type="text" name="cpf" required><br>
        RG: <input type="text" name="rg" required><br>
        Data de Nascimento: <input type="date" name="data_nascimento" required><br>
        Telefone: <input type="text" name="telefone"><br>
        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>