<?php
$host = 'localhost';
$db = 'InstituicaoVestibular';
$user = 'root';
$pass = 'luisfelipe'; // ou a senha que você configurou

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
