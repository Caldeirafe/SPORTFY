<?php
require_once 'config.php'; // Conexão com o banco de dados
require_once 'auth.php';   // Autenticação do usuário

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        die('Usuário não autenticado.');
    }
    
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

        // Prepara a SQL de inserção
        $sql = "INSERT INTO eventos (usuario_id, evento_nome, evento_data, evento_hora, evento_max_pessoas, evento_local, evento_esporte, evento_descricao) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Vincula os parâmetros
        $stmt->bindValue(1, $usuario_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $evento_nome, PDO::PARAM_STR);
        $stmt->bindValue(3, $evento_data, PDO::PARAM_STR);
        $stmt->bindValue(4, $evento_hora, PDO::PARAM_STR);
        $stmt->bindValue(5, $evento_max_pessoas, PDO::PARAM_INT);
        $stmt->bindValue(6, $evento_local, PDO::PARAM_STR);
        $stmt->bindValue(7, $evento_esporte, PDO::PARAM_STR);
        $stmt->bindValue(8, $evento_descricao, PDO::PARAM_STR);

        // Executa a consulta
         // Executa a consulta
        if ($stmt->execute()) {
            // Exibe o alerta e redireciona após 2 segundos
            echo "<script>alert('Evento criado com sucesso!'); window.location.href = 'perfil.php';</script>";
            exit();
        } else {
            echo "Erro ao criar evento: " . implode(", ", $stmt->errorInfo());
        }
        
        $stmt->close();
    } catch (PDOException $e) {
        echo "Erro ao criar evento: " . $e->getMessage();
    }
} else {
    echo "Método de requisição inválido.";
}
?>
