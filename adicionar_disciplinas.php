<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Disciplinas</title>
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
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Adicionar Disciplinas</h1>
                <p class="text-gray-600">Adicione disciplinas ao curso</p>
            </div>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $curso_id = $_POST['curso_id'];
                $nome_disciplina = $_POST['nome_disciplina'];

                // Adicionar disciplina ao curso
                $stmt = $conn->prepare("INSERT INTO Disciplinas (curso_id, nome) VALUES (?, ?)");
                $stmt->execute([$curso_id, $nome_disciplina]);
                echo '<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">Disciplina \'' . htmlspecialchars($nome_disciplina) . '\' adicionada ao curso com sucesso!</div>';

                // Formulário para adicionar mais disciplinas
                echo '
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Adicionar Mais Disciplinas</h2>
                    <form method="POST" action="adicionar_disciplinas.php" class="space-y-4">
                        <input type="hidden" name="curso_id" value="' . $curso_id . '">
                        <div>
                            <label for="nome_disciplina" class="block text-sm font-medium text-gray-700">Nome da Disciplina</label>
                            <input type="text" id="nome_disciplina" name="nome_disciplina" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Nome da Disciplina">
                        </div>
                        <div>
                            <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Adicionar Disciplina
                            </button>
                        </div>
                    </form>
                </div>';
            } else {
                echo '
                <form method="POST" action="adicionar_disciplinas.php" class="space-y-4">
                    <input type="hidden" name="curso_id" value="' . (isset($_POST['curso_id']) ? htmlspecialchars($_POST['curso_id']) : '') . '">
                    <div>
                        <label for="nome_disciplina" class="block text-sm font-medium text-gray-700">Nome da Disciplina</label>
                        <input type="text" id="nome_disciplina" name="nome_disciplina" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nome da Disciplina">
                    </div>
                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Adicionar Disciplina
                        </button>
                    </div>
                </form>';
            }
            ?>
        </div>
    </div>
</body>
</html>
