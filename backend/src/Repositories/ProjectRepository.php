<?php

use PDO;

class ProjectRepository {
    public function __construct(private PDO $pdo) {}

    public function all() {
        $stmt = $this->pdo->query("SELECT id, name, status, created_at FROM project WHERE id = :id");
        return $stmt->fetchAll();
    }

    public function insert(string $name, string $status) {
        $stmt = $this->pdo->prepare("INSERT INTO projects (name, status, created_at VALUES (:name, :status, NOW());");
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":status", $status);
        $stmt->execute();
        return (int) $this->pdo->lastInsertId();
    }
}