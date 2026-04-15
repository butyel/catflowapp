<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

$data = json_decode(file_get_contents('php://input'), true);
$medicamento_id = (int)($data['medicamento_id'] ?? 0);

if (!$medicamento_id) {
    json_response(['success' => false, 'message' => 'ID do medicamento não fornecido'], 400);
}

try {
    // Verify ownership
    $stmt_check = $pdo->prepare("SELECT m.id FROM medicamentos m JOIN gatos g ON m.gato_id = g.id WHERE m.id = ? AND (g.user_id = ? OR ?)");
    $stmt_check->execute([$medicamento_id, $user_id, is_admin()]);
    if (!$stmt_check->fetch()) {
        json_response(['success' => false, 'message' => 'Medicamento não encontrado ou acesso restrito'], 403);
    }

    $stmt = $pdo->prepare("INSERT INTO medicamentos_historico (medicamento_id) VALUES (?)");
    $stmt->execute([$medicamento_id]);

    json_response(['success' => true, 'message' => 'Dose registrada com sucesso!']);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao registrar dose: ' . $e->getMessage()], 500);
}
