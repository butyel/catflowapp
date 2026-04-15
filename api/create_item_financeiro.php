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
if (!$data) $data = $_POST;

$nome = trim($data['nome'] ?? '');
$preco_unitario = (float)($data['preco_unitario'] ?? 0);
$categoria = $data['categoria'] ?? 'outros';
$unidade = trim($data['unidade'] ?? 'un');

if (empty($nome) || $preco_unitario <= 0) {
    echo json_encode(['success' => false, 'message' => 'Nome e preço unitário são obrigatórios']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO financeiro_itens (user_id, nome, preco_unitario, categoria, unidade) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $nome, $preco_unitario, $categoria, $unidade]);
    
    echo json_encode(['success' => true, 'message' => 'Item cadastrado com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar item: ' . $e->getMessage()]);
}
