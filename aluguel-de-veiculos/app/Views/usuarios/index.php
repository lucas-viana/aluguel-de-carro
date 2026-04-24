<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h1 class="h5 mb-3">Cadastrar Usuario</h1>

                <?php if (isset($errors['geral'])): ?>
                    <div class="alert alert-danger py-2"><?php echo h($errors['geral']); ?></div>
                <?php endif; ?>

                <form method="post" data-validate="usuario" novalidate>
                    <input type="hidden" name="action" value="create">

                    <div class="mb-3">
                        <label for="nome_completo" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome_completo" name="nome_completo" required minlength="3" maxlength="120" value="<?php echo h($formData['nome_completo']); ?>">
                        <?php if (isset($errors['nome_completo'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['nome_completo']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" required maxlength="14" pattern="\d{11}" value="<?php echo h($formData['cpf']); ?>" data-mask="cpf" inputmode="numeric" placeholder="Somente numeros">
                        <?php if (isset($errors['cpf'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['cpf']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" required max="<?php echo h((new DateTimeImmutable('today'))->format('Y-m-d')); ?>" value="<?php echo h($formData['data_nascimento']); ?>">
                        <?php if (isset($errors['data_nascimento'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['data_nascimento']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" required pattern="\d{10,11}" maxlength="15" value="<?php echo h($formData['telefone']); ?>" data-mask="telefone" inputmode="numeric" placeholder="Somente numeros">
                        <?php if (isset($errors['telefone'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['telefone']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required maxlength="120" value="<?php echo h($formData['email']); ?>">
                        <?php if (isset($errors['email'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['email']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required minlength="6">
                        <?php if (isset($errors['senha'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['senha']); ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereco</label>
                        <textarea class="form-control" id="endereco" name="endereco" rows="3" required minlength="5" maxlength="255"><?php echo h($formData['endereco']); ?></textarea>
                        <?php if (isset($errors['endereco'])): ?><div class="invalid-feedback d-block"><?php echo h($errors['endereco']); ?></div><?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-brand w-100">Salvar Usuario</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h2 class="h5 mb-3">Usuarios Cadastrados</h2>

                <?php if (!$databaseReady): ?>
                    <div class="alert alert-warning"><?php echo h($dbErrorMessage); ?></div>
                <?php elseif (empty($usuarios)): ?>
                    <p class="text-secondary mb-0">Nenhum usuario cadastrado.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>E-mail</th>
                                    <th>Telefone</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?php echo h((string) $usuario['id']); ?></td>
                                        <td><?php echo h($usuario['nome_completo']); ?></td>
                                        <td><?php echo h($usuario['cpf']); ?></td>
                                        <td><?php echo h($usuario['email']); ?></td>
                                        <td><?php echo h($usuario['telefone']); ?></td>
                                        <td class="text-end">
                                            <form method="post" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este usuario?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo h((string) $usuario['id']); ?>">
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
