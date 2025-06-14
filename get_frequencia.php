<?php
include 'config.php';
if (isset($_GET['Aula_id'])) {
    $aula_id = $_GET['Aula_id'];
    $stmt = $conn->prepare("SELECT turma_id FROM Aulas WHERE id = ?");
    $stmt->execute([$aula_id]);
    $turma_id = $stmt->fetchColumn();
    if ($turma_id) {
        $stmt = $conn->prepare("
            SELECT Alunos.id, Alunos.nome AS aluno 
            FROM Alunos 
            JOIN Alunos_Turmas ON Alunos.id = Alunos_Turmas.aluno_id 
            WHERE Alunos_Turmas.turma_id = ?
        ");
        $stmt->execute([$turma_id]);
        $frequencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($frequencias);
    } else {
        echo json_encode([]);
    }
}
?>