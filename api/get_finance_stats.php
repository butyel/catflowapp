<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');

try {
    $mensal = [];
    for ($i = 5; $i >= 0; $i--) {
        $target_date = date('Y-m-01', strtotime("-$i months"));
        $target_mes = date('m', strtotime($target_date));
        $target_ano = date('Y', strtotime($target_date));
        $label = date('M', strtotime($target_date));

        $stmt = $pdo->prepare("SELECT 
            SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) as receita,
            SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) as despesa
            FROM financeiro WHERE user_id = ? AND MONTH(data) = ? AND YEAR(data) = ?");
        $stmt->execute([$user_id, $target_mes, $target_ano]);
        $row = $stmt->fetch();
        
        $mensal[] = [
            'label' => $label,
            'receita' => (float)($row['receita'] ?? 0),
            'despesa' => (float)($row['despesa'] ?? 0)
        ];
    }

    $stmt_cat = $pdo->prepare("SELECT categoria, SUM(valor) as total 
        FROM financeiro 
        WHERE user_id = ? AND tipo = 'despesa' AND MONTH(data) = ? AND YEAR(data) = ?
        GROUP BY categoria");
    $stmt_cat->execute([$user_id, $mes, $ano]);
    $cat_rows = $stmt_cat->fetchAll();
    
    $categorias_despesas = [];
    foreach ($cat_rows as $row) {
        $categorias_despesas[$row['categoria']] = (float)$row['total'];
    }

    echo json_encode([
        'success' => true,
        'mensal' => $mensal,
        'categorias_despesas' => $categorias_despesas
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao gerar estatísticas']);
}
