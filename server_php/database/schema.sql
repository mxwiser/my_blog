PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS articles (
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    excerpt TEXT NOT NULL,
    published_at TEXT NOT NULL,
    read_minutes INTEGER NOT NULL CHECK (read_minutes > 0),
    cover TEXT NOT NULL CHECK (cover IN ('landscape', 'code', 'design', 'coffee', 'notes')),
    sections_json TEXT NOT NULL CHECK (json_valid(sections_json)),
    is_featured INTEGER NOT NULL DEFAULT 0 CHECK (is_featured IN (0, 1)),
    is_sample INTEGER NOT NULL DEFAULT 0 CHECK (is_sample IN (0, 1))
);

CREATE TABLE IF NOT EXISTS tags (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS article_tags (
    article_id INTEGER NOT NULL REFERENCES articles(id) ON DELETE CASCADE,
    tag_id INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE,
    position INTEGER NOT NULL DEFAULT 0 CHECK (position >= 0),
    PRIMARY KEY (article_id, tag_id)
);

CREATE INDEX IF NOT EXISTS articles_published_at ON articles(published_at DESC, id DESC);
CREATE INDEX IF NOT EXISTS article_tags_tag_id ON article_tags(tag_id, article_id);

PRAGMA user_version = 1;
