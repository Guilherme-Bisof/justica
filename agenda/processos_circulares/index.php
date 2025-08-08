<?php
// processos_circulares/index.php
require_once __DIR__ . '/../../includes/conexao.php';
require_once __DIR__ . '/../../includes/auth.php';
permitir(['admin', 'recepcao', 'psicologa']);

// Obter filtros da URL
$filtro_status = $_GET['filtro'] ?? 'todos';
$filtro_situacao = $_GET['situacao'] ?? 'todos';

// Construir a consulta SQL com base nos filtros
$sql = "SELECT * FROM processos_circulares WHERE 1=1";

// Aplicar filtros
if ($filtro_status !== 'todos') {
    $sql .= " AND status = '" . $conn->real_escape_string($filtro_status) . "'";
}

if ($filtro_situacao !== 'todos') {
    $sql .= " AND situacao = '" . $conn->real_escape_string($filtro_situacao) . "'";
}

$sql .= " ORDER BY data_entrada DESC";

$result = $conn->query($sql);

if ($result) {
    $processos = $result->fetch_all(MYSQLI_ASSOC);
} else {
    die("Erro na consulta: " . $conn->error);
}

// Consulta para estatísticas (todos os registros, sem filtros)
$sql_stats = "SELECT * FROM processos_circulares";
$result_stats = $conn->query($sql_stats);
$todos_processos = $result_stats->fetch_all(MYSQLI_ASSOC);

// Contagem por status
$stats = [
    'total' => count($todos_processos),
    'solicitacao' => 0,
    'pre_circulo' => 0,
    'circulo_realizado' => 0,
    'andamento' => 0,
    'concluido' => 0,
    'cancelado' => 0
];

