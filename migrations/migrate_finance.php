<?php
require_once __DIR__ . '/src/db.php';

try {
    // 1. Create financeiro_itens table
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
    echo "Tabela financeiro_itens criada/verificada.\n";

    // 2. Add columns to financeiro table
    // Step by step to avoid errors if they already exist
    $columns = $pdo->query("SHOW COLUMNS FROM financeiro")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('item_id', $columns)) {
        $pdo->exec("ALTER TABLE financeiro ADD COLUMN item_id INT DEFAULT NULL AFTER user_id");
        $pdo->exec("ALTER TABLE financeiro ADD FOREIGN KEY (item_id) REFERENCES financeiro_itens(id) ON DELETE SET NULL");
        echo "Coluna item_id adicionada.\n";
    }

    if (!in_array('quantidade', $columns)) {
        $pdo->exec("ALTER TABLE financeiro ADD COLUMN quantidade DECIMAL(10,2) DEFAULT 1 AFTER categoria");
        echo "Coluna quantidade adicionada.\n";
    }

    echo "Migração concluída com sucesso!";
}
catch (Exception $e) {
    echo "Erro na migração: " . $e->getMessage();
}
