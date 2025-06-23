<?php
include 'config.php';
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Criar Turma</title>
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
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Criar Turma</h1>
                <p class="text-gray-600">Cadastre uma nova turma no sistema</p>
            </div>
            <form class="space-y-4" method="POST">
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Turma</label>
                    <input type="text" id="nome" name="nome" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Nome da Turma">
                </div>
                <div>
                    <label for="ano" class="block text-sm font-medium text-gray-700">Ano</label>
                    <input type="number" id="ano" name="ano" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ano">
                </div>
                <div>
                    <label for="curso_id" class="block text-sm font-medium text-gray-700">Curso</label>
                    <select id="curso_id" name="curso_id" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <?php
                        $stmt = $conn->query("SELECT * FROM Cursos");
                        while ($row = $stmt->fetch()) {
                            echo "<option value='{$row['id']}'>{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="flex flex-col sm:flex-row justify-end sm:space-x-2 space-y-2 sm:space-y-0">
                    <a href="menu.php" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancelar</a>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Criar Turma
                    </button>
                </div>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $stmt = $conn->prepare("INSERT INTO Turmas (curso_id, nome, ano) VALUES (?, ?, ?)");
                $stmt->execute([$_POST['curso_id'], $_POST['nome'], $_POST['ano']]);
                echo '<div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">Turma criada com sucesso!</div>';
                echo '<script>setTimeout(() => { window.location.href = "menu.php"; }, 2000);</script>';
            }
            ?>
        </div>
    </div>
</body>
</html>
