<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class VeiculoModel
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO veiculos (modelo, cor, fabricante, placa, disponivel) VALUES (:modelo, :cor, :fabricante, :placa, :disponivel)'
        );
        $stmt->execute($data);
    }

    public function deleteById(int $id): int
    {
        $stmt = $this->pdo->prepare('DELETE FROM veiculos WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount();
    }

    public function updateDisponibilidade(int $id, int $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE veiculos SET disponivel = :status WHERE id = :id');
        $stmt->execute([
            'status' => $status,
            'id' => $id,
        ]);
    }

    public function hasActiveRental(int $veiculoId): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM alugueis WHERE veiculo_id = :id AND status = 'ativo'");
        $stmt->execute(['id' => $veiculoId]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function listAll(): array
    {
        return $this->pdo->query('SELECT id, modelo, cor, fabricante, placa, disponivel FROM veiculos ORDER BY id DESC')->fetchAll();
    }

    public function listAvailable(): array
    {
        return $this->pdo->query('SELECT id, modelo, fabricante, placa FROM veiculos WHERE disponivel = 1 ORDER BY modelo')->fetchAll();
    }
}

