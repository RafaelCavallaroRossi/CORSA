<?php
include 'config.php';
session_start();

$mensagem = '';
$mensagem_tipo = '';

if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);

    $stmt = $conn->prepare("SELECT historico_pdf, documento_pdf FROM Alunos WHERE id = ?");
    $stmt->execute([$id]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($aluno) {
        $delete = $conn->prepare("DELETE FROM Alunos WHERE id = ?");
        if ($delete->execute([$id])) {
            if (!empty($aluno['historico_pdf']) && file_exists("uploads/" . $aluno['historico_pdf'])) {
                unlink("uploads/" . $aluno['historico_pdf']);
            }
            if (!empty($aluno['documento_pdf']) && file_exists("uploads/" . $aluno['documento_pdf'])) {
                unlink("uploads/" . $aluno['documento_pdf']);
            }
            $mensagem = "Aluno excluído com sucesso.";
            $mensagem_tipo = "sucesso";
        } else {
            $mensagem = "Erro ao excluir aluno.";
            $mensagem_tipo = "erro";
        }
    }
}

$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : '';
$sql = "SELECT * FROM Alunos";
$params = [];

if ($filtro !== '') {
    $sql .= " WHERE nome LIKE ? OR email LIKE ? OR matricula LIKE ?";
    $filtro_param = "%$filtro%";
    $params = [$filtro_param, $filtro_param, $filtro_param];
}
$sql .= " ORDER BY nome ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alunos Cadastrados</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
    
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex flex-col items-center p-8" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-6xl">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Alunos Cadastrados</h1>

            <?php if ($mensagem): ?>
                <div class="mb-4 text-center">
                    <div class="<?php echo $mensagem_tipo === 'sucesso' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded" role="alert">
                        <?php echo $mensagem; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="GET" class="mb-6 w-full max-w-md mx-auto">
                <input type="text" name="filtro" value="<?= htmlspecialchars($filtro) ?>"
                       class="w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Buscar por nome, e-mail ou matrícula...">
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700">Nome</th>
                            <th class="px-4 py-2 text-left text-gray-700">Email</th>
                            <th class="px-4 py-2 text-left text-gray-700">Matrícula</th>
                            <th class="px-4 py-2 text-center text-gray-700">Histórico</th>
                            <th class="px-4 py-2 text-center text-gray-700">Documento</th>
                            <th class="px-4 py-2 text-center text-gray-700">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td class="px-4 py-2"><?= htmlspecialchars($aluno['nome']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($aluno['email']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($aluno['matricula']) ?></td>
                                <td class="px-4 py-2 text-center">
                                    <?php if ($aluno['historico_pdf']): ?>
                                        <a href="<?= 'uploads/' . htmlspecialchars($aluno['historico_pdf']) ?>" target="_blank"
                                           class="text-blue-600 hover:underline">Ver PDF</a>
                                    <?php else: ?>
                                        <span class="text-gray-400">---</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <?php if ($aluno['documento_pdf']): ?>
                                        <a href="<?= 'uploads/' . htmlspecialchars($aluno['documento_pdf']) ?>" target="_blank"
                                           class="text-blue-600 hover:underline">Ver PDF</a>
                                    <?php else: ?>
                                        <span class="text-gray-400">---</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2 text-center space-x-2">
                                    <a href="editar_aluno.php?id=<?= $aluno['id'] ?>"
                                       class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 text-sm">Editar</a>
                                    <a href="?excluir=<?= $aluno['id'] ?>"
                                       onclick="return confirm('Tem certeza que deseja excluir este aluno?');"
                                       class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($alunos) === 0): ?>
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">Nenhum aluno encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 text-center">
                <a href="menu.php" class="inline-block bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Voltar ao Menu</a>
            </div>
        </div>
    </div>
</body>
</html>
