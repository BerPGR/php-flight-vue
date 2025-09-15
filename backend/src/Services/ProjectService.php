<?php

use PDO;

class ProjectService {
    public function __construct(private PDO $pdo) {}

    public function list(): array {
        $repo = new ProjectRepository($this->pdo);
        return $repo->all();
    }

    public function create(?string $name, string $status) {
        if (!$name || strlen(trim($name)) < 3) {
            throw new InvalidArgumentException('Name é obrigatório (Min. 3 caracteres)');
        }

        $repo = new ProjectRepository($this->pdo);
        $id = $repo->insert($name, $status);
        return [
            'id' => $id,
            'name' => $name,
            'status'=> $status
        ];
    }
}