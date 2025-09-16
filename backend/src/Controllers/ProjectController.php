<?php
namespace App\Controllers;

use App\Services\ProjectService;
use Flight;
use PDO;

class ProjectController
{
  public function __construct(private PDO $pdo) {}

  // GET /api/projects
  public function index(): void
  {
    $service = new ProjectService($this->pdo);
    $projects = $service->list();
    error_log('[projects.index] rows=' . count($projects));
    Flight::json($projects, 200);
  }

  // POST /api/projects
  public function store(): void
  {
    $req = Flight::request();

    $raw = $req->getBody() ?? '';
    error_log('[projects.store] raw_body=' . $raw);

    // tenta JSON
    $payload = json_decode($raw, true);
    // se não for JSON, tenta pegar form-data/x-www-form-urlencoded
    if (!is_array($payload) || empty($payload)) {
      $payload = $req->data ? $req->data->getData() : [];
    }
    error_log('[projects.store] payload=' . json_encode($payload, JSON_UNESCAPED_UNICODE));

    $name   = isset($payload['name']) ? (string) $payload['name'] : '';
    $status = isset($payload['status']) ? (string) $payload['status'] : 'active';

    $service = new ProjectService($this->pdo);
    $created = $service->create($name, $status);

    error_log('[projects.store] created=' . json_encode($created));
    Flight::json($created, 201);
  }
}
