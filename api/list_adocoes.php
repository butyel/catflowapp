<?php
// api/list_adocoes.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

$user_id = get_logged_user_id();

try {
    if (is_admin()) {
        $stmt = $pdo->query("SELECT a.*, g.nome as gato_nome, g.foto as gato_foto, u.nome as tutor_nome FROM adocoes a JOIN gatos g ON a.gato_id = g.id JOIN users u ON g.user_id = u.id ORDER BY a.data_adocao DESC");
    }
    else {
        $stmt = $pdo->prepare("SELECT a.*, g.nome as gato_nome, g.foto as gato_foto FROM adocoes a JOIN gatos g ON a.gato_id = g.id WHERE g.user_id = ? ORDER BY a.data_adocao DESC");
        $stmt->execute([$user_id]);
    }

    json_response(['success' => true, 'data' => $stmt->fetchAll()]);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao buscar adoções.'], 500);
}
?>
