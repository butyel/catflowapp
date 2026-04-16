<?php
// Debug - mostrar erros
error_log("CATFLOW: Request received - " . $_SERVER['REQUEST_URI']);
error_log("DATABASE_URL: " . (getenv('DATABASE_URL') ? 'set' : 'NOT SET'));

header('Content-Type: application/json');

try {
    // Testar conexão com banco
    $db_url = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');
    
    if (!$db_url) {
        echo json_encode([
            'status' => 'error',
            'message' => 'DATABASE_URL não configurada',
            'vercel' => getenv('VERCEL'),
            'db_url_check' => 'vazio'
        ]);
        exit;
    }
    
    $parts = parse_url($db_url);
    $host = $parts['host'] ?? '';
    $db = ltrim($parts['path'] ?? '/defaultdb', '/');
    $user = $parts['user'] ?? 'root';
    $pass = $parts['pass'] ?? '';
    
    $dsn = "mysql:host=$host;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Selecionar banco
    $pdo->exec("USE $db");
    
    echo json_encode([
        'status' => 'ok',
        'message' => 'CATFLOW API',
        'host' => $host,
        'database' => $db,
        'connected' => true
    ]);
    
} catch (\PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'host' => $host ?? 'unknown'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}