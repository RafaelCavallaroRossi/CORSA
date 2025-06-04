<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cronograma de Aulas</title>
</head>
<body>
    <h1>Cronograma de Aulas</h1>
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
        Data da Aula: <input type="date" name="data_aula" required><br>
        Tema: <input type="text" name="tema" required><br>
        Conteúdo: <textarea name="conteudo" required></textarea><br>
        <input type="submit" value="Adicionar Aula">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $conn->prepare("INSERT INTO Aulas (turma_id, data_aula, tema, conteudo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['turma_id'], $_POST['data_aula'], $_POST['tema'], $_POST['conteudo']]);
        echo "Aula adicionada ao cronograma com sucesso!";
    }
    ?>
</body>
</html>