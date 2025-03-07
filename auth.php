<?php
// auth.php

// Inicia a sessão em todas as páginas que incluírem este arquivo
session_start();

// Função para verificar se o usuário está logado
function verificaLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Função para fazer login
function fazerLogin($email, $senha) {
    $conn = conectar(); // Função definida no config.php
    
    try {
        // Busca o usuário pelo email
        $stmt = $conn->prepare("SELECT usuarios_id, nome, email, senha FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verifica se encontrou o usuário e se a senha está correta
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Guarda as informações do usuário na sessão
            $_SESSION['usuario_id'] = $usuario['usuarios_id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            
            return true;
        }
        return false;
    } catch(PDOException $e) {
        error_log("Erro no login: " . $e->getMessage());
        return false;
    }
}

// Função para fazer logout
function fazerLogout() {
    // Destrói todas as variáveis de sessão
    $_SESSION = array();
    
    // Destrói a sessão
    session_destroy();
    
    // Redireciona para a página de login
    header("Location: login.php");
    exit;
}

// Função para verificar se o usuário já está logado
function estaLogado() {
    return isset($_SESSION['usuario_id']);
}

// Função para obter dados do usuário logado
function getUsuarioLogado() {
    if (!estaLogado()) {
        return null;
    }
    
    $conn = conectar();
    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuarios_id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Erro ao buscar usuário: " . $e->getMessage());
        return null;
    }
}

// Restante do código permanece igual...
?>