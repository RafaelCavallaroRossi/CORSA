<?php
include 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Disciplinas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex items-center justify-center p-4" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Adicionar Disciplinas</h1>
                <p class="text-gray-600">Adicione disciplinas ao curso</p>
            </div>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['curso_id'], $_POST['nome_disciplina'])) {
                $curso_id = $_POST['curso_id'];
                $nome_disciplina = $_POST['nome_disciplina'];
                $stmt = $conn->prepare("INSERT INTO Disciplinas (curso_id, nome) VALUES (?, ?)");
                $stmt->execute([$curso_id, $nome_disciplina]);
                echo '<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">Disciplina \'' . htmlspecialchars($nome_disciplina) . '\' adicionada ao curso com sucesso!</div>';
                echo '
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Adicionar Mais Disciplinas</h2>
                    <form method="POST" action="adicionar_disciplinas.php" class="space-y-4">
                        <input type="hidden" name="curso_id" value="' . htmlspecialchars($curso_id) . '">
                        <div>
                            <label for="nome_disciplina" class="block text-sm font-medium text-gray-700">Nome da Disciplina</label>
                            <input type="text" id="nome_disciplina" name="nome_disciplina" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Nome da Disciplina">
                        </div>
                        <div class="flex flex-col sm:flex-row justify-end sm:space-x-2 space-y-2 sm:space-y-0">
                            <a href="menu.php" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Voltar ao Menu</a>
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Adicionar Disciplina
                            </button>
                        </div>
                    </form>
                </div>';
            } else {
                $curso_id = isset($_POST['curso_id']) ? htmlspecialchars($_POST['curso_id']) : '';
                echo '
                <form method="POST" action="adicionar_disciplinas.php" class="space-y-4">
                    <input type="hidden" name="curso_id" value="' . $curso_id . '">
                    <div>
                        <label for="nome_disciplina" class="block text-sm font-medium text-gray-700">Nome da Disciplina</label>
                        <input type="text" id="nome_disciplina" name="nome_disciplina" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Nome da Disciplina">
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end sm:space-x-2 space-y-2 sm:space-y-0">
                        <a href="menu.php" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Voltar ao Menu</a>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
