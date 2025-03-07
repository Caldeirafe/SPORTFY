<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sportfy');

// Conectar ao banco de dados
function conectar() {
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        echo "Erro na conexão: " . $e->getMessage();
        exit;
    }
}

// Configurações gerais do Sportify
define('APP_NAME', 'Sportify');
define('APP_URL', 'http://localhost/sportify');
define('APP_EMAIL', 'contato@sportfy.com');
?>