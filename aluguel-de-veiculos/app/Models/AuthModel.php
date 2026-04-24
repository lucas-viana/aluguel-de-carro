<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class AuthModel
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findUserByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare('SELECT id, nome_completo, email, senha, tipo FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);

        return $stmt->fetch();
    }
}
