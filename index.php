<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/lib/helpers.php';

startSessionIfNeeded();

$databaseReady = true;
$dbErrorMessage = '';
$totalUsuarios = 0;
$totalVeiculos = 0;
$veiculosDisponiveis = 0;
$alugueisAtivos = 0;
$ultimosAlugueis = [];

try {
    $pdo = getConnection();

    $totalUsuarios = (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
    $totalVeiculos = (int) $pdo->query('SELECT COUNT(*) FROM veiculos')->fetchColumn();
    $veiculosDisponiveis = (int) $pdo->query('SELECT COUNT(*) FROM veiculos WHERE disponivel = 1')->fetchColumn();
    $alugueisAtivos = (int) $pdo->query("SELECT COUNT(*) FROM alugueis WHERE status = 'ativo'")->fetchColumn();

    $ultimosAlugueis = $pdo->query(
        "SELECT
            a.id,
            a.data_retirada,
            a.data_entrega,
            a.status,
            u.nome_completo AS usuario,
            v.modelo AS veiculo,
            v.placa
        FROM alugueis a
        INNER JOIN usuarios u ON u.id = a.usuario_id
        INNER JOIN veiculos v ON v.id = a.veiculo_id
        ORDER BY a.id DESC
        LIMIT 5"
    )->fetchAll();
} catch (PDOException $exception) {
    $databaseReady = false;
    $dbErrorMessage = 'Nao foi possivel inicializar o banco automaticamente. Verifique permissao de acesso e credenciais do MySQL.';
}

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero-panel mb-4">
    <div>
        <h1 class="h3 mb-2">Gestao de Aluguel de Veiculos</h1>
        <p class="mb-0 text-secondary">Controle usuarios, carros disponiveis e alugueis em um unico painel.</p>
    </div>
</section>

<?php if (!$databaseReady): ?>
    <div class="alert alert-warning" role="alert">
        <?php echo h($dbErrorMessage); ?>
    </div>
<?php else: ?>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card">
                <p>Total de Usuarios</p>
                <strong><?php echo h((string) $totalUsuarios); ?></strong>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card">
                <p>Total de Veiculos</p>
                <strong><?php echo h((string) $totalVeiculos); ?></strong>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card">
                <p>Disponiveis</p>
                <strong><?php echo h((string) $veiculosDisponiveis); ?></strong>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <div class="stat-card">
                <p>Alugueis Ativos</p>
                <strong><?php echo h((string) $alugueisAtivos); ?></strong>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h2 class="h5 mb-3">Ultimos Alugueis</h2>

            <?php if (empty($ultimosAlugueis)): ?>
                <p class="text-secondary mb-0">Nenhum aluguel cadastrado ate o momento.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Usuario</th>
                                <th>Veiculo</th>
                                <th>Placa</th>
                                <th>Retirada</th>
                                <th>Entrega</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimosAlugueis as $aluguel): ?>
                                <tr>
                                    <td><?php echo h((string) $aluguel['id']); ?></td>
                                    <td><?php echo h($aluguel['usuario']); ?></td>
                                    <td><?php echo h($aluguel['veiculo']); ?></td>
                                    <td><?php echo h($aluguel['placa']); ?></td>
                                    <td><?php echo h($aluguel['data_retirada']); ?></td>
                                    <td><?php echo h($aluguel['data_entrega']); ?></td>
                                    <td>
                                        <?php if ($aluguel['status'] === 'ativo'): ?>
                                            <span class="badge text-bg-warning">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge text-bg-success">Finalizado</span>
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
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php';
