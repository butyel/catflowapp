<?php
// migrate_fix_saude.php
require_once __DIR__ . '/src/db.php';

try {
    $pdo->beginTransaction();

    // 1. Create 'saude' table correctly
    $pdo->exec("CREATE TABLE IF NOT EXISTS saude (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        tipo VARCHAR(50) NOT NULL,
        descricao VARCHAR(255) NOT NULL,
        data_evento DATE NOT NULL,
        proxima_data DATE DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )");

    // 2. Refresh 'medicamentos' table (ensure it matches expansion requirements)
    $pdo->exec("CREATE TABLE IF NOT EXISTS medicamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        nome_medicamento VARCHAR(100) NOT NULL,
        dosagem VARCHAR(100) NOT NULL,
        horario TIME NOT NULL,
        duracao_dias INT NOT NULL,
        status ENUM('ativo', 'concluido', 'suspenso') NOT NULL DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )");

    $pdo->commit();
    echo "Correção da tabela de saúde realizada com sucesso! A tabela 'saude' e 'medicamentos' foram verificadas/criadas.";
}
catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo "Erro na correção: " . $e->getMessage();
}
?>
