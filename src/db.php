<?php
// src/db.php
// Conexão com o banco de dados via PDO

// Detectar ambiente Vercel
$is_vercel = getenv('VERCEL') === '1';
$base_path = $is_vercel ? '/var/task/user' : __DIR__ . '/..';

// Carrega as configurações
$config_file = $base_path . '/config.php';
if (!file_exists($config_file)) {
    $config_file = __DIR__ . '/../config.php';
}
$config = require $config_file;

$host = $config['db_host'];
$db = $config['db_name'];
$user = $config['db_user'];
$pass = $config['db_pass'];
$charset = $config['db_charset'] ?? 'utf8mb4';

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
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro conexao BD: ' . $e->getMessage()]);
    exit;
}