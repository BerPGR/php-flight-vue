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
