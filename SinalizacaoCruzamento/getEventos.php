<?php
include 'config.php';
header('Content-Type: application/json');

try {
    $conn = Database::getInstance()->getConnection();
    
    // Busca os 10 eventos mais recentes
    // Nós pegamos 'observacao' para saber o status (Detectado/Livre)
    $stmt = $conn->prepare(
        "SELECT id_ponto, timestamp, tipo, observacao 
         FROM Eventos_Cameras 
         ORDER BY timestamp DESC 
         LIMIT 10"
    );
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($eventos);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
}
?>