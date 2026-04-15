<?php
// api/list_saude.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

$gato_id = isset($_GET['gato_id']) ? (int)$_GET['gato_id'] : 0;
$user_id = get_logged_user_id();

try {
    if ($gato_id) {
        // Specific cat health history
        $stmt = $pdo->prepare("SELECT s.* FROM saude s JOIN gatos g ON s.gato_id = g.id WHERE s.gato_id = ? AND (g.user_id = ? OR ?) ORDER BY s.data_evento DESC");
        $stmt->execute([$gato_id, $user_id, is_admin()]);
    }
    else {
        // Upcoming vaccines/appointments for all user cats
        $stmt = $pdo->prepare("SELECT s.*, g.nome as gato_nome FROM saude s JOIN gatos g ON s.gato_id = g.id WHERE s.proxima_data >= CURRENT_DATE() AND (g.user_id = ? OR ?) ORDER BY s.proxima_data ASC LIMIT 10");
        $stmt->execute([$user_id, is_admin()]);
    }

    json_response(['success' => true, 'data' => $stmt->fetchAll()]);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao buscar registros de saúde.'], 500);
}
?>
