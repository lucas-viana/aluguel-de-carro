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

        $isAdmin = ($_SESSION['usuario_tipo'] ?? '') === 'admin';

        $formData = [
            'nome_completo'   => '',
            'cpf'             => '',
            'data_nascimento' => '',
            'telefone'        => '',
            'email'           => '',
            'senha'           => '',
            'endereco'        => '',
            'tipo'            => 'comum',
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
                [$clean, $errors] = validateUsuario($_POST, $isAdmin);
                $formData = array_merge($formData, array_map('strval', $clean));

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
                $dbErrorMessage = 'Nao foi possivel carregar usuarios. Verifique permissao de acesso e credenciais do MySQL.';
            }
        }

        $this->render('usuarios/index', [
            'pageTitle'      => 'Usuarios',
            'formData'       => $formData,
            'errors'         => $errors,
            'usuarios'       => $usuarios,
            'isAdmin'        => $isAdmin,
            'databaseReady'  => $databaseReady,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }

    public function edit(): void
    {
        startSessionIfNeeded();

        $isAdmin = ($_SESSION['usuario_tipo'] ?? '') === 'admin';
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
            setFlash('error', 'Usuario invalido.');
            redirect('index.php?route=usuarios');
        }

        $errors = [];
        $databaseReady = true;
        $dbErrorMessage = '';
        $pdo = null;
        $usuario = null;

        try {
            $pdo = getConnection();
        } catch (PDOException $exception) {
            $databaseReady = false;
            $dbErrorMessage = 'Falha na conexao com o MySQL.';
        }

        if ($databaseReady && $pdo instanceof PDO) {
            $model = new UsuarioModel($pdo);

            $usuario = $model->findById($id);

            if (!$usuario) {
                setFlash('error', 'Usuario nao encontrado.');
                redirect('index.php?route=usuarios');
            }

            if (isPostRequest() && ($_POST['action'] ?? '') === 'edit') {
                [$clean, $errors] = validateUsuarioEdit($_POST, $isAdmin);

                if (empty($errors)) {
                    try {
                        $model->update($id, $clean);
                        setFlash('success', 'Usuario atualizado com sucesso.');
                        redirect('index.php?route=usuarios');
                    } catch (PDOException $exception) {
                        if ($exception->getCode() === '23000') {
                            $errors['geral'] = 'CPF ou e-mail ja cadastrado.';
                        } else {
                            $errors['geral'] = 'Erro ao atualizar usuario.';
                        }
                    }
                }

                // Re-merge os dados submetidos para repopular o formulário
                $usuario = array_merge($usuario, $_POST);
            }
        }

        $this->render('usuarios/edit', [
            'pageTitle'      => 'Editar Usuario',
            'usuario'        => $usuario,
            'errors'         => $errors,
            'isAdmin'        => $isAdmin,
            'databaseReady'  => $databaseReady,
            'dbErrorMessage' => $dbErrorMessage,
        ]);
    }
}
