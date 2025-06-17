<?php
include 'config.php';
session_start();
$mensagem = '';
$mensagem_tipo = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipo = $_POST['tipo'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $matricula = $_POST['matricula'];
    $cpf = $_POST['cpf'];
    $rg = $_POST['rg'];
    $data_nascimento = $_POST['data_nascimento'];
    $telefone = $_POST['telefone'];
    if ($tipo === 'Secretaria' || $tipo === 'Professor') {
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $tabela = 'Usuarios';
        $campos = "nome, email, senha, matricula, cpf, rg, data_nascimento, telefone, tipo";
        $valores = [$nome, $email, $senha, $matricula, $cpf, $rg, $data_nascimento, $telefone, $tipo];
        $sql = "INSERT INTO $tabela ($campos) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    } else if ($tipo === 'Aluno') {
        $tabela = 'Alunos';
        $campos = "nome, email, matricula, cpf, rg, data_nascimento, telefone";
        $valores = [$nome, $email, $matricula, $cpf, $rg, $data_nascimento, $telefone];
        $sql = "INSERT INTO $tabela ($campos) VALUES (?, ?, ?, ?, ?, ?, ?)";
    } else {
        $mensagem = "Tipo de cadastro inválido.";
        $mensagem_tipo = "erro";
    }
    if (empty($mensagem)) {
        $stmt = $conn->prepare($sql);
        if ($stmt->execute($valores)) {
            $mensagem = "$tipo cadastrado(a) com sucesso!";
            $mensagem_tipo = "sucesso";
        } else {
            $mensagem = "Erro ao cadastrar $tipo: " . $stmt->errorInfo()[2];
            $mensagem_tipo = "erro";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <script>
        function toggleSenha() {
            var tipo = document.getElementById('tipo').value;
            var senhaDiv = document.getElementById('senha-div');
            if (tipo === 'Secretaria' || tipo === 'Professor') {
                senhaDiv.style.display = 'block';
            } else {
                senhaDiv.style.display = 'none';
            }
        }
        window.onload = toggleSenha;
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex items-center justify-center p-4" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Cadastro</h1>
                <p class="text-gray-600">Preencha os dados para cadastrar um usuário</p>
            </div>
            <?php if ($mensagem): ?>
                <div class="mb-4">
                    <div class="<?php echo $mensagem_tipo === 'sucesso' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded relative text-center" role="alert">
                        <?php echo $mensagem; ?>
                    </div>
                </div>
                <?php if ($mensagem_tipo === 'sucesso'): ?>
                    <script>
                        setTimeout(() => { window.location.href = "menu.php"; }, 2000);
                    </script>
                <?php endif; ?>
            <?php endif; ?>
            <form id="cadastro-form" class="space-y-6" method="POST" autocomplete="off">
                <div class="mb-4">
                    <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de Cadastro</label>
                    <select id="tipo" name="tipo" required onchange="toggleSenha()" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="Secretaria">Secretaria</option>
                        <option value="Professor">Professor</option>
                        <option value="Aluno">Aluno</option>
                    </select>
                </div>
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
                    <div id="senha-div">
                        <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                        <input type="password" id="senha" name="senha" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-end sm:space-x-2 space-y-2 sm:space-y-0">
                    <a href="menu.php" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancelar</a>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cadastrar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", toggleSenha);
    </script>
</body>
</html>
