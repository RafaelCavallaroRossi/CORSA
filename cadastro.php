<?php
include 'config.php';
$conn = Database::getInstance()->getConnection();
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$mensagem = '';
$mensagem_tipo = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $mensagem = "Requisição inválida.";
        $mensagem_tipo = "erro";
    } else {
        $nome = trim($_POST['nome']);
        $id_ponto = trim($_POST['id_ponto']);
        $localizacao = trim($_POST['localizacao']);
        // Sanitização e validação de status
        $status = in_array($_POST['status'], ['Ativo', 'Inativo', 'Manutenção']) ? $_POST['status'] : 'Ativo';
        // Sanitização de observacao
        $observacao = htmlspecialchars($_POST['observacao']);

        // Validação de duplicidade de id_ponto
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Dispositivos WHERE id_ponto = ?");
        $stmt->execute([$id_ponto]);
        if ($stmt->fetchColumn() > 0) {
            $mensagem = "Já existe um dispositivo com este ID de ponto.";
            $mensagem_tipo = "erro";
        } else {
            // Esperamos localizacao em formato "lat,lon" (decimal)
            if (strpos($localizacao, ',') === false) {
                $mensagem = "Formato de localização inválido. Use: latitude,longitude";
                $mensagem_tipo = "erro";
            } else {
                list($latStr, $lonStr) = array_map('trim', explode(',', $localizacao, 2));
                if ($latStr === '' || $lonStr === '' || !is_numeric($latStr) || !is_numeric($lonStr)) {
                    $mensagem = "Coordenadas inválidas. Use números em formato decimal: latitude,longitude";
                    $mensagem_tipo = "erro";
                } else {
                    $latitude = floatval($latStr);
                    $longitude = floatval($lonStr);
                    // Validar intervalos
                    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
                        $mensagem = "Coordenadas fora do intervalo. Latitude deve estar entre -90 e 90; longitude entre -180 e 180.";
                        $mensagem_tipo = "erro";
                    } else {
                        // Inserir no banco. Se quiser separar lat/lon em colunas, ajuste a tabela e o SQL.
                        // Aqui mantenho localizacao como string e adiciono latitude/longitude se existirem colunas.
                        // Verifique se as colunas latitude e longitude existem; se não existirem, remova-as do INSERT.
                        $hasLatLonCols = false;
                        // Tentar detectar colunas: (opcional) -- simplificado: assumir que não existem para evitar erro.
                        if ($hasLatLonCols) {
                            $sql = "INSERT INTO Dispositivos (nome, id_ponto, localizacao, latitude, longitude, status, observacao) VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $valores = [$nome, $id_ponto, $localizacao, $latitude, $longitude, $status, $observacao];
                        } else {
                            $sql = "INSERT INTO Dispositivos (nome, id_ponto, localizacao, status, observacao) VALUES (?, ?, ?, ?, ?)";
                            $valores = [$nome, $id_ponto, $localizacao, $status, $observacao];
                        }

                        $stmt = $conn->prepare($sql);
                        if ($stmt->execute($valores)) {
                            $mensagem = "Dispositivo cadastrado com sucesso!";
                            $mensagem_tipo = "sucesso";
                        } else {
                            $mensagem = "Erro ao cadastrar dispositivo: " . $stmt->errorInfo()[2];
                            $mensagem_tipo = "erro";
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Dispositivo - CORSA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-100 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="h-screen flex" style="padding-top: 88px;">
        <?php include 'sidebar.php'; ?>
        <!-- Main content: formulário de cadastro -->
        <main class="flex-1 p-6">
            <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-2xl mx-auto">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-800 mt-4">Cadastro de Dispositivo</h1>
                    <p class="text-gray-600">Preencha os dados para cadastrar um novo dispositivo de monitoramento</p>
                </div>

                <?php if ($mensagem): ?>
                    <div class="mb-4">
                        <div class="<?php echo $mensagem_tipo === 'sucesso' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?> px-4 py-3 rounded">
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
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-4">
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome do Dispositivo</label>
                        <input type="text" id="nome" name="nome" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="id_ponto" class="block text-sm font-medium text-gray-700">ID do Ponto</label>
                        <input type="text" id="id_ponto" name="id_ponto" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="localizacao" class="block text-sm font-medium text-gray-700">Localização (latitude,longitude)</label>
                        <input type="text" id="localizacao" name="localizacao" required placeholder="-23.550520,-46.633308" title="latitude,longitude em graus decimais (use ponto . e vírgula ,)" pattern="^-?\d{1,3}\.\d+,-?\d{1,3}\.\d+$" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Use coordenadas em graus decimais: latitude,longitude (ex.: -23.550520,-46.633308).</p>
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
                        <a href="menu.php" class="w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50">
                            Voltar
                        </a>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700">
                            Cadastrar
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
