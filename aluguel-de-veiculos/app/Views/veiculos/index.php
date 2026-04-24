<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h1 class="h5 mb-3">Cadastrar Veiculo</h1>

                <?php if (isset($errors['geral'])): ?>
                    <div class="alert alert-danger py-2"><?php echo h($errors['geral']); ?></div>
                <?php endif; ?>

                <form method="post" data-validate="veiculo" novalidate>
                    <input type="hidden" name="action" value="create">

                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required minlength="2" maxlength="100" value="<?php echo h($formData['modelo']); ?>">
                        <?php if (isset($errors['modelo'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['modelo']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="cor" class="form-label">Cor</label>
                        <input type="text" class="form-control" id="cor" name="cor" required minlength="2" maxlength="40" value="<?php echo h($formData['cor']); ?>">
                        <?php if (isset($errors['cor'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['cor']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="fabricante" class="form-label">Fabricante</label>
                        <input type="text" class="form-control" id="fabricante" name="fabricante" required minlength="2" maxlength="80" value="<?php echo h($formData['fabricante']); ?>">
                        <?php if (isset($errors['fabricante'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['fabricante']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="placa" class="form-label">Placa</label>
                        <input type="text" class="form-control text-uppercase" id="placa" name="placa" required maxlength="8" pattern="[A-Za-z]{3}[0-9][A-Za-z0-9][0-9]{2}" value="<?php echo h($formData['placa']); ?>" data-mask="placa" placeholder="ABC1D23">
                        <?php if (isset($errors['placa'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['placa']); ?></div><?php endif; ?>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="disponivel" name="disponivel" value="1" <?php echo (int) $formData['disponivel'] === 1 ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="disponivel">Disponivel para aluguel</label>
                    </div>

                    <button type="submit" class="btn btn-brand w-100">Salvar Veiculo</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Veiculos Cadastrados</h2>

                <?php if (!$databaseReady): ?>
                    <div class="alert alert-warning"><?php echo h($dbErrorMessage); ?></div>
                <?php elseif (empty($veiculos)): ?>
                    <p class="text-secondary mb-0">Nenhum veiculo cadastrado.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Modelo</th>
                                    <th>Fabricante</th>
                                    <th>Placa</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($veiculos as $veiculo): ?>
                                    <tr>
                                        <td><?php echo h((string) $veiculo['id']); ?></td>
                                        <td><?php echo h($veiculo['modelo']); ?></td>
                                        <td><?php echo h($veiculo['fabricante']); ?></td>
                                        <td><?php echo h($veiculo['placa']); ?></td>
                                        <td>
                                            <?php if ((int) $veiculo['disponivel'] === 1): ?>
                                                <span class="badge text-bg-success">Disponivel</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-secondary">Alugado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end d-flex gap-2 justify-content-end">
                                            <form method="post">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?php echo h((string) $veiculo['id']); ?>">
                                                <input type="hidden" name="novo_status" value="<?php echo (int) $veiculo['disponivel'] === 1 ? '0' : '1'; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <?php echo (int) $veiculo['disponivel'] === 1 ? 'Marcar Alugado' : 'Marcar Disponivel'; ?>
                                                </button>
                                            </form>

                                            <form method="post" onsubmit="return confirm('Deseja realmente excluir este veiculo?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo h((string) $veiculo['id']); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                            </form>
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
