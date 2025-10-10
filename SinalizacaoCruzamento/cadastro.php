<?php
include 'config.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
$mensagem = '';
$mensagem_tipo = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $id_ponto = $_POST['id_ponto'];
    $localizacao = $_POST['localizacao'];
    $status = $_POST['status'];
    $observacao = $_POST['observacao'];

    $sql = "INSERT INTO Dispositivos (nome, id_ponto, localizacao, status, observacao) VALUES (?, ?, ?, ?, ?)";
    $valores = [$nome, $id_ponto, $localizacao, $status, $observacao];

    $stmt = $conn->prepare($sql);
    if ($stmt->execute($valores)) {
        $mensagem = "Dispositivo cadastrado com sucesso!";
        $mensagem_tipo = "sucesso";
    } else {
        $mensagem = "Erro ao cadastrar dispositivo: " . $stmt->errorInfo()[2];
        $mensagem_tipo = "erro";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Dispositivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex items-center justify-center p-4" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Cadastro de Dispositivo</h1>
                <p class="text-gray-600">Preencha os dados para cadastrar um novo dispositivo de monitoramento</p>
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
            <form id="cadastro-dispositivo-form" class="space-y-6" method="POST" autocomplete="off">
                <div class="mb-4">
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome do Dispositivo</label>
                    <input type="text" id="nome" name="nome" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="id_ponto" class="block text-sm font-medium text-gray-700">ID do Ponto</label>
                    <input type="text" id="id_ponto" name="id_ponto" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="localizacao" class="block text-sm font-medium text-gray-700">Localização (Endereço ou Coordenadas)</label>
                    <input type="text" id="localizacao" name="localizacao" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="Ativo">Ativo</option>
                        <option value="Inativo">Inativo</option>
                        <option value="Manutenção">Manutenção</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="observacao" class="block text-sm font-medium text-gray-700">Observação</label>
                    <textarea id="observacao" name="observacao" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
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
</body>
</html>
