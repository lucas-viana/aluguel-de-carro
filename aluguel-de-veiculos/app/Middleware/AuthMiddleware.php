<?php
declare(strict_types=1);

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Verifica se o usuário está autenticado.
     * Caso não esteja, redireciona para a página de login.
     */
    public function handle(): void
    {
        startSessionIfNeeded();

        if (!isset($_SESSION['usuario_id'])) {
            setFlash('error', 'Você precisa estar logado para acessar esta página.');
            redirect('index.php?route=login');
        }
    }
}
