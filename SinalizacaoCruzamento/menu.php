<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
include 'config.php';

// ... (todo o seu código PHP para os cards de Dispositivos Ativos e Eventos) ...
// (O código PHP de busca dos cards permanece O MESMO)
$view_date = new DateTime('now');
if (!empty($_GET['data'])) {
    $d = DateTime::createFromFormat('Y-m-d', $_GET['data']);
    if ($d && $d->format('Y-m-d') === $_GET['data']) {
        $view_date = $d;
    }
}
$date_sql = $view_date->format('Y-m-d');
$display_date = $view_date->format('d/m/Y');

$dispositivos_ativos = 0;
$eventos_no_dia = 0;
try {
    $conn = Database::getInstance()->getConnection();
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Dispositivos WHERE status = ?");
        $stmt->execute(['Ativo']);
        $dispositivos_ativos = (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        try {
            $stmt = $conn->prepare("SELECT COUNT(DISTINCT id_camera) FROM Eventos_Cameras WHERE status_camera = ?");
            $stmt->execute(['Ativo']);
            $dispositivos_ativos = (int)$stmt->fetchColumn();
        } catch (PDOException $e2) {
            $dispositivos_ativos = 0;
        }
    }
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

            <div class="mt-6 bg-white rounded shadow overflow-x-auto">
                <h2 class="text-xl font-bold p-4 border-b">Eventos Recentes (Atualizado em tempo real)</h2>
                <table class="min-w-full text-left">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2">Timestamp</th>
                            <th class="p-2">Ponto</th>
                            <th class="p-2">Tipo</th>
                            <th class="p-2">Status da Vaga</th>
                        </tr>
                    </thead>
                    <tbody id="eventos-tbody">
                        <tr>
                            <td colspan="4" class="p-2 text-center text-gray-500">Carregando eventos...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Encontra o <tbody> da nova tabela
        const tbody = document.getElementById('eventos-tbody');
        
        // Se a tabela não existir nesta página, não faz nada
        if (!tbody) {
            console.warn('Elemento #eventos-tbody não encontrado. A tabela de eventos recentes não será carregada.');
            return;
        }

        function buscarEventos() {
            // Busca os dados do novo arquivo getEventos.php
            fetch('getEventos.php') 
                .then(response => {
                    if (!response.ok) throw new Error('Resposta não OK: ' + response.status);
                    return response.json();
                })
                .then(eventos => {
                    tbody.innerHTML = ''; // Limpa a tabela
                    
                    if (eventos.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="p-2 text-center text-gray-500">Nenhum evento recente.</td></tr>';
                        return;
                    }

                    // O getEventos.php já retorna os 10 mais recentes
                    eventos.forEach(evento => {
                        let tipo = evento.tipo ? (evento.tipo.charAt(0).toUpperCase() + evento.tipo.slice(1)) : 'N/A';
                        
                        // LÓGICA CORRIGIDA:
                        // Usamos 'observacao' para o status (Detectado, Estacionado, Livre)
                        let status = evento.observacao || 'Indefinido'; 
                        let statusClass = 'text-gray-600 font-bold'; // Cor padrão
                        
                        // ### MUDANÇA DE LÓGICA DE COR (FINAL) ###
                        
                        // "Detectado" é o alerta de carro em movimento
                        if (status === 'Detectado') {
                            statusClass = 'text-yellow-600 font-bold'; // Alerta (Amarelo)
                        
                        // "Estacionado" é neutro (desconsiderado)
                        } else if (status === 'Estacionado') { 
                            statusClass = 'text-gray-500 font-bold'; // Neutro (Cinza)
                        
                        // Caso o script anterior tenha enviado "Parado"
                        } else if (status === 'Parado') { 
                            statusClass = 'text-red-600 font-bold'; 
                        
                        // Outros status
                        } else if (status === 'Inativo' || status === 'Desligado') { 
                            statusClass = 'text-red-600 font-bold';
                        }
                        
                        let ts = evento.timestamp ? evento.timestamp.replace('T', ' ').slice(0, 16) : 'N/A';

                        // Adiciona a nova linha na tabela
                        tbody.innerHTML += `
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">${ts}</td>
                                <td class="p-2">${evento.id_ponto ?? 'N/A'}</td>
                                <td class="p-2">${tipo}</td>
                                <td class="p-2 ${statusClass}">${status}</td> 
                            </tr>
                        `;
                    });
                })
                .catch(err => {
                    console.error('Erro ao carregar eventos:', err);
                    tbody.innerHTML = '<tr><td colspan="4" class="p-2 text-center text-red-500">Erro ao carregar eventos. Verifique o console.</td></tr>';
                });
        }
        
        buscarEventos(); // Busca os eventos assim que a página carrega
        setInterval(buscarEventos, 5000); // Atualiza a tabela a cada 5 segundos
    });
    </script>
</body>
</html>