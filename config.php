<?php
// config.php - Configurações do CATFLOW

// Verificar se tem DATABASE_URL
$db_url = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? '');

if ($db_url) {
    $parts = parse_url($db_url);
    return [
        'db_host' => $parts['host'] ?? 'localhost',
        'db_name' => ltrim($parts['path'] ?? '/defaultdb', '/'),
        'db_user' => $parts['user'] ?? 'root',
        'db_pass' => $parts['pass'] ?? '',
        'db_charset' => 'utf8mb4',
        'app_name' => 'CATFLOW',
        'app_url' => 'https://' . (getenv('VERCEL_URL') ?? 'catflow.vercel.app')
    ];
}

// Vercel com variáveis separadas
if (getenv('DB_HOST')) {
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

// Local
return [
    'db_host' => 'localhost',
    'db_name' => 'catflow',
    'db_user' => 'root',
    'db_pass' => '',
    'db_charset' => 'utf8mb4',
    'app_name' => 'CATFLOW',
    'app_url' => 'http://localhost'
];