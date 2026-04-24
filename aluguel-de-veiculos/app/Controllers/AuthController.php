<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AuthModel;
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
        $senha = '';
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
                        $_SESSION['usuario_id'] = $usuario['id'];
                        $_SESSION['usuario_nome'] = $usuario['nome_completo'];

                        setFlash('success', 'Login realizado com sucesso!');
                        redirect('index.php');
                    }

                    $errors['geral'] = 'Email ou senha incorretos.';
                } catch (PDOException $exception) {
                    $errors['geral'] = 'Erro ao processar login. Tente novamente.';
                }
            }
        }

        $this->render('auth/login', [
            'pageTitle' => 'Login - Sistema de Aluguel de Veiculos',
            'email' => $email,
            'senha' => $senha,
            'errors' => $errors,
            'databaseReady' => $databaseReady,
            'dbErrorMessage' => $dbErrorMessage,
        ], false);
    }
}

