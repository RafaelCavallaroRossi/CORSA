<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Frequência</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #065f46 100%);
        }
    </style>
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
                    let table = "<table class='min-w-full divide-y divide-gray-200 border'><thead class='bg-gray-100'><tr><th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Aluno</th><th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Presença</th></tr></thead><tbody class='bg-white divide-y divide-gray-200'>";
                    data.forEach(item => {
                        table += `<tr>
                                      <td class='px-4 py-2'>${item.aluno}</td>
                                      <td class='px-4 py-2'><input type='checkbox' name='presentes[]' value='${item.id}'></td>
                                  </tr>`;
                    });
                    table += "</tbody></table>";
                    frequenciaDiv.innerHTML = table;
                } else {
                    frequenciaDiv.innerHTML = "Nenhum aluno encontrado para esta aula.";
                }
            })
            .catch(error => {
                console.error('Erro ao carregar frequência:', error);
                frequenciaDiv.innerHTML = "Erro ao carregar os dados.";
            });
    } else {
        frequenciaDiv.innerHTML = "Selecione uma aula.";
    }
}
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center gradient-bg p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Registrar Frequência</h1>
                <p class="text-gray-600">Marque a presença dos alunos na aula selecionada</p>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="turma_id" class="block text-sm font-medium text-gray-700">Turma</label>
                    <select id="turma_id" name="turma_id" required onchange="carregarAulas()"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione</option>
                        <?php
                        $stmt = $conn->query("SELECT * FROM Turmas");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label for="aula_id" class="block text-sm font-medium text-gray-700">Aula</label>
                    <select id="aula_id" name="aula_id" required onchange="carregarFrequencia()"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione uma turma primeiro</option>
                </select>

                <div id="frequenciaDiv">
                    <!-- As frequências serão carregadas aqui -->
                </div>
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Registrar Frequência
                    </button>
                </div>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aula_id'])) {
    // Verificar se já foi registrada a frequência para a aula
    $aula_id = $_POST['aula_id'];
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Frequencia WHERE aula_id = ?");
    $stmt->execute([$aula_id]);
    $frequencia_existente = $stmt->fetchColumn();

    if ($frequencia_existente > 0) {
        echo '<div class="mt-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">Frequência já registrada para esta aula!</div>';
    } else {
        // Registrar presenças
        if (isset($_POST['presentes'])) {
            foreach ($_POST['presentes'] as $aluno_id) {
                $stmt = $conn->prepare("INSERT INTO Frequencia (aluno_id, aula_id, status) VALUES (?, ?, 'Presente')");
                $stmt->execute([$aluno_id, $aula_id]);
            }
        }

        // Registrar alunos ausentes
        $stmt = $conn->prepare("SELECT Alunos.id FROM Alunos");
        $stmt->execute();
        $todos_alunos = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($todos_alunos as $aluno_id) {
            if (!isset($_POST['presentes']) || !in_array($aluno_id, $_POST['presentes'])) {
                $stmt = $conn->prepare("INSERT INTO Frequencia (aluno_id, aula_id, status) VALUES (?, ?, 'Ausente')");
                $stmt->execute([$aluno_id, $aula_id]);
            }
        }

        echo '<div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">Frequência registrada com sucesso!</div>';
    }
}
?>
        </div>
    </div>
</body>
</html>