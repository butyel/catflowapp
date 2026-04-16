<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');

$path = $_SERVER['REQUEST_URI'] ?? '/';
$path = str_replace('/api/', '', $path);
$path = trim($path, '/');

if (empty($path) || $path === 'index') {
    json_response([
        'name' => 'CATFLOW API',
        'version' => '1.0.0',
        'status' => 'online'
    ]);
}

json_response(['error' => 'Endpoint não encontrado'], 404);