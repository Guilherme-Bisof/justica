<<?php 
session_start();

// Destrói todas as variáveis de sessão
$_SESSION = [];

// Se desejar destruir a sessão completamente, também pode destruir o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroi a sessão
session_destroy();

// Redireciona para a página de login
header("Location: login.php");
exit();
?>