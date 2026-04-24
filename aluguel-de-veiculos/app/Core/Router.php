<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    /**
     * Registra uma rota.
     *
     * @param string   $method      Método HTTP (GET, POST, …)
     * @param string   $route       Identificador da rota
     * @param array    $handler     [ControllerClass::class, 'metodo']
     * @param string[] $middlewares Lista de classes de middleware a executar antes do controller
     */
    public function add(string $method, string $route, array $handler, array $middlewares = []): void
    {
        $normalizedRoute = $this->normalizeRoute($route);
        $normalizedMethod = strtoupper($method);

        $this->routes[$normalizedMethod][$normalizedRoute] = [
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(string $method, string $route): void
    {
        $normalizedRoute = $this->normalizeRoute($route);
        $normalizedMethod = strtoupper($method);

        $entry = $this->routes[$normalizedMethod][$normalizedRoute] ?? null;

        if (!is_array($entry)) {
            http_response_code(404);
            echo 'Pagina nao encontrada.';
            return;
        }

        $handler     = $entry['handler'];
        $middlewares = $entry['middlewares'];

        if (!is_array($handler) || count($handler) !== 2) {
            http_response_code(404);
            echo 'Pagina nao encontrada.';
            return;
        }

        // Executa cada middleware antes de despachar para o controller
        foreach ($middlewares as $middlewareClass) {
            if (!class_exists($middlewareClass)) {
                throw new RoutingException('Middleware nao encontrado: ' . $middlewareClass);
            }

            $middleware = new $middlewareClass();

            if (!method_exists($middleware, 'handle')) {
                throw new RoutingException('Metodo handle() nao encontrado no middleware: ' . $middlewareClass);
            }

            $middleware->handle();
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
