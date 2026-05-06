<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

require_csrf();

require_once __DIR__ . '/../src/RateLimiter.php';
RateLimiter::middleware('account_update', 5, 60);

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$nome = trim($data['nome'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$current_password = $data['current_password'] ?? '';

if (empty($nome) || empty($email)) {
    json_response(['success' => false, 'message' => 'Nome e email são obrigatórios'], 400);
}

try {
    $stmt_user = $pdo->prepare("SELECT senha_hash, email FROM users WHERE id = ?");
    $stmt_user->execute([$user_id]);
    $current_user = $stmt_user->fetch();

    $email_changed = $email !== $current_user['email'];
    if ($email_changed && empty($current_password)) {
        json_response(['success' => false, 'message' => 'Informe sua senha atual para alterar o email.'], 400);
    }
    if ($email_changed && !password_verify($current_password, $current_user['senha_hash'])) {
        json_response(['success' => false, 'message' => 'Senha atual incorreta.'], 401);
    }
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
    error_log('Account update error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Erro ao atualizar perfil.'], 500);
}
