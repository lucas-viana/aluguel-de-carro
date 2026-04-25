<?php
declare(strict_types=1);

function digitsOnly(string $value): string
{
    return preg_replace('/\D+/', '', $value) ?? '';
}

function validateDateYmd(string $date): bool
{
    $parsed = DateTimeImmutable::createFromFormat('Y-m-d', $date);
    return $parsed !== false && $parsed->format('Y-m-d') === $date;
}



function validateUsuario(array $input, bool $adminCreating = false): array
{
    $errors = [];

    $nomeCompleto   = trim((string) ($input['nome_completo'] ?? ''));
    $cpf            = digitsOnly((string) ($input['cpf'] ?? ''));
    $dataNascimento = trim((string) ($input['data_nascimento'] ?? ''));
    $telefone       = digitsOnly((string) ($input['telefone'] ?? ''));
    $email          = trim((string) ($input['email'] ?? ''));
    $senha          = (string) ($input['senha'] ?? '');
    $endereco       = trim((string) ($input['endereco'] ?? ''));
    $tipo           = trim((string) ($input['tipo'] ?? 'comum'));

    if ($nomeCompleto === '' || strlen($nomeCompleto) < 3 || strlen($nomeCompleto) > 120) {
        $errors['nome_completo'] = 'Nome completo deve conter entre 3 e 120 caracteres.';
    }

    if (strlen($cpf) !== 11) {
        $errors['cpf'] = 'CPF deve conter 11 digitos.';
    }

    if (!validateDateYmd($dataNascimento)) {
        $errors['data_nascimento'] = 'Data de nascimento invalida.';
    } elseif ($dataNascimento > (new DateTimeImmutable('today'))->format('Y-m-d')) {
        $errors['data_nascimento'] = 'Data de nascimento nao pode estar no futuro.';
    }

    if (strlen($telefone) < 10 || strlen($telefone) > 11) {
        $errors['telefone'] = 'Telefone deve conter DDD + numero (10 ou 11 digitos).';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'E-mail invalido.';
    }

    if ($endereco === '' || strlen($endereco) < 5 || strlen($endereco) > 255) {
        $errors['endereco'] = 'Endereco deve conter entre 5 e 255 caracteres.';
    }

    if ($senha === '' || strlen($senha) < 6) {
        $errors['senha'] = 'Senha deve conter no minimo 6 caracteres.';
    }

    // Somente admin pode definir tipo; auto-registro sempre cria como 'comum'
    if (!$adminCreating || !in_array($tipo, ['admin', 'comum'], true)) {
        $tipo = 'comum';
    }

    return [
        [
            'nome_completo'   => $nomeCompleto,
            'cpf'             => $cpf,
            'data_nascimento' => $dataNascimento,
            'telefone'        => $telefone,
            'email'           => $email,
            'senha'           => password_hash($senha, PASSWORD_BCRYPT),
            'endereco'        => $endereco,
            'tipo'            => $tipo,
        ],
        $errors,
    ];
}

function validateUsuarioEdit(array $input, bool $adminEditing = false): array
{
    $errors = [];

    $nomeCompleto   = trim((string) ($input['nome_completo'] ?? ''));
    $cpf            = digitsOnly((string) ($input['cpf'] ?? ''));
    $dataNascimento = trim((string) ($input['data_nascimento'] ?? ''));
    $telefone       = digitsOnly((string) ($input['telefone'] ?? ''));
    $email          = trim((string) ($input['email'] ?? ''));
    $senha          = (string) ($input['senha'] ?? '');
    $endereco       = trim((string) ($input['endereco'] ?? ''));
    $tipo           = trim((string) ($input['tipo'] ?? 'comum'));

    if ($nomeCompleto === '' || strlen($nomeCompleto) < 3 || strlen($nomeCompleto) > 120) {
        $errors['nome_completo'] = 'Nome completo deve conter entre 3 e 120 caracteres.';
    }

    if (strlen($cpf) !== 11) {
        $errors['cpf'] = 'CPF deve conter 11 digitos.';
    }

    if (!validateDateYmd($dataNascimento)) {
        $errors['data_nascimento'] = 'Data de nascimento invalida.';
    } elseif ($dataNascimento > (new DateTimeImmutable('today'))->format('Y-m-d')) {
        $errors['data_nascimento'] = 'Data de nascimento nao pode estar no futuro.';
    }

    if (strlen($telefone) < 10 || strlen($telefone) > 11) {
        $errors['telefone'] = 'Telefone deve conter DDD + numero (10 ou 11 digitos).';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'E-mail invalido.';
    }

    if ($endereco === '' || strlen($endereco) < 5 || strlen($endereco) > 255) {
        $errors['endereco'] = 'Endereco deve conter entre 5 e 255 caracteres.';
    }

    // Senha é opcional na edição; se preenchida, valida o tamanho
    if ($senha !== '' && strlen($senha) < 6) {
        $errors['senha'] = 'Nova senha deve conter no minimo 6 caracteres.';
    }

    if (!$adminEditing || !in_array($tipo, ['admin', 'comum'], true)) {
        $tipo = 'comum';
    }

    $clean = [
        'nome_completo'   => $nomeCompleto,
        'cpf'             => $cpf,
        'data_nascimento' => $dataNascimento,
        'telefone'        => $telefone,
        'email'           => $email,
        'endereco'        => $endereco,
        'tipo'            => $tipo,
    ];

    if ($senha !== '') {
        $clean['senha'] = password_hash($senha, PASSWORD_BCRYPT);
    }

    return [$clean, $errors];
}

