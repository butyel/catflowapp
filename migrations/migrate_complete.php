<?php
// migrate_complete.php
require_once __DIR__ . '/src/db.php';

try {
    $pdo->beginTransaction();

    // 1. Tables check
    $pdo->exec("CREATE TABLE IF NOT EXISTS financeiro_itens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        nome VARCHAR(100) NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        categoria ENUM('racao', 'veterinario', 'areia', 'medicamentos', 'doacao', 'outros') NOT NULL,
        unidade VARCHAR(20) DEFAULT 'un',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS medicamentos_historico (
        id INT AUTO_INCREMENT PRIMARY KEY,
        medicamento_id INT NOT NULL,
        data_aplicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        observacao TEXT,
        FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id) ON DELETE CASCADE
    )");

    // 2. Financeiro columns check
    $columns = $pdo->query("SHOW COLUMNS FROM financeiro")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('item_id', $columns)) {
        $pdo->exec("ALTER TABLE financeiro ADD COLUMN item_id INT DEFAULT NULL AFTER user_id");
        $pdo->exec("ALTER TABLE financeiro ADD FOREIGN KEY (item_id) REFERENCES financeiro_itens(id) ON DELETE SET NULL");
    }

    if (!in_array('quantidade', $columns)) {
        $pdo->exec("ALTER TABLE financeiro ADD COLUMN quantidade DECIMAL(10,2) DEFAULT 1 AFTER categoria");
    }

    $pdo->commit();
    echo "Migração completa realizada com sucesso!";
}
catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo "Erro na migração: " . $e->getMessage();
}
