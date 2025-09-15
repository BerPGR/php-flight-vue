<?php
namespace App\Repositories;

use PDO;
use RuntimeException;

class ProjectRepository
{
  /** @var PDO */
  private $pdo;

  public function __construct(PDO $pdo) { $this->pdo = $pdo; }

  public function all(): array
  {
    $stmt = $this->pdo->query(
      'SELECT id, name, status, created_at FROM projects ORDER BY created_at DESC'
    );
    return $stmt->fetchAll();
  }

  public function insert(string $name, string $status): int
  {
    $stmt = $this->pdo->prepare(
      'INSERT INTO projects (name, status) VALUES (:name, :status)'
    );
    $ok = $stmt->execute([':name' => $name, ':status' => $status]);

    if (!$ok) {
      $err = $stmt->errorInfo();
      throw new RuntimeException('INSERT falhou: ' . ($err[2] ?? 'motivo desconhecido'));
    }

    $id = (int) $this->pdo->lastInsertId();
    if ($id <= 0) {
      // não deveria acontecer com INTEGER PRIMARY KEY AUTOINCREMENT
      throw new RuntimeException('lastInsertId() retornou 0');
    }
    return $id;
  }
}
