<?php
// api/register.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

$nome = sanitize_input($data['nome'] ?? '');
$email = sanitize_input($data['email'] ?? '');
$senha = $data['senha'] ?? '';
$role = sanitize_input($data['role'] ?? 'tutor');

if (empty($nome) || empty($email) || empty($senha)) {
    json_response(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'E-mail inválido.'], 400);
}

$allowed_roles = ['tutor', 'ong', 'admin'];
if (!in_array($role, $allowed_roles)) {
    $role = 'tutor';
}

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json_response(['success' => false, 'message' => 'Este e-mail já está em uso.'], 409);
    }

    $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha_hash, $role]);

    $user_id = $pdo->lastInsertId();
    login_user($user_id, $nome, $role);

    json_response(['success' => true, 'message' => 'Conta criada com sucesso.']);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro interno do servidor.'], 500);
}
?>
