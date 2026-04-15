<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Método inválido'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$id = (int)($data['id'] ?? 0);

if (!$id) {
    json_response(['success' => false, 'message' => 'ID não fornecido'], 400);
}

try {
    $stmt = $pdo->prepare("DELETE FROM financeiro_itens WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);

    json_response(['success' => true, 'message' => 'Item excluído com sucesso!']);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro interno ao excluir: ' . $e->getMessage()], 500);
}
