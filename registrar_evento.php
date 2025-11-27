<?php
// Inclui sua conexão com o banco de dados
include 'config.php'; 

// Define o cabeçalho como JSON
header('Content-Type: application/json');

// 1. Pega os dados enviados pelo Python (via POST)
$id_ponto = $_POST['id_ponto'] ?? null;
$tipo_veiculo = $_POST['tipo'] ?? null;       // 'carro', 'moto', 'livre', etc.
$observacao = $_POST['observacao'] ?? null; // 'Detectado', 'Estacionado', 'Livre'

// Validação básica
if (!$id_ponto || !$tipo_veiculo || !$observacao) {
    http_response_code(400); // Bad Request
    echo json_encode(['erro' => 'Dados incompletos: id_ponto, tipo e observacao são obrigatórios']);
    exit;
}

try {
    $conn = Database::getInstance()->getConnection();

    // 2. Buscar o ID e o Status do Dispositivo usando o id_ponto
    $stmt_dispositivo = $conn->prepare("SELECT id, status FROM Dispositivos WHERE id_ponto = ?");
    $stmt_dispositivo->execute([$id_ponto]);
    $dispositivo = $stmt_dispositivo->fetch(PDO::FETCH_ASSOC);

    if (!$dispositivo) {
        http_response_code(404); // Not Found
        echo json_encode(['erro' => "O id_ponto '$id_ponto' não foi encontrado na tabela Dispositivos"]);
        exit;
    }

    $id_camera = $dispositivo['id'];
    $status_camera = $dispositivo['status']; // 'Ativo', 'Inativo', 'Manutenção'

    // 3. Inserir o novo evento na tabela Eventos_Cameras
    $sql = "INSERT INTO Eventos_Cameras (id_camera, id_ponto, timestamp, tipo, status_camera, observacao) 
            VALUES (?, ?, NOW(), ?, ?, ?)";
    
    $stmt_evento = $conn->prepare($sql);
    
    if ($stmt_evento->execute([$id_camera, $id_ponto, $tipo_veiculo, $status_camera, $observacao])) {
        echo json_encode(['sucesso' => true, 'msg' => 'Evento registrado']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['erro' => 'Falha ao salvar o evento no banco']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    // Em produção, é melhor logar o erro do que expô-lo
    echo json_encode(['erro' => 'Exceção no DB: ' . $e->getMessage()]);
}
?>