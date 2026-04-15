<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Método inválido'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$id = (int)($data['id'] ?? 0);

if (!$id) {
    json_response(['success' => false, 'message' => 'ID não fornecido'], 400);
}

try {
    // Fetch original
    $stmt = $pdo->prepare("SELECT * FROM financeiro WHERE id = ? AND (user_id = ? OR ?)");
    $stmt->execute([$id, $user_id, is_admin()]);
    $original = $stmt->fetch();

    if (!$original) {
        json_response(['success' => false, 'message' => 'Registro não encontrado'], 404);
    }

    // Insert copy with current date
    $stmt = $pdo->prepare("INSERT INTO financeiro (user_id, item_id, tipo, descricao, categoria, valor, data, pago, valor_pago, observacao_pagamento) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id,
        $original['item_id'],
        $original['tipo'],
        $original['descricao'] . ' (Cópia)',
        $original['categoria'],
        $original['valor'],
        date('Y-m-d'),
        false, // Copy as not paid
        0,
        ''
    ]);

    json_response(['success' => true, 'message' => 'Registro duplicado com sucesso!']);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao duplicar: ' . $e->getMessage()], 500);
}
