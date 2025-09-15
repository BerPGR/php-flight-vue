<?php

use App\Controllers\ProjectController;
use Flight;


Flight::route('GET /api/health', function () {
    Flight::json(['ok' => true, 'time' => date('c')]);
});


// Lista projetos
Flight::route('GET /api/projects', function () {
    (new ProjectController(Flight::pdo()))->index();
});


// Cria projeto
Flight::route('POST /api/projects', function () {
    (new ProjectController(Flight::pdo()))->store();
});

// Qual arquivo de DB está aberto? Tabela existe? Quantas linhas?
Flight::route('GET /api/_dbcheck', function () {
    $pdo = Flight::pdo();

    $dbList = $pdo->query('PRAGMA database_list')->fetchAll();
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll();

    $projectsCount = 0;
    try {
        $projectsCount = (int) $pdo->query('SELECT COUNT(*) AS c FROM projects')->fetch()['c'];
    } catch (\Throwable $e) {
    }

    Flight::json([
        'database_list' => $dbList,     // mostra o caminho absoluto do arquivo
        'tables'        => $tables,     // lista tabelas existentes
        'projects_count' => $projectsCount
    ], 200);
});

// Inserção de teste por GET (isola problema de leitura de body)
Flight::route('GET /api/_insert_test', function () {
    $name   = $_GET['name']   ?? 'Teste via GET';
    $status = $_GET['status'] ?? 'active';
    $pdo = Flight::pdo();
    $pdo->prepare('INSERT INTO projects (name, status) VALUES (?, ?)')->execute([$name, $status]);
    Flight::json(['ok' => true], 201);
});
