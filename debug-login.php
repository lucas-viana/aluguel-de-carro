<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/lib/helpers.php';

startSessionIfNeeded();

$email = 'ana.lima@email.com';
$senha = 'senha222';

echo "=== TESTE DE LOGIN ===" . PHP_EOL;
echo "Email: $email" . PHP_EOL;
echo "Senha: $senha" . PHP_EOL;
echo PHP_EOL;

try {
    $pdo = getConnection();
    
    // Simular exatamente o que login.php faz
    $stmt = $pdo->prepare('SELECT id, nome_completo, email, senha FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        echo "❌ ERRO: Usuário não encontrado no banco de dados!" . PHP_EOL;
        exit(1);
    }
    
    echo "✓ Usuário encontrado:" . PHP_EOL;
    echo "  ID: " . $usuario['id'] . PHP_EOL;
    echo "  Nome: " . $usuario['nome_completo'] . PHP_EOL;
    echo "  Email: " . $usuario['email'] . PHP_EOL;
    echo "  Hash armazenado: " . substr($usuario['senha'], 0, 20) . "..." . PHP_EOL;
    echo PHP_EOL;
    
    // Testar password_verify
    $verificado = password_verify($senha, $usuario['senha']);
    
    if ($verificado) {
        echo "✓ password_verify SUCESSO!" . PHP_EOL;
        echo "  Senha '$senha' corresponde ao hash armazenado" . PHP_EOL;
        echo PHP_EOL;
        
        // Simular criar sessão
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome_completo'];
        
        echo "✓ Sessão criada com sucesso:" . PHP_EOL;
        echo "  \$_SESSION['usuario_id'] = " . $_SESSION['usuario_id'] . PHP_EOL;
        echo "  \$_SESSION['usuario_nome'] = " . $_SESSION['usuario_nome'] . PHP_EOL;
        echo PHP_EOL;
        echo "✅ LOGIN DEVE FUNCIONAR!" . PHP_EOL;
    } else {
        echo "❌ password_verify FALHOU!" . PHP_EOL;
        echo "  A senha '$senha' NÃO corresponde ao hash armazenado" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
