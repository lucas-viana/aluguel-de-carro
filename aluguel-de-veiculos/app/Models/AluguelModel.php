<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class AluguelModel
{
    private const MIN_DAYS_BETWEEN_RENTALS = 2;

    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): array
    {
        $errors = [];

        $this->pdo->beginTransaction();

        try {
            $usuarioStmt = $this->pdo->prepare('SELECT id FROM usuarios WHERE id = :id');
            $usuarioStmt->execute(['id' => $data['usuario_id']]);
            $usuario = $usuarioStmt->fetch();

            if (!$usuario) {
                $errors['usuario_id'] = 'Usuario selecionado nao existe.';
            }

            $veiculoStmt = $this->pdo->prepare('SELECT id, disponivel FROM veiculos WHERE id = :id FOR UPDATE');
            $veiculoStmt->execute(['id' => $data['veiculo_id']]);
            $veiculo = $veiculoStmt->fetch();

            if (!$veiculo) {
                $errors['veiculo_id'] = 'Veiculo selecionado nao existe.';
            }

            if ($veiculo && $this->hasDateConflict(
                (int) $data['veiculo_id'],
                (string) $data['data_retirada'],
                (string) $data['data_entrega']
            )) {
                $errors['veiculo_id'] = sprintf(
                    'Este veiculo exige intervalo minimo de %d dias entre alugueis. Escolha outro periodo.',
                    self::MIN_DAYS_BETWEEN_RENTALS
                );
            }

            if (!empty($errors)) {
                $this->pdo->rollBack();
                return $errors;
            }

            $insertAluguel = $this->pdo->prepare(
                "INSERT INTO alugueis (data_retirada, data_entrega, forma_pagamento, usuario_id, veiculo_id, status)
                 VALUES (:data_retirada, :data_entrega, :forma_pagamento, :usuario_id, :veiculo_id, 'ativo')"
            );
            $insertAluguel->execute($data);

            $updateVeiculo = $this->pdo->prepare('UPDATE veiculos SET disponivel = 0 WHERE id = :id');
            $updateVeiculo->execute(['id' => $data['veiculo_id']]);

            $this->pdo->commit();
            return [];
        } catch (\Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw new AluguelOperationException('Erro ao registrar aluguel.', 0, $throwable);
        }
    }

    public function finalizar(int $aluguelId): string
    {
        $this->pdo->beginTransaction();

        try {
            $aluguelStmt = $this->pdo->prepare('SELECT id, veiculo_id, status, data_retirada FROM alugueis WHERE id = :id FOR UPDATE');
            $aluguelStmt->execute(['id' => $aluguelId]);
            $aluguel = $aluguelStmt->fetch();

            if (!$aluguel) {
                $this->pdo->rollBack();
                return 'not_found';
            }

            if ($aluguel['status'] !== 'ativo') {
                $this->pdo->rollBack();
                return 'already_done';
            }

            if ($aluguel['data_retirada'] > (new \DateTimeImmutable('today'))->format('Y-m-d')) {
                $this->pdo->rollBack();
                return 'not_started';
            }

            $finalizaAluguel = $this->pdo->prepare("UPDATE alugueis SET status = 'finalizado', finalizado_em = NOW() WHERE id = :id");
            $finalizaAluguel->execute(['id' => $aluguelId]);

            $liberaVeiculo = $this->pdo->prepare('UPDATE veiculos SET disponivel = 1 WHERE id = :id');
            $liberaVeiculo->execute(['id' => $aluguel['veiculo_id']]);

            $this->pdo->commit();
            return 'ok';
        } catch (\Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw new AluguelOperationException('Erro ao finalizar aluguel.', 0, $throwable);
        }
    }

    public function listAll(): array
    {
        return $this->pdo->query(
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
    }

    private function hasDateConflict(int $veiculoId, string $dataRetirada, string $dataEntrega): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM alugueis
             WHERE veiculo_id = :veiculo_id
             AND data_retirada < DATE_ADD(:nova_entrega, INTERVAL :intervalo_fim DAY)
             AND :nova_retirada < DATE_ADD(data_entrega, INTERVAL :intervalo_inicio DAY)'
        );
        $stmt->bindValue(':veiculo_id', $veiculoId, PDO::PARAM_INT);
        $stmt->bindValue(':nova_retirada', $dataRetirada);
        $stmt->bindValue(':nova_entrega', $dataEntrega);
         $stmt->bindValue(':intervalo_fim', self::MIN_DAYS_BETWEEN_RENTALS, PDO::PARAM_INT);
         $stmt->bindValue(':intervalo_inicio', self::MIN_DAYS_BETWEEN_RENTALS, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }
}
