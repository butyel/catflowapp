<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/utils.php';

$user_id = get_logged_user_id();
if (!$user_id) {
    json_response(['success' => false, 'message' => 'Não autorizado'], 401);
}

try {
    $alerts = [];
    $today = date('Y-m-d');
    $nextWeek = date('Y-m-d', strtotime('+7 days'));

    // 1. Low Stock Alerts
    $stmtStock = $pdo->prepare("SELECT nome_item, quantidade_atual, unidade, estoque_minimo FROM estoque WHERE user_id = ? AND quantidade_atual <= estoque_minimo");
    $stmtStock->execute([$user_id]);
    foreach ($stmtStock->fetchAll() as $item) {
        $alerts[] = [
            'type' => 'estoque',
            'title' => 'Estoque Baixo',
            'message' => "{$item['nome_item']} está com {$item['quantidade_atual']} {$item['unidade']}",
            'severity' => 'yellow'
        ];
    }

    // 2. Health Alerts (Vaccines, Appointments, etc.)
    $stmtHealth = $pdo->prepare("SELECT s.tipo, s.descricao, s.proxima_data, g.nome as gato_nome FROM saude s JOIN gatos g ON s.gato_id = g.id WHERE (g.user_id = ? OR ?) AND s.proxima_data BETWEEN ? AND ? ORDER BY s.proxima_data ASC");
    $stmtHealth->execute([$user_id, is_admin(), $today, $nextWeek]);
    foreach ($stmtHealth->fetchAll() as $h) {
        $isToday = $h['proxima_data'] === $today;
        $alerts[] = [
            'type' => 'saude',
            'title' => $h['tipo'],
            'message' => "{$h['gato_nome']}: {$h['descricao']}",
            'date' => format_date($h['proxima_data']),
            'isToday' => $isToday,
            'severity' => in_array(strtolower($h['tipo']), ['cirurgia', 'exame']) ? 'red' : 'blue'
        ];
    }

    // 3. Medication Alerts (Today's doses)
    // We check active medications. For demo purposes, we alert if it's active.
    $stmtMeds = $pdo->prepare("SELECT m.nome_medicamento, g.nome as gato_nome FROM medicamentos m JOIN gatos g ON m.gato_id = g.id WHERE (g.user_id = ? OR ?) AND m.status = 'ativo'");
    $stmtMeds->execute([$user_id, is_admin()]);
    foreach ($stmtMeds->fetchAll() as $m) {
        $alerts[] = [
            'type' => 'medicamento',
            'title' => 'Medicamento',
            'message' => "{$m['gato_nome']}: {$m['nome_medicamento']}",
            'severity' => 'purple'
        ];
    }

    json_response(['success' => true, 'data' => $alerts]);
}
catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao carregar alertas: ' . $e->getMessage()], 500);
}
