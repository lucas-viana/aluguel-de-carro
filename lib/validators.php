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

function isValidCPF(string $cpf): bool
{
    $cpf = digitsOnly($cpf);
    $isValid = true;

    if (strlen($cpf) !== 11) {
        $isValid = false;
    }

    if ($isValid && preg_match('/^(\d)\1{10}$/', $cpf) === 1) {
        $isValid = false;
    }

    if ($isValid) {
        for ($length = 9; $length < 11; $length++) {
            $sum = 0;

            for ($i = 0; $i < $length; $i++) {
                $sum += ((int) $cpf[$i]) * (($length + 1) - $i);
            }

            $digit = ((10 * $sum) % 11) % 10;

            if ((int) $cpf[$length] !== $digit) {
                $isValid = false;
                break;
            }
        }
    }

    return $isValid;
}

function validateUsuario(array $input): array
{
    $errors = [];

    $nomeCompleto = trim((string) ($input['nome_completo'] ?? ''));
    $cpf = digitsOnly((string) ($input['cpf'] ?? ''));
    $dataNascimento = trim((string) ($input['data_nascimento'] ?? ''));
    $telefone = digitsOnly((string) ($input['telefone'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $endereco = trim((string) ($input['endereco'] ?? ''));

    if ($nomeCompleto === '' || strlen($nomeCompleto) < 3 || strlen($nomeCompleto) > 120) {
        $errors['nome_completo'] = 'Nome completo deve conter entre 3 e 120 caracteres.';
    }

    if (!isValidCPF($cpf)) {
        $errors['cpf'] = 'CPF invalido.';
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

    return [
        [
            'nome_completo' => $nomeCompleto,
            'cpf' => $cpf,
            'data_nascimento' => $dataNascimento,
            'telefone' => $telefone,
            'email' => $email,
            'endereco' => $endereco,
        ],
        $errors,
    ];
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
