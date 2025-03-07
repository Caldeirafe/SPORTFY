<?php
require_once 'config.php';
require_once 'auth.php';

// Verifica se está logado
verificaLogin();

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Busca os dados do usuário
    $user_id = $_SESSION['usuario_id'];
    $conn = conectar();

    // Coleta os dados do formulário
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];

    // Verifica se há uma nova foto de perfil
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = $_FILES['foto'];
        $foto_nome = $foto['name'];
        $foto_tmp = $foto['tmp_name'];
        $foto_ext = strtolower(pathinfo($foto_nome, PATHINFO_EXTENSION));

        // Valida a extensão do arquivo
        $extensoes_permitidas = ['jpg', 'jpeg', 'png'];
        if (in_array($foto_ext, $extensoes_permitidas)) {
            // Define o nome e o caminho da foto
            $novo_nome_foto = 'perfil_' . $user_id . '.' . $foto_ext;
            $caminho_foto = 'uploads/' . $novo_nome_foto;

            // Move a foto para o diretório desejado
            move_uploaded_file($foto_tmp, $caminho_foto);
        } else {
            // Caso a foto não tenha a extensão permitida
            header("Location: editar_perfil.php?erro=Formato de foto não permitido. Apenas JPG e PNG são aceitos.");
            exit;
        }
    } else {
        // Caso não haja foto nova, mantém a foto atual
        $novo_nome_foto = $_POST['foto_atual'];
    }

    // Atualiza as informações no banco de dados
    try {
        $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, telefone = ?, cidade = ?, estado = ?, foto = ? WHERE usuarios_id = ?");
        $stmt->execute([$nome, $telefone, $cidade, $estado, $novo_nome_foto, $user_id]);

        // Redireciona para a página de perfil com mensagem de sucesso
        header("Location: perfil.php?atualizado=sucesso");
        exit;
    } catch (PDOException $e) {
        // Em caso de erro, redireciona com a mensagem de erro
        header("Location: editar_perfil.php?erro=Erro ao atualizar perfil: " . $e->getMessage());
        exit;
    }
}
?>
