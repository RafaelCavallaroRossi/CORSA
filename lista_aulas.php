<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Aulas</title>
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
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Lista de Aulas</h1>
                <p class="text-gray-600">Consulte as aulas cadastradas por turma</p>
            </div>
            <form method="POST" class="space-y-4 mb-6">
                <div>
                    <label for="turma_id" class="block text-sm font-medium text-gray-700">Turma</label>
                    <select id="turma_id" name="turma_id" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <?php
                        $stmt = $conn->query("SELECT * FROM Turmas");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ver Aulas
                    </button>
                </div>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $stmt = $conn->prepare("SELECT * FROM Aulas WHERE turma_id = ?");
                $stmt->execute([$_POST['turma_id']]);
                $aulas = $stmt->fetchAll();

                echo "<div class='overflow-x-auto'><table class='min-w-full divide-y divide-gray-200 border'>
                        <thead class='bg-gray-100'>
                            <tr>
                                <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Data</th>
                                <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Tema</th>
                                <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Conteúdo</th>
                                <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Registrar Frequência</th>
                            </tr>
                        </thead>
                        <tbody class='bg-white divide-y divide-gray-200'>";

                foreach ($aulas as $aula) {
                    echo "<tr>
                            <td class='px-4 py-2'>{$aula['data_aula']}</td>
                            <td class='px-4 py-2'>{$aula['tema']}</td>
                            <td class='px-4 py-2'>{$aula['conteudo']}</td>
                            <td class='px-4 py-2'>
                                <a href='lista_frequencia.php?aula_id={$aula['id']}' class='text-blue-600 hover:underline'>Registrar Frequência</a>
                            </td>
                          </tr>";
                }
                echo "</tbody></table></div>";
            }
            ?>
        </div>
    </div>
</body>
</html>
