<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../src/auth.php";
require_once __DIR__ . "/../src/db.php";
require_once __DIR__ . "/../src/utils.php";

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(["success" => false, "message" => "Não autorizado"], 401);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    json_response(["success" => false, "message" => "Método inválido"], 405);
}

$data = json_decode(file_get_contents("php://input"), true);
$id = (int)($data["id"] ?? 0);
$nome = trim($data["nome"] ?? "");
$preco_unitario = (float)($data["preco_unitario"] ?? 0);
$categoria = $data["categoria"] ?? "outros";
$unidade = trim($data["unidade"] ?? "un");

if (!$id || empty($nome) || $preco_unitario <= 0) {
    json_response(["success" => false, "message" => "Dados incompletos"], 400);
}

try {
    $stmt = $pdo->prepare("UPDATE financeiro_itens SET nome = ?, preco_unitario = ?, categoria = ?, unidade = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$nome, $preco_unitario, $categoria, $unidade, $id, $user_id]);
    
    json_response(["success" => true, "message" => "Item atualizado com sucesso!"]);
} catch (Exception $e) {
    json_response(["success" => false, "message" => "Erro interno ao atualizar: " . $e->getMessage()], 500);
}
