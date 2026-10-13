<?php

declare(strict_types=1);

namespace Blog;

use PDO;

final class ArticleRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /** 标签精确匹配；关键词匹配标题、摘要和标签，所有用户输入均绑定参数。 */
    public function all(string $tag = '', string $query = ''): array
    {
        $conditions = [];
        $parameters = [];
        if ($tag !== '') {
            $conditions[] = 'EXISTS (
                SELECT 1 FROM article_tags at
                JOIN tags t ON t.id = at.tag_id
                WHERE at.article_id = a.id AND t.name = :tag
            )';
            $parameters['tag'] = $tag;
        }
        if ($query !== '') {
            // %、_、反斜杠作为普通搜索字符，避免被当成 LIKE 通配符。
            $needle = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query) . '%';
            $conditions[] = "(a.title LIKE :title ESCAPE '\\'
                OR a.excerpt LIKE :excerpt ESCAPE '\\'
                OR EXISTS (
                    SELECT 1 FROM article_tags at JOIN tags t ON t.id = at.tag_id
                    WHERE at.article_id = a.id AND t.name LIKE :tagQuery ESCAPE '\\'
                ))";
            $parameters += ['title' => $needle, 'excerpt' => $needle, 'tagQuery' => $needle];
        }

        $where = $conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions);
        $statement = $this->pdo->prepare('SELECT a.* FROM articles a' . $where . ' ORDER BY a.published_at DESC, a.id DESC');
        $statement->execute($parameters);
        $rows = $statement->fetchAll();
        $tags = $this->tagsByArticle();

        return array_map(fn (array $row): array => $this->toArticle($row, $tags[(int) $row['id']] ?? []), $rows);
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM articles WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        if ($row === false) {
            return null;
        }
        $statement = $this->pdo->prepare('SELECT t.name FROM tags t
            JOIN article_tags at ON at.tag_id = t.id
            WHERE at.article_id = :id ORDER BY at.position, t.id');
        $statement->execute(['id' => $id]);

        return $this->toArticle($row, $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    public function tags(): array
    {
        $rows = $this->pdo->query('SELECT t.name, COUNT(at.article_id) AS count
            FROM tags t JOIN article_tags at ON at.tag_id = t.id
            GROUP BY t.id ORDER BY count DESC, t.id')->fetchAll();
        return array_map(fn (array $row): array => [
            'name' => $row['name'],
            'count' => (int) $row['count'],
        ], $rows);
    }

    private function tagsByArticle(): array
    {
        $rows = $this->pdo->query('SELECT at.article_id, t.name FROM article_tags at
            JOIN tags t ON t.id = at.tag_id ORDER BY at.article_id, at.position, t.id')->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $result[(int) $row['article_id']][] = $row['name'];
        }
        return $result;
    }

    private function toArticle(array $row, array $tags): array
    {
        return [
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'excerpt' => $row['excerpt'],
            'date' => $row['published_at'],
            'readMinutes' => (int) $row['read_minutes'],
            'cover' => $row['cover'],
            'tags' => $tags,
            'sections' => json_decode($row['sections_json'], true, 512, JSON_THROW_ON_ERROR),
            'isFeatured' => (bool) $row['is_featured'],
            'isSample' => (bool) $row['is_sample'],
        ];
    }
}
