<?php
session_start();
include '../conexao.php';
include '../menu.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../geral.css">
    <title>Painel de Controle - Administrador</title>
</head>
<body>

    <div class="menu">
    <h2>Painel de Controle - Administrador</h2>
    <ul style="list-style-type: none; padding: 0;">
        <li><a href="cadastro.php" class="button">Cadastrar Usuario</a></li>
        <li><a href="lista_usuarios.php" class="button">Lista de Usuários Cadastrados</a></li>
    </ul>

</div>
<footer class="rodape">
    <div class="container-rodape">
        <div class="logo-rodape">
        <img src="../IMG/sp.png" alt="Logo Governo de São Paulo">
        </div>
        <div class="info-rodape">
            <p>© 2024 Governo do Estado de São Paulo</p>
            <p>Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>