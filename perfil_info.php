<?php
require_once 'config.php';
require_once 'auth.php';

verificaLogin();
$conn = conectar();
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuarios_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>
                <!-- Conteúdo do Perfil -->
                <div class="column is-11">
                    <div class="box has-text-centered">
                        <h3 class="title is-4">Próximos Jogos</h3>
                        <a href="#" class="button is-success">Ver Jogos</a>
                    </div> 

                    <!-- Mapa -->
                    <div class="box">
                        <h3 class="title is-4 has-text-centered">Localização</h3>
                        <iframe class="has-ratio" width="100%" height="400" src="https://www.google.com/maps/embed?..." allowfullscreen></iframe>
                    </div>
                </div>