<?php
require_once 'config.php';
require_once 'auth.php';

// Verifica se está logado
verificaLogin();

// Verifica se a variável 'atualizado' está presente no URL e exibe a mensagem
if (isset($_GET['atualizado']) && $_GET['atualizado'] == 'sucesso') {
    echo '<script>alert("Perfil atualizado com sucesso!");</script>';
}

// Busca dados do usuário
$conn = conectar();
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuarios_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sportfy</title>
    
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
                <a class="navbar-item" href="daoplay.php">Da o Play</a>
                <a class="navbar-item" href="perfil.php">Perfil</a>
                <a class="navbar-item" href="logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <section class="section">
        <div class="container">
            <div class="columns">
                
                <!-- Sidebar do Perfil -->
                <aside class="column is-3">
                    <div class="card">
                        <div class="card-image has-text-centered">
                            <figure id="img-perfil" class="image is-128x128 is-inline-block">
                            <img src="<?php echo $usuario['foto'] ? 'uploads/' . htmlspecialchars($usuario['foto']) : 'img/usuario.png'; ?>" alt="Imagem do perfil">
                            </figure>
                        </div>
                        <div class="card-content">
                            <p class="title is-4 has-text-centered"><?php echo htmlspecialchars($usuario['nome']); ?></p>
                            <p class="subtitle is-6 has-text-centered"><?php echo htmlspecialchars($usuario['sexo']); ?> | <?php

                                $data_nascimento = $usuario['data_nascimento']; // Pegando a data do usuário
                                $data_nascimento_obj = new DateTime($data_nascimento); // Criando um objeto DateTime
                                $hoje = new DateTime(); // Pegando a data atual
                                $idade = $hoje->diff($data_nascimento_obj)->y; // Calculando a diferença em anos
                                echo htmlspecialchars($idade); // Exibindo a idade?> Anos</p>
                            
                            <div class="buttons is-centered">
                            <button class="button is-info is-fullwidth" onclick="carregarConteudo('perfil_info')">Início</button>
                                <button class="button is-primary is-fullwidth" onclick="carregarConteudo('compromissos')">Meus Compromissos</button>
                                <button class="button is-link is-fullwidth" onclick="carregarConteudo('criar_evento')">Criar Evento</button>
                                <button class="button is-warning is-fullwidth" onclick="carregarConteudo('editar_perfil')">Editar Perfil</button>
                            </div>
                        </div>
                    </div>
                </aside>

                    <!-- Conteúdo do Perfil (Carregamento dinâmico) -->

                <div class="column is-10">
                    <div id="conteudoDinamico">
                        <?php
                            if(!isset ($_GET['Ajax']) || ($_GET['Ajax']) != 'NovoJogo') {
                                include 'perfil_info.php';
                            }else {
                                 include 'criar_evento.php'; // Carrega o perfil por padrão
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
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

    <script>

        
        // Adicione dentro da tag <script> existente
function atualizarImagemPerfil() {
    // Recarregar a página para mostrar a imagem atualizada
    location.reload();
}

        // Script para ativar o menu hamburguer
        document.addEventListener('DOMContentLoaded', () => {
            const navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
            if (navbarBurgers.length > 0) {
                navbarBurgers.forEach( el => {
                    el.addEventListener('click', () => {
                        const target = el.dataset.target;
                        const $target = document.getElementById(target);
                        el.classList.toggle('is-active');
                        $target.classList.toggle('is-active');
                    });
                });
            }
        });

        function carregarConteudo(pagina) {
            fetch(pagina + '.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('conteudoDinamico').innerHTML = html;
                })
                .catch(error => console.error('Erro ao carregar conteúdo:', error));
        }

    </script>

</body>
</html>