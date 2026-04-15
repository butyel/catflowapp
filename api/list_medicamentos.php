<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

$gato_id = (int)($_GET['gato_id'] ?? 0);

try {
    $sql = "SELECT m.*, g.nome as gato_nome FROM medicamentos m JOIN gatos g ON m.gato_id = g.id WHERE (g.user_id = ? OR ?)";
    $params = [$user_id, is_admin()];

    if ($gato_id) {
        $sql .= " AND m.gato_id = ?";
        $params[] = $gato_id;
    }

    $sql .= " ORDER BY m.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $medicamentos = $stmt->fetchAll();

    // Fetch history for each med
    foreach ($medicamentos as &$med) {
        $stmt_hist = $pdo->prepare("SELECT id, data_aplicacao, observacao FROM medicamentos_historico WHERE medicamento_id = ? ORDER BY data_aplicacao DESC LIMIT 10");
        $stmt_hist->execute([$med['id']]);
        $med['historico'] = $stmt_hist->fetchAll();
    }

    json_response(['success' => true, 'data' => $medicamentos]);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao listar medicamentos: ' . $e->getMessage()], 500);
}
