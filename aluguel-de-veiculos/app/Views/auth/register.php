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
        .register-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .register-card {
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .register-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            padding: 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: none;
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

        .btn-register {
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

        .btn-register:hover {
            transform: translateY(-2px);
        }

        .register-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.9rem;
        }

        .register-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <h1>RentCar</h1>
            <p>Crie sua conta para gerenciar alugueis</p>
        </div>

        <?php if (!empty($errors['geral'])): ?>
            <div class="alert-error"><?php echo h($errors['geral']); ?></div>
        <?php endif; ?>

        <?php if (!$databaseReady): ?>
            <div class="alert-error"><?php echo h($dbErrorMessage); ?></div>
        <?php else: ?>

        <form method="POST" action="index.php?route=register" novalidate>

            <div class="mb-3">
                <label for="reg_nome_completo" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="reg_nome_completo" name="nome_completo"
                       required minlength="3" maxlength="120"
                       value="<?php echo h($formData['nome_completo']); ?>"
                       placeholder="Seu nome completo">
                <?php if (!empty($errors['nome_completo'])): ?>
                    <div class="invalid-feedback"><?php echo h($errors['nome_completo']); ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="reg_cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="reg_cpf" name="cpf"
                       required maxlength="14" inputmode="numeric"
                       value="<?php echo h($formData['cpf']); ?>"
                       placeholder="Somente numeros">
                <?php if (!empty($errors['cpf'])): ?>
                    <div class="invalid-feedback"><?php echo h($errors['cpf']); ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="reg_data_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control" id="reg_data_nascimento" name="data_nascimento"
                       required max="<?php echo h((new DateTimeImmutable('today'))->format('Y-m-d')); ?>"
                       value="<?php echo h($formData['data_nascimento']); ?>">
                <?php if (!empty($errors['data_nascimento'])): ?>
                    <div class="invalid-feedback"><?php echo h($errors['data_nascimento']); ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="reg_telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="reg_telefone" name="telefone"
                       required inputmode="numeric" maxlength="15"
                       value="<?php echo h($formData['telefone']); ?>"
                       placeholder="DDD + numero">
                <?php if (!empty($errors['telefone'])): ?>
                    <div class="invalid-feedback"><?php echo h($errors['telefone']); ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="reg_email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="reg_email" name="email"
                       required maxlength="120"
                       value="<?php echo h($formData['email']); ?>"
                       placeholder="seu-email@exemplo.com">
                <?php if (!empty($errors['email'])): ?>
                    <div class="invalid-feedback"><?php echo h($errors['email']); ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="reg_senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="reg_senha" name="senha"
                       required minlength="6" placeholder="Minimo 6 caracteres">
                <?php if (!empty($errors['senha'])): ?>
                    <div class="invalid-feedback"><?php echo h($errors['senha']); ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="reg_endereco" class="form-label">Endereco</label>
                <textarea class="form-control" id="reg_endereco" name="endereco"
                          rows="2" required minlength="5" maxlength="255"
                          placeholder="Rua, numero - Cidade/UF"><?php echo h($formData['endereco']); ?></textarea>
                <?php if (!empty($errors['endereco'])): ?>
                    <div class="invalid-feedback"><?php echo h($errors['endereco']); ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-register">Criar Conta</button>
        </form>

        <?php endif; ?>

        <div class="register-footer">
            <p>Ja tem uma conta? <a href="index.php?route=login">Entrar</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="assets/js/validation.js"></script>
</body>
</html>
