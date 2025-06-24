<?php
include 'config.php';
session_start();

$todas_turmas = $conn->query("SELECT id, nome FROM Turmas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

$filtro_turma = isset($_GET['turma_id']) ? intval($_GET['turma_id']) : 0;
$relatorio = [];

if ($filtro_turma > 0) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Aulas WHERE turma_id = ?");
    $stmt->execute([$filtro_turma]);
    $total_aulas = (int) $stmt->fetchColumn();
    $sql = "
        SELECT a.id AS aluno_id, a.nome,
            SUM(CASE WHEN f.status = 'Ausente' THEN 1 ELSE 0 END) AS faltas,
            SUM(CASE WHEN f.status = 'Presente' THEN 1 ELSE 0 END) AS presencas
        FROM Alunos a
        JOIN Alunos_Turmas atur ON atur.aluno_id = a.id
        LEFT JOIN Frequencia f ON f.aluno_id = a.id
        LEFT JOIN Aulas au ON au.id = f.aula_id AND au.turma_id = ?
        WHERE atur.turma_id = ?
        GROUP BY a.id, a.nome
        ORDER BY a.nome
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$filtro_turma, $filtro_turma]);
    $relatorio = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Faltas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex flex-col items-center p-6" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-5xl">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Relatório de Faltas por Turma</h1>

            <form method="GET" class="mb-6 text-center">
                <label for="turma_id" class="text-sm font-medium text-gray-700 mr-2">Selecione a Turma:</label>
                <select name="turma_id" id="turma_id" onchange="this.form.submit()" class="py-2 px-3 border border-gray-300 rounded-md">
                    <option value="">-- Selecione --</option>
                    <?php foreach ($todas_turmas as $turma): ?>
                        <option value="<?= $turma['id'] ?>" <?= $filtro_turma == $turma['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($turma['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($filtro_turma && $relatorio): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-700">Aluno</th>
                                <th class="px-4 py-2 text-center text-gray-700">Total de Aulas</th>
                                <th class="px-4 py-2 text-center text-gray-700">Presenças</th>
                                <th class="px-4 py-2 text-center text-gray-700">Faltas</th>
                                <th class="px-4 py-2 text-center text-gray-700">Presença (%)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($relatorio as $aluno): 
                                $faltas = (int)$aluno['faltas'];
                                $presencas = (int)$aluno['presencas'];
                                $aulas_registradas = $faltas + $presencas;
                                $total = max($total_aulas, $aulas_registradas);
                                $porcentagem = $total > 0 ? round(($presencas / $total) * 100, 1) : 0;
                            ?>
                                <tr>
                                    <td class="px-4 py-2"><?= htmlspecialchars($aluno['nome']) ?></td>
                                    <td class="px-4 py-2 text-center"><?= $total_aulas ?></td>
                                    <td class="px-4 py-2 text-center"><?= $presencas ?></td>
                                    <td class="px-4 py-2 text-center"><?= $faltas ?></td>
                                    <td class="px-4 py-2 text-center"><?= $porcentagem ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($filtro_turma): ?>
                <p class="text-center text-gray-500 mt-4">Nenhum dado de frequência encontrado para esta turma.</p>
            <?php endif; ?>

            <div class="mt-6 text-center">
                <a href="menu.php" class="inline-block bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Voltar ao Menu</a>
            </div>
        </div>
    </div>
</body>
</html>
