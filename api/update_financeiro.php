<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = (int)($data['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
    exit;
}

$descricao = trim($data['descricao'] ?? '');
$valor = (float)($data['valor'] ?? 0);
$categoria = $data['categoria'] ?? 'outros';
$data_reg = $data['data'] ?? date('Y-m-d');
$quantidade = (float)($data['quantidade'] ?? 1);

if (empty($descricao) || $valor <= 0) {
    echo json_encode(['success' => false, 'message' => 'Descrição e valor são obrigatórios']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE financeiro SET descricao = ?, valor = ?, categoria = ?, data = ?, quantidade = ? WHERE id = ? AND (user_id = ? OR ?)");
    $stmt->execute([$descricao, $valor, $categoria, $data_reg, $quantidade, $id, $user_id, is_admin()]);
    
    echo json_encode(['success' => true, 'message' => 'Registro atualizado com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()]);
}
