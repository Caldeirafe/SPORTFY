<?php
require_once 'config.php';
require_once 'auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        die('Usuário não autenticado.');
    }
    
    $evento_id = $_POST['evento_id'];
    $usuario_id = $_SESSION['usuario_id'];
    $evento_nome = $_POST['evento_nome'];
    $evento_data = $_POST['evento_data'];
    $evento_hora = $_POST['evento_hora'];
    $evento_max_pessoas = $_POST['evento_max_pessoas'];
    $evento_local = $_POST['evento_local'];
    $evento_esporte = $_POST['evento_esporte'];
    $evento_descricao = $_POST['evento_descricao'];

    try {
        // Estabelece a conexão com o banco de dados
        $conn = conectar();

        // Prepara a SQL de atualização
        $sql = "UPDATE eventos 
                SET evento_nome = ?, 
                    evento_data = ?, 
                    evento_hora = ?, 
                    evento_max_pessoas = ?, 
                    evento_local = ?, 
                    evento_esporte = ?, 
                    evento_descricao = ? 
                WHERE evento_id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($sql);

        // Vincula os parâmetros
        $stmt->bindValue(1, $evento_nome, PDO::PARAM_STR);
        $stmt->bindValue(2, $evento_data, PDO::PARAM_STR);
        $stmt->bindValue(3, $evento_hora, PDO::PARAM_STR);
        $stmt->bindValue(4, $evento_max_pessoas, PDO::PARAM_INT);
        $stmt->bindValue(5, $evento_local, PDO::PARAM_STR);
        $stmt->bindValue(6, $evento_esporte, PDO::PARAM_STR);
        $stmt->bindValue(7, $evento_descricao, PDO::PARAM_STR);
        $stmt->bindValue(8, $evento_id, PDO::PARAM_INT);
        $stmt->bindValue(9, $usuario_id, PDO::PARAM_INT);

        // Executa a consulta
        if ($stmt->execute()) {
            // Verifica se algum registro foi atualizado
            if ($stmt->rowCount() > 0) {
                // Exibe o alerta e redireciona após 2 segundos
                echo "<script>alert('Evento atualizado com sucesso!'); window.location.href = 'perfil.php';</script>";
                exit();
            } else {
                echo "Nenhum evento encontrado ou você não tem permissão para editar este evento.";
            }
        } else {
            echo "Erro ao atualizar evento: " . implode(", ", $stmt->errorInfo());
        }
        
    } catch (PDOException $e) {
        echo "Erro ao atualizar evento: " . $e->getMessage();
    }
} else {
    echo "Método de requisição inválido.";
}
?>