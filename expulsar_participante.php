<?php
require_once 'config.php';
require_once 'auth.php';

verificaLogin(); // Garante que o usuário está logado

$pdo = conectar();

// Verifica se os parâmetros foram passados
if (isset($_POST['evento_id']) && isset($_POST['usuario_id'])) {
    $evento_id = $_POST['evento_id'];
    $usuario_id = $_POST['usuario_id'];

    // Remove a participação do usuário do evento
    $stmt = $pdo->prepare("DELETE FROM participacoes WHERE evento_id = :evento_id AND usuario_id = :usuario_id");
    $stmt->bindParam(':evento_id', $evento_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redireciona de volta para a página de participantes
    header("Location: ver_participantes.php?evento_id=$evento_id");
    exit();
} else {
    echo "Parâmetros inválidos.";
}
?>
