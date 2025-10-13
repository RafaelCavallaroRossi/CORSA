<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>CORSA - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php 
    include 'config.php';
    include 'cabecalho.php'; 
    session_start();
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: index.php");
        exit;
    }
    ?>
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
                        <i class="fa-solid fa-square-plus mr-2"></i>Cadastrar Dispositivos
                    </a>
                </li>
                <li>
                    <a href="visualizarMapa.php" class="block p-2 hover:bg-gray-200 rounded">
                        <i class="fa fa-eye mr-2"></i>Visualizar Mapa
                    </a>
                </li>
                <li>
                    <a href="relatorioEventos.php" class="block p-2 hover:bg-gray-200 rounded">
                        <i class="fa fa-file-alt mr-2"></i>Relatório de Eventos
                    </a>
                </li>
                <li>
                    <a href="editarCamera.php" class="block p-2 hover:bg-gray-200 rounded">
                        <i class="fa-solid fa-pencil mr-2"></i>Editar Câmeras
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
                    <!--<tbody>
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
                    </tbody>-->
                    <tbody id="eventos-tbody">
                        <!-- Eventos serão inseridos aqui via JavaScript -->
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('getEventos.php')
                .then(response => response.json())
                .then(eventos => {
                    const tbody = document.getElementById('eventos-tbody');
                    tbody.innerHTML = '';
                    eventos.slice(0, 10).forEach(evento => { // Mostra só os 10 mais recentes
                        let tipo = evento.tipo.charAt(0).toUpperCase() + evento.tipo.slice(1);
                        let statusClass = 'text-gray-600 font-bold';
                        if (evento.status_camera === 'em funcionamento') statusClass = 'text-green-600 font-bold';
                        else if (evento.status_camera === 'pouco alterado') statusClass = 'text-yellow-600 font-bold';
                        else if (evento.status_camera === 'muito alterado') statusClass = 'text-orange-600 font-bold';
                        else if (evento.status_camera === 'desligado') statusClass = 'text-red-600 font-bold';

                        tbody.innerHTML += `
                            <tr class="border-b">
                                <td class="p-2">${evento.timestamp.replace('T', ' ').slice(0, 16)}</td>
                                <td class="p-2">${evento.id_ponto}</td>
                                <td class="p-2">${tipo}</td>
                                <td class="p-2 ${statusClass}">${evento.status_camera}</td>
                            </tr>
                        `;
                    });
                });
        });
    </script>
</body>
</html>