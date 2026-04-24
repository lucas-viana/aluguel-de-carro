<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/lib/validators.php';

startSessionIfNeeded();

$formData = [
    'usuario_id' => '',
    'veiculo_id' => '',
    'data_retirada' => (new DateTimeImmutable('today'))->format('Y-m-d'),
    'data_entrega' => (new DateTimeImmutable('today +1 day'))->format('Y-m-d'),
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
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        [$clean, $errors] = validateAluguel($_POST);
        $formData = array_merge($formData, $clean);

        if (empty($errors)) {
            try {
                $pdo->beginTransaction();

                $usuarioStmt = $pdo->prepare('SELECT id FROM usuarios WHERE id = :id');
                $usuarioStmt->execute(['id' => $clean['usuario_id']]);
                $usuario = $usuarioStmt->fetch();

                if (!$usuario) {
                    $errors['usuario_id'] = 'Usuario selecionado nao existe.';
                }

                $veiculoStmt = $pdo->prepare('SELECT id, disponivel FROM veiculos WHERE id = :id FOR UPDATE');
                $veiculoStmt->execute(['id' => $clean['veiculo_id']]);
                $veiculo = $veiculoStmt->fetch();

                if (!$veiculo) {
                    $errors['veiculo_id'] = 'Veiculo selecionado nao existe.';
                } elseif ((int) $veiculo['disponivel'] !== 1) {
                    $errors['veiculo_id'] = 'Este veiculo nao esta disponivel para aluguel.';
                }

                if (!empty($errors)) {
                    $pdo->rollBack();
                } else {
                    $insertAluguel = $pdo->prepare(
                        "INSERT INTO alugueis (data_retirada, data_entrega, forma_pagamento, usuario_id, veiculo_id, status)
                         VALUES (:data_retirada, :data_entrega, :forma_pagamento, :usuario_id, :veiculo_id, 'ativo')"
                    );
                    $insertAluguel->execute($clean);

                    $updateVeiculo = $pdo->prepare('UPDATE veiculos SET disponivel = 0 WHERE id = :id');
                    $updateVeiculo->execute(['id' => $clean['veiculo_id']]);

                    $pdo->commit();
                    setFlash('success', 'Aluguel criado com sucesso e veiculo marcado como alugado.');
                    redirect('alugueis.php');
                }
            } catch (Throwable $throwable) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors['geral'] = 'Erro ao registrar aluguel.';
            }
        }
    }

    if ($action === 'finalizar') {
        $aluguelId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);

        if ($aluguelId === false || $aluguelId <= 0) {
            setFlash('error', 'Aluguel invalido para finalizacao.');
            redirect('alugueis.php');
        }

        try {
            $pdo->beginTransaction();

            $aluguelStmt = $pdo->prepare('SELECT id, veiculo_id, status FROM alugueis WHERE id = :id FOR UPDATE');
            $aluguelStmt->execute(['id' => $aluguelId]);
            $aluguel = $aluguelStmt->fetch();

            if (!$aluguel) {
                $pdo->rollBack();
                setFlash('warning', 'Aluguel nao encontrado.');
                redirect('alugueis.php');
            }

            if ($aluguel['status'] !== 'ativo') {
                $pdo->rollBack();
                setFlash('warning', 'Este aluguel ja foi finalizado.');
                redirect('alugueis.php');
            }

            $finalizaAluguel = $pdo->prepare("UPDATE alugueis SET status = 'finalizado', finalizado_em = NOW() WHERE id = :id");
            $finalizaAluguel->execute(['id' => $aluguelId]);

            $liberaVeiculo = $pdo->prepare('UPDATE veiculos SET disponivel = 1 WHERE id = :id');
            $liberaVeiculo->execute(['id' => $aluguel['veiculo_id']]);

            $pdo->commit();
            setFlash('success', 'Aluguel finalizado e veiculo liberado para novo aluguel.');
        } catch (Throwable $throwable) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            setFlash('error', 'Erro ao finalizar aluguel.');
        }

        redirect('alugueis.php');
    }
}

if ($databaseReady && $pdo instanceof PDO) {
    try {
        $usuarios = $pdo->query('SELECT id, nome_completo, cpf FROM usuarios ORDER BY nome_completo')->fetchAll();
        $veiculosDisponiveis = $pdo->query('SELECT id, modelo, fabricante, placa FROM veiculos WHERE disponivel = 1 ORDER BY modelo')->fetchAll();

        $alugueis = $pdo->query(
            "SELECT
                a.id,
                a.data_retirada,
                a.data_entrega,
                a.forma_pagamento,
                a.status,
                a.finalizado_em,
                u.nome_completo AS usuario,
                v.modelo AS veiculo,
                v.placa
            FROM alugueis a
            INNER JOIN usuarios u ON u.id = a.usuario_id
            INNER JOIN veiculos v ON v.id = a.veiculo_id
            ORDER BY a.id DESC"
        )->fetchAll();
    } catch (PDOException $exception) {
        $databaseReady = false;
        $dbErrorMessage = 'Nao foi possivel inicializar o banco automaticamente. Verifique permissao de acesso e credenciais do MySQL.';
    }
}

