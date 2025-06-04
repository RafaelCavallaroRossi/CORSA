<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Turma</title>
</head>
<body>
    <h1>Criar Turma</h1>
    <form method="POST">
        Nome da Turma: <input type="text" name="nome" required><br>
        Ano: <input type="number" name="ano" required><br>
        Curso: 
        <select name="curso_id" required>
            <?php
            $stmt = $conn->query("SELECT * FROM Cursos");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
            }
            ?>
        </select><br>
        <input type="submit" value="Criar Turma">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $conn->prepare("INSERT INTO Turmas (curso_id, nome, ano) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['curso_id'], $_POST['nome'], $_POST['ano']]);
        echo "Turma criada com sucesso!";
    }
    ?>
</body>
</html>