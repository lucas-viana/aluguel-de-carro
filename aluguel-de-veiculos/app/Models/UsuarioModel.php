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
            'INSERT INTO usuarios (nome_completo, cpf, data_nascimento, telefone, email, senha, endereco, tipo)
             VALUES (:nome_completo, :cpf, :data_nascimento, :telefone, :email, :senha, :endereco, :tipo)'
        );
        $stmt->execute($data);
    }

    public function update(int $id, array $data): void
    {
        $fields = 'nome_completo = :nome_completo, cpf = :cpf, data_nascimento = :data_nascimento,
                   telefone = :telefone, email = :email, endereco = :endereco, tipo = :tipo';

        if (!empty($data['senha'])) {
            $fields .= ', senha = :senha';
        }

        $stmt = $this->pdo->prepare("UPDATE usuarios SET {$fields} WHERE id = :id");
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nome_completo, cpf, data_nascimento, telefone, email, endereco, tipo
             FROM usuarios WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
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
            'SELECT id, nome_completo, cpf, data_nascimento, telefone, email, endereco, tipo FROM usuarios ORDER BY id DESC'
        )->fetchAll();
    }

    public function listBasic(): array
    {
        return $this->pdo->query('SELECT id, nome_completo, cpf FROM usuarios ORDER BY nome_completo')->fetchAll();
    }
}
