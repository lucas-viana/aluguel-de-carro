<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
echo "=== DEBUG LOGIN ===\n";
echo "URL: " . $_SERVER['REQUEST_URI'] . "\n";
echo "METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "\n";

echo "SESSION STATUS: " . session_status() . "\n";
if (session_status() === PHP_SESSION_NONE) {
    echo "  (PHP_SESSION_NONE - não iniciada)\n";
    session_start();
} elseif (session_status() === PHP_SESSION_ACTIVE) {
    echo "  (PHP_SESSION_ACTIVE - ativa)\n";
} elseif (session_status() === PHP_SESSION_DISABLED) {
    echo "  (PHP_SESSION_DISABLED - desabilitada)\n";
}

echo "\nSESSION ID: " . session_id() . "\n";
echo "SESSION DATA: " . json_encode($_SESSION) . "\n";
echo "\n";

if (isset($_SESSION['usuario_id'])) {
    echo "⚠️ usuario_id encontrado na sessão!\n";
    echo "REDIRECIONANDO PARA INDEX.PHP\n";
    header('Location: index.php');
    exit;
} else {
    echo "✓ Nenhum usuario_id na sessão\n";
    echo "✓ Continuando para mostrar login.php\n";
}

echo "</pre>";
?>
<hr>
<p>Se chegou aqui, a página de login deveria aparecer agora!</p>
