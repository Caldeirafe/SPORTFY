<?php
require_once 'config.php';
require_once 'auth.php';

verificaLogin(); // Garante que o usuário está logado

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['evento_id'])) {
    $evento_id = $_POST['evento_id'];
    $usuario_id = $_SESSION['usuario_id'];

    // Conectar ao banco de dados
    $pdo = conectar();

    // Verifica se o evento pertence ao usuário logado
    $stmt = $pdo->prepare("SELECT * FROM eventos WHERE evento_id = ? AND usuario_id = ?");
    $stmt->execute([$evento_id, $usuario_id]);
    $evento = $stmt->fetch();

    if ($evento) {
        // Deleta o evento
        $stmt = $pdo->prepare("DELETE FROM eventos WHERE evento_id = ?");
        $stmt->execute([$evento_id]);

        // Redireciona após a exclusão
        header("Location: perfil.php?deletado=sucesso");
        exit;
    } else {
        die("Ação não permitida.");
    }
} else {
    die("Requisição inválida.");
}
