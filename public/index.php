<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/functions.php';

use App\Action\User\ListUsersAction;
use App\Core\TwigResponse;
use App\Domain\User\UserService;
use App\Infrastructure\Persistence\PdoUserRepository;
use App\Infrastructure\Factory\TwigFactory;
use App\Responder\UserResponder;
use FastRoute\RouteCollector;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__ . '/../'), '.env.local');
$dotenv->load();

// PDO connection
$pdo = new \PDO(
  env('db.dsn'),
  env('db.username'),
  env('db.password'),
  [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]
);

// Twig
TwigResponse::setTwig(TwigFactory::create());

// Router
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) use ($pdo) {
  $r->get('/users', new ListUsersAction(
    new UserService(new PdoUserRepository($pdo)),
    new UserResponder()
  ));
});

// Dispatch
$routeInfo = $dispatcher->dispatch(
  $_SERVER['REQUEST_METHOD'],
  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

match ($routeInfo[0]) {
  FastRoute\Dispatcher::FOUND => ($routeInfo[1])($_SERVER, $_GET, $_POST),
  FastRoute\Dispatcher::NOT_FOUND => (function () {
    http_response_code(404);
    echo '404 Not found';
  })(),
  FastRoute\Dispatcher::METHOD_NOT_ALLOWED => (function () {
    http_response_code(405);
    echo '405 Method not allowed';
  })(),
};