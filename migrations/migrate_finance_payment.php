<?php
require_once __DIR__ . '/src/db.php';

echo "<h1>Migração de Banco de Dados</h1>";
echo "<pre>";
try {
    // Check if columns already exist to avoid errors
    $check = $pdo->query("SHOW COLUMNS FROM financeiro LIKE 'pago'");
    if (!$check->fetch()) {
        echo "Adicionando colunas de pagamento...\n";
        $pdo->exec("ALTER TABLE financeiro ADD COLUMN pago BOOLEAN NOT NULL DEFAULT FALSE");
        $pdo->exec("ALTER TABLE financeiro ADD COLUMN valor_pago DECIMAL(10,2) NOT NULL DEFAULT 0");
        $pdo->exec("ALTER TABLE financeiro ADD COLUMN observacao_pagamento TEXT DEFAULT NULL");
        echo "Sucesso! Colunas de pagamento adicionadas.\n";
    }
    else {
        echo "As colunas já existem no banco de dados.\n";
    }
}
catch (Exception $e) {
    echo "Erro na migração: " . $e->getMessage() . "\n";
}
echo "</pre>";
echo "<p><a href='financeiro.php'>Clique aqui para voltar ao Financeiro</a></p>";
?>
