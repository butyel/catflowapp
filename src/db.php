<?php
// Conexão com banco

// Buscar DATABASE_URL das variáveis de ambiente
$db_url = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? getenv('MYSQL_URL') ?: ($_ENV['MYSQL_URL'] ?? ''));

if ($db_url) {
    $parts = parse_url($db_url);
    $host = $parts['host'] ?? '';
    $port = $parts['port'] ?? '3306';
    $db = ltrim($parts['path'] ?? '/defaultdb', '/');
    $user = $parts['user'] ?? 'root';
    $pass = $parts['pass'] ?? '';
} else {
    // Desenvolvimento local
    $host = 'localhost';
    $port = '3306';
    $db = 'catflow';
    $user = 'root';
    $pass = '';
}

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Ativar SSL se for Aiven ou se solicitado explicitamente na URL
if (strpos($host, 'aivencloud.com') !== false || strpos($db_url, 'ssl-mode=REQUIRED') !== false) {
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false; 
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
}
catch (\PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    error_log('DB Connection Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro de conexao com o banco de dados.']);
    exit;
}