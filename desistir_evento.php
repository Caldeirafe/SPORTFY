<?php
session_start();
require_once 'config.php';
$pdo = conectar();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento_id'])) {
    $evento_id = $_POST['evento_id'];
    $usuario_id = $_SESSION['usuario_id'];

     // Busca informações sobre o evento
     $stmt = $pdo->prepare("
     SELECT evento_max_pessoas, 
         (SELECT COUNT(*) FROM participacoes WHERE evento_id = eventos.evento_id) AS inscritos 
     FROM eventos 
     WHERE evento_id = ?
    ");
    $stmt->execute([$evento_id]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário está inscrito no evento
    $stmt = $pdo->prepare("SELECT * FROM participacoes WHERE evento_id = ? AND usuario_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);

    if ($stmt->fetch()) {
        // Remove a participação do evento
        $stmt = $pdo->prepare("DELETE FROM participacoes WHERE evento_id = ? AND usuario_id = ?");
        $stmt->execute([$evento_id, $usuario_id]);

        // Atualiza qtd. participantes
        $stmt = $pdo->prepare("update eventos set inscritos = ? where evento_id = ?");
        $stmt->execute([$evento['inscritos']-1, $evento_id]);

        // Redireciona após a desistência
        header('Location: daoplay.php?status=desistiu');
        exit;
    } else {
        echo "Você não está inscrito neste evento.";
        exit;
    }
} else {
    echo "Requisição inválida.";
    exit;
}
?>