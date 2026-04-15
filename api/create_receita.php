<?php
require_once __DIR__ . '/base.php';

rateLimit('create_financeiro', 30);
requireAuth();

$user_id = get_logged_user_id();
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    ApiResponse::errorJson('Dados inválidos ou ausentes', 400);
}

$tipo = $data['tipo'] ?? 'receita';
if (!in_array($tipo, ['receita', 'despesa'])) {
    ApiResponse::errorJson('Tipo inválido', 400);
}

$descricao = trim($data['descricao'] ?? '');
if (empty($descricao)) {
    ApiResponse::errorJson('Descrição é obrigatória', 400);
}

if (strlen($descricao) > 255) {
    ApiResponse::errorJson('Descrição muito longa (máximo 255 caracteres)', 400);
}

$categoria = $data['categoria'] ?? 'outros';
$valid_categorias = ['racao', 'veterinario', 'areia', 'medicamentos', 'doacao', 'outros'];
if (!in_array($categoria, $valid_categorias)) {
    ApiResponse::errorJson('Categoria inválida', 400);
}

$valor = !empty($data['valor']) ? (float)$data['valor'] : 0;
if ($valor <= 0) {
    ApiResponse::errorJson('Valor deve ser maior que zero', 400);
}

if ($valor > 999999.99) {
    ApiResponse::errorJson('Valor muito alto', 400);
}

$data_registro = $data['data'] ?? date('Y-m-d');
if (!Security::validateDate($data_registro)) {
    ApiResponse::errorJson('Data inválida', 400);
}

$quantidade = !empty($data['quantidade']) ? (float)$data['quantidade'] : 1;
$item_id = !empty($data['item_id']) ? (int)$data['item_id'] : null;

try {
    $stmt = $pdo->prepare("INSERT INTO financeiro (user_id, tipo, descricao, categoria, valor, data, quantidade, item_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $tipo, $descricao, $categoria, $valor, $data_registro, $quantidade, $item_id]);
    
    ApiResponse::successJson(['id' => $pdo->lastInsertId()], 'Registro financeiro salvo com sucesso!');
} catch (Exception $e) {
    ApiResponse::errorJson('Erro interno ao salvar', 500);
}
