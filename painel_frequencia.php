<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Frequência</title>
</head>
<body>
    <h1>Listas de Frequência</h1>
    <form method="GET">
        Aula: 
        <select id="aula_id" name="aula_id" required>
            <option value="">Selecione uma Aula</option>
            <?php
            $stmt = $conn->query("SELECT * FROM Aulas");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['id']}'>{$row['tema']} - {$row['data_aula']}</option>";
            }
            ?>
        </select>
        <input type="submit" value="Ver Frequência">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['aula_id'])) {
        $aula_id = $_GET['aula_id'];
        $stmt = $conn->prepare("
            SELECT Alunos.nome AS aluno, Frequencia.status 
            FROM Frequencia 
            JOIN Alunos ON Frequencia.aluno_id = Alunos.id 
            WHERE Frequencia.aula_id = ?
        ");
        $stmt->execute([$aula_id]);
        $frequencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($frequencias) {
            echo "<table border='1'><tr><th>Aluno</th><th>Status</th></tr>";
            foreach ($frequencias as $row) {
                echo "<tr>
                        <td>{$row['aluno']}</td>
                        <td>{$row['status']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "Nenhuma frequência registrada para esta aula.";
        }
    }
    ?>
</body>
</html>