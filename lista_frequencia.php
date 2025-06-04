<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar Frequência</title>
    <script>
        function carregarAulas() {
            const turmaSelect = document.getElementById("turma_id");
            const aulaSelect = document.getElementById("aula_id");
            aulaSelect.innerHTML = ""; // Limpar aulas

            if (turmaSelect.value) {
                fetch(`get_aulas.php?turma_id=${turmaSelect.value}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(aula => {
                            const option = document.createElement("option");
                            option.value = aula.id;
                            option.textContent = `${aula.tema} - ${aula.data_aula}`;
                            aulaSelect.appendChild(option);
                        });
                    });
            }
        }

        function carregarFrequencia() {
            const aulaSelect = document.getElementById("aula_id");
            const frequenciaDiv = document.getElementById("frequenciaDiv");
            frequenciaDiv.innerHTML = ""; // Limpar frequências anteriores

            if (aulaSelect.value) {
                fetch(`get_frequencia.php?Aula_id=${aulaSelect.value}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            let table = "<table border='1'><tr><th>Aluno</th><th>Presença</th></tr>";
                            data.forEach(item => {
                                table += `<tr>
                                              <td>${item.aluno}</td>
                                              <td><input type='checkbox' name='presentes[]' value='${item.id}'></td>
                                          </tr>`;
                            });
                            table += "</table>";
                            frequenciaDiv.innerHTML = table;
                        } else {
                            frequenciaDiv.innerHTML = "Nenhuma frequência registrada para esta aula.";
                        }
                    });
            }
        }
    </script>
</head>
<body>
    <h1>Registrar Frequência</h1>
    <form method="POST">
        Turma: 
        <select id="turma_id" name="turma_id" required onchange="carregarAulas()">
            <option value="">Selecione</option>
            <?php
            $stmt = $conn->query("SELECT * FROM Turmas");
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['id']}'>{$row['nome']}</option>";
            }
            ?>
        </select><br>
        
        Aula: 
        <select id="aula_id" name="aula_id" required onchange="carregarFrequencia()">
            <option value="">Selecione uma turma primeiro</option>
        </select><br>
        
        <div id="frequenciaDiv">
            <!-- As frequências serão carregadas aqui -->
        </div>
        
        <input type="submit" value="Registrar Frequência">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aula_id'])) {
        // Registrar presenças
        foreach ($_POST['presentes'] as $aluno_id) {
            $stmt = $conn->prepare("INSERT INTO Frequencia (aluno_id, aula_id, status) VALUES (?, ?, 'Presente')");
            $stmt->execute([$aluno_id, $_POST['aula_id']]);
        }

        // Registrar alunos ausentes
        $stmt = $conn->prepare("SELECT Alunos.id FROM Alunos");
        $stmt->execute();
        $todos_alunos = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($todos_alunos as $aluno_id) {
            if (!in_array($aluno_id, $_POST['presentes'])) {
                $stmt = $conn->prepare("INSERT INTO Frequencia (aluno_id, aula_id, status) VALUES (?, ?, 'Ausente')");
                $stmt->execute([$aluno_id, $_POST['aula_id']]);
            }
        }

        echo "Frequência registrada com sucesso!";
    }
    ?>
</body>
</html>