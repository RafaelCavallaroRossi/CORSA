<?php
require 'vendor/autoload.php'; // Carrega o autoloader do Composer para MongoDB

use MongoDB\Client;

// Conexão com o MongoDBaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
$cliente = new Client("mongodb://localhost:27017");
$banco = $cliente->monitoramento;
$colecao = $banco->sensores;

// Busca todos os sensores
$sensores = $colecao->find();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Monitoramento Ambiental</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; }
        h2 { color: #004d40; }
        .sensor { background-color: #ffffff; border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
        .alerta { color: #b71c1c; font-weight: bold; }
        .subtitulo { color: #00796b; }
    </style>
</head>
<body>
    <h1>Monitoramento Ambiental - Sensores Ativos</h1>

    <?php foreach ($sensores as $sensor): ?>
        <div class="sensor">
            <h2><?php echo $sensor['tipo']; ?> - <?php echo $sensor['modelo']; ?></h2>
            <p><strong>Status:</strong> <?php echo $sensor['status']; ?></p>
            <p><strong>Localização:</strong> <?php echo $sensor['localizacao']['cidade'] . ", " . $sensor['localizacao']['estado']; ?></p>
            <p><strong>Coordenadas:</strong> Lat <?php echo $sensor['localizacao']['coordenadas']['latitude']; ?>, Long <?php echo $sensor['localizacao']['coordenadas']['longitude']; ?></p>

            <h3 class="subtitulo">Últimas Leituras</h3>
            <ul>
                <?php
                $leituras = $sensor['leituras'];
                usort($leituras, function($a, $b) {
                    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
                });
                $ultimas = array_slice($leituras, 0, 3); // Mostrar apenas as 3 mais recentes
                foreach ($ultimas as $leitura): ?>
                    <li>
                        <strong><?php echo $leitura['timestamp']; ?>:</strong><br>
                        <?php foreach ($leitura['medicoes'] as $tipo => $valor): ?>
                            - <?php echo $tipo; ?>: <?php echo $valor; ?><br>
                        <?php endforeach; ?>
                        <?php if (!empty($leitura['alertas'])): ?>
                            <div class="alerta">
                                Alerta(s):<br>
                                <?php foreach ($leitura['alertas'] as $alerta): ?>
                                    - <?php echo $alerta['tipo']; ?> acima do limite (<?php echo $alerta['valorAtual']; ?> > <?php echo $alerta['limite']; ?>)<br>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h3 class="subtitulo">Histórico de Status</h3>
            <ul>
                <?php foreach ($sensor['historicoStatus'] as $item): ?>
                    <li><?php echo $item['status']; ?> desde <?php echo $item['desde']; ?><?php echo isset($item['ate']) ? ' até ' . $item['ate'] : ''; ?></li>
                <?php endforeach; ?>
            </ul>

            <h3 class="subtitulo">Última Manutenção</h3>
            <?php if (!empty($sensor['manutencoes'])): 
                $ultimaManutencao = end($sensor['manutencoes']); ?>
                <p>
                    <strong>Data:</strong> <?php echo $ultimaManutencao['data']; ?><br>
                    <strong>Técnico:</strong> <?php echo $ultimaManutencao['tecnico']; ?><br>
                    <strong>Descrição:</strong> <?php echo $ultimaManutencao['descricao']; ?>
                </p>
            <?php else: ?>
                <p>Sem registros de manutenção.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</body>
</html>

