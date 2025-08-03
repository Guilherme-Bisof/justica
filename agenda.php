<?php
include 'conexao.php';

$sql = "SELECT id, nome_completo, data_agendamento, hora_agendamento, prioridade, psicologa, status 
        FROM pedidos_escuta";
$result = $conn->query($sql);

$eventos = [];
while ($row = $result->fetch_assoc()) {
    $eventos[] = $row;
}

echo json_encode($eventos);
?>
