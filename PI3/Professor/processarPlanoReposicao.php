<?php
session_start();
include '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_usuario = $_SESSION['id_usuario'];
    $nomeProfessor = $_POST['nomeProfessor'];
    $rm = $_POST['rm'];
    $turno = $_POST['turno'];
    $motivo = $_POST['motivo'];
    $data_nao_ministrada = $_POST['data_nao_ministrada'];
    $disciplina_nao_ministrada = $_POST['disciplina_nao_ministrada'];
    $quantidade_aulas = $_POST['quantidade_aulas'];
    $datas_reposicao = $_POST['datas_reposicao'];
    $horarios_inicio = $_POST['horarios_inicio'];
    $horarios_fim = $_POST['horarios_fim'];
    $id_justificativa = $_POST['id_justificativa'];
 
    try {
        $conn->beginTransaction();

        $sql = "INSERT INTO reposicoes (
                    turno, motivo, data_nao_ministrada, disciplina_nao_ministrada, quantidade_aulas, id_usuario, id_justificativa
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$turno, $motivo, $data_nao_ministrada, $disciplina_nao_ministrada, $quantidade_aulas, $id_usuario, $id_justificativa]);

        $reposicao_id = $conn->lastInsertId();

        for ($i = 0; $i < $quantidade_aulas; $i++) {
            if (!empty($datas_reposicao[$i]) && !empty($horarios_inicio[$i]) && !empty($horarios_fim[$i])) {
                $sql_aula = "INSERT INTO aulas_reposicao (
                                reposicao_id, data_reposicao, horario_inicio, horario_fim
                            ) VALUES (?, ?, ?, ?)";
                $stmt_aula = $conn->prepare($sql_aula);
                $stmt_aula->execute([
                    $reposicao_id, $datas_reposicao[$i], $horarios_inicio[$i], $horarios_fim[$i]
                ]);
            }
        }

        $sql_update_status = "UPDATE justificativas SET status = 'aceito' WHERE id_justificativa = ?";
        $stmt_update = $conn->prepare($sql_update_status);
        $stmt_update->execute([$id_justificativa]);

        $conn->commit();

        $_SESSION['mensagem_sucesso'] = "Plano de reposição enviado com sucesso!";

        header("Location: menuprofessor.php");
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        echo "Erro ao processar o plano de reposição: " . $e->getMessage();
    }
} else {
    header("Location: menuprofessor.php");
    exit;
}