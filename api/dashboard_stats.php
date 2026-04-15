<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

$user_id = get_logged_user_id();
$is_admin = is_admin();

try {
    $today = date('Y-m-d');
    $thisMonth = date('Y-m');
    
    $stats = [];
    
    $stmtGatos = $pdo->prepare("SELECT COUNT(*) FROM gatos WHERE status IN ('ativo', 'tratamento')" . ($is_admin ? '' : ' AND user_id = ?'));
    if (!$is_admin) $stmtGatos->execute([$user_id]);
    else $stmtGatos->execute();
    $stats['gatos_ativos'] = (int)$stmtGatos->fetchColumn();
    
    $stmtTrat = $pdo->prepare("SELECT COUNT(*) FROM gatos WHERE status = 'tratamento'" . ($is_admin ? '' : ' AND user_id = ?'));
    if (!$is_admin) $stmtTrat->execute([$user_id]);
    else $stmtTrat->execute();
    $stats['em_tratamento'] = (int)$stmtTrat->fetchColumn();
    
    $stmtMed = $pdo->prepare("SELECT COUNT(*) FROM medicamentos m JOIN gatos g ON m.gato_id = g.id WHERE m.status = 'ativo'" . ($is_admin ? '' : ' AND g.user_id = ?'));
    if (!$is_admin) $stmtMed->execute([$user_id]);
    else $stmtMed->execute();
    $stats['medicamentos_ativos'] = (int)$stmtMed->fetchColumn();
    
    $stmtVac = $pdo->prepare("SELECT COUNT(*) FROM saude s JOIN gatos g ON s.gato_id = g.id WHERE s.proxima_data IS NOT NULL AND s.proxima_data <= DATE_ADD(?, INTERVAL 7 DAY)" . ($is_admin ? '' : ' AND g.user_id = ?'));
    if (!$is_admin) $stmtVac->execute([$today, $user_id]);
    else $stmtVac->execute([$today]);
    $stats['vacinas_pendentes'] = (int)$stmtVac->fetchColumn();
    
    $stmtReceitas = $pdo->prepare(
        "SELECT COALESCE(SUM(valor), 0) as total FROM financeiro WHERE tipo = 'receita' AND DATE_FORMAT(data, '%Y-%m') = ?" . ($is_admin ? '' : ' AND user_id = ?')
    );
    $stmtReceitas->execute($is_admin ? [$thisMonth] : [$thisMonth, $user_id]);
    $stats['total_receitas'] = (float)$stmtReceitas->fetch()['total'];
    
    $stmtDespesas = $pdo->prepare(
        "SELECT COALESCE(SUM(valor), 0) as total FROM financeiro WHERE tipo = 'despesa' AND DATE_FORMAT(data, '%Y-%m') = ?" . ($is_admin ? '' : ' AND user_id = ?')
    );
    $stmtDespesas->execute($is_admin ? [$thisMonth] : [$thisMonth, $user_id]);
    $stats['total_despesas'] = (float)$stmtDespesas->fetch()['total'];
    
    $stmtEstoque = $pdo->prepare("SELECT COUNT(*) FROM estoque WHERE quantidade_atual <= estoque_minimo AND quantidade_atual > 0" . ($is_admin ? '' : ' AND user_id = ?'));
    if (!$is_admin) $stmtEstoque->execute([$user_id]);
    else $stmtEstoque->execute();
    $stats['estoque_baixo'] = (int)$stmtEstoque->fetchColumn();
    
    $stmtAdocoes = $pdo->prepare(
        "SELECT COUNT(*) FROM adocoes WHERE DATE_FORMAT(data_adocao, '%Y-%m') = ?" . ($is_admin ? '' : ' AND gato_id IN (SELECT id FROM gatos WHERE user_id = ?)')
    );
    $stmtAdocoes->execute($is_admin ? [$thisMonth] : [$thisMonth, $user_id]);
    $stats['adocoes_mes'] = (int)$stmtAdocoes->fetchColumn();
    
    $alerts = [];
    
    $stmtAlerts = $pdo->prepare(
        "SELECT s.*, g.nome as gato_nome FROM saude s JOIN gatos g ON s.gato_id = g.id 
         WHERE s.proxima_data IS NOT NULL AND s.proxima_data <= DATE_ADD(?, INTERVAL 7 DAY)" . 
         ($is_admin ? '' : ' AND g.user_id = ?') . " ORDER BY s.proxima_data ASC LIMIT 5"
    );
    $stmtAlerts->execute($is_admin ? [$today] : [$today, $user_id]);
    while ($row = $stmtAlerts->fetch()) {
        $isToday = $row['proxima_data'] === $today;
        $alerts[] = [
            'type' => 'saude',
            'severity' => $isToday ? 'red' : 'yellow',
            'title' => $isToday ? 'HOJE' : 'Em breve',
            'message' => $row['descricao'] . ' - ' . $row['gato_nome'],
            'date' => date('d/m', strtotime($row['proxima_data'])),
            'isToday' => $isToday
        ];
    }
    
    if ($stats['medicamentos_ativos'] > 0) {
        $stmtMed = $pdo->prepare(
            "SELECT m.*, g.nome as gato_nome FROM medicamentos m JOIN gatos g ON m.gato_id = g.id 
             WHERE m.status = 'ativo'" . ($is_admin ? '' : ' AND g.user_id = ?') . " ORDER BY m.horario LIMIT 3"
        );
        $stmtMed->execute($is_admin ? [] : [$user_id]);
        while ($row = $stmtMed->fetch()) {
            $alerts[] = [
                'type' => 'medicamento',
                'severity' => 'blue',
                'title' => '💊 Medicação',
                'message' => $row['nome_medicamento'] . ' - ' . $row['gato_nome'],
                'date' => $row['horario'],
                'isToday' => false
            ];
        }
    }
    
    if ($stats['estoque_baixo'] > 0) {
        $stmtEst = $pdo->prepare(
            "SELECT nome_item, quantidade_atual, estoque_minimo FROM estoque 
             WHERE quantidade_atual <= estoque_minimo" . ($is_admin ? '' : ' AND user_id = ?') . " LIMIT 3"
        );
        $stmtEst->execute($is_admin ? [] : [$user_id]);
        while ($row = $stmtEst->fetch()) {
            $alerts[] = [
                'type' => 'estoque',
                'severity' => 'purple',
                'title' => '📦 Estoque',
                'message' => $row['nome_item'] . ' - ' . $row['quantidade_atual'] . ' restante(s)',
                'isToday' => false
            ];
        }
    }
    
    $recent_transactions = [];
    $stmtTrans = $pdo->prepare(
        "SELECT * FROM financeiro WHERE DATE_FORMAT(data, '%Y-%m') = ?" . ($is_admin ? '' : ' AND user_id = ?') . " ORDER BY data DESC LIMIT 5"
    );
    $stmtTrans->execute($is_admin ? [$thisMonth] : [$thisMonth, $user_id]);
    while ($row = $stmtTrans->fetch()) {
        $recent_transactions[] = $row;
    }
    
    json_response([
        'success' => true,
        'data' => [
            'stats' => $stats,
            'alerts' => $alerts,
            'recent_transactions' => $recent_transactions
        ]
    ]);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao carregar dados'], 500);
}
