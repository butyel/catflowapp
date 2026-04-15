<?php
// api/create_adocao.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data)
    $data = $_POST;

$gato_id = isset($data['gato_id']) ? (int)$data['gato_id'] : 0;
$adotante_nome = sanitize_input($data['adotante_nome'] ?? '');
$contato = sanitize_input($data['contato'] ?? '');
$data_adocao = sanitize_input($data['data_adocao'] ?? date('Y-m-d'));
$observacoes = sanitize_input($data['observacoes'] ?? '');

if (!$gato_id || empty($adotante_nome) || empty($contato)) {
    json_response(['success' => false, 'message' => 'Preencha os dados do adotante.'], 400);
}

$user_id = get_logged_user_id();
if (!is_admin()) {
    $stmt = $pdo->prepare("SELECT user_id FROM gatos WHERE id = ?");
    $stmt->execute([$gato_id]);
    $gato = $stmt->fetch();
    if (!$gato || $gato['user_id'] != $user_id) {
        json_response(['success' => false, 'message' => 'Acesso negado ao gato especificado.'], 403);
    }
}

try {
    $pdo->beginTransaction();

    // Record adoption
    $stmt = $pdo->prepare("INSERT INTO adocoes (gato_id, adotante_nome, contato, data_adocao, observacoes) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$gato_id, $adotante_nome, $contato, $data_adocao, $observacoes]);

    // Update cat status
    $stmt2 = $pdo->prepare("UPDATE gatos SET status = 'adotado' WHERE id = ?");
    $stmt2->execute([$gato_id]);

    $pdo->commit();
    json_response(['success' => true, 'message' => 'Adoção registrada com sucesso.']);
}
catch (Exception $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'message' => 'Erro ao registrar adoção.'], 500);
}
?>
