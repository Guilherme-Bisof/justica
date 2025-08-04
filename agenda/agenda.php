<?php

require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/auth.php';
permitir(['admin', 'recepcao_agenda', 'psicologa']);

$action = $_GET['action'] ?? '';

if ($action === 'delete') {
    $id = $_POST['id'];
    
    $sql = "DELETE FROM agendamentos_escuta WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit;
}

// ... [código para create] ...


header('Content-Type: application/json; charset=utf-8');

// Sempre retorna dados de exemplo primeiro para testar
$eventos = [
    [
        'id' => 1,
        'title' => "Mayara Santos\n14:00 Psic. Aline",
        'start' => '2025-08-05T14:00:00',
        'extendedProps' => [
            'id' => 1,
            'nome_completo' => 'Mayara Santos',
            'data_agendamento' => '2025-08-05',
            'hora_agendamento' => '14:00',
            'psicologa' => 'Psic. Aline',
            'prioridade' => 'Alta',
            'status' => 'Agendado'
        ]
    ],
    [
        'id' => 2,
        'title' => "Felipe Oliveira\n09:30 Psic. Hugo",
        'start' => '2025-08-12T09:30:00',
        'extendedProps' => [
            'id' => 2,
            'nome_completo' => 'Felipe Oliveira',
            'data_agendamento' => '2025-08-12',
            'hora_agendamento' => '09:30',
            'psicologa' => 'Psic. Hugo',
            'prioridade' => 'Média',
            'status' => 'Confirmado'
        ]
    ],
    [
        'id' => 3,
        'title' => "Sérgio Almeida\n11:00 Psic. Laura",
        'start' => '2025-08-19T11:00:00',
        'extendedProps' => [
            'id' => 3,
            'nome_completo' => 'Sérgio Almeida',
            'data_agendamento' => '2025-08-19',
            'hora_agendamento' => '11:00',
            'psicologa' => 'Psic. Laura',
            'prioridade' => 'Baixa',
            'status' => 'Agendado'
        ]
    ]
];

echo json_encode($eventos, JSON_UNESCAPED_UNICODE);
?>