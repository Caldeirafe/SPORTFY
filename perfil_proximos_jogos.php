<?php
require_once 'config.php';
require_once 'auth.php';

// Verifica se usuário está logado
verificaLogin();

// Busca dados do usuário
$usuario = getUsuarioLogado();

// Busca os próximos jogos
function getProximosJogos() {
    $conn = conectar();
    try {
        // Busca jogos que ainda não aconteceram
        $sql = "SELECT j.*, 
                    e.nome as esporte_nome,
                    (SELECT COUNT(*) FROM participantes WHERE jogo_id = j.id) as participantes_atuais 
                FROM jogos j 
                JOIN esportes e ON j.esporte_id = e.id 
                WHERE j.data >= CURRENT_DATE 
                ORDER BY j.data ASC, j.horario ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Erro ao buscar jogos: " . $e->getMessage());
        return [];
    }
}

// Verifica se usuário já está participando do jogo
function usuarioParticipa($jogo_id) {
    $conn = conectar();
    try {
        $stmt = $conn->prepare("SELECT id FROM participantes WHERE jogo_id = ? AND usuario_id = ?");
        $stmt->execute([$jogo_id, $_SESSION['usuario_id']]);
        return $stmt->fetch() !== false;
    } catch(PDOException $e) {
        return false;
    }
}

// Processa a participação em um jogo
if (isset($_POST['participar'])) {
    $jogo_id = $_POST['jogo_id'];
    $conn = conectar();
    
    try {
        // Verifica se ainda há vagas
        $stmt = $conn->prepare("SELECT capacidade FROM jogos WHERE id = ?");
        $stmt->execute([$jogo_id]);
        $jogo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM participantes WHERE jogo_id = ?");
        $stmt->execute([$jogo_id]);
        $participantes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($participantes['total'] < $jogo['capacidade']) {
            $stmt = $conn->prepare("INSERT INTO participantes (jogo_id, usuario_id) VALUES (?, ?)");
            $stmt->execute([$jogo_id, $_SESSION['usuario_id']]);
            $mensagem = "Você foi inscrito no jogo com sucesso!";
        } else {
            $erro = "Desculpe, este jogo já está com todas as vagas preenchidas.";
        }
    } catch(PDOException $e) {
        $erro = "Erro ao participar do jogo.";
    }
}

$proximosJogos = getProximosJogos();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/Frame 1.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="./style/style.css">
    <link rel="shortcut icon" href="assets/Frame 1.png" type="image/x-icon">
    <title>Próximos Jogos - Sportfy</title>
</head>

<body>
    <!-- Navbar -->
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

    <main id="section-jogos" class="section">
        <div class="columns is-desktop">
            <!-- Sidebar do Perfil -->
            <aside class="column is-3">
                    <div class="card">
                        <div class="card-image has-text-centered">
                            <figure class="image is-128x128 is-inline-block">
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
                                <button class="button is-primary is-fullwidth">Meus Compromissos</button>
                                <button class="button is-link is-fullwidth">Criar Evento</button>
                                <a href="editar_perfil.php" class="button is-warning is-fullwidth">Editar Perfil</a>
                            </div>
                        </div>
                    </div>
                </aside>

            <!-- Conteúdo Principal -->
            <div class="column is-9">
                <?php if (isset($mensagem)): ?>
                    <div class="notification is-success">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($erro)): ?>
                    <div class="notification is-danger">
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <div class="box">
                    <h2 class="title is-4">Próximos Jogos</h2>
                    
                    <?php if (empty($proximosJogos)): ?>
                        <p class="has-text-centered">Nenhum jogo disponível no momento.</p>
                    <?php else: ?>
                        <?php foreach ($proximosJogos as $jogo): ?>
                            <div class="box">
                                <div class="content">
                                    <h3 class="title is-5"><?php echo htmlspecialchars($jogo['esporte_nome']); ?></h3>
                                    <p class="subtitle is-6">
                                        <strong>Local:</strong> <?php echo htmlspecialchars($jogo['local']); ?>
                                    </p>
                                    <p class="subtitle is-6">
                                        <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($jogo['data'])); ?> às <?php echo $jogo['horario']; ?>
                                    </p>
                                    <p class="subtitle is-6">
                                        <strong>Capacidade:</strong> <?php echo $jogo['participantes_atuais']; ?>/<?php echo $jogo['capacidade']; ?>
                                    </p>
                                    
                                    <?php if (!usuarioParticipa($jogo['id'])): ?>
                                        <form method="POST">
                                            <input type="hidden" name="jogo_id" value="<?php echo $jogo['id']; ?>">
                                            <div class="buttons is-centered">
                                                <button type="submit" name="participar" class="button is-primary">
                                                    Participar
                                                </button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <p class="has-text-centered has-text-success">
                                            <span class="icon">
                                                <i class="fas fa-check"></i>
                                            </span>
                                            Você já está participando deste jogo
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

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