<?php
require 'vendor/autoload.php'; // Composer para MongoDB

$cliente = new MongoDB\Client("mongodb://localhost:27017");
$colecao = $cliente->monitoramento->sensores;

$sensores = $colecao->find();

echo "<h2>Sensores Ambientais</h2>";
foreach ($sensores as $sensor) {
    echo "<h3>" . $sensor['tipo'] . " em " . $sensor['localizacao']['cidade'] . "</h3>";
    foreach ($sensor['leituras'] as $leitura) {
        echo "Data/Hora: " . $leitura['timestamp'] . " - Valor: " . $leitura['valor'] . "<br>";
    }
    echo "<hr>";
}
?>
