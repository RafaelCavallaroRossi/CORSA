<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header class="w-full bg-blue-900 text-white py-4 px-6 flex justify-between items-center shadow-md fixed top-0 left-0 z-10">
    <div class="flex items-center space-x-2">
        <?php
            $menuLink = '#';
            if (isset($_SESSION['tipo'])) {
                if ($_SESSION['tipo'] === 'Secretaria') {
                    $menuLink = 'menu.php';
                } elseif ($_SESSION['tipo'] === 'Professor') {
                    $menuLink = 'painel_professor.php';
                }
            }
            ?>
            <a href="<?php echo $menuLink; ?>" class="flex items-center space-x-2 hover:text-gray-300 transition">
                <i class="fa-solid fa-school text-2xl"></i>
                <span class="font-bold text-lg">Escolinha do...</span>
            </a>
    </div>
    <div class="flex items-center space-x-4">
        <?php if (isset($_SESSION['usuario_id'])): ?>
            <span class="hidden sm:inline">
                Olá, <?php echo htmlspecialchars($_SESSION['nome'] ?? $_SESSION['tipo'] ?? 'Usuário'); ?>
            </span>
            <form action="logout.php" method="post" class="inline">
                <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white font-medium transition">Sair</button>
            </form>
        <?php endif; ?>
    </div>
</header>
