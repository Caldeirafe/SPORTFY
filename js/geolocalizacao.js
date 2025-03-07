// geolocalizacao.js

// Função para pegar a localização do usuário via navegador
function getUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            // Agora você pode passar esses valores para o backend via AJAX ou formulário
            console.log("Latitude: " + latitude + ", Longitude: " + longitude);

            // Exemplo de chamada AJAX para enviar para o PHP
            fetch("seu_script.php?latitude=" + latitude + "&longitude=" + longitude)
                .then(response => response.json())
                .then(data => {
                    // Processar os eventos encontrados (dados retornados)
                    console.log(data);
                });
        }, function() {
            alert("Erro ao obter a localização.");
        });
    } else {
        alert("Geolocalização não é suportada neste navegador.");
    }
}

// Chama a função quando a página é carregada
window.onload = function() {
    getUserLocation();
};
