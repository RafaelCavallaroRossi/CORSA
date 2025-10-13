<?php
include 'config.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: menu.php");
    exit;
}

// Busca o dispositivo pelo ID
$stmt = $conn->prepare("SELECT * FROM Dispositivos WHERE id = ?");
$stmt->execute([$id]);
$camera = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$camera) {
    header("Location: menu.php");
    exit;
}

$mensagem = '';
$mensagem_tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $mensagem = "Requisição inválida.";
        $mensagem_tipo = "erro";
    } else {
        $nome = $_POST['nome'];
        $id_ponto = $_POST['id_ponto'];
        $localizacao = $_POST['localizacao'];
        // Sanitização e validação de status
        $status = in_array($_POST['status'], ['Ativo', 'Inativo', 'Manutenção']) ? $_POST['status'] : 'Ativo';
        // Sanitização de observacao
        $observacao = htmlspecialchars($_POST['observacao']);

        // Validação de duplicidade de id_ponto (exceto o próprio registro)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Dispositivos WHERE id_ponto = ? AND id <> ?");
        $stmt->execute([$id_ponto, $id]);
        if ($stmt->fetchColumn() > 0) {
            $mensagem = "Já existe outro dispositivo com este ID de ponto.";
            $mensagem_tipo = "erro";
        } elseif (!preg_match('/^-?\d{1,3}\.\d+,-?\d{1,3}\.\d+$/', $localizacao)) {
            $mensagem = "Formato de localização inválido. Use: latitude,longitude";
            $mensagem_tipo = "erro";
        } else {
            $sql = "UPDATE Dispositivos SET nome = ?, id_ponto = ?, localizacao = ?, status = ?, observacao = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$nome, $id_ponto, $localizacao, $status, $observacao, $id])) {
                $mensagem = "Dados da câmera atualizados com sucesso!";
                $mensagem_tipo = "sucesso";
                // Atualiza os dados exibidos no formulário
                $stmt = $conn->prepare("SELECT * FROM Dispositivos WHERE id = ?");
                $stmt->execute([$id]);
                $camera = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $mensagem = "Erro ao atualizar dados: " . $stmt->errorInfo()[2];
                $mensagem_tipo = "erro";
            }
        }
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Câmera</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex items-center justify-center p-6" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Editar Câmera</h1>

            <?php if ($mensagem): ?>
                <div class="mb-4 text-center">
                    <div class="<?= $mensagem_tipo === 'sucesso' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded">
                        <?= $mensagem; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Câmera</label>
                        <input type="text" name="nome" value="<?= htmlspecialchars($camera['nome']) ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div>
                        <label for="id_ponto" class="block text-sm font-medium text-gray-700">ID do Ponto</label>
                        <input type="text" name="id_ponto" value="<?= htmlspecialchars($camera['id_ponto']) ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div>
                        <label for="localizacao" class="block text-sm font-medium text-gray-700">Localização</label>
                        <input type="text" name="localizacao" value="<?= htmlspecialchars($camera['localizacao']) ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                            <option value="Ativo" <?= $camera['status'] === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                            <option value="Inativo" <?= $camera['status'] === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                            <option value="Manutenção" <?= $camera['status'] === 'Manutenção' ? 'selected' : '' ?>>Manutenção</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="observacao" class="block text-sm font-medium text-gray-700">Observação</label>
                    <textarea name="observacao" rows="2" class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"><?= htmlspecialchars($camera['observacao']) ?></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <a href="menu.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
