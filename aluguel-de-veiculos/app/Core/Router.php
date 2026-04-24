<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $route, array $handler): void
    {
        $normalizedRoute = $this->normalizeRoute($route);
        $normalizedMethod = strtoupper($method);

        $this->routes[$normalizedMethod][$normalizedRoute] = $handler;
    }

    public function dispatch(string $method, string $route): void
    {
        $normalizedRoute = $this->normalizeRoute($route);
        $normalizedMethod = strtoupper($method);

        $handler = $this->routes[$normalizedMethod][$normalizedRoute] ?? null;

        if (!is_array($handler) || count($handler) !== 2) {
            http_response_code(404);
            echo 'Pagina nao encontrada.';
            return;
        }

        [$controllerClass, $controllerMethod] = $handler;

        if (!class_exists($controllerClass)) {
            throw new RoutingException('Controller nao encontrado: ' . $controllerClass);
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $controllerMethod)) {
            throw new RoutingException('Metodo nao encontrado no controller: ' . $controllerMethod);
        }

        $controller->{$controllerMethod}();
    }

    private function normalizeRoute(string $route): string
    {
        $trimmed = trim($route);
        $trimmed = trim($trimmed, '/');

        if ($trimmed === '') {
            return 'dashboard';
        }

        return $trimmed;
    }
}
