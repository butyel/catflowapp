<?php
// src/utils.php
// Funcoes utilitarias diversas

function sanitize_input($data)
{
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function json_response($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function format_currency($value)
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function format_date($date)
{
    return date('d/m/Y', strtotime($date));
}

function require_csrf()
{
    if (!isset($_SESSION['csrf_token'])) {
        return;
    }
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        json_response(['success' => false, 'message' => 'Token de seguranca invalido'], 403);
    }
}

function verify_gato_ownership($pdo, $gato_id, $user_id)
{
    $stmt = $pdo->prepare("SELECT user_id FROM gatos WHERE id = ?");
    $stmt->execute([$gato_id]);
    $row = $stmt->fetch();
    if (!$row) {
        json_response(['success' => false, 'message' => 'Gato nao encontrado'], 404);
    }
    if ($row['user_id'] != $user_id && !is_admin()) {
        json_response(['success' => false, 'message' => 'Acesso negado'], 403);
    }
    return $row;
}
?>
