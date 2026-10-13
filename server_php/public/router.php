<?php

declare(strict_types=1);

// 仅允许 public 内的文件由开发服务器直接提供，数据库始终在站点根目录之外。
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$file = is_string($path) ? realpath(__DIR__ . '/' . $path) : false;
if ($file !== false && str_starts_with($file, __DIR__ . DIRECTORY_SEPARATOR) && is_file($file)) {
    return false;
}
require __DIR__ . '/index.php';
