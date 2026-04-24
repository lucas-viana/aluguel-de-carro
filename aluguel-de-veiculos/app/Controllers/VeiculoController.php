<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\VeiculoModel;
use PDO;
use PDOException;

class VeiculoController extends Controller
{
    public function index(): void
    {
        startSessionIfNeeded();

        $formData = [
            'modelo' => '',
            'cor' => '',
            'fabricante' => '',
            'placa' => '',
            'disponivel' => 1,
        ];
        $errors = [];
        $veiculos = [];
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
            $model = new VeiculoModel($pdo);
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                [$clean, $errors] = validateVeiculo($_POST);
                $formData = array_merge($formData, $clean);

                if (empty($errors)) {
                    try {
                        $model->create($clean);

                        setFlash('success', 'Veiculo cadastrado com sucesso.');
                        redirect('index.php?route=veiculos');
                    } catch (PDOException $exception) {
                        if ($exception->getCode() === '23000') {
                            $errors['geral'] = 'Placa ja cadastrada.';
                        } else {
                            $errors['geral'] = 'Erro ao cadastrar veiculo.';
                        }
                    }
                }
            }

            if ($action === 'toggle') {
                $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
                $novoStatus = filter_var($_POST['novo_status'] ?? null, FILTER_VALIDATE_INT);

                if ($id === false || $id <= 0 || !in_array($novoStatus, [0, 1], true)) {
                    setFlash('error', 'Dados invalidos para atualizar status.');
                    redirect('index.php?route=veiculos');
                }

                try {
                    if ($novoStatus === 1 && $model->hasActiveRental($id)) {
                        setFlash('error', 'Nao e possivel marcar como disponivel: existe aluguel ativo para este veiculo.');
                        redirect('index.php?route=veiculos');
                    }

                    $model->updateDisponibilidade($id, $novoStatus);
                    setFlash('success', 'Status do veiculo atualizado.');
                } catch (PDOException $exception) {
                    setFlash('error', 'Erro ao atualizar status do veiculo.');
                }

                redirect('index.php?route=veiculos');
            }

            if ($action === 'delete') {
                $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

                if ($id === false || $id <= 0) {
                    setFlash('error', 'Veiculo invalido para exclusao.');
                    redirect('index.php?route=veiculos');
                }

                try {
                    $deletedRows = $model->deleteById($id);

                    if ($deletedRows > 0) {
                        setFlash('success', 'Veiculo removido com sucesso.');
                    } else {
                        setFlash('warning', 'Veiculo nao encontrado.');
                    }
                } catch (PDOException $exception) {
                    if ($exception->getCode() === '23000') {
                        setFlash('error', 'Nao e possivel excluir veiculo com alugueis vinculados.');
                    } else {
                        setFlash('error', 'Erro ao excluir veiculo.');
                    }
                }

                redirect('index.php?route=veiculos');
            }
        }

        if ($databaseReady && $pdo instanceof PDO) {
            try {
                $veiculos = (new VeiculoModel($pdo))->listAll();
            } catch (PDOException $exception) {
                $databaseReady = false;
                $dbErrorMessage = 'Nao foi possivel inicializar o banco automaticamente. Verifique permissao de acesso e credenciais do MySQL.';
            }
        }

        $this->render('veiculos/index', [
            'pageTitle' => 'Veiculos',
            'formData' => $formData,
            'errors' => $errors,
            'veiculos' => $veiculos,
            'databaseReady' => $databaseReady,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }
}
