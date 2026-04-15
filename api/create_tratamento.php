<?php
// api/create_tratamento.php
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
$nome_medicamento = sanitize_input($data['nome_medicamento'] ?? '');
$dosagem = sanitize_input($data['dosagem'] ?? '');
$horario = sanitize_input($data['horario'] ?? '');
$duracao_dias = isset($data['duracao_dias']) ? (int)$data['duracao_dias'] : 0;

if (!$gato_id || empty($nome_medicamento) || empty($dosagem) || empty($horario) || empty($duracao_dias)) {
    json_response(['success' => false, 'message' => 'Todos os campos do tratamento são obrigatórios.'], 400);
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
    $stmt = $pdo->prepare("INSERT INTO medicamentos (gato_id, nome_medicamento, dosagem, horario, duracao_dias, status) VALUES (?, ?, ?, ?, ?, 'ativo')");
    $stmt->execute([$gato_id, $nome_medicamento, $dosagem, $horario, $duracao_dias]);
    json_response(['success' => true, 'message' => 'Tratamento adicionado com sucesso.']);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao adicionar tratamento.'], 500);
}
?>
