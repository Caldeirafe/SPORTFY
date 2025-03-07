<?php
require_once 'config.php';
require_once 'auth.php';

// Verifica se está logado
verificaLogin();

// Busca dados do usuário
$conn = conectar();
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuarios_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
$evento_id = $_GET['evento_id'];
$pdo = conectar();

// Verifica se o ID do evento foi passado na URL
if (!isset($_GET['evento_id'])) {
    die('ID do evento não especificado.');
}

// Busca os dados do evento no banco de dados
$sql = "SELECT * FROM eventos WHERE evento_id = :evento_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':evento_id', $evento_id, PDO::PARAM_INT);
$stmt->execute();
$evento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evento) {
    die('Evento não encontrado.');
}
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
                                <img src="img/usuario.png" alt="Imagem do perfil">
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
                                <a href="perfil.php" class="button is-warning is-fullwidth">Voltar</a>
                            </div>
                        </div>
                    </div>
                </aside>



<div class="box">
    <h3 class="title is-4 has-text-centered">Editar Evento</h3>

    <form method="POST" action="atualizar_evento.php">
        <input type="hidden" name="evento_id" value="<?= htmlspecialchars($evento['evento_id']) ?>">

        <div class="field">
            <label class="label">Nome do Evento</label>
            <div class="control">
                <input class="input" type="text" name="evento_nome" value="<?= htmlspecialchars($evento['evento_nome']) ?>" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Data</label>
            <div class="control">
                <input class="input" type="date" name="evento_data" value="<?= htmlspecialchars($evento['evento_data']) ?>" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Hora</label>
            <div class="control">
                <input class="input" type="time" name="evento_hora" value="<?= htmlspecialchars($evento['evento_hora']) ?>" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Número Máximo de Pessoas</label>
            <div class="control">
                <input class="input" type="number" name="evento_max_pessoas" value="<?= htmlspecialchars($evento['evento_max_pessoas']) ?>" min="1" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Local</label>
            <div class="control">
                <input class="input" type="text" name="evento_local" value="<?= htmlspecialchars($evento['evento_local']) ?>">
            </div>
        </div>

        <div class="field">
            <label class="label">Esporte</label>
            <div class="control">
                <div class="select">
                    <select name="evento_esporte" required>
                        <option value="futebol" <?= $evento['evento_esporte'] == 'futebol' ? 'selected' : '' ?>>Futebol</option>
                        <option value="basquete" <?= $evento['evento_esporte'] == 'basquete' ? 'selected' : '' ?>>Basquete</option>
                        <option value="volei" <?= $evento['evento_esporte'] == 'volei' ? 'selected' : '' ?>>Vôlei</option>
                        <option value="outros" <?= $evento['evento_esporte'] == 'outros' ? 'selected' : '' ?>>Outros</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="field">
            <label class="label">Descrição</label>
            <div class="control">
                <textarea class="textarea" name="evento_descricao" maxlength="100" required><?= htmlspecialchars($evento['evento_descricao']) ?></textarea>
            </div>
        </div>

        <div class="field is-grouped is-grouped-centered">
            <div class="control">
                <button class="button is-success" type="submit">Salvar Alterações</button>
            </div>
        </div>
    </form>
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
        </script>

</body>

</html>