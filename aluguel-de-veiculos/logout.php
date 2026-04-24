<?php
// Inicia a sessão para poder manipulá-la
session_start();

// Remove todas as variáveis de sessão
session_unset();

// Destrói a sessão completamente
session_destroy();

// Redireciona o usuário para a tela de login ou index
header("Location: login.php");
exit();
?>