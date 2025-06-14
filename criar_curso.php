<?php
include 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Curso</title>
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
    <header class="w-full bg-blue-900 text-white py-4 px-6 flex justify-between items-center shadow-md fixed top-0 left-0 z-10">
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-school text-2xl"></i>
            <span class="font-bold text-lg">Escolinha do...</span>
        </div>
        <div class="flex items-center space-x-4">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <span class="hidden sm:inline">
                    Olá, <?php echo htmlspecialchars($_SESSION['nome'] ?? $_SESSION['tipo'] ?? 'Usuário'); ?>
                </span>
                <form action="logout.php" method="post" class="inline">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white font-medium transition">Sair</button>
                </form>
            <?php endif; ?>
        </div>
    </header>
    <div class="w-full min-h-screen gradient-bg flex items-center justify-center p-4" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Criar Curso</h1>
                <p class="text-gray-600">Cadastre um novo curso no sistema</p>
            </div>
            <form class="space-y-4" method="POST">
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome do Curso</label>
                    <input type="text" id="nome" name="nome" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Nome do Curso">
                </div>
                <div>
                    <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea id="descricao" name="descricao" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Descrição do curso"></textarea>
                </div>
                <div class="flex flex-col sm:flex-row justify-end sm:space-x-2 space-y-2 sm:space-y-0">
                    <a href="menu.php" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancelar</a>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Criar Curso
                    </button>
                </div>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $stmt = $conn->prepare("INSERT INTO Cursos (nome, descricao) VALUES (?, ?)");
                $stmt->execute([$_POST['nome'], $_POST['descricao']]);
                $curso_id = $conn->lastInsertId();
                echo '<div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">Curso criado com sucesso!</div>';
                echo '
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Adicionar Disciplinas ao Curso</h2>
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
            }
            ?>
        </div>
    </div>
</body>
</html>
