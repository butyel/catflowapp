<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/utils.php';

header('Content-Type: application/json');
require_login();

$user_id = get_logged_user_id();
$is_admin = is_admin();

$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'] ?? 'gatos';
$format = $data['format'] ?? 'csv';

if (!in_array($type, ['gatos', 'financeiro', 'saude', 'adocoes'])) {
    json_response(['success' => false, 'message' => 'Tipo inválido'], 400);
}

if (!in_array($format, ['csv', 'pdf'])) {
    json_response(['success' => false, 'message' => 'Formato inválido'], 400);
}

try {
    $filename = "catflow_{$type}_" . date('Y-m-d') . ".{$format}";
    $export_dir = __DIR__ . '/../exports';
    if (!is_dir($export_dir)) {
        mkdir($export_dir, 0755, true);
    }
    
    $stmt = match($type) {
        'gatos' => $pdo->prepare("SELECT nome, raca, sexo, idade, peso, status, data_chegada FROM gatos" . ($is_admin ? '' : ' WHERE user_id = ?')),
        'financeiro' => $pdo->prepare("SELECT tipo, descricao, valor, data, categoria FROM financeiro WHERE DATE_FORMAT(data, '%Y-%m') = ?" . ($is_admin ? '' : ' AND user_id = ?')),
        'saude' => $pdo->prepare("SELECT g.nome as gato_nome, s.tipo, s.descricao, s.data as data_procedimento, s.proxima_data FROM saude s JOIN gatos g ON s.gato_id = g.id" . ($is_admin ? ' WHERE 1=1' : ' WHERE g.user_id = ?')),
        'adocoes' => $pdo->prepare("SELECT g.nome as gato_nome, a.nome_adotante, a.contato, a.data_adocao, a.observacoes FROM adocoes a JOIN gatos g ON a.gato_id = g.id" . ($is_admin ? ' WHERE 1=1' : ' WHERE g.user_id = ?'))
    };
    
    if ($type === 'financeiro') {
        $currentMonth = date('Y-m');
        $stmt->execute($is_admin ? [$currentMonth] : [$currentMonth, $user_id]);
    } elseif ($type === 'saude' || $type === 'adocoes') {
        $stmt->execute($is_admin ? [] : [$user_id]);
    } else {
        $stmt->execute($is_admin ? [] : [$user_id]);
    }
    
    $rows = $stmt->fetchAll();
    
    if ($type === 'gatos') {
        $headers = ['Nome', 'Raça', 'Sexo', 'Idade', 'Peso', 'Status', 'Data de Chegada'];
        $rows = array_map(function($r) {
            return [
                $r['nome'],
                $r['raca'],
                $r['sexo'],
                $r['idade'],
                $r['peso'],
                $r['status'],
                $r['data_chegada']
            ];
        }, $rows);
    } elseif ($type === 'financeiro') {
        $headers = ['Tipo', 'Descrição', 'Valor', 'Data', 'Categoria'];
        $rows = array_map(function($r) {
            return [
                $r['tipo'],
                $r['descricao'],
                $r['valor'],
                $r['data'],
                $r['categoria']
            ];
        }, $rows);
    } elseif ($type === 'saude') {
        $headers = ['Gato', 'Tipo', 'Descrição', 'Data', 'Próxima Data'];
        $rows = array_map(function($r) {
            return [
                $r['gato_nome'],
                $r['tipo'],
                $r['descricao'],
                $r['data_procedimento'],
                $r['proxima_data']
            ];
        }, $rows);
    } elseif ($type === 'adocoes') {
        $headers = ['Gato', 'Adotante', 'Contato', 'Data da Adoção', 'Observações'];
        $rows = array_map(function($r) {
            return [
                $r['gato_nome'],
                $r['nome_adotante'],
                $r['contato'],
                $r['data_adocao'],
                $r['observacoes']
            ];
        }, $rows);
    }
    
    if ($format === 'csv') {
        $filepath = $export_dir . '/' . $filename;
        $handle = fopen($filepath, 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, $headers, ';');
        foreach ($rows as $row) {
            fputcsv($handle, $row, ';');
        }
        fclose($handle);
        
        json_response([
            'success' => true,
            'message' => 'Exportação concluída',
            'file' => '/exports/' . $filename,
            'rows' => count($rows)
        ]);
    } else {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>CATFLOW - ' . ucfirst($type) . '</title>';
        $html .= '<style>body{font-family:Arial,sans-serif;padding:20px}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#14b8a6;color:#fff}tr:nth-child(even){background:#f9f9f9}</style></head><body>';
        $html .= '<h1>CATFLOW - ' . ucfirst($type) . '</h1><p>Exportado em: ' . date('d/m/Y H:i') . '</p>';
        $html .= '<table><thead><tr>';
        foreach ($headers as $h) $html .= '<th>' . $h . '</th>';
        $html .= '</tr></thead><tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) $html .= '<td>' . htmlspecialchars($cell ?? '') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';
        
        $filepath = $export_dir . '/' . $filename;
        file_put_contents($filepath, $html);
        
        json_response([
            'success' => true,
            'message' => 'Exportação concluída',
            'file' => '/exports/' . $filename,
            'rows' => count($rows)
        ]);
    }
} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Erro ao exportar: ' . $e->getMessage()], 500);
}