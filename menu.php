<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'Secretaria') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Secretaria - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'cabecalho.php'; ?>

<!-- ATENÇÃO! Renomear o arquivo contendo ".html" para ver direto no navegador, ou manter o ".php" e abrir com o xampp. -->

    <div class="h-screen flex" style="padding-top: 88px;">
        <nav class="w-64 bg-white shadow-md">
            <div class="p-6 text-xl font-bold">CORSA</div>
            <ul class="mt-6 space-y-2">
                <li>
                    <a href="menu.php" class="block p-2 hover:bg-gray-200 rounded font-semibold text-blue-700">
                        <i class="fa fa-home mr-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="cadastro.php" class="block p-2 hover:bg-gray-200 rounded">
                        <i class="fa fa-user-plus mr-2"></i>Cadastrar Usuário
                    </a>
                </li>
                <li>
                    <a href="criar_curso.php" class="block p-2 hover:bg-gray-200 rounded">
                        <i class="fa fa-book mr-2"></i>Criar Curso
                    </a>
                </li>
                <li>
                    <a href="criar_turma.php" class="block p-2 hover:bg-gray-200 rounded">
                        <i class="fa fa-users mr-2"></i>Criar Turma
                    </a>
                </li>
                <li>
                    <a href="vincular_alunos_turma.php" class="block p-2 hover:bg-gray-200 rounded">
                        <i class="fa fa-link mr-2"></i>Vincular Alunos à Turma
                    </a>
                </li>
                <li>
                    <a href="visualizar_alunos.php" class="block p-2 hover:bg-gray-200 rounded">
                        <i class="fa fa-eye mr-2"></i>Visualizar Alunos
                    </a>
                </li>
                <li>
                    <a href="relatorio_faltas.php" class="block p-2 hover:bg-gray-200 rounded">
                        <i class="fa fa-file-alt mr-2"></i>Relatório de Faltas
                    </a>
                </li>
            </ul>
        </nav>

        <main class="flex-1 p-6">
            <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded shadow flex items-center">
                    <i class="fa fa-desktop text-blue-500 text-2xl mr-3"></i>
                    <span>Dispositivos Ativos: <span class="font-bold">5</span></span>
                </div>
                <div class="bg-white p-4 rounded shadow flex items-center">
                    <i class="fa fa-calendar-day text-green-500 text-2xl mr-3"></i>
                    <span>Eventos Hoje: <span class="font-bold">12</span></span>
                </div>
                <div class="bg-white p-4 rounded shadow flex items-center">
                    <i class="fa fa-exclamation-triangle text-yellow-500 text-2xl mr-3"></i>
                    <span>Alertas Pendentes: <span class="font-bold">2</span></span>
                </div>
            </div>

            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2">Timestamp</th>
                            <th class="p-2">ID do Ponto</th>
                            <th class="p-2">Tipo</th>
                            <th class="p-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="p-2">18/09/2025 14:23</td>
                            <td class="p-2">P1</td>
                            <td class="p-2">Veículo</td>
                            <td class="p-2 text-green-600 font-bold">Detectado</td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2">18/09/2025 14:25</td>
                            <td class="p-2">P2</td>
                            <td class="p-2">Pedestre</td>
                            <td class="p-2 text-yellow-600 font-bold">Aguardando</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
