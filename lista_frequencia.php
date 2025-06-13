<?php
include 'config.php';
session_start();

$aula_id = $_GET['aula_id'] ?? null;
$turma_id = null;
$aula = null;
$alunos = [];

if ($aula_id) {
    // Obter dados da aula e turma
    $stmt = $conn->prepare("SELECT * FROM Aulas WHERE id = ?");
    $stmt->execute([$aula_id]);
    $aula = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($aula) {
        $turma_id = $aula['turma_id'];

        // Obter alunos da turma
        $stmt = $conn->prepare("
            SELECT Alunos.id, Alunos.nome
            FROM Alunos
            JOIN Alunos_Turmas ON Alunos.id = Alunos_Turmas.aluno_id
            WHERE Alunos_Turmas.turma_id = ?
            ORDER BY Alunos.nome
        ");
        $stmt->execute([$turma_id]);
        $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Processar submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aula_id'])) {
    $aula_id_post = $_POST['aula_id'];

    // Verificar se frequência já foi registrada para esta aula
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Frequencia WHERE aula_id = ?");
    $stmt->execute([$aula_id_post]);
    $frequencia_existente = $stmt->fetchColumn();

    if ($frequencia_existente > 0) {
        $msg = ['type' => 'warning', 'text' => 'Frequência já registrada para esta aula!'];
    } else {
        // Registrar presenças
        $presentes = $_POST['presentes'] ?? [];
        foreach ($presentes as $aluno_id) {
            $stmt = $conn->prepare("INSERT INTO Frequencia (aluno_id, aula_id, status) VALUES (?, ?, 'Presente')");
            $stmt->execute([$aluno_id, $aula_id_post]);
        }

        // Registrar ausentes (apenas alunos daquela turma)
        $stmt = $conn->prepare("
            SELECT Alunos.id
            FROM Alunos
            JOIN Alunos_Turmas ON Alunos.id = Alunos_Turmas.aluno_id
            WHERE Alunos_Turmas.turma_id = (
                SELECT turma_id FROM Aulas WHERE id = ?
            )
        ");
        $stmt->execute([$aula_id_post]);
        $todos_alunos = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($todos_alunos as $aluno_id) {
            if (!in_array($aluno_id, $presentes)) {
                $stmt = $conn->prepare("INSERT INTO Frequencia (aluno_id, aula_id, status) VALUES (?, ?, 'Ausente')");
                $stmt->execute([$aluno_id, $aula_id_post]);
            }
        }

        $msg = ['type' => 'success', 'text' => 'Frequência registrada com sucesso!'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Registrar Frequência</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
<div class="min-h-screen flex items-center justify-center gradient-bg p-4">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Registrar Frequência</h1>

        <?php if (!empty($msg)): ?>
            <div class="mb-4 px-4 py-3 rounded <?php
                echo $msg['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-yellow-100 text-yellow-700 border border-yellow-400';
            ?>">
                <?php echo htmlspecialchars($msg['text']); ?>
            </div>
        <?php endif; ?>

        <?php if (!$aula): ?>
            <p class="text-red-600">Aula não encontrada ou parâmetro inválido.</p>
            <a href="lista_aula.php" class="text-blue-600 hover:underline">Voltar para lista de aulas</a>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="aula_id" value="<?php echo $aula['id']; ?>" />

                <div class="mb-4">
                    <label class="block font-medium text-gray-700 mb-1">Turma</label>
                    <input type="text" disabled value="<?php
                        // Obter nome da turma
                        $stmt = $conn->prepare("SELECT nome FROM Turmas WHERE id = ?");
                        $stmt->execute([$turma_id]);
                        echo htmlspecialchars($stmt->fetchColumn());
                    ?>" class="w-full px-3 py-2 border rounded-md bg-gray-100 cursor-not-allowed" />
                </div>

                <div class="mb-4">
                    <label class="block font-medium text-gray-700 mb-1">Aula</label>
                    <input type="text" disabled value="<?php echo htmlspecialchars($aula['tema'] . ' - ' . $aula['data_aula']); ?>"
                        class="w-full px-3 py-2 border rounded-md bg-gray-100 cursor-not-allowed" />
                </div>

                <div class="mb-6">
                    <label class="block font-medium text-gray-700 mb-2">Alunos</label>
                    <?php if (count($alunos) === 0): ?>
                        <p class="text-gray-600">Nenhum aluno cadastrado nesta turma.</p>
                    <?php else: ?>
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Aluno</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Presente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alunos as $aluno): ?>
                                    <tr>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($aluno['nome']); ?></td>
                                        <td class="px-4 py-2 text-center">
                                            <input type="checkbox" name="presentes[]" value="<?php echo $aluno['id']; ?>" />
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Registrar Frequência
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>