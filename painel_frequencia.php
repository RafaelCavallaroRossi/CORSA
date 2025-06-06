<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel de Frequência</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #065f46 100%);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center gradient-bg p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Listas de Frequência</h1>
                <p class="text-gray-600">Consulte a frequência dos alunos por aula</p>
            </div>
            <form method="GET" class="space-y-4 mb-6">
                <div>
                    <label for="aula_id" class="block text-sm font-medium text-gray-700">Aula</label>
                    <select id="aula_id" name="aula_id" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione uma Aula</option>
                        <?php
                        $stmt = $conn->query("SELECT * FROM Aulas");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['tema']} - {$row['data_aula']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ver Frequência
                    </button>
                </div>
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
                    echo "<div class='overflow-x-auto'><table class='min-w-full divide-y divide-gray-200 border'>
                            <thead class='bg-gray-100'>
                                <tr>
                                    <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Aluno</th>
                                    <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Status</th>
                                </tr>
                            </thead>
                            <tbody class='bg-white divide-y divide-gray-200'>";
                    foreach ($frequencias as $row) {
                        echo "<tr>
                                <td class='px-4 py-2'>{$row['aluno']}</td>
                                <td class='px-4 py-2'>{$row['status']}</td>
                              </tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<div class='mt-4 text-center text-gray-600'>Nenhuma frequência registrada para esta aula.</div>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
