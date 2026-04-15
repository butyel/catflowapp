<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../src/auth.php";
require_once __DIR__ . "/../src/db.php";
require_once __DIR__ . "/../src/utils.php";

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(["success" => false, "message" => "Não autorizado"], 401);
}

$action = $_GET["action"] ?? "";

try {
    switch ($action) {
        case "list":
            $stmt = $pdo->prepare("SELECT * FROM estoque WHERE user_id = ? ORDER BY nome_item ASC");
            $stmt->execute([$user_id]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(["success" => true, "data" => $items]);
            break;

        case "upsert":
            if ($_SERVER["REQUEST_METHOD"] !== "POST") json_response(["success" => false, "message" => "Método inválido"], 405);
            $data = json_decode(file_get_contents("php://input"), true);
            $id = (int)($data["id"] ?? 0);
            $nome = trim($data["nome_item"] ?? "");
            $categoria = $data["categoria"] ?? "outro";
            $unidade = trim($data["unidade"] ?? "un");
            $minimo = (float)($data["estoque_minimo"] ?? 0);
            $quantidade_inicial = (float)($data["quantidade_atual"] ?? 0);

            if (empty($nome)) json_response(["success" => false, "message" => "Nome é obrigatório"], 400);

            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE estoque SET nome_item = ?, categoria = ?, unidade = ?, estoque_minimo = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$nome, $categoria, $unidade, $minimo, $id, $user_id]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO estoque (user_id, nome_item, categoria, quantidade_atual, unidade, estoque_minimo) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $nome, $categoria, $quantidade_inicial, $unidade, $minimo]);
            }
            json_response(["success" => true, "message" => "Item salvo com sucesso!"]);
            break;

        case "move":
            if ($_SERVER["REQUEST_METHOD"] !== "POST") json_response(["success" => false, "message" => "Método inválido"], 405);
            $data = json_decode(file_get_contents("php://input"), true);
            $id = (int)($data["estoque_id"] ?? 0);
            $tipo = $data["tipo"] ?? ""; // entrada, saida, ajuste
            $quant = (float)($data["quantidade"] ?? 0);
            $obs = trim($data["observacao"] ?? "");

            if (!$id || !$quant || !in_array($tipo, ["entrada", "saida", "ajuste"])) {
                json_response(["success" => false, "message" => "Dados inválidos"], 400);
            }

            $pdo->beginTransaction();
            
            // Check current stock
            $stmt = $pdo->prepare("SELECT quantidade_atual FROM estoque WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
            $current = $stmt->fetchColumn();
            if ($current === false) throw new Exception("Item não encontrado");

            $new_quant = $current;
            if ($tipo === "entrada") $new_quant += $quant;
            elseif ($tipo === "saida") $new_quant -= $quant;
            elseif ($tipo === "ajuste") $new_quant = $quant;

            // Update balance
            $upd = $pdo->prepare("UPDATE estoque SET quantidade_atual = ? WHERE id = ?");
            $upd->execute([$new_quant, $id]);

            // Record movement
            $mov = $pdo->prepare("INSERT INTO estoque_movimentacoes (estoque_id, tipo, quantidade, observacao) VALUES (?, ?, ?, ?)");
            $mov->execute([$id, $tipo, $quant, $obs]);

            $pdo->commit();
            json_response(["success" => true, "message" => "Movimentação registrada!"]);
            break;

        case "delete":
            if ($_SERVER["REQUEST_METHOD"] !== "POST") json_response(["success" => false, "message" => "Método inválido"], 405);
            $data = json_decode(file_get_contents("php://input"), true);
            $id = (int)($data["id"] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM estoque WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
            json_response(["success" => true, "message" => "Item removido!"]);
            break;

        case "history":
            $id = (int)($_GET["estoque_id"] ?? 0);
            $stmt = $pdo->prepare("SELECT * FROM estoque_movimentacoes WHERE estoque_id = ? ORDER BY data_movimento DESC LIMIT 20");
            $stmt->execute([$id]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(["success" => true, "data" => $history]);
            break;

        default:
            json_response(["success" => false, "message" => "Ação inválida"], 400);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(["success" => false, "message" => "Erro: " . $e->getMessage()], 500);
}
