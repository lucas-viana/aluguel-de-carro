<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getConnection();
    $stmt = $pdo->prepare('SELECT email, senha FROM usuarios WHERE email = ?');
    $stmt->execute(['ana.lima@email.com']);
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        echo "Email: " . $usuario['email'] . PHP_EOL;
        echo "Hash armazenado: " . $usuario['senha'] . PHP_EOL;
        echo PHP_EOL;
        
        // Testar password_verify
        $teste_senha = 'senha222';
        $resultado = password_verify($teste_senha, $usuario['senha']);
        echo "Testando password_verify('senha222', hash): " . ($resultado ? 'SUCESSO ✓' : 'FALHA ✗') . PHP_EOL;
    } else {
        echo 'Usuário não encontrado' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
}
