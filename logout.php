<?php
session_start();
session_destroy();
echo "✅ Sessão destruída! Você foi deslogado.<br>";
echo "<a href='login.php'>Clique aqui para fazer login novamente</a>";
