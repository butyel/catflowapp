<?php
// fix_login.php
require_once __DIR__ . '/src/db.php';

echo "<h1>Depuração de Usuários</h1>";

try {
    // 1. Listar usuários cadastrados
    $stmt = $pdo->query("SELECT id, nome, email, role FROM users");
    $users = $stmt->fetchAll();

    if (empty($users)) {
        echo "<p style='color:red'>Nenhum usuário encontrado no banco de dados.</p>";
    }
    else {
        echo "<h3>Usuários no Banco:</h3><ul>";
        foreach ($users as $u) {
            echo "<li><strong>{$u['nome']}</strong> ({$u['email']}) - Role: {$u['role']}</li>";
        }
        echo "</ul>";
    }

    // 2. Forçar reset da senha do admin padrão
    $email = 'admin@catflow.com';
    $nova_senha = 'admin123';
    $hash = password_hash($nova_senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin) {
        $stmt = $pdo->prepare("UPDATE users SET senha_hash = ? WHERE email = ?");
        $stmt->execute([$hash, $email]);
        echo "<p style='color:green'>Senha do usuário <strong>$email</strong> resetada para: <strong>$nova_senha</strong></p>";
    }
    else {
        $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha_hash, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute(['Administrador', $email, $hash]);
        echo "<p style='color:green'>Usuário <strong>$email</strong> não existia e foi CRIADO com a senha: <strong>$nova_senha</strong></p>";
    }

    echo "<p><a href='index.php'>Voltar para o Login</a></p>";

}
catch (Exception $e) {
    echo "<p style='color:red'>Erro: " . $e->getMessage() . "</p>";
}
