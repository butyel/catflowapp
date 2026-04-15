<?php
require_once __DIR__ . '/base.php';

rateLimit('delete', 10);
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::errorJson('Método não permitido', 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$id = (int)($data['id'] ?? 0);

if (!$id) {
    ApiResponse::errorJson('ID não fornecido', 400);
}

$user_id = get_logged_user_id();
$is_admin = is_admin();

try {
    $stmt = $pdo->prepare("DELETE FROM financeiro WHERE id = ? AND (user_id = ? OR ? = 1)");
    $stmt->execute([$id, $user_id, $is_admin ? 1 : 0]);

    if ($stmt->rowCount() > 0) {
        ApiResponse::successJson(null, 'Registro excluído com sucesso!');
    } else {
        ApiResponse::errorJson('Registro não encontrado ou sem permissão', 403);
    }
} catch (Exception $e) {
    ApiResponse::errorJson('Erro ao excluir registro', 500);
}