function normalizePlaca(string $placa): string
{
    $placa = strtoupper(trim($placa));
    return preg_replace('/[^A-Z0-9]/', '', $placa) ?? '';
}

function validateVeiculo(array $input): array
{
    $errors = [];

    $modelo = trim((string) ($input['modelo'] ?? ''));
    $cor = trim((string) ($input['cor'] ?? ''));
    $fabricante = trim((string) ($input['fabricante'] ?? ''));
    $placa = normalizePlaca((string) ($input['placa'] ?? ''));
    $disponivel = (int) (($input['disponivel'] ?? '1') === '1');

    if ($modelo === '' || strlen($modelo) < 2 || strlen($modelo) > 100) {
        $errors['modelo'] = 'Modelo deve conter entre 2 e 100 caracteres.';
    }

    if ($cor === '' || strlen($cor) < 2 || strlen($cor) > 40) {
        $errors['cor'] = 'Cor deve conter entre 2 e 40 caracteres.';
    }

    if ($fabricante === '' || strlen($fabricante) < 2 || strlen($fabricante) > 80) {
        $errors['fabricante'] = 'Fabricante deve conter entre 2 e 80 caracteres.';
    }

    if (preg_match('/^[A-Z]{3}\d[A-Z0-9]\d{2}$/', $placa) !== 1) {
        $errors['placa'] = 'Placa invalida. Use formato ABC1D23 ou ABC1234.';
    }

    return [
        [
            'modelo' => $modelo,
            'cor' => $cor,
            'fabricante' => $fabricante,
            'placa' => $placa,
            'disponivel' => $disponivel,
        ],
        $errors,
    ];
}

function validateAluguel(array $input): array
{
    $errors = [];

    $usuarioId = filter_var($input['usuario_id'] ?? null, FILTER_VALIDATE_INT);
    $veiculoId = filter_var($input['veiculo_id'] ?? null, FILTER_VALIDATE_INT);
    $dataRetirada = trim((string) ($input['data_retirada'] ?? ''));
    $dataEntrega = trim((string) ($input['data_entrega'] ?? ''));
    $formaPagamento = trim((string) ($input['forma_pagamento'] ?? ''));
    $metodos = array_keys(paymentMethods());

    if ($usuarioId === false || $usuarioId <= 0) {
        $errors['usuario_id'] = 'Usuario invalido.';
    }

    if ($veiculoId === false || $veiculoId <= 0) {
        $errors['veiculo_id'] = 'Veiculo invalido.';
    }

    if (!validateDateYmd($dataRetirada)) {
        $errors['data_retirada'] = 'Data de retirada invalida.';
    }

    if (!validateDateYmd($dataEntrega)) {
        $errors['data_entrega'] = 'Data de entrega invalida.';
    }

    if (!isset($errors['data_retirada']) && !isset($errors['data_entrega']) && $dataEntrega < $dataRetirada) {
        $errors['data_entrega'] = 'Data de entrega deve ser maior ou igual a data de retirada.';
    }

    if (!in_array($formaPagamento, $metodos, true)) {
        $errors['forma_pagamento'] = 'Forma de pagamento invalida.';
    }

    return [
        [
            'usuario_id' => (int) $usuarioId,
            'veiculo_id' => (int) $veiculoId,
            'data_retirada' => $dataRetirada,
            'data_entrega' => $dataEntrega,
            'forma_pagamento' => $formaPagamento,
        ],
        $errors,
    ];
}
