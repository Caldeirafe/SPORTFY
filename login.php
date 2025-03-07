<?php
require_once 'config.php';
require_once 'auth.php';

// Verifica se o usuário está logado
$logado = isset($_SESSION['usuario_id']);

// Se já estiver logado, redireciona para o perfil
if ($logado) {
    header("Location: perfil.php");
    exit;
}

// Processa o formulário de login
$erro = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    if (fazerLogin($email, $senha)) {
        header("Location: perfil.php");
        exit;
    } else {
        $erro = "Email ou senha incorretos"; // Mensagem ajustada para ficar mais clara
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="./style/style.css">
    <link rel="shortcut icon" href="assets/Frame 1.png" type="image/x-icon">
    <title>Login - Sportfy</title>
</head>
<body id="body-login">
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

<section id="section-login" class="section">
    <div class="container">
        <div class="box">
            <h1 class="title has-text-centered">Login</h1>

            <!-- Exibe a mensagem de erro dentro do box -->
            <?php if ($erro): ?>
                <div class="notification is-danger">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" id="form">
                <div class="field">
                    <label class="label">Email</label>
                    <div class="control">
                        <input class="input" type="email" name="email" placeholder="Email" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label">Senha</label>
                    <div class="control">
                        <input class="input" type="password" name="senha" placeholder="Senha" required>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button class="button is-link is-fullwidth" type="submit">Entrar</button>
                    </div>
                </div>
                <div class="has-text-centered">
                    <p>Ainda não tem uma conta? <a href="cadastro.php">Cadastrar-se</a></p>
                </div>
            </form>
        </div>
    </div>
</section>

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
    // Script para ativar o menu hamburguer
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
</script>
</body>
</html>
