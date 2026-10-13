<?php

declare(strict_types=1);

use Blog\Database;

$config = require dirname(__DIR__) . '/bootstrap.php';
$pdo = null;

try {
    $pdo = Database::connect($config, false);
    $version = (int) $pdo->query('PRAGMA user_version')->fetchColumn();
    if ($version > 1) {
        throw new RuntimeException('数据库结构版本高于当前程序，停止初始化。');
    }
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->beginTransaction();
    $pdo->exec(file_get_contents(dirname(__DIR__) . '/database/schema.sql'));

    $inserted = 0;
    if (in_array('--seed', $argv, true) && (int) $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn() === 0) {
        $seed = json_decode(file_get_contents(dirname(__DIR__) . '/database/seed.json'), true, 512, JSON_THROW_ON_ERROR);
        $articleStatement = $pdo->prepare('INSERT INTO articles
            (id, title, excerpt, published_at, read_minutes, cover, sections_json, is_featured, is_sample)
            VALUES (:id, :title, :excerpt, :date, :readMinutes, :cover, :sections, :isFeatured, :isSample)');
        $tagStatement = $pdo->prepare('INSERT INTO tags (name) VALUES (:name) ON CONFLICT(name) DO NOTHING');
        $tagLookup = $pdo->prepare('SELECT id FROM tags WHERE name = :name');
        $linkStatement = $pdo->prepare('INSERT INTO article_tags (article_id, tag_id, position) VALUES (:article, :tag, :position)');

        foreach ($seed as $article) {
            $articleStatement->execute([
                'id' => $article['id'], 'title' => $article['title'], 'excerpt' => $article['excerpt'],
                'date' => $article['date'], 'readMinutes' => $article['readMinutes'], 'cover' => $article['cover'],
                'sections' => json_encode($article['sections'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'isFeatured' => (int) $article['isFeatured'], 'isSample' => (int) $article['isSample'],
            ]);
            foreach ($article['tags'] as $position => $name) {
                $tagStatement->execute(['name' => $name]);
                $tagLookup->execute(['name' => $name]);
                $linkStatement->execute(['article' => $article['id'], 'tag' => $tagLookup->fetchColumn(), 'position' => $position]);
            }
            $inserted++;
        }
    }

    $pdo->commit();
    echo json_encode([
        'sqliteVersion' => $pdo->query('SELECT sqlite_version()')->fetchColumn(),
        'insertedArticles' => $inserted,
        'totalArticles' => (int) $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn(),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . PHP_EOL;
} catch (Throwable $error) {
    if ($pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}
