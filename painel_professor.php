<?php
session_start();
include 'config.php';

// Verificação de sessão
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'Professor') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Professor</title>
</head>
<body>
    <h1>Painel do Professor</h1>
    <nav>
        <ul>
            <li><a href="lista_frequencia.php">Registrar Frequência</a></li>
            <li><a href="painel_frequencia.php">Ver Frequência</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>
</body>
</html>