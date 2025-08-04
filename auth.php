<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

function permitir($tiposPermitidos) {
    if (!in_array($_SESSION['usuario_tipo'], (array)$tiposPermitidos)) {
        header("Location: acesso_negado.php");
        exit();
    }
}


?>
