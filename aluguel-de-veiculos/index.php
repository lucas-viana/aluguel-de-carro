<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Core/Bootstrap.php';

use App\Controllers\AluguelController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UsuarioController;
use App\Controllers\VeiculoController;
use App\Core\Router;

$route = (string) ($_GET['route'] ?? 'dashboard');

$router = new Router();
$router->add('GET', 'dashboard', [DashboardController::class, 'index']);
$router->add('GET', 'usuarios', [UsuarioController::class, 'index']);
$router->add('POST', 'usuarios', [UsuarioController::class, 'index']);
$router->add('GET', 'veiculos', [VeiculoController::class, 'index']);
$router->add('POST', 'veiculos', [VeiculoController::class, 'index']);
$router->add('GET', 'alugueis', [AluguelController::class, 'index']);
$router->add('POST', 'alugueis', [AluguelController::class, 'index']);
$router->add('GET', 'login', [AuthController::class, 'login']);
$router->add('POST', 'login', [AuthController::class, 'login']);

$router->dispatch((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'), $route);
