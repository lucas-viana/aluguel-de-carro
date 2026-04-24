<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class UsuarioModel
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (nome_completo, cpf, data_nascimento, telefone, email, senha, endereco) VALUES (:nome_completo, :cpf, :data_nascimento, :telefone, :email, :senha, :endereco)'
        );
        $stmt->execute($data);
    }

    public function deleteById(int $id): int
    {
        $stmt = $this->pdo->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount();
    }

    public function listAll(): array
    {
        return $this->pdo->query(
            'SELECT id, nome_completo, cpf, data_nascimento, telefone, email, endereco FROM usuarios ORDER BY id DESC'
        )->fetchAll();
    }

    public function listBasic(): array
    {
        return $this->pdo->query('SELECT id, nome_completo, cpf FROM usuarios ORDER BY nome_completo')->fetchAll();
    }
}

