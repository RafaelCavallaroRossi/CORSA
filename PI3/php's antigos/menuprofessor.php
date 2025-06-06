<?php
session_start();
include 'config.php';

// Verificação de sessão
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo'] != 'Professor') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Professor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #065f46 100%);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center gradient-bg p-4">
        <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800 mt-4">Painel do Professor</h1>
                <p class="text-gray-600">Bem-vindo, professor! Gerencie suas turmas e frequências.</p>
            </div>
            <nav class="space-y-4">
                <a href="lista_frequencia.php" class="block w-full text-center py-2 px-4 rounded-md bg-blue-600 text-white font-medium hover:bg-blue-700 transition">Registrar Frequência</a>
                <a href="painel_frequencia.php" class="block w-full text-center py-2 px-4 rounded-md bg-green-600 text-white font-medium hover:bg-green-700 transition">Ver Frequência</a>
                <a href="logout.php" class="block w-full text-center py-2 px-4 rounded-md bg-red-600 text-white font-medium hover:bg-red-700 transition">Sair</a>
            </nav>
        </div>
    </div>
</body>
</html>
