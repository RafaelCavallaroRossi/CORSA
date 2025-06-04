<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'Secretaria') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel da Secretaria</title>
    <link rel="stylesheet" href="../geral.css"> 
    <style>
        .link-button {
            display: inline-block;
            padding: 8px 15px;
            background-color: #c20000;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            font-weight: bold;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        .link-button:hover {
            background-color: #ff0000d7;
        }
        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .subtitulo {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        .painel-links {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        .painel-links a {
            background: #1e3a8a;
            color: #fff;
            padding: 10px 18px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }
        .painel-links a:hover {
            background: #065f46;
        }
        .painel-titulo {
            margin-top: 30px;
            font-size: 1.5em;
            font-weight: bold;
            color: #1e3a8a;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="cabecalho">
        <img src="../IMG/logofatec.png" class="logo-fatec">
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="botoes-container">
                <a class="nav-link link-button" href="menu.php">Página Inicial</a>
                <a class="nav-link link-button" href="../logout.php">Sair</a>
            </div>
            <span class="nav-link subtitulo">
                Bem-vindo(a), <?php echo isset($_SESSION['nome_usuario']) ? $_SESSION['nome_usuario'] : 'Secretaria'; ?>
            </span>
        </div>
    </div>
</nav>

<div class="container">
    <div class="painel-titulo">Painel da Secretaria</div>
    <div class="painel-links">
        <a href="cadastrar_aluno.php">Cadastrar Aluno</a>
        <a href="cadastrar_professor.php">Cadastrar Professor</a>
        <a href="cadastrar_secretaria.php">Cadastrar Secretaria</a>
        <a href="criar_turma.php">Criar Turma</a>
        <a href="vincular_alunos_turma.php">Vincular Alunos à Turma</a>
    </div>
</div>
</body>
</html>
