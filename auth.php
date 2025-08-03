<?php
include 'auth.php';
permitir(['admin', 'recepcao_agenda']);
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Função para restringir por tipo
function permitir($tiposPermitidos) {
    if (!in_array($_SESSION['usuario_tipo'], (array)$tiposPermitidos)) {
        header("Location: acesso_negado.php");
        exit();
    }
}


?>
