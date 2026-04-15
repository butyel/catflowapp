<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

try {
    $stmt = $pdo->prepare("SELECT * FROM financeiro_itens WHERE user_id = ? ORDER BY nome ASC");
    $stmt->execute([$user_id]);
    $itens = $stmt->fetchAll();

    json_response(['success' => true, 'data' => $itens]);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao buscar itens.'], 500);
}
