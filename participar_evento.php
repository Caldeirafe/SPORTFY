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
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participacoes WHERE evento_id = ? AND usuario_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "Você já está inscrito neste evento.";
        exit;
    }

    // Busca informações sobre o evento
    $stmt = $pdo->prepare("
        SELECT evento_max_pessoas, 
            (SELECT COUNT(*) FROM participacoes WHERE evento_id = eventos.evento_id) AS inscritos 
        FROM eventos 
        WHERE evento_id = ?
    ");
    $stmt->execute([$evento_id]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o evento foi encontrado
    if (!$evento) {
        echo "Evento não encontrado.";
        exit;
    }

    // Verifica se o evento já está lotado
    if ($evento['inscritos'] >= $evento['evento_max_pessoas']) {
        echo "Este evento já está lotado.";
        exit;
    }

    // Inscreve o usuário no evento
    $stmt = $pdo->prepare("INSERT INTO participacoes (usuario_id, evento_id) VALUES (?, ?)");
    $stmt->execute([$usuario_id, $evento_id]);

    // Atualiza qtd. participantes
    $stmt = $pdo->prepare("update eventos set inscritos = ? where evento_id = ?");
    $stmt->execute([$evento['inscritos']+1, $evento_id]);

    // Redireciona para a página principal após a inscrição
    header('Location: daoplay.php?status=participou');
    exit;
}
?>
