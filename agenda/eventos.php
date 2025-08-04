<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/auth.php';
permitir(['admin', 'recepcao_agenda', 'psicologa']);

// Consultar eventos no banco de dados
$sql = "SELECT * FROM agendamentos_escuta";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$eventos = [];
while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        'id' => $row['id'],
        'title' => $row['nome_completo'] . " - " . $row['hora_agendamento'],
        'start' => $row['data_agendamento'] . 'T' . $row['hora_agendamento'],
        'extendedProps' => [
            'id' => $row['id'],
            'nome_completo' => $row['nome_completo'],
            'data_agendamento' => $row['data_agendamento'],
            'hora_agendamento' => $row['hora_agendamento'],
            'psicologa' => $row['psicologa'],
            'prioridade' => $row['prioridade'],
            'observacoes' => $row['observacoes'],
            'status' => $row['status']
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($eventos, JSON_UNESCAPED_UNICODE);
?>