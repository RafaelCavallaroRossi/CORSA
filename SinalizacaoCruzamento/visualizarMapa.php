<?php
include 'config.php';
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Busca todos os dispositivos cadastrados
$stmt = $conn->prepare("SELECT id, nome, id_ponto, localizacao, status, observacao FROM Dispositivos");
$stmt->execute();
$dispositivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
$googleMapsApiKey = $_ENV['GOOGLE_MAPS_API_KEY'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Mapa de Dispositivos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
    <style>
        #map {
            width: 100%;
            height: 500px;
            border-radius: 12px;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'cabecalho.php'; ?>
    <div class="w-full min-h-screen gradient-bg flex flex-col items-center p-8" style="padding-top: 88px;">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-6xl">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Mapa de Dispositivos</h1>
            <div id="map"></div>
            <div class="mt-6 text-center">
                <a href="menu.php" class="inline-block bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Voltar ao Menu</a>
            </div>
        </div>
    </div>
    <script>
        // Dados dos dispositivos vindos do PHP
        const dispositivos = <?php echo json_encode($dispositivos); ?>;

        const apiKey = <?php echo json_encode($googleMapsApiKey); ?>;

        // Função para obter coordenadas a partir do campo localizacao
        async function getLatLng(localizacao) {
            // Se já for coordenada, retorna direto
            if (/^-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?$/.test(localizacao)) {
                const [lat, lng] = localizacao.split(',').map(Number);
                return {lat, lng};
            }
            // Se for endereço, usa geocoding
            const response = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(localizacao)}&key=${apiKey}`);
            const data = await response.json();
            if (data.status === "OK") {
                return data.results[0].geometry.location;
            }
            return null;
        }

        // Ícones personalizados por status
        function getMarkerIcon(status) {
            if (status === "Ativo") return "http://maps.google.com/mapfiles/ms/icons/green-dot.png";
            if (status === "Inativo") return "http://maps.google.com/mapfiles/ms/icons/red-dot.png";
            return "http://maps.google.com/mapfiles/ms/icons/yellow-dot.png";
        }

        async function initMap() {
            // Posição inicial (Brasil)
            const center = { lat: -14.2350, lng: -51.9253 };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 4,
                center: center,
            });

            // Adiciona marcadores
            for (const disp of dispositivos) {
                const latlng = await getLatLng(disp.localizacao);
                if (!latlng) continue;
                const marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    title: disp.nome + " (" + disp.status + ")",
                    icon: getMarkerIcon(disp.status)
                });
                const info = `
                    <div>
                        <strong>${disp.nome}</strong><br>
                        ID do Ponto: ${disp.id_ponto}<br>
                        Status: <span style="color:${disp.status === 'Ativo' ? 'green' : (disp.status === 'Inativo' ? 'red' : 'orange')}">${disp.status}</span><br>
                        Localização: ${disp.localizacao}<br>
                        ${disp.observacao ? 'Obs: ' + disp.observacao : ''}
                    </div>
                `;
                const infowindow = new google.maps.InfoWindow({ content: info });
                marker.addListener("click", () => {
                    infowindow.open(map, marker);
                });
            }
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode($googleMapsApiKey); ?>&callback=initMap"></script>
</body>
</html>