$pageTitle = 'Alugueis';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h1 class="h5 mb-3">Novo Aluguel</h1>

                <?php if (isset($errors['geral'])): ?>
                    <div class="alert alert-danger py-2"><?php echo h($errors['geral']); ?></div>
                <?php endif; ?>

                <?php if (!$databaseReady): ?>
                    <div class="alert alert-warning"><?php echo h($dbErrorMessage); ?></div>
                <?php elseif (empty($usuarios) || empty($veiculosDisponiveis)): ?>
                    <div class="alert alert-info py-2">Cadastre ao menos 1 usuario e 1 veiculo disponivel para criar um aluguel.</div>
                <?php else: ?>
                    <form method="post" data-validate="aluguel" novalidate>
                        <input type="hidden" name="action" value="create">

                        <div class="mb-3">
                            <label for="usuario_id" class="form-label">Usuario</label>
                            <select class="form-select" id="usuario_id" name="usuario_id" required>
                                <option value="">Selecione um usuario</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?php echo h((string) $usuario['id']); ?>" <?php echo (string) $formData['usuario_id'] === (string) $usuario['id'] ? 'selected' : ''; ?>>
                                        <?php echo h($usuario['nome_completo'] . ' - CPF ' . $usuario['cpf']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['usuario_id'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['usuario_id']); ?></div><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="veiculo_id" class="form-label">Veiculo Disponivel</label>
                            <select class="form-select" id="veiculo_id" name="veiculo_id" required>
                                <option value="">Selecione um veiculo</option>
                                <?php foreach ($veiculosDisponiveis as $veiculo): ?>
                                    <option value="<?php echo h((string) $veiculo['id']); ?>" <?php echo (string) $formData['veiculo_id'] === (string) $veiculo['id'] ? 'selected' : ''; ?>>
                                        <?php echo h($veiculo['fabricante'] . ' ' . $veiculo['modelo'] . ' - ' . $veiculo['placa']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['veiculo_id'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['veiculo_id']); ?></div><?php endif; ?>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="data_retirada" class="form-label">Data de Retirada</label>
                                <input type="date" class="form-control" id="data_retirada" name="data_retirada" required value="<?php echo h($formData['data_retirada']); ?>">
                                <?php if (isset($errors['data_retirada'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['data_retirada']); ?></div><?php endif; ?>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="data_entrega" class="form-label">Data de Entrega</label>
                                <input type="date" class="form-control" id="data_entrega" name="data_entrega" required value="<?php echo h($formData['data_entrega']); ?>">
                                <?php if (isset($errors['data_entrega'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['data_entrega']); ?></div><?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
                            <select class="form-select" id="forma_pagamento" name="forma_pagamento" required>
                                <?php foreach (paymentMethods() as $key => $label): ?>
                                    <option value="<?php echo h($key); ?>" <?php echo $formData['forma_pagamento'] === $key ? 'selected' : ''; ?>><?php echo h($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['forma_pagamento'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['forma_pagamento']); ?></div><?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-brand w-100">Registrar Aluguel</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Historico de Alugueis</h2>

                <?php if (!$databaseReady): ?>
                    <div class="alert alert-warning"><?php echo h($dbErrorMessage); ?></div>
                <?php elseif (empty($alugueis)): ?>
                    <p class="text-secondary mb-0">Nenhum aluguel cadastrado.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Usuario</th>
                                    <th>Veiculo</th>
                                    <th>Retirada</th>
                                    <th>Entrega</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alugueis as $aluguel): ?>
                                    <tr>
                                        <td><?php echo h((string) $aluguel['id']); ?></td>
                                        <td><?php echo h($aluguel['usuario']); ?></td>
                                        <td><?php echo h($aluguel['veiculo'] . ' - ' . $aluguel['placa']); ?></td>
                                        <td><?php echo h($aluguel['data_retirada']); ?></td>
                                        <td><?php echo h($aluguel['data_entrega']); ?></td>
                                        <td>
                                            <?php if ($aluguel['status'] === 'ativo'): ?>
                                                <span class="badge text-bg-warning">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-success">Finalizado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($aluguel['status'] === 'ativo'): ?>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Finalizar este aluguel?');">
                                                    <input type="hidden" name="action" value="finalizar">
                                                    <input type="hidden" name="id" value="<?php echo h((string) $aluguel['id']); ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Finalizar</button>
                                                </form>
                                            <?php else: ?>
                                                <small class="text-secondary">Finalizado em <?php echo h((string) $aluguel['finalizado_em']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php';
