<?php
include 'config.php';

// Busca os 100 eventos mais recentes das câmeras
$stmt = $conn->prepare("SELECT id, id_camera, id_ponto, timestamp, tipo, status_camera, observacao FROM Eventos_Cameras ORDER BY timestamp DESC LIMIT 100");
$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($eventos);
