<?php
session_start();
require_once 'config.php';
$pdo = conectar();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $evento_id = $_POST['evento_id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Verifica se o usuário já está inscrito
    $stmt = $pdo->prepare("SELECT * FROM participacoes WHERE evento_id = ? AND usuario_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);
    if ($stmt->fetch()) {
        echo "Você já está inscrito neste evento.";
        exit;
    }

    // Verifica se o evento ainda tem vagas disponíveis, contando o criador como participante
    $stmt = $pdo->prepare("SELECT evento_max_pessoas, usuario_id AS criador_id, 
                        (SELECT COUNT(*) FROM participacoes WHERE evento_id = ?) + 1 AS inscritos 
                        FROM eventos WHERE evento_id = ?");
    $stmt->execute([$evento_id, $evento_id]);
    $evento = $stmt->fetch();

    // Verifica se o evento já está lotado
    if ($evento['inscritos'] >= $evento['evento_max_pessoas']) {
        echo "Este evento já está lotado.";
        exit;
    }

    // Inscreve o usuário no evento
    $stmt = $pdo->prepare("INSERT INTO participacoes (usuario_id, evento_id) VALUES (?, ?)");
    $stmt->execute([$usuario_id, $evento_id]);

    // Atualiza o contador de inscritos na tabela eventos
    $stmt = $pdo->prepare("UPDATE eventos SET inscritos = inscritos + 1 WHERE evento_id = ?");
    $stmt->execute([$evento_id]);

    // Redireciona para a página principal após a inscrição
    header('Location: index.php');
    exit;
}
?>
