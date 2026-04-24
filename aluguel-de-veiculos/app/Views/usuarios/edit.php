<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h1 class="h5 mb-0">Editar Usuario</h1>
                    <a href="index.php?route=usuarios" class="btn btn-sm btn-outline-secondary">Voltar</a>
                </div>

                <?php if (!$databaseReady): ?>
                    <div class="alert alert-warning"><?php echo h($dbErrorMessage); ?></div>
                <?php else: ?>

                <?php if (isset($errors['geral'])): ?>
                    <div class="alert alert-danger py-2"><?php echo h($errors['geral']); ?></div>
                <?php endif; ?>

                <form method="post" action="index.php?route=usuarios/edit&id=<?php echo h((string) ($usuario['id'] ?? '')); ?>" novalidate>
                    <input type="hidden" name="action" value="edit">

                    <div class="mb-3">
                        <label for="edit_nome_completo" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="edit_nome_completo" name="nome_completo"
                               required minlength="3" maxlength="120"
                               value="<?php echo h((string) ($usuario['nome_completo'] ?? '')); ?>">
                        <?php if (isset($errors['nome_completo'])): ?>
                            <div class="invalid-feedback d-block"><?php echo h($errors['nome_completo']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="edit_cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" id="edit_cpf" name="cpf"
                               required maxlength="14" inputmode="numeric" placeholder="Somente numeros"
                               value="<?php echo h((string) ($usuario['cpf'] ?? '')); ?>">
                        <?php if (isset($errors['cpf'])): ?>
                            <div class="invalid-feedback d-block"><?php echo h($errors['cpf']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="edit_data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" class="form-control" id="edit_data_nascimento" name="data_nascimento"
                               required max="<?php echo h((new DateTimeImmutable('today'))->format('Y-m-d')); ?>"
                               value="<?php echo h((string) ($usuario['data_nascimento'] ?? '')); ?>">
                        <?php if (isset($errors['data_nascimento'])): ?>
                            <div class="invalid-feedback d-block"><?php echo h($errors['data_nascimento']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="edit_telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="edit_telefone" name="telefone"
                               required inputmode="numeric" placeholder="Somente numeros" maxlength="15"
                               value="<?php echo h((string) ($usuario['telefone'] ?? '')); ?>">
                        <?php if (isset($errors['telefone'])): ?>
                            <div class="invalid-feedback d-block"><?php echo h($errors['telefone']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="edit_email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="edit_email" name="email"
                               required maxlength="120"
                               value="<?php echo h((string) ($usuario['email'] ?? '')); ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback d-block"><?php echo h($errors['email']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="edit_senha" class="form-label">Nova Senha <span class="text-muted fw-normal">(deixe em branco para manter a atual)</span></label>
                        <input type="password" class="form-control" id="edit_senha" name="senha" minlength="6" placeholder="Minimo 6 caracteres">
                        <?php if (isset($errors['senha'])): ?>
                            <div class="invalid-feedback d-block"><?php echo h($errors['senha']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="edit_endereco" class="form-label">Endereco</label>
                        <textarea class="form-control" id="edit_endereco" name="endereco" rows="3" required minlength="5" maxlength="255"><?php echo h((string) ($usuario['endereco'] ?? '')); ?></textarea>
                        <?php if (isset($errors['endereco'])): ?>
                            <div class="invalid-feedback d-block"><?php echo h($errors['endereco']); ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if ($isAdmin): ?>
                    <div class="mb-3">
                        <label for="edit_tipo" class="form-label">Tipo de Usuario</label>
                        <select class="form-select" id="edit_tipo" name="tipo">
                            <option value="comum" <?php echo ($usuario['tipo'] ?? 'comum') === 'comum' ? 'selected' : ''; ?>>Comum</option>
                            <option value="admin" <?php echo ($usuario['tipo'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-brand flex-fill">Salvar Alteracoes</button>
                        <a href="index.php?route=usuarios" class="btn btn-outline-secondary flex-fill">Cancelar</a>
                    </div>
                </form>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
