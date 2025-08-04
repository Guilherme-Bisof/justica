<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/auth.php';
permitir(['admin', 'recepcao_agenda', 'psicologa']);

// Consulta para buscar os eventos
$sql = "SELECT id, nome_completo, data_agendamento, hora_agendamento, psicologa, prioridade, observacoes, status 
        FROM agendamentos_escuta";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['nome_completo'],
        'start' => $row['data_agendamento'] . 'T' . $row['hora_agendamento'],
        'extendedProps' => [
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
echo json_encode($events);
?>