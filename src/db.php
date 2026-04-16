<?php
// Conexão com banco

// Buscar DATABASE_URL das variáveis de ambiente
$db_url = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');

if ($db_url) {
    $parts = parse_url($db_url);
    $host = $parts['host'] ?? '';
    $db = ltrim($parts['path'] ?? '/defaultdb', '/');
    $user = $parts['user'] ?? 'root';
    $pass = $parts['pass'] ?? '';
} else {
    // Desenvolvimento local
    $host = 'localhost';
    $db = 'catflow';
    $user = 'root';
    $pass = '';
}

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
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
    echo json_encode(['success' => false, 'message' => 'Erro BD: ' . $e->getMessage()]);
    exit;
}