<?php
// migrate_cats_v2.php
require_once __DIR__ . '/src/db.php';

try {
    $pdo->beginTransaction();

    $columns_res = $pdo->query("SHOW COLUMNS FROM gatos");
    $columns = $columns_res->fetchAll(PDO::FETCH_COLUMN);

    // 1. Add new columns if they don't exist
    if (!in_array('raca', $columns)) {
        $pdo->exec("ALTER TABLE gatos ADD COLUMN raca VARCHAR(100) DEFAULT NULL AFTER foto");
    }

    if (!in_array('data_nascimento', $columns)) {
        $pdo->exec("ALTER TABLE gatos ADD COLUMN data_nascimento DATE DEFAULT NULL AFTER sexo");
    }

    if (!in_array('cor_padrao', $columns)) {
        $pdo->exec("ALTER TABLE gatos ADD COLUMN cor_padrao VARCHAR(100) DEFAULT NULL AFTER data_nascimento");
    }

    if (!in_array('microchip', $columns)) {
        $pdo->exec("ALTER TABLE gatos ADD COLUMN microchip VARCHAR(50) DEFAULT NULL AFTER cor_padrao");
    }

    if (!in_array('pedigree', $columns)) {
        $pdo->exec("ALTER TABLE gatos ADD COLUMN pedigree VARCHAR(100) DEFAULT NULL AFTER microchip");
    }

    if (!in_array('historico', $columns)) {
        $pdo->exec("ALTER TABLE gatos ADD COLUMN historico TEXT DEFAULT NULL AFTER status");
    }

    // 2. Update status enum
    // Note: status was: residente, tratamento, adotado
    // Target: ativo, Doado, aposentado, óbito
    // We'll keep 'adotado' and 'residente' mapping or just allow the new ones.
    // To be safe, we modify the column to include all values and then map existing ones if needed.
    $pdo->exec("ALTER TABLE gatos MODIFY COLUMN status ENUM('residente', 'tratamento', 'adotado', 'ativo', 'Doado', 'aposentado', 'óbito') NOT NULL DEFAULT 'ativo'");

    // Optional: Migrate existing status values
    $pdo->exec("UPDATE gatos SET status = 'ativo' WHERE status = 'residente'");
    $pdo->exec("UPDATE gatos SET status = 'Doado' WHERE status = 'adotado'");
    // 'tratamento' can stay or be moved to 'ativo' depending on preference, let's keep it for now or move to ativo
    $pdo->exec("UPDATE gatos SET status = 'ativo' WHERE status = 'tratamento'");

    $pdo->commit();
    echo "Migração de gatos v2 realizada com sucesso!";
}
catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo "Erro na migração: " . $e->getMessage();
}
