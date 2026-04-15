<?php
// migrate_expansion_v1.php
require_once __DIR__ . '/src/db.php';

try {
    $pdo->beginTransaction();

    // 1. Create 'alas' table
    $pdo->exec("CREATE TABLE IF NOT EXISTS alas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    // 2. Update 'gatos' table
    $columns_res = $pdo->query("SHOW COLUMNS FROM gatos");
    $columns = $columns_res->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('ala_id', $columns)) {
        $pdo->exec("ALTER TABLE gatos ADD COLUMN ala_id INT DEFAULT NULL AFTER user_id");
        $pdo->exec("ALTER TABLE gatos ADD FOREIGN KEY (ala_id) REFERENCES alas(id) ON DELETE SET NULL");
    }

    if (!in_array('doencas_pre_existentes', $columns)) {
        $pdo->exec("ALTER TABLE gatos ADD COLUMN doencas_pre_existentes TEXT DEFAULT NULL AFTER pedigree");
    }

    // 3. Create 'peso_historico' table
    $pdo->exec("CREATE TABLE IF NOT EXISTS peso_historico (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        peso DECIMAL(5,2) NOT NULL,
        data DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )");

    // 4. Create 'monitoramento_diario' table
    $pdo->exec("CREATE TABLE IF NOT EXISTS monitoramento_diario (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        comportamento VARCHAR(255),
        caixa_areia VARCHAR(255),
        apetite VARCHAR(255),
        energia VARCHAR(255),
        data DATE NOT NULL,
        observacoes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )");

    // 5. Create 'estoque' table
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

    // 6. Create 'galeria' table
    $pdo->exec("CREATE TABLE IF NOT EXISTS galeria (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        foto_path VARCHAR(255) NOT NULL,
        legenda VARCHAR(255),
        data DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )");

    $pdo->commit();
    echo "Expansão de banco de dados v1 realizada com sucesso!";
}
catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo "Erro na migração: " . $e->getMessage();
}
