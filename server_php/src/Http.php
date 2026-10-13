<?php

declare(strict_types=1);

namespace Blog;

final class Http
{
    public static function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        exit;
    }

    public static function queryString(string $name, int $maxLength = 200): string
    {
        $value = $_GET[$name] ?? '';
        if (!is_string($value) || strlen($value) > $maxLength) {
            throw new ApiException(400, 'invalid_parameter', "参数 {$name} 不合法或过长。");
        }
        return trim($value);
    }
}
