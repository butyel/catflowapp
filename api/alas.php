<?php
// api/alas.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

$action = $_GET['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $pdo->prepare("SELECT * FROM alas WHERE user_id = ? ORDER BY nome ASC");
        $stmt->execute([$user_id]);
        json_response(['success' => true, 'data' => $stmt->fetchAll()]);
    }
    elseif ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $nome = sanitize_input($data['nome'] ?? '');
        $descricao = sanitize_input($data['descricao'] ?? '');

        if (empty($nome)) {
            json_response(['success' => false, 'message' => 'Nome da ala é obrigatório'], 400);
        }

        $stmt = $pdo->prepare("INSERT INTO alas (user_id, nome, descricao) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $nome, $descricao]);
        json_response(['success' => true, 'message' => 'Ala criada com sucesso!', 'id' => $pdo->lastInsertId()]);
    }
    elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int)($data['id'] ?? 0);

        $stmt = $pdo->prepare("DELETE FROM alas WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        json_response(['success' => true, 'message' => 'Ala excluída com sucesso!']);
    }
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()], 500);
}
