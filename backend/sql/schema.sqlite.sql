CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    status TEXT NOT NULL CHECK (status IN ('active', 'paused', 'done')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);