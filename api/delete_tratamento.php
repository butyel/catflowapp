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
    // Check ownership via the cat
    $stmt_check = $pdo->prepare("SELECT m.id FROM medicamentos m JOIN gatos g ON m.gato_id = g.id WHERE m.id = ? AND (g.user_id = ? OR ?)");
    $stmt_check->execute([$id, $user_id, is_admin()]);
    if (!$stmt_check->fetch()) {
        json_response(['success' => false, 'message' => 'Acesso negado'], 403);
    }

    $stmt = $pdo->prepare("DELETE FROM medicamentos WHERE id = ?");
    $stmt->execute([$id]);

    json_response(['success' => true, 'message' => 'Tratamento excluído com sucesso!']);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao excluir: ' . $e->getMessage()], 500);
}
