<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo h($pageTitle); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            padding: 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.9rem;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1>RentCar</h1>
            <p>Sistema de Aluguel de Veiculos</p>
        </div>

        <?php if (!empty($errors['geral'])): ?>
            <div class="alert-error">
                <?php echo h($errors['geral']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?route=login" novalidate>
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    class="form-control <?php echo !empty($errors['email']) ? 'is-invalid' : ''; ?>"
                    id="email"
                    name="email"
                    value="<?php echo h($email); ?>"
                    required
                    placeholder="seu-email@exemplo.com"
                >
                <?php if (!empty($errors['email'])): ?>
                    <div class="invalid-feedback">
                        <?php echo h($errors['email']); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="senha" class="form-label">Senha</label>
                <input
                    type="password"
                    class="form-control <?php echo !empty($errors['senha']) ? 'is-invalid' : ''; ?>"
                    id="senha"
                    name="senha"
                    required
                    placeholder="Digite sua senha"
                >
                <?php if (!empty($errors['senha'])): ?>
                    <div class="invalid-feedback">
                        <?php echo h($errors['senha']); ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-login">Entrar</button>
        </form>

        <div class="login-footer">
            <p>
                Nao tem uma conta?
                <a href="index.php?route=register">Cadastre-se</a>
            </p>
        </div>
    </div>
</div>

<?php echo !$databaseReady ? '<div style="position: fixed; bottom: 10px; left: 10px; background: #dc3545; color: white; padding: 10px; border-radius: 5px;">' . h($dbErrorMessage) . '</div>' : ''; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="assets/js/validation.js"></script>
</body>
</html>

