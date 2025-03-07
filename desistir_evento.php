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

    // Verifica se o usuário está inscrito no evento
    $stmt = $pdo->prepare("SELECT * FROM participacoes WHERE evento_id = ? AND usuario_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);

    if ($stmt->fetch()) {
        // Remove a participação do evento
        $stmt = $pdo->prepare("DELETE FROM participacoes WHERE evento_id = ? AND usuario_id = ?");
        $stmt->execute([$evento_id, $usuario_id]);

        // Redireciona após a desistência
        header('Location: perfil.php?desistiu=sucesso');
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
