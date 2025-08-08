<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/auth.php';
permitir(['admin', 'recepcao_agenda', 'psicologa']);

$action = $_GET['action'] ?? '';

if ($action === 'create') {
    // Receber dados do formulário
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION['usuario_tipo'] === 'psicologa') {
        $psicologa_id = $_SESSION['usuario_id'];
        $psicologa_nome = $_SESSION['usuario_nome'];
    } else {
        $psicologa_id = $data['psicologa_id'];
        $psicologa_nome = $data['psicologa'];
    }
    
    // Inserir no banco de dados
    $sql = "INSERT INTO agendamentos_escuta (
        nome_completo, 
        psicologa_id,
        psicologa,
        data_agendamento, 
        hora_agendamento, 
        prioridade, 
        observacoes, 
        status,
        pedido_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $pedido_id = $data['pedido_id'] ?? null;
    $stmt->bind_param(
        "sissssssi",
        $data['nome_completo'],
        $psicologa_id,
        $psicologa_nome,
        $data['data_agendamento'],
        $data['hora_agendamento'],
        $data['psicologa'],
        $data['prioridade'],
        $data['observacoes'],
        $data['status'],
        $pedido_id
    );
    
    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        // Retornar o novo evento criado
        echo json_encode([
            'id' => $id,
            'title' => $data['nome_completo'] . " - " . $data['hora_agendamento'],
            'start' => $data['data_agendamento'] . 'T' . $data['hora_agendamento'],
            'extendedProps' => [
                'id' => $id,
                'nome_completo' => $data['nome_completo'],
                'psicologa' => $psicologa_nome,
                'data_agendamento' => $data['data_agendamento'],
                'hora_agendamento' => $data['hora_agendamento'],
                'prioridade' => $data['prioridade'],
                'observacoes' => $data['observacoes'],
                'status' => $data['status']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit;
}

if ($action === 'delete') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    
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

// ... (código para listar eventos do banco) ...
?>