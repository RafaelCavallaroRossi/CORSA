<?php
include 'config.php';

$mensagem = '';
$mensagem_tipo = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $matricula = $_POST['matricula'];
    $cpf = $_POST['cpf'];
    $rg = $_POST['rg'];
    $data_nascimento = $_POST['data_nascimento'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $tipo = 'Secretaria';

    $stmt = $conn->prepare("INSERT INTO Usuarios (nome, email, senha, matricula, cpf, rg, data_nascimento, telefone, endereco, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$nome, $email, $senha, $matricula, $cpf, $rg, $data_nascimento, $telefone, $endereco, $tipo])) {
        $mensagem = "Secretaria cadastrada com sucesso!";
        $mensagem_tipo = "sucesso";
    } else {
        $mensagem = "Erro ao cadastrar secretaria: " . $stmt->errorInfo()[2];
        $mensagem_tipo = "erro";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Secretaria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #065f46 100%);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center gradient-bg p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl">
            <div class="text-center mb-8">
                <img src="https://via.placeholder.com/150x50?text=PréVest+Comunitário" alt="Logo" class="mx-auto h-12">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Cadastro de Secretaria</h1>
                <p class="text-gray-600">Preencha os dados para cadastrar uma secretaria</p>
            </div>
            <?php if ($mensagem): ?>
                <div class="mb-4">
                    <div class="<?php echo $mensagem_tipo === 'sucesso' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded relative text-center" role="alert">
                        <?php echo $mensagem; ?>
                    </div>
                </div>
                <?php if ($mensagem_tipo === 'sucesso'): ?>
                    <script>
                        setTimeout(() => { window.location.href = "login.php"; }, 2000);
                    </script>
                <?php endif; ?>
            <?php endif; ?>
            <form id="cadastro-form" class="space-y-6" method="POST" autocomplete="off">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                        <input type="text" id="nome" name="nome" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                        <input type="text" id="cpf" name="cpf" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="rg" class="block text-sm font-medium text-gray-700">RG</label>
                        <input type="text" id="rg" name="rg" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                        <input type="email" id="email" name="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="matricula" class="block text-sm font-medium text-gray-700">Matrícula</label>
                        <input type="text" id="matricula" name="matricula" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input type="password" id="senha" name="senha" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço Completo</label>
                        <input type="text" id="endereco" name="endereco" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <a href="login.php" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancelar</a>
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
