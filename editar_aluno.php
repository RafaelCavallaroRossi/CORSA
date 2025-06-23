<?php
include 'config.php';
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: visualizar_alunos.php");
    exit;
}

// Carregar dados do aluno
$stmt = $conn->prepare("SELECT * FROM Alunos WHERE id = ?");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    header("Location: visualizar_alunos.php");
    exit;
}

$mensagem = '';
$mensagem_tipo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $matricula = $_POST['matricula'];
    $cpf = $_POST['cpf'];
    $rg = $_POST['rg'];
    $data_nascimento = $_POST['data_nascimento'];
    $telefone = $_POST['telefone'];

    $uploadDir = "uploads/";
    $historico_nome = $aluno['historico_pdf'];
    $documento_nome = $aluno['documento_pdf'];

    if (!empty($_FILES['historico_pdf']['name'])) {
        if ($historico_nome && file_exists($uploadDir . $historico_nome)) unlink($uploadDir . $historico_nome);
        $historico_nome = 'historico_' . uniqid() . '.pdf';
        move_uploaded_file($_FILES['historico_pdf']['tmp_name'], $uploadDir . $historico_nome);
    }

    if (!empty($_FILES['documento_pdf']['name'])) {
        if ($documento_nome && file_exists($uploadDir . $documento_nome)) unlink($uploadDir . $documento_nome);
        $documento_nome = 'documento_' . uniqid() . '.pdf';
        move_uploaded_file($_FILES['documento_pdf']['tmp_name'], $uploadDir . $documento_nome);
    }

    $sql = "UPDATE Alunos SET nome = ?, email = ?, matricula = ?, cpf = ?, rg = ?, data_nascimento = ?, telefone = ?, historico_pdf = ?, documento_pdf = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute([$nome, $email, $matricula, $cpf, $rg, $data_nascimento, $telefone, $historico_nome, $documento_nome, $id])) {
        $mensagem = "Dados atualizados com sucesso!";
        $mensagem_tipo = "sucesso";
        // Recarrega os dados atualizados
        $stmt = $conn->prepare("SELECT * FROM Alunos WHERE id = ?");
        $stmt->execute([$id]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $mensagem = "Erro ao atualizar dados: " . $stmt->errorInfo()[2];
        $mensagem_tipo = "erro";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Aluno</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex items-center justify-center p-6" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-3xl">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Editar Aluno</h1>

            <?php if ($mensagem): ?>
                <div class="mb-4 text-center">
                    <div class="<?= $mensagem_tipo === 'sucesso' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded">
                        <?= $mensagem; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($aluno['email']) ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div>
                        <label for="matricula" class="block text-sm font-medium text-gray-700">Matrícula</label>
                        <input type="text" name="matricula" value="<?= htmlspecialchars($aluno['matricula']) ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                        <input type="text" name="cpf" value="<?= htmlspecialchars($aluno['cpf']) ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div>
                        <label for="rg" class="block text-sm font-medium text-gray-700">RG</label>
                        <input type="text" name="rg" value="<?= htmlspecialchars($aluno['rg']) ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div>
                        <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                        <input type="date" name="data_nascimento" value="<?= $aluno['data_nascimento'] ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" name="telefone" value="<?= htmlspecialchars($aluno['telefone']) ?>" required class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                    </div>
                </div>

                <div>
                    <label for="historico_pdf" class="block text-sm font-medium text-gray-700">Histórico Escolar (PDF)</label>
                    <?php if ($aluno['historico_pdf']): ?>
                        <p class="text-sm mt-1 mb-2 text-blue-600">
                            <a href="uploads/<?= htmlspecialchars($aluno['historico_pdf']) ?>" target="_blank">Ver atual</a>
                        </p>
                    <?php endif; ?>
                    <input type="file" name="historico_pdf" accept="application/pdf" class="block w-full text-sm">
                </div>

                <div>
                    <label for="documento_pdf" class="block text-sm font-medium text-gray-700">Documento (PDF)</label>
                    <?php if ($aluno['documento_pdf']): ?>
                        <p class="text-sm mt-1 mb-2 text-blue-600">
                            <a href="uploads/<?= htmlspecialchars($aluno['documento_pdf']) ?>" target="_blank">Ver atual</a>
                        </p>
                    <?php endif; ?>
                    <input type="file" name="documento_pdf" accept="application/pdf" class="block w-full text-sm">
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="visualizar_alunos.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
