<?php
class ApiResponse {
    public static function success($data = null, string $message = 'Operação realizada com sucesso'): array {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function error(string $message, int $code = 400, $data = null): array {
        http_response_code($code);
        return [
            'success' => false,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function json($response): void {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public static function successJson($data = null, string $message = 'Operação realizada com sucesso'): void {
        self::json(self::success($data, $message));
    }

    public static function errorJson(string $message, int $code = 400, $data = null): void {
        self::json(self::error($message, $code, $data));
    }
}
