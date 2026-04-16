<?php
// Router para CATFLOW no Vercel
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: text/html; charset=utf-8');

$path = $_SERVER['REQUEST_URI'] ?? '/';
$path = trim(parse_url($path, PHP_URL_PATH), '/');

// Debug - mostrar se é / ou caminho vazio
if (empty($path) || $path === '' || $path === '/') {
    // Redirecionar para index.php
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

// 404
http_response_code(404);
echo "<h1>404 - Página não encontrada</h1>";
echo "<p>Rota: $path</p>";