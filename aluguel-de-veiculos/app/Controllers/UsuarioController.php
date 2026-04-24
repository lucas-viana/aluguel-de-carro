<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UsuarioModel;
use PDO;
use PDOException;

class UsuarioController extends Controller
{
    public function index(): void
    {
        startSessionIfNeeded();

        $formData = [
            'nome_completo' => '',
            'cpf' => '',
            'data_nascimento' => '',
            'telefone' => '',
            'email' => '',
            'senha' => '',
            'endereco' => '',
        ];
        $errors = [];
        $usuarios = [];
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
            $model = new UsuarioModel($pdo);
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                [$clean, $errors] = validateUsuario($_POST);
                $formData = array_merge($formData, $clean);

                if (empty($errors)) {
                    try {
                        $model->create($clean);

                        setFlash('success', 'Usuario cadastrado com sucesso.');
                        redirect('index.php?route=usuarios');
                    } catch (PDOException $exception) {
                        if ($exception->getCode() === '23000') {
                            $errors['geral'] = 'CPF ou e-mail ja cadastrado.';
                        } else {
                            $errors['geral'] = 'Erro ao cadastrar usuario.';
                        }
                    }
                }
            }

            if ($action === 'delete') {
                $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

                if ($id === false || $id <= 0) {
                    setFlash('error', 'Usuario invalido para exclusao.');
                    redirect('index.php?route=usuarios');
                }

                try {
                    $deletedRows = $model->deleteById($id);

                    if ($deletedRows > 0) {
                        setFlash('success', 'Usuario removido com sucesso.');
                    } else {
                        setFlash('warning', 'Usuario nao encontrado.');
                    }
                } catch (PDOException $exception) {
                    if ($exception->getCode() === '23000') {
                        setFlash('error', 'Nao e possivel excluir usuario com alugueis vinculados.');
                    } else {
                        setFlash('error', 'Erro ao excluir usuario.');
                    }
                }

                redirect('index.php?route=usuarios');
            }
        }

        if ($databaseReady && $pdo instanceof PDO) {
            try {
                $usuarios = (new UsuarioModel($pdo))->listAll();
            } catch (PDOException $exception) {
                $databaseReady = false;
                $dbErrorMessage = 'Nao foi possivel inicializar o banco automaticamente. Verifique permissao de acesso e credenciais do MySQL.';
            }
        }

        $this->render('usuarios/index', [
            'pageTitle' => 'Usuarios',
            'formData' => $formData,
            'errors' => $errors,
            'usuarios' => $usuarios,
            'databaseReady' => $databaseReady,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }
}

