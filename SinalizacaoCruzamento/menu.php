<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
include 'config.php';
include 'cabecalho.php';
?>
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
    <div class="h-screen flex" style="padding-top: 88px;">
        <?php include 'sidebar.php'; ?>
        <main class="flex-1 p-6">
            <h1 class="text-3xl font-bold mb-6">Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white p-4 rounded shadow flex items-center">
                    <i class="fa fa-desktop text-blue-500 text-2xl mr-3"></i>
                    <span>Dispositivos Ativos: <span class="font-bold">5</span></span>
                </div>
                <div class="bg-white p-4 rounded shadow flex items-center">
                    <i class="fa fa-calendar-day text-green-500 text-2xl mr-3"></i>
                    <span>Eventos Hoje: <span class="font-bold">12</span></span>
                </div>
            </div>

            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="min-w-full text-left">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2">Timestamp</th>
                            <th class="p-2">ID da Câmera</th>
                            <th class="p-2">ID do Ponto</th>
                            <th class="p-2">Tipo</th>
                            <th class="p-2">Status da Câmera</th>
                            <th class="p-2">Observação</th>
                        </tr>
                    </thead>
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
                    eventos.slice(0, 10).forEach(evento => {
                        // Garante capitalização consistente do tipo
                        let tipo = evento.tipo ? (evento.tipo.charAt(0).toUpperCase() + evento.tipo.slice(1)) : '';
                        // Mapeia status da câmera para classes visuais (consistentes com relatorio)
                        let status = evento.status_camera || '';
                        let statusClass = 'text-gray-600 font-bold';
                        if (status === 'Ativo') statusClass = 'text-green-600 font-bold';
                        else if (status === 'Inativo') statusClass = 'text-red-600 font-bold';
                        else if (status === 'Em Manutenção') statusClass = 'text-yellow-600 font-bold';

                        // Formata timestamp para exibir até minutos; funciona com 'YYYY-MM-DD HH:MM:SS' ou ISO
                        let ts = evento.timestamp ? evento.timestamp.replace('T', ' ').slice(0,16) : '';

                        tbody.innerHTML += `
                            <tr class="border-b">
                                <td class="p-2">${ts}</td>
                                <td class="p-2">${evento.id_camera ?? ''}</td>
                                <td class="p-2">${evento.id_ponto ?? ''}</td>
                                <td class="p-2">${tipo}</td>
                                <td class="p-2 ${statusClass}">${status}</td>
                                <td class="p-2">${evento.observacao ?? ''}</td>
                            </tr>
                        `;
                    });
                })
                .catch(err => {
                    console.error('Erro ao carregar eventos:', err);
                });
        });
    </script>
</body>
</html>