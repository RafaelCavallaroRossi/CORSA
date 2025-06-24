<?php include 'config.php'; ?>
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar Aulas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex items-center justify-center p-4" style="padding-top: 88px;">
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
                            $selected = (isset($_POST['turma_id']) && $_POST['turma_id'] == $row['id']) ? 'selected' : '';
                            echo "<option value='{$row['id']}' $selected>{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="mostrar_ministradas" id="mostrar_ministradas" class="h-4 w-4 text-blue-600 border-gray-300 rounded" <?= isset($_POST['mostrar_ministradas']) ? 'checked' : '' ?>>
                    <label for="mostrar_ministradas" class="text-sm text-gray-700">Mostrar aulas já ministradas</label>
                </div>
                <div class="flex flex-col sm:flex-row justify-end sm:space-x-2 space-y-2 sm:space-y-0">
                    <a href="painel_professor.php" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancelar</a>
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ver Aulas
                    </button>
                </div>
            </form>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $turma_id = $_POST['turma_id'];
                $mostrar_ministradas = isset($_POST['mostrar_ministradas']);

                $sql = "
                    SELECT a.*, 
                        CASE 
                            WHEN COUNT(f.id) > 0 THEN 1
                            ELSE 0
                        END AS ministrada
                    FROM Aulas a
                    LEFT JOIN Frequencia f ON f.aula_id = a.id
                    WHERE a.turma_id = ?
                    GROUP BY a.id
                    ORDER BY a.data_aula ASC
                ";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$turma_id]);
                $aulas = $stmt->fetchAll();

                echo "<div class='overflow-x-auto'><table class='min-w-full divide-y divide-gray-200 border'>
                        <thead class='bg-gray-100'>
                            <tr>
                                <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase whitespace-nowrap'>Data</th>
                                <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Tema</th>
                                <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Conteúdo</th>
                                <th class='px-4 py-2 text-center text-xs font-medium text-gray-700 uppercase'>Status</th>
                                <th class='px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase'>Ação</th>
                            </tr>
                        </thead>
                        <tbody class='bg-white divide-y divide-gray-200'>";

                foreach ($aulas as $aula) {
                    $ministrada = $aula['ministrada'] == 1;
                    if (!$mostrar_ministradas && $ministrada) continue;

                    $statusIcon = $ministrada
                        ? "<i class='fas fa-check text-green-600'></i>"
                        : "<i class='fas fa-times text-red-600'></i>";

                    echo "<tr>
                        <td class='px-4 py-2 whitespace-nowrap'>{$aula['data_aula']}</td>
                        <td class='px-4 py-2'>{$aula['tema']}</td>
                        <td class='px-4 py-2'>{$aula['conteudo']}</td>
                        <td class='px-4 py-2 text-center'>$statusIcon</td>
                        <td class='px-4 py-2'>
                            <a href='lista_frequencia.php?aula_id={$aula['id']}&turma_id={$aula['turma_id']}' class='text-blue-600 hover:underline'>Registrar Frequência</a>
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
