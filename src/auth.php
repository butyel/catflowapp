<?php
// src/auth.php
// Funções de Autenticação e Middleware
session_start();
date_default_timezone_set('America/Sao_Paulo');

function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

function require_admin()
{
    require_login();
    if ($_SESSION['user_role'] !== 'admin') {
        header('HTTP/1.0 403 Forbidden');
        echo "Acesso Negado.";
        exit;
    }
}

function get_logged_user_id()
{
    return $_SESSION['user_id'] ?? null;
}

function is_admin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function login_user($id, $nome, $role, $foto = null)
{
    $_SESSION['user_id'] = $id;
    $_SESSION['user_nome'] = $nome;
    $_SESSION['user_role'] = $role;
    $_SESSION['user_foto'] = $foto;
}

function logout_user()
{
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
