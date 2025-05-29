<?php
session_start();
include 'conexao.php';
include 'menu.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu de Solicitações</title>
    <link rel="stylesheet" href="geral.css"> <!-- Link para o CSS -->
</head>
<body>
    
    <div class="menu">
    <p>Selecione uma opção abaixo para continuar:</p>
    <ul style="list-style-type: none; padding: 0;">
        <li><a href="formfaltateste.php" class="button">Formulário de Justificativa de Falta</a></li>
        <li><a href="formreposicaoteste.php" class="button">Formulário de Reposição de Aula</a></li>
        <li><a href="acompanhamento.php" class="button">Acompanhamento de Solicitações</a></li>
    </ul>
    </div>

<footer class="rodape">
    <div class="container-rodape">
        <div class="logo-rodape">
            <img src="IMG/sp.png" alt="Logo Governo de São Paulo">
        </div>
        <div class="info-rodape">
            <p>© 2024 Governo do Estado de São Paulo</p>
            <p>Todos os direitos reservados.</p>
        </div>
    </div>
   
</footer>
</body>
</html>

