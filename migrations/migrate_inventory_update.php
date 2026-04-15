<?php
require_once __DIR__ . '/src/db.php';

echo "<h1>Migração de Estoque v1.1</h1>";
echo "<pre>";

try {
    $pdo->beginTransaction();

    // 1. Ensure 'estoque' table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS estoque (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        nome_item VARCHAR(100) NOT NULL,
        categoria ENUM('racao', 'areia', 'medicamento', 'limpeza', 'outro') NOT NULL,
        quantidade_atual DECIMAL(10,2) DEFAULT 0,
        unidade VARCHAR(20) DEFAULT 'un',
        estoque_minimo DECIMAL(10,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 2. Create 'estoque_movimentacoes' table for history
    $pdo->exec("CREATE TABLE IF NOT EXISTS estoque_movimentacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        estoque_id INT NOT NULL,
        tipo ENUM('entrada', 'saida', 'ajuste') NOT NULL,
        quantidade DECIMAL(10,2) NOT NULL,
        observacao TEXT,
        data_movimento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (estoque_id) REFERENCES estoque(id) ON DELETE CASCADE
    )");

    $pdo->commit();
    echo "Sucesso! Tabelas de estoque e movimentações preparadas.\n";
}
catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo "Erro na migração: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo "<p><a href='estoque.php'>Ir para o Estoque</a></p>";
?>
