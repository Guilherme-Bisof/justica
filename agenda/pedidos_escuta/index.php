<?php
require_once __DIR__ . '/../../includes/conexao.php';
require_once __DIR__ . '/../../includes/auth.php';
permitir(['admin', 'recepcao_agenda']);

// Consulta pedidos usando MySQLi
$sql = "SELECT * FROM pedidos_escuta ORDER BY data_entrada DESC";
$result = $conn->query($sql);

if ($result) {
    $pedidos = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos de Escuta</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --gray: #95a5a6;
            --light-gray: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .header::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -30px;
            width: 250px;
            height: 250px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 20px 25px;
            border: none;
        }
        
        .table-container {
            overflow: hidden;
            border-radius: 0 0 12px 12px;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: var(--light-gray);
            font-weight: 600;
            color: var(--dark);
            border-bottom: 2px solid #dee2e6;
            padding: 16px 20px;
        }
        
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .table tbody td {
            padding: 14px 20px;
            vertical-align: middle;
            border-top: 1px solid #edf2f7;
        }
        
        .badge-prioridade {
            padding: 7px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
            min-width: 70px;
            text-align: center;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .btn-new {
            background: linear-gradient(135deg, var(--accent), #16a085);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(26, 188, 156, 0.3);
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-new:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(26, 188, 156, 0.4);
            color: white;
        }
        
        .btn-back {
            position: absolute;
            left: 20px;
            top: 20px;
            z-index: 10;
        }
        
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        .btn-action i {
            font-size: 0.9rem;
        }
        
        .btn-action:hover {
            transform: scale(1.1);
        }
        
        .empty-state {
            padding: 50px 20px;
            text-align: center;
            background-color: white;
            border-radius: 12px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #e9ecef;
            margin-bottom: 20px;
        }
        
        .priority-high {
            background-color: #ffebee;
            color: #d32f2f;
            border-left: 4px solid #f44336;
        }
        
        .priority-medium {
            background-color: #fff8e1;
            color: #f57c00;
            border-left: 4px solid #ffb300;
        }
        
        .priority-low {
            background-color: #e8f5e9;
            color: #388e3c;
            border-left: 4px solid #4caf50;
        }
        
        .status-pendente {
            background-color: #e0e0e0;
            color: #424242;
        }
        
        .status-agendado {
            background-color: #bbdefb;
            color: #0d47a1;
        }
        
        .status-concluido {
            background-color: #c8e6c9;
            color: #1b5e20;
        }
        
        .status-recusado {
            background-color: #ffcdd2;
            color: #b71c1c;
        }
        
        .action-cell {
            min-width: 150px;
        }
        
        .page-title {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .page-description {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
            
            .table {
                min-width: 800px;
            }
            
            .btn-back {
                position: relative;
                left: 0;
                top: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <div class="container position-relative">
            <!-- Botão Voltar ao Painel -->
            <a href="../../painel.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar ao Painel
            </a>
            
            <h1 class="page-title"><i class="fas fa-headphones me-2"></i>Pedidos de Escuta</h1>
            <p class="page-description">Controle e gerenciamento de solicitações de escuta</p>
        </div>
    </div>

    <div class="container py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-2"></i>
                    <span>Lista de Pedidos</span>
                </div>
                <a href="novo.php" class="btn btn-new">
                    <i class="fas fa-plus me-1"></i>Novo Pedido
                </a>
            </div>
            
            <div class="table-container">
                <?php if(count($pedidos) > 0): ?>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-calendar-day me-2"></i> Data Entrada</th>
                                <th><i class="fas fa-user me-2"></i> Nome</th>
                                <th><i class="fas fa-building me-2"></i> Referência</th>
                                <th><i class="fas fa-exclamation-triangle me-2"></i> Tipo Violência</th>
                                <th><i class="fas fa-flag me-2"></i> Prioridade</th>
                                <th><i class="fas fa-tasks me-2"></i> Status</th>
                                <th class="action-cell"><i class="fas fa-cogs me-2"></i> Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($pedido['data_entrada'])) ?></td>
                                <td><?= htmlspecialchars($pedido['nome_completo']) ?></td>
                                <td><?= htmlspecialchars($pedido['referencia']) ?></td>
                                <td><?= htmlspecialchars($pedido['tipo_violencia']) ?></td>
                                <td>
                                    <span class="badge-prioridade 
                                        <?= $pedido['prioridade'] === 'Alta' ? 'priority-high' : '' ?>
                                        <?= $pedido['prioridade'] === 'Média' ? 'priority-medium' : '' ?>
                                        <?= $pedido['prioridade'] === 'Baixa' ? 'priority-low' : '' ?>">
                                        <?= $pedido['prioridade'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge 
                                        <?= $pedido['status'] === 'Pendente' ? 'status-pendente' : '' ?>
                                        <?= $pedido['status'] === 'Agendado' ? 'status-agendado' : '' ?>
                                        <?= $pedido['status'] === 'Concluído' ? 'status-concluido' : '' ?>
                                        <?= $pedido['status'] === 'Recusado' ? 'status-recusado' : '' ?>">
                                        <?= $pedido['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <?php if ($pedido['status'] === 'Pendente'): ?>
                                            <a href="../../agenda/index.php?pedido_id=<?= $pedido['id'] ?>" 
                                            class="btn btn-success btn-action text-white" 
                                            title="Agendar">
                                            <i class="fas fa-calendar-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="editar.php?id=<?= $pedido['id'] ?>" 
                                           class="btn btn-warning btn-action" 
                                           title="Editar">
                                           <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="excluir.php?id=<?= $pedido['id'] ?>" 
                                           class="btn btn-danger btn-action" 
                                           title="Excluir"
                                           onclick="return confirm('Tem certeza que deseja excluir este pedido?')">
                                           <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3 class="text-muted mb-2">Nenhum pedido encontrado</h3>
                        <p class="text-muted mb-4">Você ainda não possui pedidos de escuta cadastrados</p>
                        <a href="novo.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Criar Primeiro Pedido
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted">
            <p>Sistema de Pedidos de Escuta &copy; <?= date('Y') ?></p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Adicionar efeito de confirmação para exclusão
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-danger');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Tem certeza que deseja excluir este pedido permanentemente?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>