<?php
// api/monitoring.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

$action = $_GET['action'] ?? 'list';

try {
    if ($action === 'list') {
        $gato_id = (int)($_GET['gato_id'] ?? 0);
        if (!$gato_id)
            json_response(['success' => false, 'message' => 'ID do gato obrigatório'], 400);

        $stmt = $pdo->prepare("SELECT * FROM monitoramento_diario WHERE gato_id = ? ORDER BY data DESC, created_at DESC LIMIT 30");
        $stmt->execute([$gato_id]);
        json_response(['success' => true, 'data' => $stmt->fetchAll()]);
    }
    elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $gato_id = (int)($data['gato_id'] ?? 0);
        $comportamento = sanitize_input($data['comportamento'] ?? '');
        $caixa_areia = sanitize_input($data['caixa_areia'] ?? '');
        $apetite = sanitize_input($data['apetite'] ?? '');
        $energia = sanitize_input($data['energia'] ?? '');
        $data_log = $data['data'] ?? date('Y-m-d');
        $observacoes = sanitize_input($data['observacoes'] ?? '');

        if (!$gato_id)
            json_response(['success' => false, 'message' => 'ID do gato obrigatório'], 400);

        $stmt = $pdo->prepare("INSERT INTO monitoramento_diario (gato_id, comportamento, caixa_areia, apetite, energia, data, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$gato_id, $comportamento, $caixa_areia, $apetite, $energia, $data_log, $observacoes]);

        json_response(['success' => true, 'message' => 'Monitoramento registrado!', 'id' => $pdo->lastInsertId()]);
    }
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()], 500);
}
