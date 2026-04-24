<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Core/Bootstrap.php';

use App\Controllers\AluguelController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UsuarioController;
use App\Controllers\VeiculoController;
use App\Core\Router;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

$route = (string) ($_GET['route'] ?? 'dashboard');

$router = new Router();

// Middlewares reutilizáveis
$auth      = [AuthMiddleware::class];
$authAdmin = [AuthMiddleware::class, AdminMiddleware::class];

// Rotas exclusivas para administradores
$router->add('GET',  'dashboard',      [DashboardController::class, 'index'], $authAdmin);
$router->add('GET',  'usuarios',       [UsuarioController::class,  'index'], $authAdmin);
$router->add('POST', 'usuarios',       [UsuarioController::class,  'index'], $authAdmin);
$router->add('GET',  'usuarios/edit',  [UsuarioController::class,  'edit'],  $authAdmin);
$router->add('POST', 'usuarios/edit',  [UsuarioController::class,  'edit'],  $authAdmin);
$router->add('GET',  'veiculos',       [VeiculoController::class,  'index'], $authAdmin);
$router->add('POST', 'veiculos',       [VeiculoController::class,  'index'], $authAdmin);

// Rotas acessíveis por todos os usuários autenticados (admin + comum)
$router->add('GET',  'alugueis', [AluguelController::class, 'index'], $auth);
$router->add('POST', 'alugueis', [AluguelController::class, 'index'], $auth);

// Rotas públicas — sem middleware
$router->add('GET',  'login',    [AuthController::class, 'login']);
$router->add('POST', 'login',    [AuthController::class, 'login']);
$router->add('GET',  'register', [AuthController::class, 'register']);
$router->add('POST', 'register', [AuthController::class, 'register']);

$router->dispatch((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'), $route);
