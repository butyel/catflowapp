<?php
// api/list_lembretes.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

$user_id = get_logged_user_id();

try {
    $stmt = $pdo->prepare("SELECT id, titulo, descricao, DATE_FORMAT(data_lembrete, '%d/%m %H:%i') as data_lembrete FROM lembretes WHERE user_id = ? AND status = 'pendente' ORDER BY data_lembrete ASC");
    $stmt->execute([$user_id]);
    $lembretes = $stmt->fetchAll();

    json_response(['success' => true, 'data' => $lembretes]);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao buscar lembretes.'], 500);
}
?>
