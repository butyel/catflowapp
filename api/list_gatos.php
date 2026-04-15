<?php
require_once __DIR__ . '/base.php';

rateLimit('list_gatos', 30);
requireAuth();

$user_id = get_logged_user_id();
$is_admin = is_admin();
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? '%' . Security::sanitizeInput($_GET['search']) . '%' : null;

try {
    $countSql = $is_admin ? "SELECT COUNT(*) as total FROM gatos" : "SELECT COUNT(*) as total FROM gatos WHERE user_id = ?";
    $countParams = $is_admin ? [] : [$user_id];
    
    if ($search) {
        $countSql .= " WHERE (nome LIKE ? OR raca LIKE ? OR cor_padrao LIKE ?)";
        if (!$is_admin) $countSql .= " AND user_id = ?";
        $countParams = array_merge([$search, $search, $search], $is_admin ? [] : [$user_id]);
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch()['total'];
    $totalPages = ceil($total / $limit);

    if ($is_admin) {
        $sql = "SELECT g.*, u.nome as tutor_nome, a.nome as ala_nome 
                FROM gatos g 
                JOIN users u ON g.user_id = u.id 
                LEFT JOIN alas a ON g.ala_id = a.id";
        $params = [];
        
        if ($search) {
            $sql .= " WHERE (g.nome LIKE ? OR g.raca LIKE ? OR g.cor_padrao LIKE ?)";
            $params = [$search, $search, $search];
        }
        $sql .= " ORDER BY g.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } else {
        $sql = "SELECT g.*, a.nome as ala_nome 
                FROM gatos g 
                LEFT JOIN alas a ON g.ala_id = a.id 
                WHERE g.user_id = ?";
        $params = [$user_id];
        
        if ($search) {
            $sql .= " AND (g.nome LIKE ? OR g.raca LIKE ? OR g.cor_padrao LIKE ?)";
            $params = array_merge([$user_id], [$search, $search, $search]);
        }
        $sql .= " ORDER BY g.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
    
    $gatos = $stmt->fetchAll();

    ApiResponse::successJson([
        'data' => $gatos,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $total,
            'per_page' => $limit
        ]
    ]);
} catch (Exception $e) {
    ApiResponse::errorJson('Erro ao buscar gatos', 500);
}
