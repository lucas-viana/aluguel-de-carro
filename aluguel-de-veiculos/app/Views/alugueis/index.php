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
                    <div class="alert alert-info py-2">Cadastre ao menos 1 usuario e 1 veiculo para criar um aluguel.</div>
                <?php else: ?>
                    <form method="post" data-validate="aluguel" novalidate>
                        <input type="hidden" name="action" value="create">

                        <div class="alert alert-info py-2">
                            O mesmo veiculo pode ser alugado novamente somente com intervalo minimo de 2 dias apos a data de entrega anterior.
                        </div>

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
                            <label for="veiculo_id" class="form-label">Veiculo</label>
                            <select class="form-select" id="veiculo_id" name="veiculo_id" required>
                                <option value="">Selecione um veiculo</option>
                                <?php foreach ($veiculosDisponiveis as $veiculo): ?>
                                    <option value="<?php echo h((string) $veiculo['id']); ?>" <?php echo (string) $formData['veiculo_id'] === (string) $veiculo['id'] ? 'selected' : ''; ?>>
                                        <?php echo h($veiculo['fabricante'] . ' ' . $veiculo['modelo'] . ' - ' . $veiculo['placa']); ?>
                                        <?php echo (int) $veiculo['disponivel'] === 1 ? '' : ' (indisponivel hoje)'; ?>
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
                                    <?php $isFutureRental = $aluguel['status'] === 'ativo' && $aluguel['data_retirada'] > (new DateTimeImmutable('today'))->format('Y-m-d'); ?>
                                    <tr>
                                        <td><?php echo h((string) $aluguel['id']); ?></td>
                                        <td><?php echo h($aluguel['usuario']); ?></td>
                                        <td><?php echo h($aluguel['veiculo'] . ' - ' . $aluguel['placa']); ?></td>
                                        <td><?php echo h($aluguel['data_retirada']); ?></td>
                                        <td><?php echo h($aluguel['data_entrega']); ?></td>
                                        <td>
                                            <?php if ($isFutureRental): ?>
                                                <span class="badge text-bg-info">Agendado</span>
                                            <?php elseif ($aluguel['status'] === 'ativo'): ?>
                                                <span class="badge text-bg-warning">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge text-bg-success">Finalizado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($aluguel['status'] === 'ativo' && !$isFutureRental): ?>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Finalizar este aluguel?');">
                                                    <input type="hidden" name="action" value="finalizar">
                                                    <input type="hidden" name="id" value="<?php echo h((string) $aluguel['id']); ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Finalizar</button>
                                                </form>
                                            <?php elseif ($isFutureRental): ?>
                                                <small class="text-secondary">Inicio em <?php echo h($aluguel['data_retirada']); ?></small>
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

