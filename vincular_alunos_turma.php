<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Vincular Alunos à Turma</title>
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
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Vincular Alunos à Turma</h1>
                <p class="text-gray-600">Selecione a turma e os alunos para vincular</p>
            </div>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                echo '<div class="mb-4">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            Alunos vinculados à turma com sucesso!
                        </div>
                      </div>';
            }
            ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label for="turma_id" class="block text-sm font-medium text-gray-700">Turma</label>
                    <select name="turma_id" id="turma_id" required
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
                    <label for="alunos" class="block text-sm font-medium text-gray-700">Alunos</label>
                    <select name="alunos[]" id="alunos" multiple required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 h-32">
                        <?php
                        $stmt = $conn->query("SELECT * FROM Alunos");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                    <span class="text-xs text-gray-500">Segure Ctrl (Windows) ou Command (Mac) para selecionar vários alunos.</span>
                </div>
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Vincular Alunos
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
