<?php
include 'config.php';
$conn = Database::getInstance()->getConnection();
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$por_pagina = 20;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $por_pagina;

// Busca total de eventos para paginação
$total = $conn->query("SELECT COUNT(*) FROM Eventos_Cameras")->fetchColumn();
$total_paginas = ceil($total / $por_pagina);

// Busca eventos paginados
$stmt = $conn->prepare("SELECT id_camera, id_ponto, timestamp, tipo, status_camera FROM Eventos_Cameras ORDER BY timestamp DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $por_pagina, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
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
                                    <td class="px-4 py-2"><?= htmlspecialchars($evento['status_camera']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-500">Nenhum evento registrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <a href="?pagina=<?= $i ?>" class="px-3 py-1 rounded <?= $i == $pagina ? 'bg-blue-700 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>

            <div class="mt-6 text-center">
                <a href="menu.php" class="inline-block bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Voltar ao Menu</a>
            </div>
        </div>
    </div>
</body>
</html>
