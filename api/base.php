<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/Security.php';
require_once __DIR__ . '/../src/ApiResponse.php';
require_once __DIR__ . '/../src/RateLimiter.php';

Security::setSecurityHeaders();

function requireAuth(): void {
    if (!isset($_SESSION['user_id'])) {
        ApiResponse::errorJson('Unauthorized', 401);
    }
}

function requireAdmin(): void {
    requireAuth();
    if ($_SESSION['user_role'] !== 'admin') {
        ApiResponse::errorJson('Forbidden', 403);
    }
}

function validateCSRF(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!Security::validateCSRFToken($token)) {
        ApiResponse::errorJson('Token de segurança inválido', 403);
    }
}

function validatePost(array $required): void {
    $error = Security::validateRequired($required, $_POST);
    if ($error) {
        ApiResponse::errorJson($error, 400);
    }
}

function sanitizePost(array $fields): array {
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = $_POST[$field] ?? '';
    }
    return Security::sanitizeArray($data);
}

function rateLimit(string $key = 'api', int $max = 30): void {
    RateLimiter::middleware($key, $max, 60);
}
