<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Aulas</title>
</head>
<body>
    <h1>Lista de Aulas</h1>
    <form method="POST">
        Turma: 
        <select name="turma_id" required>
            <?php
            $stmt = $conn->query("SELECT * FROM Turmas");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
            }
            ?>
        </select>
        <input type="submit" value="Ver Aulas">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $conn->prepare("SELECT * FROM Aulas WHERE turma_id = ?");
        $stmt->execute([$_POST['turma_id']]);
        $aulas = $stmt->fetchAll();

        echo "<table border='1'>
                <tr>
                    <th>Data</th>
                    <th>Tema</th>
                    <th>Conteúdo</th>
                    <th>Registrar Frequência</th>
                </tr>";

        foreach ($aulas as $aula) {
            echo "<tr>
                    <td>{$aula['data_aula']}</td>
                    <td>{$aula['tema']}</td>
                    <td>{$aula['conteudo']}</td>
                    <td>
                        <a href='lista_frequencia.php?aula_id={$aula['id']}'>Registrar Frequência</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    }
    ?>
</body>
</html>