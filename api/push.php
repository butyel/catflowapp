<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

$pdo->exec("CREATE TABLE IF NOT EXISTS push_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    endpoint VARCHAR(500) NOT NULL,
    keys_json TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_endpoint (endpoint(255)),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

$user_id = get_logged_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['action'])) {
        $endpoint = $data['endpoint'] ?? '';
        $keys = $data['keys'] ?? [];
        
        if (empty($endpoint)) {
            json_response(['success' => false, 'message' => 'Endpoint obrigatório'], 400);
        }
        
        try {
            $keys_json = json_encode($keys);
            $stmt = $pdo->prepare("INSERT INTO push_subscriptions (user_id, endpoint, keys_json) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE keys_json = VALUES(keys_json)");
            $stmt->execute([$user_id, $endpoint, $keys_json]);
            
            json_response(['success' => true, 'message' => 'Inscrição salva']);
        } catch (Exception $e) {
            json_response(['success' => false, 'message' => 'Erro ao salvar'], 500);
        }
    }
    
    if ($data['action'] === 'unsubscribe') {
        $endpoint = $data['endpoint'] ?? '';
        
        try {
            $stmt = $pdo->prepare("DELETE FROM push_subscriptions WHERE user_id = ? AND endpoint = ?");
            $stmt->execute([$user_id, $endpoint]);
            
            json_response(['success' => true, 'message' => 'Inscrição removida']);
        } catch (Exception $e) {
            json_response(['success' => false, 'message' => 'Erro ao remover'], 500);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM push_subscriptions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $subscriptions = $stmt->fetchAll();
        
        $subs = array_map(function($s) {
            return [
                'endpoint' => $s['endpoint'],
                'keys' => json_decode($s['keys_json'], true)
            ];
        }, $subscriptions);
        
        json_response(['success' => true, 'data' => $subs]);
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Erro ao buscar'], 500);
    }
}
