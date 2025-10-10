<?php
include 'config.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Busca todos os eventos das câmeras
$stmt = $conn->prepare("SELECT id_camera, id_ponto, timestamp, tipo, status_camera FROM Eventos_Cameras ORDER BY timestamp DESC LIMIT 100");
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Eventos das Câmeras</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex flex-col items-center p-6" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-5xl">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Relatório de Eventos das Câmeras</h1>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700">ID da Câmera</th>
                            <th class="px-4 py-2 text-left text-gray-700">ID do Ponto</th>
                            <th class="px-4 py-2 text-left text-gray-700">Timestamp</th>
                            <th class="px-4 py-2 text-left text-gray-700">Tipo Identificado</th>
                            <th class="px-4 py-2 text-left text-gray-700">Status da Câmera</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (count($eventos) > 0): ?>
                            <?php foreach ($eventos as $evento): ?>
                                <tr>
                                    <td class="px-4 py-2"><?= htmlspecialchars($evento['id_camera']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($evento['id_ponto']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($evento['timestamp']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($evento['tipo']) ?></td>
                                    <td class="px-4 py-2">
                                        <?php
                                            $status = $evento['status_camera'];
                                            $color = 'gray-600';
                                            if ($status === 'em funcionamento') $color = 'green-600';
                                            elseif ($status === 'pouco alterado') $color = 'yellow-600';
                                            elseif ($status === 'muito alterado') $color = 'orange-600';
                                            elseif ($status === 'desligado') $color = 'red-600';
                                        ?>
                                        <span class="font-bold text-<?= $color ?>"><?= htmlspecialchars($status) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-500">Nenhum evento registrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

<!--<tbody id="eventos-tbody">
</tbody>-->
                    
                </table>
            </div>

            <div class="mt-6 text-center">
                <a href="menu.php" class="inline-block bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Voltar ao Menu</a>
            </div>
        </div>
    </div>
<!--
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
</script> -->
</body>
</html>
