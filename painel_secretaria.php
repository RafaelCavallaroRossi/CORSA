<?php
session_start();
include 'config.php';

// Verificação de sessão
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'Secretaria') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel da Secretaria</title>
</head>
<body>
    <h1>Painel da Secretaria</h1>
    <nav>
        <ul>
            <li><a href="cadastrar_aluno.php">Cadastrar Aluno</a></li>
            <li><a href="cadastrar_professor.php">Cadastrar Professor</a></li>
            <li><a href="cadastrar_secretaria.php">Cadastrar Secretaria</a></li>
            <li><a href="criar_turma.php">Criar Turma</a></li>
            <li><a href="vincular_alunos_turma.php">Vincular Alunos à Turma</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>
</body>
</html>