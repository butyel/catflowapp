<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$nome = trim($data['nome'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($nome) || empty($email)) {
    json_response(['success' => false, 'message' => 'Nome e email são obrigatórios'], 400);
}

try {
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ?, senha_hash = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $hash, $user_id]);
    }
    else {
        $stmt = $pdo->prepare("UPDATE users SET nome = ?, email = ? WHERE id = ?");
        $stmt->execute([$nome, $email, $user_id]);
    }

    // Update session
    $_SESSION['user_nome'] = $nome;

    json_response(['success' => true, 'message' => 'Perfil atualizado com sucesso!']);
}
catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        json_response(['success' => false, 'message' => 'Este email já está em uso'], 400);
    }
    json_response(['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()], 500);
}
