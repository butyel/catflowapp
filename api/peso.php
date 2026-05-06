<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Nao autorizado'], 401);
}

$action = $_GET['action'] ?? 'list';

try {
    if ($action === 'list') {
        $gato_id = (int)($_GET['gato_id'] ?? 0);
        if (!$gato_id)
            json_response(['success' => false, 'message' => 'ID do gato obrigatorio'], 400);

        verify_gato_ownership($pdo, $gato_id, $user_id);

        $stmt = $pdo->prepare("SELECT * FROM peso_historico WHERE gato_id = ? ORDER BY data DESC");
        $stmt->execute([$gato_id]);
        json_response(['success' => true, 'data' => $stmt->fetchAll()]);
    }
    elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_csrf();
        $data = json_decode(file_get_contents('php://input'), true);
        $gato_id = (int)($data['gato_id'] ?? 0);
        $peso = (float)($data['peso'] ?? 0);
        $data_reg = $data['data'] ?? date('Y-m-d');

        if (!$gato_id || $peso <= 0)
            json_response(['success' => false, 'message' => 'ID do gato e peso obrigatorios'], 400);

        verify_gato_ownership($pdo, $gato_id, $user_id);

        $stmt = $pdo->prepare("INSERT INTO peso_historico (gato_id, peso, data) VALUES (?, ?, ?)");
        $stmt->execute([$gato_id, $peso, $data_reg]);
        json_response(['success' => true, 'message' => 'Peso registrado!', 'id' => $pdo->lastInsertId()]);
    }
}
catch (Exception $e) {
    error_log('Peso API error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Erro interno do servidor.'], 500);
}
