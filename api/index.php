<?php
// Ocultar erros em produção — nunca expor detalhes internos
if (getenv('APP_ENV') === 'development' || getenv('VERCEL_ENV') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}
// Router para CATFLOW no Vercel
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: text/html; charset=utf-8');

$path = $_SERVER['REQUEST_URI'] ?? '/';
$path = trim(parse_url($path, PHP_URL_PATH), '/');

if (empty($path) || $path === '' || $path === '/') {
    include __DIR__ . '/../index.php';
    exit;
}

// Se for .php, incluir
if (strpos($path, '.php') !== false) {
    $file = __DIR__ . '/../' . $path;
    if (file_exists($file)) {
        include $file;
        exit;
    }
}

// 404 — sem expor o caminho da requisição
http_response_code(404);
echo "<h1>404 - Página não encontrada</h1>";