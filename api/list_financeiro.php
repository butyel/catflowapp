<?php
// api/list_financeiro.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

$user_id = get_logged_user_id();
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');

try {
    $stmt = $pdo->prepare("SELECT * FROM financeiro WHERE user_id = ? AND MONTH(data) = ? AND YEAR(data) = ? ORDER BY data DESC");
    $stmt->execute([$user_id, $mes, $ano]);
    $registros = $stmt->fetchAll();

    // Calculate totals
    $total_receitas = 0;
    $total_despesas = 0;
    foreach ($registros as $r) {
        if ($r['tipo'] === 'receita')
            $total_receitas += $r['valor'];
        else
            $total_despesas += $r['valor'];
    }

    $saldo = $total_receitas - $total_despesas;

    json_response([
        'success' => true,
        'data' => $registros,
        'summary' => [
            'total_receitas' => $total_receitas,
            'total_despesas' => $total_despesas,
            'saldo' => $saldo
        ]
    ]);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao buscar financeiro.'], 500);
}
?>