foreach ($todos_processos as $processo) {
    if ($processo['situacao'] === 'Solicitação') $stats['solicitacao']++;
    if ($processo['situacao'] === 'Pré-círculo') $stats['pre_circulo']++;
    if ($processo['situacao'] === 'Círculo realizado') $stats['circulo_realizado']++;
    if ($processo['status'] === 'Em andamento') $stats['andamento']++;
    if ($processo['status'] === 'Concluído') $stats['concluido']++;
    if ($processo['status'] === 'Cancelado') $stats['cancelado']++;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Processos Circulares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        .status-solicitacao {
            background-color: #e3f2fd;
            color: #1565c0;
            border-left: 4px solid #2196f3;
        }
        
        .status-pre {
            background-color: #fff8e1;
            color: #f57c00;
            border-left: 4px solid #ffb300;
        }
        
        .status-realizado {
            background-color: #e8f5e9;
            color: #388e3c;
            border-left: 4px solid #4caf50;
        }
        
        .status-em-andamento {
            background-color: #e3f2fd;
            color: #1565c0;
            border-left: 4px solid #2196f3;
        }
        
        .status-concluido {
            background-color: #e8f5e9;
            color: #388e3c;
            border-left: 4px solid #4caf50;
        }
        
        .status-cancelado {
            background-color: #ffebee;
            color: #d32f2f;
            border-left: 4px solid #f44336;
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
        
        .stats-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .filter-badge {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 5px 15px;
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }
        
        .filter-badge.active {
            background-color: var(--accent);
            color: white;
            border-color: var(--accent);
        }
        
        @media (max-width: 768px) {
            .table-container {
                overflow-x: auto;
            }
            
            .table {
                min-width: 1000px;
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
            <a href="../../painel.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar ao Painel
            </a>
            
            <h1 class="page-title"><i class="fas fa-sync-alt me-2"></i>Gestão de Processos Circulares</h1>
            <p class="page-description">Cadastro e consulta de processos circulares</p>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['total'] ?></div>
                    <div class="stats-label">Total de Processos</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['andamento'] ?></div>
                    <div class="stats-label">Em Andamento</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= $stats['concluido'] ?></div>
                    <div class="stats-label">Concluídos</div>
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <h4><i class="fas fa-filter me-2"></i> Filtros</h4>
            <div class="d-flex flex-wrap">
                <!-- Filtros de Status -->
                <a href="?filtro=todos&situacao=<?= $filtro_situacao ?>" 
                   class="filter-badge <?= $filtro_status === 'todos' ? 'active' : '' ?>">
                    <i class="fas fa-globe-americas me-2"></i> Todos
                </a>
                <a href="?filtro=Em andamento&situacao=<?= $filtro_situacao ?>" 
                   class="filter-badge <?= $filtro_status === 'Em andamento' ? 'active' : '' ?>">
                    <i class="fas fa-hourglass-half me-2"></i> Em andamento
                </a>
                <a href="?filtro=Concluído&situacao=<?= $filtro_situacao ?>" 
                   class="filter-badge <?= $filtro_status === 'Concluído' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle me-2"></i> Concluído
                </a>
                <a href="?filtro=Cancelado&situacao=<?= $filtro_situacao ?>" 
                   class="filter-badge <?= $filtro_status === 'Cancelado' ? 'active' : '' ?>">
                    <i class="fas fa-ban me-2"></i> Cancelado
                </a>
                
                <!-- Separador visual -->
                <div class="w-100 my-2"></div>
                
                <!-- Filtros de Situação -->
                <a href="?filtro=<?= $filtro_status ?>&situacao=todos" 
                   class="filter-badge <?= $filtro_situacao === 'todos' ? 'active' : '' ?>">
                    <i class="fas fa-list me-2"></i> Todas situações
                </a>
                <a href="?filtro=<?= $filtro_status ?>&situacao=Solicitação" 
                   class="filter-badge <?= $filtro_situacao === 'Solicitação' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt me-2"></i> Solicitação
                </a>
                <a href="?filtro=<?= $filtro_status ?>&situacao=Pré-círculo" 
                   class="filter-badge <?= $filtro_situacao === 'Pré-círculo' ? 'active' : '' ?>">
                    <i class="fas fa-clock me-2"></i> Pré-círculo
                </a>
                <a href="?filtro=<?= $filtro_status ?>&situacao=Círculo realizado" 
                   class="filter-badge <?= $filtro_situacao === 'Círculo realizado' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle me-2"></i> Círculo realizado
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-2"></i>
                    <span>Lista de Processos Circulares</span>
                    <?php if($filtro_status !== 'todos' || $filtro_situacao !== 'todos'): ?>
                        <span class="badge bg-info ms-2">
                            Filtro: 
                            <?= $filtro_status !== 'todos' ? "Status: $filtro_status" : '' ?>
                            <?= $filtro_situacao !== 'todos' ? "Situação: $filtro_situacao" : '' ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="calendario.php
                    " class="btn btn-info me-2">
                    <i class="fas fa-calendar-alt me-1"></i>Calendário
                    </a>
                    <a href="novo.php" class="btn btn-new">
                        <i class="fas fa-plus me-1"></i>Novo Processo
                    </a>
                </div>
            </div>
            
            <div class="table-container">
                <?php if(count($processos) > 0): ?>
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i> Processo</th>
                                <th><i class="fas fa-user-friends me-2"></i> Infratores</th>
                                <th><i class="fas fa-user-injured me-2"></i> Vítimas</th>
                                <th><i class="fas fa-calendar-day me-2"></i> Data BO</th>
                                <th><i class="fas fa-calendar-plus me-2"></i> Data Entrada</th>
                                <th><i class="fas fa-tasks me-2"></i> Situação</th>
                                <th><i class="fas fa-user-tie me-2"></i> Facilitador</th>
                                <th><i class="fas fa-info-circle me-2"></i> Status</th>
                                <th class="action-cell"><i class="fas fa-cogs me-2"></i> Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($processos as $processo): ?>
                            <tr>
                                <td><?= htmlspecialchars($processo['numero_processo']) ?></td>
                                <td><?= htmlspecialchars($processo['infratores']) ?></td>
                                <td><?= htmlspecialchars($processo['vitimas']) ?></td>
                                <td><?= date('d/m/Y', strtotime($processo['data_bo'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($processo['data_entrada'])) ?></td>
                                <td>
                                    <span class="status-badge 
                                        <?= 'status-' . strtolower(str_replace(' ', '-', $processo['situacao'])) ?>">
                                        <?= $processo['situacao'] ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($processo['facilitador']) ?></td>
                                <td>
                                    <span class="status-badge 
                                        <?= 'status-' . strtolower(str_replace(' ', '-', $processo['status'])) ?>">
                                        <?= $processo['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="editar.php?id=<?= $processo['id'] ?>" 
                                           class="btn btn-warning btn-action" 
                                           title="Editar">
                                           <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="excluir.php?id=<?= $processo['id'] ?>" 
                                           class="btn btn-danger btn-action" 
                                           title="Excluir"
                                           onclick="return confirm('Tem certeza que deseja excluir este processo?')">
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
                        <h3 class="text-muted mb-2">Nenhum processo encontrado</h3>
                        <p class="text-muted mb-4">
                            <?php if($filtro_status !== 'todos' || $filtro_situacao !== 'todos'): ?>
                                Não foram encontrados processos com os filtros aplicados.
                            <?php else: ?>
                                Você ainda não possui processos cadastrados
                            <?php endif; ?>
                        </p>
                        <a href="novo.php" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-1"></i>Criar Primeiro Processo
                        </a>
                        <?php if($filtro_status !== 'todos' || $filtro_situacao !== 'todos'): ?>
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="fas fa-times me-1"></i>Limpar Filtros
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted">
            <p>Sistema de Gestão de Processos Circulares</p>
            <p>Escritividade para obtenção da personalidade de pessoas internas</p>
            <p>&copy; <?= date('Y') ?> Departamento de Licença. Todos os direitos reservados.</p>
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
                    if (!confirm('Tem certeza que deseja excluir este processo permanentemente?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>