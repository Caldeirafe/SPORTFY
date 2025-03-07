<?php
session_start();
require_once 'config.php';
$pdo = conectar();

// Verifica se o usuário está logado
$logado = isset($_SESSION['usuario_id']);
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Obtém o termo da pesquisa, se existir
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

// Monta a consulta SQL filtrando pelo nome do evento, local ou esporte, se houver busca
if (!empty($busca)) {
    $sql = "SELECT * FROM eventos 
            WHERE (evento_nome LIKE :busca OR evento_local LIKE :busca OR evento_esporte LIKE :busca) 
            AND evento_data >= CURDATE() 
            ORDER BY evento_data ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':busca', "%$busca%", PDO::PARAM_STR);
} else {
    $sql = "SELECT * FROM eventos WHERE evento_data >= CURDATE() ORDER BY evento_data ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sportfy - Da o Play</title>

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
    <section class="hero is-success is-medium">
        <div class="hero-body has-text-centered">
            <p class="title">Da o Play</p>
            <p class="subtitle">Encontre partidas e quadras disponíveis na sua região!</p>
            <div class="container has-text-centered">
                <?php if ($logado): ?>
                    <!-- Usuário logado: mostrar botão "Criar Novo Jogo" -->
                    <a href="perfil.php?Ajax=NovoJogo" class="button is-dark is-large">Criar Novo Jogo</a>
                <?php else: ?>
                    <!-- Usuário não logado: mostrar botão "Cadastre-se" -->
                    <a href="cadastro.php" class="button is-dark is-large">Cadastre-se</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Seção de Busca -->
    <section class="section">
        <div class="container">
            <div class="box">
                <h2 class="title is-4">Busque por jogos ou quadras</h2>
                <form method="GET" action="">
                    <div class="field has-addons">
                        <div class="control is-expanded">
                            <input class="input" type="text" name="busca" placeholder="Digite o esporte ou local..." value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>">
                        </div>
                        <div class="control">
                            <button class="button is-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Seção de Partidas Disponíveis -->
    <section class="section">
        <div class="container">
            <h2 class="title is-4 has-text-centered">Partidas Disponíveis</h2>

            <div class="columns is-multiline">

                <?php foreach ($eventos as $evento): ?>
                    <div class="column is-4">
                        <div class="card">
                            <div class="card-content">
                                <p class="title is-5"><?= htmlspecialchars($evento['evento_nome']) ?></p>
                                <p><strong>Local:</strong> <?= htmlspecialchars($evento['evento_local']) ?> |
                                    <strong>Esporte:</strong> <?= htmlspecialchars($evento['evento_esporte']) ?></p>
                                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($evento['evento_data'])) ?> | 
                                    <strong>Horário:</strong> <?= htmlspecialchars($evento['evento_hora']) ?></p>
                                <p><strong>Participantes:</strong> <?= $evento['inscritos'] ?> / <?= $evento['evento_max_pessoas'] ?> pessoas</p>
                                <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($evento['evento_descricao'])) ?></p>

                                <?php
                                // Verifica se o usuário é o criador ou um participante
                                $isCriador = ($evento['usuario_id'] == $usuario_id);
                                $isParticipante = false;

                                if ($usuario_id) {
                                    $stmt = $pdo->prepare("SELECT * FROM participacoes WHERE evento_id = ? AND usuario_id = ?");
                                    $stmt->execute([$evento['evento_id'], $usuario_id]);
                                    if ($stmt->fetch()) {
                                        $isParticipante = true;
                                    }
                                }
                                ?>

                                <!-- Botões de Ação -->
                                <div class="buttons mt-3">
                                    <?php if ($isCriador): ?>
                                        <!-- Botões de Editar e Excluir para o Criador -->
                                        <a href="editar_evento.php?evento_id=<?= $evento['evento_id'] ?>" class="button is-warning">Editar</a>
                                        <form method="POST" action="deletar_evento.php" style="display:inline;">
                                            <input type="hidden" name="evento_id" value="<?= $evento['evento_id'] ?>">
                                            <button type="submit" class="button is-danger" onclick="return confirm('Tem certeza que deseja excluir este evento?')">Excluir</button>
                                        </form>
                                    <?php elseif ($isParticipante): ?>
                                        <!-- Botão de Desistir para Participantes -->
                                        <form method="POST" action="desistir_evento.php" style="display:inline;">
                                            <input type="hidden" name="evento_id" value="<?= $evento['evento_id'] ?>">
                                            <button type="submit" class="button is-danger" onclick="return confirm('Tem certeza que deseja desistir deste evento?')">Desistir</button>
                                        </form>
                                    <?php else: ?>
                                        <!-- Botão de Participar -->
                                        <form method="POST" action="participar_evento.php" class="mt-2">
                                            <input type="hidden" name="evento_id" value="<?= $evento['evento_id'] ?>">
                                            <button type="submit" class="button is-success is-fullwidth">Participar</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

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