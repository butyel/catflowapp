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

$data = json_decode(file_get_contents('php://input'), true);

$title = $data['title'] ?? 'CATFLOW';
$body = $data['body'] ?? '';
$target = $data['target'] ?? 'all';
$resource_type = $data['resource_type'] ?? null;
$resource_id = $data['resource_id'] ?? null;

if (empty($body)) {
    json_response(['success' => false, 'message' => 'Mensagem obrigatória'], 400);
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS push_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        body TEXT NOT NULL,
        resource_type VARCHAR(50),
        resource_id INT,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    $resource_type = $data['resource_type'] ?? null;
    $resource_id = $data['resource_id'] ?? null;
    
    $stmt = $pdo->prepare("INSERT INTO push_notifications (user_id, title, body, resource_type, resource_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $body, $resource_type, $resource_id]);
    
    $notification_id = $pdo->lastInsertId();
    
    $stmt = $pdo->query("SELECT endpoint, keys_json FROM push_subscriptions");
    $subscriptions = $stmt->fetchAll();
    
    $sent_count = 0;
    foreach ($subscriptions as $sub) {
        $sent_count++;
    }
    
    json_response([
        'success' => true,
        'message' => 'Notificação enviada',
        'notification_id' => $notification_id,
        'recipients' => $sent_count
    ]);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao enviar: ' . $e->getMessage()], 500);
}