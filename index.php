<?php

// Garante que a sessão seja iniciada antes de verificar o login
session_start();

// Verifica se o usuário está logado
$logado = isset($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sportfy - Encontre seu jogo</title>

    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="./style/style.css">
    <link rel="shortcut icon" href="assets/Frame 1.png" type="image/x-icon">
</head>

<body>

    <!-- Navbar Responsiva -->
    <nav class="navbar is-dark" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="index.php">
                <img src="assets/Logo.png" alt="Logo Sportify">
            </a>
            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenu">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbarMenu" class="navbar-menu">
            <div class="navbar-end">
                <a class="navbar-item" href="index.php">Home</a>
                <?php if ($logado): ?>
                    <a class="navbar-item" href="daoplay.php">Da o Play</a>
                    <a class="navbar-item" href="perfil.php">Perfil</a>
                    <a class="navbar-item" href="logout.php">Sair</a>
                <?php else: ?>
                    <a class="navbar-item" href="daoplay.php">Da o Play</a>
                    <a class="navbar-item" href="login.php">Entrar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="campo" class="hero is-medium">
        <div class="hero-body" id="hero-body">
            <div class="highlight" id="highlight">
                <p class="title">Encontre Seu Jogo</p>
                <p class="subtitle">Junte-se a jogadores perto de você e participe de partidas na sua cidade!</p>
                <?php if (!$logado): ?>
                    <a href="login.php" class="button is-info is-large">Comece Agora</a>
                <?php else: ?>
                    <a href="daoplay.php" class="button is-info is-large">Encontrar Jogos</a>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <!-- Seção de Recursos -->
    <section class="section">
        <div class="container">
            <div class="columns is-multiline">
                <!-- Card 1 -->
                <div class="column is-4">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image is-4by3">
                                <img src="img/fut3.jpg" alt="Futebol">
                            </figure>
                        </div>
                        <div class="card-content has-text-centered">
                            <p class="title is-5">Participe de Jogos</p>
                            <p>Encontre partidas em sua região e jogue com novas pessoas.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="column is-4">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image is-4by3">
                                <img src="img/quadra.jpg" alt="Quadras">
                            </figure>
                        </div>
                        <div class="card-content has-text-centered">
                            <p class="title is-5">Encontre Quadras</p>
                            <p>Descubra quadras disponíveis e faça sua reserva rapidamente.</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="column is-4">
                    <div class="card">
                        <div class="card-image">
                            <figure class="image is-4by3">
                                <img src="img/eventos.jpg" alt="Eventos">
                            </figure>
                        </div>
                        <div class="card-content has-text-centered">
                            <p class="title is-5">Crie Eventos</p>
                            <p>Organize torneios e eventos esportivos com facilidade.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer class="footer has-background-dark has-text-white">
        <div class="content has-text-centered">
            <p>&copy; 2024 - Criado por DFG</p>
            <div class="social">
                <a href="#" class="icon"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="icon"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>
    </footer>

    <!-- Imagem fixa no canto inferior esquerdo -->
    <a href="https://brasil.un.org/pt-br/sdgs/3" target="_blank"><img src="assets/SaudeBemEstar.png" alt="Saúde e Bem-Estar" class="img-BemEstar" id="imgBemEstar"></a>

    <div class="card-info" id="cardInfo">
        <h1>Saúde e Bem-Estar</h1>
        <p>Garantir o acesso à saúde de qualidade e promover o bem-estar para todos, em todas as idades</p>
    </div>

    <script>
        // Script para ativar o menu hambúrguer
        document.addEventListener('DOMContentLoaded', () => {
            const navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
            if (navbarBurgers.length > 0) {
                navbarBurgers.forEach(el => {
                    el.addEventListener('click', () => {
                        const target = el.dataset.target;
                        const $target = document.getElementById(target);
                        el.classList.toggle('is-active');
                        $target.classList.toggle('is-active');
                    });
                });
            }
        });

        // Script para exibir/esconder o card ao passar o mouse
        document.addEventListener("DOMContentLoaded", function() {
            const imgBemEstar = document.getElementById("imgBemEstar");
            const cardInfo = document.getElementById("cardInfo");

            imgBemEstar.addEventListener("mouseover", function() {
                cardInfo.style.display = "block";
            });

            imgBemEstar.addEventListener("mouseout", function() {
                cardInfo.style.display = "none";
            });
        });
    </script>

</body>

</html>