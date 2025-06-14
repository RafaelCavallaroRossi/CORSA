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

    <header class="w-full bg-blue-900 text-white py-4 px-6 flex justify-between items-center shadow-md fixed top-0 left-0 z-10">
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-school text-2xl"></i>
            <span class="font-bold text-lg">Escolinha do...</span>
        </div>
        <div class="flex items-center space-x-4">
            <?php session_start(); if (isset($_SESSION['usuario_id'])): ?>
                <span class="hidden sm:inline">
                    Olá, <?php echo htmlspecialchars($_SESSION['nome'] ?? $_SESSION['tipo'] ?? 'Usuário'); ?>
                </span>
                <form action="logout.php" method="post" class="inline">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white font-medium transition">Sair</button>
                </form>
            <?php endif; ?>
        </div>
    </header>

    <div class="w-full h-screen gradient-bg flex items-center justify-center p-4" style="padding-top: 88px;">
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
                <div class="flex justify-end space-x-2">
                    <a href="painel_professor.php" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancelar</a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ver Frequência
                    </button>
                </div>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['aula_id'])) {
                $aula_id = $_GET['aula_id'];

                // Obter a turma da aula
                $stmt = $conn->prepare("SELECT turma_id FROM Aulas WHERE id = ?");
                $stmt->execute([$aula_id]);
                $turma_id = $stmt->fetchColumn();

                // Verificar se a turma foi encontrada
                if ($turma_id) {
                    $stmt = $conn->prepare("
                        SELECT Alunos.nome AS aluno, Frequencia.status 
                        FROM Frequencia 
                        JOIN Alunos ON Frequencia.aluno_id = Alunos.id 
                        JOIN Alunos_Turmas ON Alunos.id = Alunos_Turmas.aluno_id 
                        WHERE Frequencia.aula_id = ? AND Alunos_Turmas.turma_id = ?
                    ");
                    $stmt->execute([$aula_id, $turma_id]);
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
                } else {
                    echo "<div class='mt-4 text-center text-gray-600'>Aula não encontrada.</div>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
