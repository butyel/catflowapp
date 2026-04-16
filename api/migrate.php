<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');

$tables = [
    "users" => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha_hash VARCHAR(255) NOT NULL,
        role ENUM('admin', 'tutor') NOT NULL DEFAULT 'tutor',
        foto VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "alas" => "CREATE TABLE IF NOT EXISTS alas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        nome VARCHAR(100) NOT NULL,
        descricao TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    "gatos" => "CREATE TABLE IF NOT EXISTS gatos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        ala_id INT DEFAULT NULL,
        nome VARCHAR(100) NOT NULL,
        foto VARCHAR(255) DEFAULT NULL,
        raca VARCHAR(100) DEFAULT NULL,
        sexo ENUM('M', 'F') NOT NULL,
        data_nascimento DATE DEFAULT NULL,
        cor_padrao VARCHAR(100) DEFAULT NULL,
        microchip VARCHAR(50) DEFAULT NULL,
        pedigree VARCHAR(100) DEFAULT NULL,
        doencas_pre_existentes TEXT DEFAULT NULL,
        idade VARCHAR(50) DEFAULT NULL,
        castrado BOOLEAN NOT NULL DEFAULT FALSE,
        peso DECIMAL(5,2) DEFAULT NULL,
        personalidade VARCHAR(255) DEFAULT NULL,
        status ENUM('ativo', 'Doado', 'aposentado', 'óbito') NOT NULL DEFAULT 'ativo',
        historico TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (ala_id) REFERENCES alas(id) ON DELETE SET NULL
    )",
    "saude" => "CREATE TABLE IF NOT EXISTS saude (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        tipo VARCHAR(50) NOT NULL,
        descricao VARCHAR(255) NOT NULL,
        data_evento DATE NOT NULL,
        proxima_data DATE DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )",
    "peso_historico" => "CREATE TABLE IF NOT EXISTS peso_historico (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        peso DECIMAL(5,2) NOT NULL,
        data DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )",
    "monitoramento_diario" => "CREATE TABLE IF NOT EXISTS monitoramento_diario (
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
    )",
    "estoque" => "CREATE TABLE IF NOT EXISTS estoque (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        nome_item VARCHAR(100) NOT NULL,
        categoria ENUM('racao', 'areia', 'medicamento', 'limpeza', 'outro') NOT NULL,
        quantidade_atual DECIMAL(10,2) DEFAULT 0,
        unidade VARCHAR(20) DEFAULT 'un',
        estoque_minimo DECIMAL(10,2) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    "galeria" => "CREATE TABLE IF NOT EXISTS galeria (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        foto_path VARCHAR(255) NOT NULL,
        legenda VARCHAR(255),
        data DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )",
    "medicamentos" => "CREATE TABLE IF NOT EXISTS medicamentos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        nome_medicamento VARCHAR(100) NOT NULL,
        dosagem VARCHAR(100) NOT NULL,
        horario TIME NOT NULL,
        duracao_dias INT NOT NULL,
        status ENUM('ativo', 'concluido', 'suspenso') NOT NULL DEFAULT 'ativo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )",
    "financeiro_itens" => "CREATE TABLE IF NOT EXISTS financeiro_itens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        nome VARCHAR(100) NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        categoria ENUM('racao', 'veterinario', 'areia', 'medicamentos', 'doacao', 'outros') NOT NULL,
        unidade VARCHAR(20) DEFAULT 'un',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    "financeiro" => "CREATE TABLE IF NOT EXISTS financeiro (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        item_id INT DEFAULT NULL,
        tipo ENUM('receita', 'despesa') NOT NULL,
        descricao VARCHAR(255) NOT NULL,
        categoria ENUM('racao', 'veterinario', 'areia', 'medicamentos', 'doacao', 'outros') NOT NULL,
        quantidade DECIMAL(10,2) DEFAULT 1,
        valor DECIMAL(10,2) NOT NULL,
        data DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES financeiro_itens(id) ON DELETE SET NULL
    )",
    "adocoes" => "CREATE TABLE IF NOT EXISTS adocoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        gato_id INT NOT NULL,
        adotante_nome VARCHAR(100) NOT NULL,
        contato VARCHAR(255) NOT NULL,
        data_adocao DATE NOT NULL,
        observacoes TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (gato_id) REFERENCES gatos(id) ON DELETE CASCADE
    )",
    "lembretes" => "CREATE TABLE IF NOT EXISTS lembretes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        titulo VARCHAR(100) NOT NULL,
        descricao TEXT,
        data_lembrete DATETIME NOT NULL,
        status ENUM('pendente', 'concluido') NOT NULL DEFAULT 'pendente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    "medicamentos_historico" => "CREATE TABLE IF NOT EXISTS medicamentos_historico (
        id INT AUTO_INCREMENT PRIMARY KEY,
        medicamento_id INT NOT NULL,
        data_aplicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        observacao TEXT,
        FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id) ON DELETE CASCADE
    )"
];

$indexes = [
    "CREATE INDEX idx_gatos_user ON gatos(user_id)",
    "CREATE INDEX idx_gatos_status ON gatos(status)",
    "CREATE INDEX idx_saude_gato ON saude(gato_id)",
    "CREATE INDEX idx_medicamentos_gato ON medicamentos(gato_id)",
    "CREATE INDEX idx_financeiro_user ON financeiro(user_id)",
    "CREATE INDEX idx_financeiro_data ON financeiro(data)",
    "CREATE INDEX idx_adocoes_gato ON adocoes(gato_id)",
    "CREATE INDEX idx_lembretes_user ON lembretes(user_id)"
];

try {
    $created = [];
    $errors = [];
    
    foreach ($tables as $table => $sql) {
        try {
            $pdo->exec($sql);
            $created[] = $table;
        } catch (Exception $e) {
            $errors[$table] = $e->getMessage();
        }
    }
    
    foreach ($indexes as $sql) {
        try {
            $pdo->exec($sql);
        } catch (Exception $e) {
        }
    }
    
    json_response([
        'success' => true,
        'tables_created' => count($created),
        'tables' => $created,
        'errors' => $errors
    ]);
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Erro: ' . $e->getMessage()
    ], 500);
}