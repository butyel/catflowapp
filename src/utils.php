<?php
// src/utils.php
// Funções utilitárias diversas

function sanitize_input($data)
{
    return htmlspecialchars(strip_tags(trim($data)));
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
?>
