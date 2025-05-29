<?php
session_start();
include '../conexao.php';
include '../menu.php';

function formatarTexto($texto) {
    $texto = str_replace('_', ' ', $texto);
    $texto = ucwords($texto);
    return $texto;
}
function formatarDataParaBR($data) {
    if (preg_match('/^\d{4}[-\/]\d{2}[-\/]\d{2}$/', $data)) {
        $timestamp = strtotime($data);
        return date("d/m/Y", $timestamp);
    } else {
        return "Formato de data inválido!";
    }
}

if (!isset($_SESSION['id_usuario'])) {
    echo "ID do usuário não está definido na sessão.";
    exit;
}
$id_usuario = $_SESSION['id_usuario'];


$sql_pendentes = "SELECT r.id_reposicao, u.nome_usuario, u.rm, r.data_envio, r.motivo
                   FROM reposicoes r 
                   JOIN usuarios u ON r.id_usuario = u.id_usuario
                   WHERE r.status = 'pendente' AND r.id_usuario = ?";
$stmt_pendentes = $conn->prepare($sql_pendentes);
$stmt_pendentes->execute([$id_usuario]);
$formularios_pendentes = $stmt_pendentes->fetchAll(PDO::FETCH_ASSOC);


$sql_respondidos = "SELECT r.*, u.nome_usuario, u.rm
                   FROM reposicoes r 
                   JOIN usuarios u ON r.id_usuario = u.id_usuario
                   WHERE r.status = 'aceito' OR r.status='recusado' AND r.id_usuario = ?";
$stmt_respondidos = $conn->prepare($sql_respondidos);
$stmt_respondidos->execute([$id_usuario]);
$formularios_respondidos = $stmt_respondidos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhamento de Formulários</title>
    <link rel="stylesheet" href="../geral.css">
    <style>
  
        .tabela-reposicao {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: Arial, sans-serif;
        }
        .tabela-reposicao th, .tabela-reposicao td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .tabela-reposicao th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .tabela-reposicao tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .tabela-reposicao tr:nth-child(odd) {
            background-color: #ffffff;
        }
 
        .container-acompanhamento {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            overflow-y: auto; 
        }
        h2, h3 {
            color: #333;
            text-align: center;
        }
        .btn-editar {
            color: #0066cc;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-editar:hover {
            text-decoration: underline;
        }

        .modal {
    display: none; 
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5); 
}

.modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    width: 80%;
    max-width: 500px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}


.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #c20000;
    text-decoration: none;
}
.btn-vermelho-pequeno {
    background-color: #c20000; 
    color: white;
    border: none;
    padding: 5px 10px; 
    font-size: 12px;
    border-radius: 4px; 
    cursor: pointer;
    text-align: center;
    transition: background-color 0.3s;
}

.btn-vermelho-pequeno:hover {
    background-color: #e60000; 
}
    </style>
</head>
<body>
<div id="comentarioModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Comentário</h3>
        <p id="modalComentario">Nenhum comentário disponível.</p>
    </div>
</div>
    <div class="page-container">
    <div class="container-acompanhamento">
        <h2>Acompanhamento de Formulários de Reposição</h2>
        <h3>Formulários Pendentes</h3>
        <table class="tabela-reposicao">
            <thead>
                <tr>
                    <th>Data de Envio</th>
                    <th>Detalhes da Reposição</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($formularios_pendentes)): ?>
                    <?php foreach ($formularios_pendentes as $form): ?>
                        <tr>
                            <td><?= htmlspecialchars(formatarDataParaBR($form['data_envio'])) ?></td>
                            <td><?= htmlspecialchars($form['motivo']) ?></td>
                            <td>Pendente</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Nenhum formulário pendente.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h3>Formulários com Resposta</h3>
        <table class="tabela-reposicao">
            <thead>
                <tr>
                    <th>Data de Envio</th>
                    <th>Detalhes da Reposição</th>
                    <th>Status</th>
                    <th>Comentário</th>
                    <th>Data da Resposta</th>
                    <th>Edição</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($formularios_respondidos)): ?>
                    <?php foreach ($formularios_respondidos as $form): ?>
                        <tr>
                            <td><?= htmlspecialchars(formatarDataParaBR($form['data_envio'])) ?></td>
                            <td><?= htmlspecialchars($form['motivo']) ?></td>
                            <td><?= htmlspecialchars(formatarTexto($form['status'])) ?></td>
                            <td>
    <?php if (!empty($form['comentario'])): ?>
        <button 
            class="btn-vermelho-pequeno" 
            onclick="abrirModal('<?= htmlspecialchars($form['comentario']) ?>')">
            Ver Comentário
        </button>
    <?php else: ?>
        <span>Nenhum.</span>
    <?php endif; ?>
</td>
                            <td><?= htmlspecialchars(formatarDataParaBR($form['data_resposta'])) ?></td>
                            <?php if ($form['status'] == 'recusado'):?>




                            <td>
                                <?php if ($form['status'] == 'recusado'):?>
                                    <a href="editarform.php?id_reposicao=<?= htmlspecialchars($form['id_reposicao']) ?>" class="btn-vermelho-pequeno">Editar</a>
                                <?php endif; ?>
                            </td>


                            
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhum formulário com resposta.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
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
    
</div>
<script>

    const modal = document.getElementById('comentarioModal');
    const modalComentario = document.getElementById('modalComentario');
    const closeModal = document.querySelector('.close');


    function abrirModal(comentario) {
        modalComentario.textContent = comentario;
        modal.style.display = 'block';
    }


    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });


    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>
</body>
</html>