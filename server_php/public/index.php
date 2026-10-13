<?php

declare(strict_types=1);

use Blog\ApiException;
use Blog\ArticleRepository;
use Blog\Database;
use Blog\Http;

$config = require dirname(__DIR__) . '/bootstrap.php';

try {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (!is_string($path) || !str_starts_with($path, '/api/')) {
        throw new ApiException(404, 'not_found', '接口不存在。');
    }
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        header('Allow: GET');
        throw new ApiException(405, 'method_not_allowed', '此接口仅支持 GET 请求。');
    }

    // 健康检查使用内存数据库，即使磁盘数据库尚未初始化也能检查实际版本。
    if ($path === '/api/health') {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            throw new ApiException(503, 'sqlite_driver_missing', 'PHP 未启用 pdo_sqlite 扩展。');
        }
        $pdo = new PDO('sqlite::memory:');
        Database::assertVersion($pdo, $config['minimumSqliteVersion']);
        Http::json(['data' => [
            'status' => 'ok',
            'phpVersion' => PHP_VERSION,
            'sqliteVersion' => $pdo->query('SELECT sqlite_version()')->fetchColumn(),
            'databaseInitialized' => is_file($config['databasePath']),
        ]]);
    }

    if ($path !== '/api/articles' && $path !== '/api/tags' && !preg_match('~^/api/articles/[1-9][0-9]*$~', $path)) {
        throw new ApiException(404, 'not_found', '接口不存在。');
    }
    $repository = new ArticleRepository(Database::connect($config));
    if ($path === '/api/articles') {
        $articles = $repository->all(Http::queryString('tag'), Http::queryString('q', 500));
        Http::json(['data' => $articles, 'total' => count($articles)]);
    }
    if ($path === '/api/tags') {
        Http::json(['data' => $repository->tags()]);
    }

    $id = filter_var(substr($path, strlen('/api/articles/')), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        throw new ApiException(400, 'invalid_id', '文章编号不合法。');
    }
    $article = $repository->find($id);
    if ($article === null) {
        throw new ApiException(404, 'article_not_found', '文章不存在。');
    }
    Http::json(['data' => $article]);
} catch (ApiException $error) {
    Http::json(['error' => ['code' => $error->errorCode, 'message' => $error->getMessage()]], $error->status);
} catch (Throwable $error) {
    error_log((string) $error);
    Http::json(['error' => ['code' => 'internal_error', 'message' => '服务暂时不可用，请稍后重试。']], 500);
}
