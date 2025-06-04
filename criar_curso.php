<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Curso</title>
</head>
<body>
    <h1>Criar Curso</h1>
    <form method="POST">
        Nome do Curso: <input type="text" name="nome" required><br>
        Descrição: <textarea name="descricao" required></textarea><br>
        <input type="submit" value="Criar Curso">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Criar curso
        $stmt = $conn->prepare("INSERT INTO Cursos (nome, descricao) VALUES (?, ?)");
        $stmt->execute([$_POST['nome'], $_POST['descricao']]);
        $curso_id = $conn->lastInsertId();
        echo "Curso criado com sucesso!<br>";

        // Formulário para adicionar disciplinas
        echo '<h2>Adicionar Disciplinas ao Curso</h2>';
        echo '<form method="POST" action="adicionar_disciplinas.php">
                <input type="hidden" name="curso_id" value="' . $curso_id . '">
                Nome da Disciplina: <input type="text" name="nome_disciplina" required><br>
                <input type="submit" value="Adicionar Disciplina">
              </form>';
    }
    ?>
</body>
</html>