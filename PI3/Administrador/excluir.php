<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id_usuario'])) {
    $id = $_GET['id_usuario'];

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);

    $_SESSION['mensagem_sucesso'] = "Usuário excluído com sucesso!";
    header("Location: lista_usuarios.php");
    exit;
} else {
    $_SESSION['mensagem_erro'] = "ID de usuário inválido!";
    header("Location: lista_usuarios.php");
    exit;
}
?>