<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Aluno</title>
</head>
<body>
    <h1>Cadastrar Aluno</h1>
    <form method="POST">
        Nome: <input type="text" name="nome" required><br>
        Email: <input type="email" name="email" required><br>
        Matrícula: <input type="text" name="matricula" required><br>
        CPF: <input type="text" name="cpf" required><br>
        RG: <input type="text" name="rg" required><br>
        Data de Nascimento: <input type="date" name="data_nascimento"><br>
        Telefone: <input type="text" name="telefone"><br>
        <input type="submit" value="Cadastrar">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $conn->prepare("INSERT INTO Alunos (nome, email, matricula, cpf, rg, data_nascimento, telefone) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['nome'], $_POST['email'], $_POST['matricula'], $_POST['cpf'], $_POST['rg'], $_POST['data_nascimento'], $_POST['telefone']]);
        echo "Aluno cadastrado com sucesso!";
    }
    ?>
</body>
</html>