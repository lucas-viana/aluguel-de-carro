<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AluguelOperationException;
use App\Models\AluguelModel;
use App\Models\UsuarioModel;
use App\Models\VeiculoModel;
use PDO;
use PDOException;

class AluguelController extends Controller
{
    public function index(): void
    {
        startSessionIfNeeded();

        $formData = [
            'usuario_id' => '',
            'veiculo_id' => '',
            'data_retirada' => (new \DateTimeImmutable('today'))->format('Y-m-d'),
            'data_entrega' => (new \DateTimeImmutable('today +1 day'))->format('Y-m-d'),
            'forma_pagamento' => 'pix',
        ];
        $errors = [];
        $usuarios = [];
        $veiculosDisponiveis = [];
        $alugueis = [];
        $databaseReady = true;
        $dbErrorMessage = '';
        $pdo = null;

        try {
            $pdo = getConnection();
        } catch (PDOException $exception) {
            $databaseReady = false;
            $dbErrorMessage = 'Falha na conexao com o MySQL. Verifique DB_HOST, DB_NAME, DB_USER e DB_PASS.';
        }

        if ($databaseReady && $pdo instanceof PDO && isPostRequest()) {
            $aluguelModel = new AluguelModel($pdo);
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                [$clean, $errors] = validateAluguel($_POST);
                $formData = array_merge($formData, $clean);

                if (empty($errors)) {
                    try {
                        $domainErrors = $aluguelModel->create($clean);

                        if (empty($domainErrors)) {
                            setFlash('success', 'Aluguel registrado com sucesso.');
                            redirect('index.php?route=alugueis');
                        }

                        $errors = array_merge($errors, $domainErrors);
                    } catch (AluguelOperationException $exception) {
                        $errors['geral'] = 'Erro ao registrar aluguel.';
                    }
                }
            }

            if ($action === 'finalizar') {
                $aluguelId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

                if ($aluguelId === false || $aluguelId <= 0) {
                    setFlash('error', 'Aluguel invalido para finalizacao.');
                    redirect('index.php?route=alugueis');
                }

                try {
                    $result = $aluguelModel->finalizar($aluguelId);

                    if ($result === 'ok') {
                        setFlash('success', 'Aluguel finalizado e veiculo liberado para novo aluguel.');
                    } elseif ($result === 'not_found') {
                        setFlash('warning', 'Aluguel nao encontrado.');
                    } elseif ($result === 'not_started') {
                        setFlash('warning', 'Este aluguel ainda nao iniciou e nao pode ser finalizado.');
                    } else {
                        setFlash('warning', 'Este aluguel ja foi finalizado.');
                    }
                } catch (AluguelOperationException $exception) {
                    setFlash('error', 'Erro ao finalizar aluguel.');
                }

                redirect('index.php?route=alugueis');
            }
        }

        if ($databaseReady && $pdo instanceof PDO) {
            try {
                $usuarioModel = new UsuarioModel($pdo);
                $veiculoModel = new VeiculoModel($pdo);
                $aluguelModel = new AluguelModel($pdo);

                $usuarios = $usuarioModel->listBasic();
                $veiculosDisponiveis = $veiculoModel->listForRental();
                $alugueis = $aluguelModel->listAll();
            } catch (PDOException $exception) {
                $databaseReady = false;
                $dbErrorMessage = 'Nao foi possivel inicializar o banco automaticamente. Verifique permissao de acesso e credenciais do MySQL.';
            }
        }

        $this->render('alugueis/index', [
            'pageTitle' => 'Alugueis',
            'formData' => $formData,
            'errors' => $errors,
            'usuarios' => $usuarios,
            'veiculosDisponiveis' => $veiculosDisponiveis,
            'alugueis' => $alugueis,
            'databaseReady' => $databaseReady,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }
}
