<?php
include 'config.php';
if (isset($_GET['turma_id'])) {
    $turma_id = $_GET['turma_id'];
    $stmt = $conn->prepare("SELECT * FROM Aulas WHERE turma_id = ?");
    $stmt->execute([$turma_id]);
    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($aulas);
}
