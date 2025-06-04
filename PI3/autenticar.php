<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id_usuario, nome_usuario, senha, tipo_usuario, rm FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nome_usuario'] = $usuario['nome_usuario'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
        $_SESSION['rm'] = $usuario['rm'];

        // Redirecionamento conforme o tipo de usuário
        if ($usuario['tipo_usuario'] == 'Secretaria') {
            header('Location: painel_secretaria.php');
            exit;
        } elseif ($usuario['tipo_usuario'] == 'professor') {
            header('Location: Professor/menuprofessor.php');
            exit;
        }  else {
            header('Location: index.php');
            exit;
        }
    } else {
        $_SESSION['erro_login'] = 'Email ou senha inválidos.';
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
