<?php
header('Content-Type: application/json; charset=utf-8');
include '../conexao.php';

$sql = "SELECT id, nome_completo, data_agendamento, hora_agendamento, prioridade, psicologa, status 
        FROM pedidos_escuta";
$result = $conn->query($sql);

$eventos = [
    [
        'id'    => 1,
        'title' => 'Maria Silva - Psicóloga Ana',
        'start' => '2025-08-05T14:00:00',
        'extendedProps' => [
            'id' => 1,
            'nome_completo' => 'Maria Silva',
            'data_agendamento' => '2025-08-05',
            'hora_agendamento' => '14:00',
            'psicologa' => 'Ana',
            'prioridade' => 'Alta',
            'status' => 'Agendado'
        ]
    ],
    [
        'id'    => 2,
        'title' => 'João Santos - Psicóloga Carla',
        'start' => '2025-08-07T09:30:00',
        'extendedProps' => [
            'id' => 2,
            'nome_completo' => 'João Santos',
            'data_agendamento' => '2025-08-07',
            'hora_agendamento' => '09:30',
            'psicologa' => 'Carla',
            'prioridade' => 'Média',
            'status' => 'Confirmado'
        ]
    ]
];

echo json_encode($eventos, JSON_UNESCAPED_UNICODE);