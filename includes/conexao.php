<?php 
    $host = "localhost";
    $user = "root";
    $pass = "suporte123@"; 
    $dbname = "justica";

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
?>