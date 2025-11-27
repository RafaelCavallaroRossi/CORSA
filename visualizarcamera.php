<?php
include 'config.php';
$conn = Database::getInstance()->getConnection();
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT id, nome, id_ponto, localizacao, status FROM Dispositivos ORDER BY nome ASC");
$stmt->execute();
$dispositivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'cabecalho.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Visualizar Câmeras</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
    <style>
        .modal {
            transition: opacity 0.25s ease;
        }
        .modal-hiding {
            opacity: 0 !important;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    
    <?php include 'cabecalho.php'; ?>

    <div class="h-screen flex" style="padding-top: 88px;">
        
        <?php include 'sidebar.php'; ?>
        
        <main class="flex-1 p-6 flex items-start justify-center">
            
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-6xl">
                <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Visualizar Câmeras do Sistema</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <?php if (count($dispositivos) > 0): ?>
                        <?php foreach ($dispositivos as $disp): ?>
                            
                            <div class="border rounded-lg shadow-md bg-gray-50 p-4 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($disp['nome']) ?></h3>
                                    <p class="text-sm text-gray-600 mb-2">ID do Ponto: <strong><?= htmlspecialchars($disp['id_ponto']) ?></strong></p>
                                    
                                    <?php 
                                    $statusClass = 'text-gray-500';
                                    if ($disp['status'] === 'Ativo') {
                                        $statusClass = 'text-green-600';
                                    } else if ($disp['status'] === 'Inativo') {
                                        $statusClass = 'text-red-600';
                                    } else if ($disp['status'] === 'Manutenção') {
                                        $statusClass = 'text-yellow-600';
                                    }
                                    ?>
                                    <p class="text-sm font-medium">Status: <span class="<?= $statusClass ?>"><?= htmlspecialchars($disp['status']) ?></span></p>
                                    
                                    <p class="text-sm text-gray-500 mt-1">Local: <?= htmlspecialchars($disp['localizacao']) ?></p>
                                </div>
                                
                                <button type="button" 
                                        onclick="openCameraModal('<?= htmlspecialchars($disp['id_ponto']) ?>', '<?= htmlspecialchars($disp['nome']) ?>')"
                                        class="mt-4 w-full bg-blue-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-blue-700 transition-colors">
                                    <i class="fa fa-video mr-1"></i> Acessar Câmera
                                </button>
                            </div>
                            
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 col-span-full">Nenhum dispositivo cadastrado no sistema.</p>
                    <?php endif; ?>

                </div> </div> </main>
    </div>

    <div id="cameraModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75 opacity-0 pointer-events-none">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl transform transition-transform scale-95">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 id="modalTitle" class="text-xl font-bold">Visualizando Câmera</h3>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-800 text-2xl">×</button>
            </div>
            <div class="p-4 bg-gray-900">
                <img id="cameraStream" src="" alt="Stream da Câmera" class="w-full h-auto bg-black">
                
                <p class="text-xs text-gray-400 mt-2 text-center">
                    Aguardando stream da câmera... Se a imagem não aparecer, verifique se o servidor de stream (MJPEG) está ativo.
                </p>
            </div>
        </div>
    </div>


    <script>
        const modal = document.getElementById('cameraModal');
        const modalTitle = document.getElementById('modalTitle');
        const cameraStream = document.getElementById('cameraStream');
        const closeModalBtn = document.getElementById('closeModalBtn');

        function openCameraModal(id_ponto, nome) {
            modalTitle.innerText = `Câmera: ${nome} (${id_ponto})`;
            
            const pc_ip = '10.72.99.217'; // <-- COLOQUE O IP DO SEU PC AQUI
            const port = '5001'; 
        
            let streamUrl = `http://${pc_ip}:${port}/video_feed/${id_ponto}`;

            cameraStream.src = streamUrl;
            
            modal.classList.remove('opacity-0', 'pointer-events-none', 'modal-hiding');
            modal.querySelector('.transform').classList.remove('scale-95');
        }

        function closeModal() {
            modal.classList.add('modal-hiding');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('opacity-0', 'pointer-events-none');
            }, 250);
            cameraStream.src = '';
        }

        closeModalBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    </script>
</body>
</html>