<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Disciplinas</title>
</head>
<body>
    <h1>Adicionar Disciplinas</h1>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $curso_id = $_POST['curso_id'];
        $nome_disciplina = $_POST['nome_disciplina'];

        // Adicionar disciplina ao curso
        $stmt = $conn->prepare("INSERT INTO Disciplinas (curso_id, nome) VALUES (?, ?)");
        $stmt->execute([$curso_id, $nome_disciplina]);
        echo "Disciplina '{$nome_disciplina}' adicionada ao curso com sucesso!<br>";

        // Formulário para adicionar mais disciplinas
        echo '<h2>Adicionar Mais Disciplinas</h2>';
        echo '<form method="POST" action="adicionar_disciplinas.php">
                <input type="hidden" name="curso_id" value="' . $curso_id . '">
                Nome da Disciplina: <input type="text" name="nome_disciplina" required><br>
                <input type="submit" value="Adicionar Disciplina">
              </form>';
    } else {
        echo "Nenhuma disciplina foi adicionada.";
    }
    ?>
</body>
</html>