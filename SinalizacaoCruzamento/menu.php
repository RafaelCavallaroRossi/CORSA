<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
include 'config.php';

// Obtém data visualizada (GET ?data=YYYY-MM-DD) ou usa hoje
$view_date = new DateTime('now');
if (!empty($_GET['data'])) {
    $d = DateTime::createFromFormat('Y-m-d', $_GET['data']);
    if ($d && $d->format('Y-m-d') === $_GET['data']) {
        $view_date = $d;
    }
}
$date_sql = $view_date->format('Y-m-d');
$display_date = $view_date->format('d/m/Y');

// Calcula dispositivos ativos a partir da tabela Dispositivos (apenas registros com status = 'Ativo')
// e eventos na data selecionada
$dispositivos_ativos = 0;
$eventos_no_dia = 0;
try {
    $conn = Database::getInstance()->getConnection();

    // Contar dispositivos registrados como 'Ativo' na tabela Dispositivos
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Dispositivos WHERE status = ?");
        $stmt->execute(['Ativo']);
        $dispositivos_ativos = (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        // Fallback: se tabela Dispositivos não existir, tenta contar devices ativos via Eventos_Cameras (menos confiável)
        try {
            $stmt = $conn->prepare("SELECT COUNT(DISTINCT id_camera) FROM Eventos_Cameras WHERE status_camera = ?");
            $stmt->execute(['Ativo']);
            $dispositivos_ativos = (int)$stmt->fetchColumn();
        } catch (PDOException $e2) {
            $dispositivos_ativos = 0;
        }
    }

    // Eventos no dia visualizado (usa DATE(timestamp) = ?)
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Eventos_Cameras WHERE DATE(timestamp) = ?");
        $stmt->execute([$date_sql]);
        $eventos_no_dia = (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        $eventos_no_dia = 0;
    }
} catch (Exception $e) {
    $dispositivos_ativos = 0;
    $eventos_no_dia = 0;
}

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
                    <span>Dispositivos Ativos: <span class="font-bold"><?= htmlspecialchars($dispositivos_ativos, ENT_QUOTES, 'UTF-8') ?></span></span>
                </div>
                <div class="bg-white p-4 rounded shadow flex items-center">
                    <i class="fa fa-calendar-day text-green-500 text-2xl mr-3"></i>
                    <span>Eventos em <?= htmlspecialchars($display_date, ENT_QUOTES, 'UTF-8') ?>: <span class="font-bold"><?= htmlspecialchars($eventos_no_dia, ENT_QUOTES, 'UTF-8') ?></span></span>
                </div>
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
                });         });
    </script>    
</body>
</html>
