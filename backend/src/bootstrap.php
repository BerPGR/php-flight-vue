<?php

use Dotenv\Dotenv;
use Flight;
use PDO;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

Flight::set('config', [
    'app_env' => $_ENV['APP_ENV'] ?? "dev",
    'front_origin' => $_ENV['APP_FRONT_ORIGIN'] ?? 'http://localhost:5173',
    'db' => [
        'dsn' => $_ENV['DB_DSN'] ?? 'sqlite:' . __DIR__ . "/../banco.sqlite",
        'user' => $_ENV['user'] ?? 'root',
        'pass' => $_ENV['password'] ?? '',
    ],
]);

Flight::before('start', function () {
    $conf = Flight::get('config');
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: ' . $conf['front_origin']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') { http_response_code(204); exit; }
  });
  

$config = Flight::get('config');
Flight::register('pdo', PDO::class, [
    $config['db']['dsn'],
    null,
    null,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
], function (PDO $pdo) {
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->exec('PRAGMA synchronous = NORMAL');
    $pdo->exec('PRAGMA budy_timeout = 5000');
});

Flight::map('error',function(Throwable $e) {
    $isDev = Flight::get('config')['app_env'] === 'dev';
    $body = [
        'error' => true,
        'message' => $e->getMessage(),
    ];
    if ($isDev) {
        $body['type'] = get_class($e);
        $body['file'] = $e->getFile();
        $body['line'] = $e->getLine();
    }
    Flight::json($body, 500);
});

Flight::map('notFound', function() {
    Flight::json(['error' => true, 'message' => 'Rota não encontrada'], 404);
});

require __DIR__ .'/Routes.php';