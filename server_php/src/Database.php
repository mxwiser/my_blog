<?php

declare(strict_types=1);

namespace Blog;

use PDO;
use RuntimeException;

final class Database
{
    public static function connect(array $config, bool $mustExist = true): PDO
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            throw new ApiException(503, 'sqlite_driver_missing', 'PHP 未启用 pdo_sqlite 扩展。');
        }

        $path = $config['databasePath'];
        if ($mustExist && !is_file($path)) {
            throw new ApiException(503, 'database_not_initialized', '数据库尚未初始化，请运行 scripts/init.php。');
        }
        if (!$mustExist && !is_dir(dirname($path))) {
            if (!mkdir(dirname($path), 0770, true) && !is_dir(dirname($path))) {
                throw new RuntimeException('无法创建数据库目录。');
            }
        }

        $pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        self::assertVersion($pdo, $config['minimumSqliteVersion']);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA busy_timeout = 5000');

        return $pdo;
    }

    public static function assertVersion(PDO $pdo, string $minimumVersion): void
    {
        $version = (string) $pdo->query('SELECT sqlite_version()')->fetchColumn();
        if (version_compare($version, $minimumVersion, '<')) {
            throw new ApiException(
                503,
                'sqlite_version_unsupported',
                "PHP 当前加载 SQLite {$version}，需要 {$minimumVersion} 或更新版本。",
            );
        }
    }
}
