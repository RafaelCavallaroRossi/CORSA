<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Vincular Alunos à Turma</title>
</head>
<body>
    <h1>Vincular Alunos à Turma</h1>
    <form method="POST">
        Turma: 
        <select name="turma_id" required>
            <?php
            $stmt = $conn->query("SELECT * FROM Turmas");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
            }
            ?>
        </select><br>
        Alunos: 
        <select name="alunos[]" multiple required>
            <?php
            $stmt = $conn->query("SELECT * FROM Alunos");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
            }
            ?>
        </select><br>
        <input type="submit" value="Vincular Alunos">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        foreach ($_POST['alunos'] as $aluno_id) {
            $stmt = $conn->prepare("INSERT INTO Alunos_Turmas (turma_id, aluno_id) VALUES (?, ?)");
            $stmt->execute([$_POST['turma_id'], $aluno_id]);
        }
        echo "Alunos vinculados à turma com sucesso!";
    }
    ?>
</body>
</html>