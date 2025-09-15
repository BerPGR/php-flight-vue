<?php
use App\Services\ProjectService;
use Flight;
use PDO;

class ProjectController {
    public function __construct(
        private PDO $pdo,
    ) {}

    public function index() {
        $service = new ProjectService($this->pdo);
        $projects = $service->list();
        Flight::json($projects, 200);
    }

    public function store() {
        $payload = json_decode(Flight::request()->getBody(), true);

        $service = new ProjectService($this->pdo);
        $created = $service->create($payload['name'] ?? null, $payload['status'] ?? 'active');
        if ($created) {
            Flight::json($created, 201);
        }
    }
}