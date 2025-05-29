<?php
session_start();
include '../conexao.php';
include '../menu.php';

$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $tipo_usuario = $_POST['nivel'];
    $rm = $_POST['rm'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO usuarios (nome_usuario, email, senha, tipo_usuario, rm) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome, $email, $senha, $tipo_usuario, $rm]);

        $mensagem = 'Usuário cadastrado com sucesso!';
        $tipo_mensagem = 'sucesso';
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $mensagem = 'Usuário já cadastrado!';
            $tipo_mensagem = 'erro';
        } else {
            $mensagem = 'Erro ao cadastrar usuário!';
            $tipo_mensagem = 'erro';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PréVest Comunitário - Cadastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #065f46 100%);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <?php if (!empty($mensagem)): ?>
        <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded shadow-lg text-white <?php echo $tipo_mensagem === 'sucesso' ? 'bg-green-600' : 'bg-red-600'; ?>" id="alerta-balao">
            <?php echo $mensagem; ?>
        </div>
        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const alert = document.getElementById('alerta-balao');
            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease-in-out';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            }
        });
        </script>
    <?php endif; ?>

    <div class="min-h-screen flex items-center justify-center gradient-bg p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-xl">
            <div class="text-center mb-8">
                <img src="https://via.placeholder.com/150x50?text=PréVest+Comunitário" alt="Logo" class="mx-auto h-12">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Cadastro de Usuário</h1>
                <p class="text-gray-600">Preencha os dados para realizar o cadastro</p>
            </div>
            <form action="cadastro.php" method="post" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                        <input type="text" id="nome" name="nome" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                        <input type="email" id="email" name="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="rm" class="block text-sm font-medium text-gray-700">RM</label>
                        <input type="text" id="rm" name="rm" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input type="password" id="senha" name="senha" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="nivel" class="block text-sm font-medium text-gray-700">Nível de Acesso</label>
                        <select name="nivel" id="nivel" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecione...</option>
                            <option value="professor">Professor</option>
                            <option value="coordenador">Coordenador</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <a href="../login.php" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancelar</a>
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>