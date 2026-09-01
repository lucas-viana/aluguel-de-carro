<?php
/**
 * Script para popular as senhas dos usuários de teste
 * 
 * Execute este script uma vez após rodar o seed.sql:
 * php database/setup-passwords.php
 * 
 * Credenciais de teste:
 * - lucas.almeida@email.com / senha123
 * - mariana.costa@email.com / senha456
 * - rafael.souza@email.com / senha789
 * - camila.pereira@email.com / senha000
 * - bruno.martins@email.com / senha111
 * - ana.lima@email.com / senha222
 */

require_once __DIR__ . '/../config/database.php';

$usuarios = [
    ['email' => 'lucas.almeida@email.com', 'senha' => 'senha123'],
    ['email' => 'mariana.costa@email.com', 'senha' => 'senha123'],
    ['email' => 'rafael.souza@email.com', 'senha' => 'senha123'],
    ['email' => 'camila.pereira@email.com', 'senha' => 'senha123'],
    ['email' => 'bruno.martins@email.com', 'senha' => 'senha123'],
    ['email' => 'ana.lima@email.com', 'senha' => 'senha123'],
];

try {
    $pdo = getConnection();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('UPDATE usuarios SET senha = :senha WHERE email = :email');

    foreach ($usuarios as $usuario) {
        $senhaHasheada = password_hash($usuario['senha'], PASSWORD_BCRYPT);
        $stmt->execute([
            ':email' => $usuario['email'],
            ':senha' => $senhaHasheada
        ]);
        echo "✓ Senha atualizada para: {$usuario['email']}\n";
    }

    $pdo->commit();
    echo "\n✅ Senhas de teste foram configuradas com sucesso!\n";

} catch (Exception $e) {
    echo "❌ Erro ao configurar senhas: " . $e->getMessage() . "\n";
    exit(1);
}
