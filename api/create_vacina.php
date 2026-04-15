<?php
// api/create_vacina.php
// Also used for other health events like consulta, cirurgia, exame
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
$tipo = sanitize_input($data['tipo'] ?? 'vacina');
$descricao = sanitize_input($data['descricao'] ?? '');
$data_evento = sanitize_input($data['data_evento'] ?? '');
$proxima_data = !empty($data['proxima_data']) ? sanitize_input($data['proxima_data']) : null;

if (!$gato_id || empty($tipo) || empty($descricao) || empty($data_evento)) {
    json_response(['success' => false, 'message' => 'Dados incompletos.'], 400);
}

// Security: Verify ownership
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
    $stmt = $pdo->prepare("INSERT INTO saude (gato_id, tipo, descricao, data_evento, proxima_data) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$gato_id, $tipo, $descricao, $data_evento, $proxima_data]);
    json_response(['success' => true, 'message' => 'Registro de saúde adicionado com sucesso.']);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao adicionar registro de saúde.'], 500);
}
?>
