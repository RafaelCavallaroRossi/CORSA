<?php 
session_start();
include 'config.php';
$conn = Database::getInstance()->getConnection();

// Limite de tentativas
if (!isset($_SESSION['tentativas_login'])) {
    $_SESSION['tentativas_login'] = 0;
}
if ($_SESSION['tentativas_login'] >= 5) {
    $_SESSION['erro_index'] = "Muitas tentativas. Tente novamente em alguns minutos.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['erro_index'] = "Requisição inválida.";
        header("Location: index.php");
        exit;
    }
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tentativas_login'] = 0; // zera tentativas ao logar
        header("Location: menu.php");
        exit;
    } else {
        $_SESSION['tentativas_login'] += 1;
        $_SESSION['erro_index'] = "Email ou senha inválidos.";
        header("Location: index.php");
        exit;
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
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="bg-gray-50 font-sans">
    <div class="w-full min-h-screen gradient-bg flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Acesso ao Sistema</h1>
                <p class="text-gray-600">Gerencie o monitoramento de câmeras</p>
            </div>
            <?php if (isset($_SESSION['erro_index'])): ?>
                <div class="mb-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert" id="alerta-balao">
                        <?php echo $_SESSION['erro_index']; unset($_SESSION['erro_index']); ?>
                    </div>
                </div>
            <?php endif; ?>
            <form id="index-form" class="space-y-4" action="index.php" method="post">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                    <input type="email" id="email" name="email" required 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="user123@gmail.com">
                </div>
                <div>
                    <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                    <input type="password" id="senha" name="senha" required 
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div>
                    <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Entrar
                    </button>
                </div>
            </form>
        </div>
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
</body>
</html>
