<?php
declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = [], bool $useLayout = true): void
    {
        $viewPath = APP_PATH . '/Views/' . $view . '.php';

        if (!is_file($viewPath)) {
            http_response_code(500);
            echo 'View nao encontrada.';
            return;
        }

        extract($data, EXTR_SKIP);

        if ($useLayout) {
            require_once APP_PATH . '/Views/layout/header.php';
            require_once $viewPath;
            require_once APP_PATH . '/Views/layout/footer.php';
            return;
        }

        require_once $viewPath;
    }
}
