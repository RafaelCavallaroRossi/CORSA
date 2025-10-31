<?php
// Reusable sidebar for CORSA
// Marca o item ativo de acordo com o nome do arquivo atual
$current = basename($_SERVER['PHP_SELF']);
?>
<nav class="w-64 bg-white shadow-md">
    <div class="p-6 text-xl font-bold">CORSA</div>
    <ul class="mt-6 space-y-2">
        <li>
            <a href="menu.php" class="block p-2 hover:bg-gray-200 rounded <?php if ($current == 'menu.php') echo 'font-semibold text-blue-700'; ?>">
                <i class="fa fa-home mr-2"></i>Dashboard
            </a>
        </li>
        <li>
            <a href="cadastro.php" class="block p-2 hover:bg-gray-200 rounded <?php if ($current == 'cadastro.php') echo 'font-semibold text-blue-700'; ?>">
                <i class="fa-solid fa-square-plus mr-2"></i>Cadastrar Dispositivos
            </a>
        </li>
        <li>
            <a href="visualizarMapa.php" class="block p-2 hover:bg-gray-200 rounded <?php if ($current == 'visualizarMapa.php') echo 'font-semibold text-blue-700'; ?>">
                <i class="fa fa-eye mr-2"></i>Visualizar Mapa
            </a>
        </li>
        <li>
            <a href="relatorioEventos.php" class="block p-2 hover:bg-gray-200 rounded <?php if ($current == 'relatorioEventos.php') echo 'font-semibold text-blue-700'; ?>">
                <i class="fa fa-file-alt mr-2"></i>Relatório de Eventos
            </a>
        </li>
        <li>
            <a href="editarCamera.php" class="block p-2 hover:bg-gray-200 rounded <?php if ($current == 'editarCamera.php') echo 'font-semibold text-blue-700'; ?>">
                <i class="fa-solid fa-pencil mr-2"></i>Editar Câmeras
            </a>
        </li>
    </ul>
</nav>