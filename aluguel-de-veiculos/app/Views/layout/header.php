<?php
declare(strict_types=1);

startSessionIfNeeded();

$pageTitle = $pageTitle ?? 'Sistema de Aluguel de Veiculos';
$flashMessages = getFlashMessages();
$flashTypeMap = [
    'success' => 'success',
    'error' => 'danger',
    'warning' => 'warning',
    'info' => 'info',
];
?>
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
</head>
<body>
<nav class="navbar navbar-expand-lg border-bottom sticky-top app-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">RentCar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Alternar navegacao">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto gap-lg-2 align-items-lg-center">
                <?php if (($_SESSION['usuario_tipo'] ?? '') === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php?route=dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?route=usuarios">Usuarios</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php?route=veiculos">Veiculos</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="index.php?route=alugueis">Alugueis</a></li>
                <?php if (isset($_SESSION['usuario_nome'])): ?>
                    <li class="nav-item d-flex align-items-center gap-2 ms-lg-2">
                        <?php $tipo = $_SESSION['usuario_tipo'] ?? 'comum'; ?>
                        <span class="badge <?php echo $tipo === 'admin' ? 'bg-danger' : 'bg-secondary'; ?>">
                            <?php echo h(ucfirst($tipo)); ?>
                        </span>
                        <span class="nav-link text-muted px-0" style="cursor:default;">
                            <?php echo h($_SESSION['usuario_nome']); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-sm btn-outline-danger px-3" href="logout.php"
                           onclick="return confirm('Deseja realmente sair?');">Sair</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php foreach ($flashMessages as $flash): ?>
        <?php $type = $flashTypeMap[$flash['type']] ?? 'secondary'; ?>
        <div class="alert alert-<?php echo h($type); ?> alert-dismissible fade show" role="alert">
            <?php echo h($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endforeach; ?>
