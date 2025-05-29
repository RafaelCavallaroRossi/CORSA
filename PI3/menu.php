<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sua Página</title>
    <link rel="stylesheet" href="../geral.css"> 
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="cabecalho">
        <img src="../IMG/logofatec.png" class="logo-fatec">
        <div class="collapse navbar-collapse" id="navbarNav">

            <div class="botoes-container">
                <a class="nav-link link-button" href="
                <?php
                    if ($_SESSION['tipo_usuario'] == 'administrador') {
                        echo 'admin_dashboard.php';
                    } elseif ($_SESSION['tipo_usuario'] == 'professor') {
                        echo 'menuprofessor.php';
                    } elseif ($_SESSION['tipo_usuario'] == 'coordenador') {
                        echo 'menucoordenador.php';
                    }
                ?>
                ">Página Inicial</a>
                <a class="nav-link link-button" href="../logout.php">Sair</a>
            </div>

            <span class="nav-link subtitulo">Bem-vindo(a), <?php echo $_SESSION['nome_usuario'] . ", RM: " . $_SESSION['rm']; ?></span>
        </div>
    </div>
</nav>


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
    </style>
</body>
</html>