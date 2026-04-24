<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\DashboardModel;
use PDOException;

class DashboardController extends Controller
{
    public function index(): void
    {
        startSessionIfNeeded();

        $databaseReady = true;
        $dbErrorMessage = '';
        $totalUsuarios = 0;
        $totalVeiculos = 0;
        $veiculosDisponiveis = 0;
        $alugueisAtivos = 0;
        $ultimosAlugueis = [];

        try {
            $model = new DashboardModel(getConnection());
            $stats = $model->getStats();
            $ultimosAlugueis = $model->getRecentRentals();

            $totalUsuarios = $stats['totalUsuarios'];
            $totalVeiculos = $stats['totalVeiculos'];
            $veiculosDisponiveis = $stats['veiculosDisponiveis'];
            $alugueisAtivos = $stats['alugueisAtivos'];
        } catch (PDOException $exception) {
            $databaseReady = false;
            $dbErrorMessage = 'Nao foi possivel inicializar o banco automaticamente. Verifique permissao de acesso e credenciais do MySQL.';
        }

        $this->render('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'databaseReady' => $databaseReady,
            'dbErrorMessage' => $dbErrorMessage,
            'totalUsuarios' => $totalUsuarios,
            'totalVeiculos' => $totalVeiculos,
            'veiculosDisponiveis' => $veiculosDisponiveis,
            'alugueisAtivos' => $alugueisAtivos,
            'ultimosAlugueis' => $ultimosAlugueis,
        ]);
    }
}
