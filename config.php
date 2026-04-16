<?php
// config.php
// Configurações do CATFLOW

// Detectar ambiente Vercel
$is_vercel = getenv('VERCEL') === '1';
$base_path = $is_vercel ? '/var/task/user' : __DIR__;

// DATABASE_URL tem prioridade
$database_url = getenv('DATABASE_URL');
if ($database_url) {
    $db_url = parse_url($database_url);
    return [
        'db_host' => $db_url['host'] ?? '',
        'db_name' => ltrim($db_url['path'] ?? '/defaultdb', '/'),
        'db_user' => $db_url['user'] ?? '',
        'db_pass' => $db_url['pass'] ?? '',
        'db_charset' => 'utf8mb4',
        'app_name' => 'CATFLOW',
        'app_url' => 'https://' . (getenv('VERCEL_URL') ?? 'catflow.vercel.app')
    ];
}

// Variáveis separadas (Vercel)
if ($is_vercel && getenv('DB_HOST')) {
    return [
        'db_host' => getenv('DB_HOST'),
        'db_name' => getenv('DB_NAME') ?: 'defaultdb',
        'db_user' => getenv('DB_USER'),
        'db_pass' => getenv('DB_PASS'),
        'db_charset' => 'utf8mb4',
        'app_name' => 'CATFLOW',
        'app_url' => 'https://' . (getenv('VERCEL_URL') ?? 'catflow.vercel.app')
    ];
}

// Desenvolvimento local
return [
    'db_host' => 'localhost',
    'db_name' => 'catflow',
    'db_user' => 'root',
    'db_pass' => '',
    'db_charset' => 'utf8mb4',
    'app_name' => 'CATFLOW',
    'app_url' => 'http://localhost'
];