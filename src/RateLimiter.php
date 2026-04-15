<?php
class RateLimiter {
    private static function getStorageFile(string $key): string {
        $dir = sys_get_temp_dir() . '/catflow_ratelimit';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir . '/' . md5($key) . '.json';
    }

    public static function check(string $identifier, int $maxAttempts = 60, int $windowSeconds = 60): array {
        $file = self::getStorageFile($identifier);
        $now = time();
        $data = ['attempts' => [], 'blocked_until' => null];
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true) ?: $data;
        }

        if ($data['blocked_until'] && $now < $data['blocked_until']) {
            $remaining = $data['blocked_until'] - $now;
            return [
                'allowed' => false,
                'remaining' => $remaining,
                'message' => "Aguarde {$remaining} segundos"
            ];
        }

        $data['attempts'] = array_filter($data['attempts'], function($timestamp) use ($now, $windowSeconds) {
            return ($now - $timestamp) < $windowSeconds;
        });

        if (count($data['attempts']) >= $maxAttempts) {
            $data['blocked_until'] = $now + 60;
            file_put_contents($file, json_encode($data));
            return [
                'allowed' => false,
                'remaining' => 60,
                'message' => 'Muitas tentativas. Tente novamente em 1 minuto.'
            ];
        }

        $data['attempts'][] = $now;
        file_put_contents($file, json_encode($data));
        
        return [
            'allowed' => true,
            'remaining' => $maxAttempts - count($data['attempts'])
        ];
    }

    public static function middleware(string $key = 'default', int $maxAttempts = 30, int $windowSeconds = 60): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $result = self::check($ip . '_' . $key, $maxAttempts, $windowSeconds);
        
        if (!$result['allowed']) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $result['message'],
                'retry_after' => $result['remaining']
            ]);
            exit;
        }
    }
}
