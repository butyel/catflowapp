<?php
require_once __DIR__ . '/base.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::errorJson('Método não permitido', 405);
}

RateLimiter::middleware('login', 5, 60);

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$email = trim($data['email'] ?? '');
$senha = $data['senha'] ?? '';

if (empty($email) || empty($senha)) {
    ApiResponse::errorJson('Preencha todos os campos', 400);
}

if (!Security::validateEmail($email)) {
    ApiResponse::errorJson('E-mail inválido', 400);
}

try {
    $stmt = $pdo->prepare("SELECT id, nome, senha_hash, role, foto FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha_hash'])) {
        login_user($user['id'], $user['nome'], $user['role'], $user['foto']);
        
        session_regenerate_id(true);
        
        if (class_exists('Logger')) {
            Logger::login($user['id'], true);
        }
        
        ApiResponse::successJson([
            'user_id' => $user['id'],
            'nome' => $user['nome'],
            'role' => $user['role']
        ], 'Login realizado com sucesso');
    }
    elseif ($user) {
        if (class_exists('Logger')) {
            Logger::login($user['id'], false);
        }
        ApiResponse::errorJson('Senha incorreta', 401);
    }
    else {
        ApiResponse::errorJson('E-mail não cadastrado', 401);
    }
}
catch (Exception $e) {
    ApiResponse::errorJson('Erro no servidor', 500);
}
