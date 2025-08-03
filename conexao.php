<?php 
    $host = "localhost";
    $user = "root";
    $pass = ""; 
    $dbname = "justica";

    $conn = new mysqli($host, $user, $pass, $fbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
?>