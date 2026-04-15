<?php
class Logger {
    private static $table = 'logs_auditoria';
    
    public static function init(): void {
        global $pdo;
        $pdo->exec("CREATE TABLE IF NOT EXISTS logs_auditoria (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            acao VARCHAR(50) NOT NULL,
            entidade VARCHAR(50) NOT NULL,
            entidade_id INT,
            detalhes TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    public static function log(string $acao, string $entidade, ?int $entidade_id = null, ?array $detalhes = null): void {
        global $pdo;
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $user_id = $_SESSION['user_id'] ?? null;
        $detalhes_json = $detalhes ? json_encode($detalhes, JSON_UNESCAPED_UNICODE) : null;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO logs_auditoria (user_id, acao, entidade, entidade_id, detalhes, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $acao, $entidade, $entidade_id, $detalhes_json, $ip, $ua]);
        } catch (Exception $e) {
            error_log("Logger Error: " . $e->getMessage());
        }
    }
    
    public static function create(string $entidade, int $id, array $data): void {
        self::log('create', $entidade, $id, $data);
    }
    
    public static function update(string $entidade, int $id, array $before, array $after): void {
        self::log('update', $entidade, $id, ['before' => $before, 'after' => $after]);
    }
    
    public static function delete(string $entidade, int $id, array $data): void {
        self::log('delete', $entidade, $id, $data);
    }
    
    public static function login(int $user_id, bool $success = true): void {
        self::log($success ? 'login_success' : 'login_failed', 'user', $user_id);
    }
    
    public static function logout(int $user_id): void {
        self::log('logout', 'user', $user_id);
    }
    
    public static function getRecent(int $limit = 50, ?int $user_id = null): array {
        global $pdo;
        
        $sql = "SELECT l.*, u.nome as usuario_nome FROM logs_auditoria l LEFT JOIN users u ON l.user_id = u.id";
        $params = [];
        
        if ($user_id) {
            $sql .= " WHERE l.user_id = ?";
            $params[] = $user_id;
        }
        
        $sql .= " ORDER BY l.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public static function getByEntity(string $entidade, int $id): array {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM logs_auditoria WHERE entidade = ? AND entidade_id = ? ORDER BY created_at DESC");
        $stmt->execute([$entidade, $id]);
        return $stmt->fetchAll();
    }
}
