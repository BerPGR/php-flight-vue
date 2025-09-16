<?php
namespace App\Services;

use App\Repositories\ProjectRepository;
use InvalidArgumentException;
use PDO;

class ProjectService
{
  public function __construct(private PDO $pdo) {}

  public function list(): array
  {
    $repo = new ProjectRepository($this->pdo);
    return $repo->all();
  }

  public function create($name, $status): array
  {
    if (!$name || strlen(trim($name)) < 3) {
      throw new InvalidArgumentException('name é obrigatório (mín. 3 caracteres)');
    }

    $allowed = ['active','paused','done'];
    if (!in_array($status, $allowed, true)) {
      throw new InvalidArgumentException('status inválido (use: active|paused|done)');
    }

    $repo = new ProjectRepository($this->pdo);
    $id = $repo->insert($name, $status);

    return ['id' => $id, 'name' => $name, 'status' => $status];
  }
}
