<?php
declare(strict_types=1);

namespace App\Middleware;

class AdminMiddleware
{
    /**
     * Verifica se o usuário autenticado é do tipo administrador.
     * Caso contrário, redireciona com mensagem de acesso negado.
     */
    public function handle(): void
    {
        startSessionIfNeeded();

        if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {
            setFlash('error', 'Acesso restrito. Apenas administradores podem acessar esta area.');
            redirect('index.php?route=alugueis');
        }
    }
}
