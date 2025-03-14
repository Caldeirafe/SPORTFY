<?php
require_once 'config.php';
require_once 'auth.php';

verificaLogin();
$conn = conectar();
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuarios_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Perfil</title>
    

<script>
    function initMap() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    var map = new google.maps.Map(document.getElementById('map'), {
                        zoom: 15,
                        center: userLocation
                    });

                    new google.maps.Marker({
                        position: userLocation,
                        map: map,
                        title: 'Sua localização'
                    });
                },
                function() {
                    alert("Erro ao obter localização");
                }
            );
        } else {
            alert("Geolocalização não suportada pelo seu navegador");
        }
    }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBbhtwoMur3kXRC3VLgZ2AObHk55K7IYMc" async defer></script>


</head>
<body>
    <div class="column is-11">
        <div class="box has-text-centered">
            <h3 class="title is-4">Próximos Jogos</h3>
            <a href="daoplay.php" class="button is-success">Ver Jogos</a>
        </div> 

        <!-- Mapa -->
        <div class="box">
            <h3 class="title is-4 has-text-centered">Localização</h3>
            <div id="map" style="width: 100%; height: 400px;"></div>
        </div>
    </div>

    <script>
        window.onload = function() {
            initMap();
        };
    </script>
</body>
</html>