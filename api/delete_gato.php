<?php
require_once __DIR__ . '/base.php';

rateLimit('delete', 10);
requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::errorJson('Método não permitido', 405);
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$gato_id = isset($data['id']) ? (int)$data['id'] : 0;

if (!$gato_id) {
    ApiResponse::errorJson('ID requerido', 400);
}

$user_id = get_logged_user_id();
$is_admin = is_admin();

try {
    $stmt = $pdo->prepare("SELECT user_id, foto FROM gatos WHERE id = ?");
    $stmt->execute([$gato_id]);
    $gato = $stmt->fetch();

    if (!$gato) {
        ApiResponse::errorJson('Gato não encontrado', 404);
    }

    if (!$is_admin && $gato['user_id'] != $user_id) {
        ApiResponse::errorJson('Acesso negado', 403);
    }

    if ($gato['foto'] && file_exists(__DIR__ . '/../' . $gato['foto'])) {
        unlink(__DIR__ . '/../' . $gato['foto']);
    }

    $del_stmt = $pdo->prepare("DELETE FROM gatos WHERE id = ?");
    $del_stmt->execute([$gato_id]);

    ApiResponse::successJson(null, 'Gato removido com sucesso');
} catch (Exception $e) {
    ApiResponse::errorJson('Erro ao remover gato', 500);
}
