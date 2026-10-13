<?php

declare(strict_types=1);

return [
    'databasePath' => getenv('BLOG_DATABASE_PATH') ?: __DIR__ . '/storage/blog.sqlite',
    // PHP 实际加载的 SQLite 库须满足要求，不能仅依赖 sqlite3 命令行版本。
    'minimumSqliteVersion' => '3.54.0',
];
