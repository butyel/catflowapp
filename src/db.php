<?php
// src/db.php
// Conexão com o banco de dados via PDO

// Carrega as configurações do arquivo raiz
$config = require __DIR__ . '/../config.php';

$host = $config['db_host'];
$db = $config['db_name'];
$user = $config['db_user'];
$pass = $config['db_pass'];
$charset = $config['db_charset'];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
}
catch (\PDOException $e) {
    // Retornando erro explícito em JSON para que o front-end mostre o real problema
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro conexao BD: ' . $e->getMessage()]);
    exit;
}
?>
