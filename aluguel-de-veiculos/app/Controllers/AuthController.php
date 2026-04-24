<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AuthModel;
use App\Models\UsuarioModel;
use PDO;
use PDOException;

class AuthController extends Controller
{
    public function login(): void
    {
        startSessionIfNeeded();

        if (isset($_SESSION['usuario_id'])) {
            redirect('index.php');
        }

        $email = '';
        $errors = [];
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
            $email = trim((string) ($_POST['email'] ?? ''));
            $senha = (string) ($_POST['senha'] ?? '');

            if ($email === '') {
                $errors['email'] = 'Email e obrigatorio.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email invalido.';
            }

            if ($senha === '') {
                $errors['senha'] = 'Senha e obrigatoria.';
            }

            if (empty($errors)) {
                try {
                    $authModel = new AuthModel($pdo);
                    $usuario = $authModel->findUserByEmail($email);

                    if ($usuario && password_verify($senha, $usuario['senha'])) {
                        $_SESSION['usuario_id']   = $usuario['id'];
                        $_SESSION['usuario_nome'] = $usuario['nome_completo'];
                        $_SESSION['usuario_tipo'] = $usuario['tipo'];

                        setFlash('success', 'Login realizado com sucesso!');

                        // Redireciona conforme o perfil
                        if ($usuario['tipo'] === 'admin') {
                            redirect('index.php?route=dashboard');
                        } else {
                            redirect('index.php?route=alugueis');
                        }
                    }

                    $errors['geral'] = 'Email ou senha incorretos.';
                } catch (PDOException $exception) {
                    $errors['geral'] = 'Erro ao processar login. Tente novamente.';
                }
            }
        }

        $this->render('auth/login', [
            'pageTitle'      => 'Login - RentCar',
            'email'          => $email,
            'errors'         => $errors,
            'databaseReady'  => $databaseReady,
            'dbErrorMessage' => $dbErrorMessage,
        ], false);
    }

    public function register(): void
    {
        startSessionIfNeeded();

        if (isset($_SESSION['usuario_id'])) {
            redirect('index.php');
        }

        $formData = [
            'nome_completo'   => '',
            'cpf'             => '',
            'data_nascimento' => '',
            'telefone'        => '',
            'email'           => '',
            'senha'           => '',
            'endereco'        => '',
        ];
        $errors = [];
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
            // Auto-registro sempre cria como 'comum' (adminCreating = false)
            [$clean, $errors] = validateUsuario($_POST, false);
            $formData = array_merge($formData, array_map('strval', $clean));

            if (empty($errors)) {
                try {
                    (new UsuarioModel($pdo))->create($clean);
                    setFlash('success', 'Conta criada com sucesso! Faca login para continuar.');
                    redirect('index.php?route=login');
                } catch (PDOException $exception) {
                    if ($exception->getCode() === '23000') {
                        $errors['geral'] = 'CPF ou e-mail ja cadastrado.';
                    } else {
                        $errors['geral'] = 'Erro ao criar conta. Tente novamente.';
                    }
                }
            }
        }

        $this->render('auth/register', [
            'pageTitle'      => 'Criar Conta - RentCar',
            'formData'       => $formData,
            'errors'         => $errors,
            'databaseReady'  => $databaseReady,
            'dbErrorMessage' => $dbErrorMessage,
        ], false);
    }
}
