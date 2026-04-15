<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

$user_id = get_logged_user_id();
$is_admin = is_admin();

if (!$is_admin) {
    json_response(['success' => false, 'message' => 'Acesso restrito a administradores'], 403);
}

$config = require __DIR__ . '/../config.php';
$db_name = $config['db_name'];

$backup_dir = __DIR__ . '/../backups';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

$timestamp = date('Y-m-d_H-i-s');
$filename = "catflow_backup_{$timestamp}.sql";
$filepath = $backup_dir . '/' . $filename;

$command = "mysqldump -h{$config['db_host']} -u{$config['db_user']}" . 
    ($config['db_pass'] ? " -p{$config['db_pass']}" : '') . 
    " --single-transaction --quick --lock-tables=false {$db_name} > \"{$filepath}\"";

exec($command . " 2>&1", $output, $return_code);

if ($return_code !== 0 || !file_exists($filepath)) {
    json_response(['success' => false, 'message' => 'Erro ao criar backup', 'details' => implode("\n", $output)], 500);
}

$files = glob($backup_dir . '/catflow_backup_*.sql');
usort($files, function($a, $b) { return filemtime($b) - filemtime($a); });

$max_backups = 10;
while (count($files) > $max_backups) {
    $oldest = array_pop($files);
    @unlink($oldest);
}

$file_size = filesize($filepath);
$created_at = date('d/m/Y H:i', filemtime($filepath));

json_response([
    'success' => true,
    'message' => 'Backup criado com sucesso',
    'backup' => [
        'filename' => $filename,
        'size' => $file_size,
        'created_at' => $created_at
    ],
    'total_backups' => count($files)
]);