<?php
require_once 'config.php';
require_once 'auth.php';

verificaLogin(); // Garante que o usuário está logado

$pdo = conectar();

// Pega o ID do evento
$evento_id = $_GET['evento_id'];


// Busca dados do usuário
$conn = conectar();
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuarios_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
$evento_id = $_GET['evento_id'];
$pdo = conectar();

// Busca os participantes do evento
$sql = "
    SELECT u.usuarios_id, u.nome, u.data_nascimento, u.sexo
    FROM participacoes p
    JOIN usuarios u ON p.usuario_id = u.usuarios_id
    WHERE p.evento_id = :evento_id
";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':evento_id', $evento_id, PDO::PARAM_INT);
$stmt->execute();
$participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<section class="section">
    <div class="container">
        <h1 class="title has-text-centered">Participantes do Evento</h1>
        
        <?php if (count($participantes) > 0): ?>
            <table class="table is-striped is-bordered is-hoverable is-fullwidth">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Idade</th>
                        <th>Sexo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participantes as $participante): ?>
                        <?php
                        // Calculando a idade
                        $data_nascimento = new DateTime($participante['data_nascimento']);
                        $data_atual = new DateTime();
                        $idade = $data_atual->diff($data_nascimento)->y;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($participante['nome']) ?></td>
                            <td><?= $idade ?> anos</td>
                            <td><?= ucfirst(htmlspecialchars($participante['sexo'])) ?></td>
                            <td>
                                <form method="POST" action="expulsar_participante.php" style="display:inline;">
                                    <input type="hidden" name="evento_id" value="<?= $evento_id ?>">
                                    <input type="hidden" name="usuario_id" value="<?= $participante['usuarios_id'] ?>">
                                    <button type="submit" class="button is-danger" onclick="return confirm('Tem certeza que deseja expulsar este participante?')">Expulsar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="has-text-centered">Nenhum participante encontrado.</p>
        <?php endif; ?>

    </div>
</section>

</body>
</html>
