<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class DashboardModel
{
    public function __construct(private PDO $pdo)
    {
    }

    public function getStats(): array
    {
        $totalUsuarios = (int) $this->pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
        $totalVeiculos = (int) $this->pdo->query('SELECT COUNT(*) FROM veiculos')->fetchColumn();
        $veiculosDisponiveis = (int) $this->pdo->query('SELECT COUNT(*) FROM veiculos WHERE disponivel = 1')->fetchColumn();
        $alugueisAtivos = (int) $this->pdo->query("SELECT COUNT(*) FROM alugueis WHERE status = 'ativo'")->fetchColumn();

        return [
            'totalUsuarios' => $totalUsuarios,
            'totalVeiculos' => $totalVeiculos,
            'veiculosDisponiveis' => $veiculosDisponiveis,
            'alugueisAtivos' => $alugueisAtivos,
        ];
    }

    public function getRecentRentals(int $limit = 5): array
    {
        $safeLimit = max(1, $limit);

        $stmt = $this->pdo->prepare(
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
            LIMIT :limit"
        );
        $stmt->bindValue(':limit', $safeLimit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
