<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/functions.php';

use App\Action\User\ListUsersAction;
use App\Domain\User\UserService;
use App\Infrastructure\Persistence\PdoUserRepository;
use App\Responder\UserResponder;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__ . '/../'), '.env.local');
$dotenv->load();

// Primitive DI — swap for a real container in production
$pdo = new \PDO('sqlite::memory:');
$repo = new PdoUserRepository($pdo);
$service = new UserService($repo);
$responder = new UserResponder();
$action = new ListUsersAction($service, $responder);

// Rudimentary router
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/users' && $method === 'GET') {
  $action($_SERVER, $_GET, $_POST);
} else {
  http_response_code(404);
  echo '404 Not found';
}