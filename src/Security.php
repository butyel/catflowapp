<?php
class Security {
    public static function generateCSRFToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken(?string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
    }

    public static function getCSRFField(): string {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    public static function sanitizeInput(string $value): string {
        $value = trim($value);
        $value = stripslashes($value);
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeArray(array $array): array {
        return array_map([self::class, 'sanitizeInput'], $array);
    }

    public static function validateRequired(array $fields, array $data): ?string {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                return "Campo obrigatório: {$field}";
            }
        }
        return null;
    }

    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validateDate(string $date, string $format = 'Y-m-d'): bool {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function validateNumeric(string $value): bool {
        return is_numeric($value);
    }

    public static function setSecurityHeaders(): void {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }

    public static function rateLimit(string $key, int $maxAttempts = 60, int $windowSeconds = 60): bool {
        $cacheFile = sys_get_temp_dir() . '/ratelimit_' . md5($key);
        $data = file_exists($cacheFile) ? json_decode(file_get_contents($cacheFile), true) : ['count' => 0, 'time' => time()];
        
        if (time() - $data['time'] > $windowSeconds) {
            $data = ['count' => 0, 'time' => time()];
        }
        
        $data['count']++;
        file_put_contents($cacheFile, json_encode($data));
        
        return $data['count'] <= $maxAttempts;
    }
}

function csrf_field(): string {
    return Security::getCSRFField();
}
