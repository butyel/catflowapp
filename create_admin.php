<?php
// create_admin.php
// Script de conveniência para criar um administrador inicial
require_once __DIR__ . '/src/db.php';

$nome = 'Administrador Sistema';
$email = 'admin@catflow.com';
$senha = 'admin123';
$role = 'admin';

$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // Verifica se já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "O usuário admin ($email) já existe no banco de dados. Pode fazer login com a senha definida anteriormente.";
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha_hash, $role]);

    echo "<h1>Admin Criado com Sucesso!</h1>";
    echo "<p><strong>Login:</strong> $email</p>";
    echo "<p><strong>Senha:</strong> $senha</p>";
    echo "<p><a href='/index.php'>Ir para o Login</a></p>";
    echo "<p><em>Aviso: Após testar, apague este arquivo (create_admin.php) por segurança.</em></p>";
}
catch (Exception $e) {
    echo "Erro ao criar admin: " . $e->getMessage();
}
?>
