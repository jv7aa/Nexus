<?php
// 1. Inicia a sessão (precisamos "entrar" nela para poder destruí-la)
session_start();

// 2. Limpa todas as variáveis da sessão
$_SESSION = array();

// 3. Destrói a sessão no servidor
session_destroy();

// 4. O PULO DO GATO: Redireciona para o login
header("Location: index.php");

// 5. Encerra o script para garantir que nada mais rode
exit;
?>