<?php
require_once __DIR__ . '/../../includes/conexao.php';
require_once __DIR__ . '/../../includes/auth.php';
permitir(['admin', 'recepcao_agenda']);

// Processar o formulário quando for submetido
$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter dados do POST e sanitizar
    $nome_completo = $conn->real_escape_string($_POST['nome_completo']);
    $referencia = $conn->real_escape_string($_POST['referencia']);
    $tipo_violencia = $conn->real_escape_string($_POST['tipo_violencia']);
    $prioridade = $conn->real_escape_string($_POST['prioridade']);
    $observacoes = $conn->real_escape_string($_POST['observacoes']);
    
    // Obter a data atual no formato YYYY-MM-DD
    $data_entrada = date('Y-m-d');
    
    // SQL para inserir o novo pedido
    $sql = "INSERT INTO pedidos_escuta 
            (data_entrada, nome_completo, referencia, tipo_violencia, prioridade, observacoes, status) 
            VALUES 
            ('$data_entrada', '$nome_completo', '$referencia', '$tipo_violencia', '$prioridade', '$observacoes', 'Pendente')";
    
    if ($conn->query($sql)) {
        $success = "Pedido cadastrado com sucesso!";
    } else {
        $error = "Erro ao cadastrar pedido: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Pedido de Escuta</title>
    
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
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 20px 25px;
            border: none;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-control, .form-select {
            padding: 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            background: white;
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, var(--accent), #16a085);
            border: none;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(26, 188, 156, 0.3);
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(26, 188, 156, 0.4);
        }
        
        .btn-cancel {
            background: var(--gray);
            border: none;
            padding: 14px 28px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-cancel:hover {
            background: #7f8c8d;
            color: white;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .page-title {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .page-description {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .required::after {
            content: " *";
            color: var(--danger);
        }
        
        .priority-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .priority-high .priority-indicator {
            background-color: var(--danger);
        }
        
        .priority-medium .priority-indicator {
            background-color: var(--warning);
        }
        
        .priority-low .priority-indicator {
            background-color: var(--success);
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <div class="container">
            <h1 class="page-title"><i class="fas fa-headphones me-2"></i>Novo Pedido de Escuta</h1>
            <p class="page-description">Cadastre uma nova solicitação de escuta psicológica</p>
        </div>
    </div>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-alt me-2"></i>
                            <span>Formulário de Cadastro</span>
                        </div>
                    </div>
                    
                    <div class="form-container">
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $success ?>
                                <a href="index.php" class="btn btn-sm btn-outline-success ms-3">
                                    Ver Pedidos <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="novo.php">
                            <div class="form-group">
                                <label for="nome_completo" class="form-label required">
                                    <i class="fas fa-user"></i> Nome Completo
                                </label>
                                <input type="text" class="form-control" id="nome_completo" name="nome_completo" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="referencia" class="form-label required">
                                            <i class="fas fa-building"></i> Referência
                                        </label>
                                        <select class="form-select" id="referencia" name="referencia" required>
                                            <option value="">Selecione a origem...</option>
                                            <option value="DDM">DDM (Delegacia de Defesa da Mulher)</option>
                                            <option value="Conselho Tutelar">Conselho Tutelar</option>
                                            <option value="Ministério Público">Ministério Público</option>
                                            <option value="Defensoria Pública">Defensoria Pública</option>
                                            <option value="Outro">Outro</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_violencia" class="form-label required">
                                            <i class="fas fa-exclamation-triangle"></i> Tipo de Violência
                                        </label>
                                        <select class="form-select" id="tipo_violencia" name="tipo_violencia" required>
                                            <option value="">Selecione o tipo...</option>
                                            <option value="Física">Física</option>
                                            <option value="Psicológica">Psicológica</option>
                                            <option value="Sexual">Sexual</option>
                                            <option value="Patrimonial">Patrimonial</option>
                                            <option value="Moral">Moral</option>
                                            <option value="Institucional">Institucional</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="prioridade" class="form-label required">
                                    <i class="fas fa-flag"></i> Prioridade
                                </label>
                                <select class="form-select" id="prioridade" name="prioridade" required>
                                    <option value="">Selecione a prioridade...</option>
                                    <option value="Alta" class="priority-high">
                                        <span class="priority-indicator"></span> Alta Urgência
                                    </option>
                                    <option value="Média" class="priority-medium">
                                        <span class="priority-indicator"></span> Média Urgência
                                    </option>
                                    <option value="Baixa" class="priority-low">
                                        <span class="priority-indicator"></span> Baixa Urgência
                                    </option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="observacoes" class="form-label">
                                    <i class="fas fa-sticky-note"></i> Observações
                                </label>
                                <textarea class="form-control" id="observacoes" name="observacoes" placeholder="Detalhes relevantes sobre o caso..."></textarea>
                            </div>
                            
                            <div class="form-group">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    O status deste pedido será definido automaticamente como <strong>Pendente</strong> e a data de entrada será a data atual.
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <button type="submit" class="btn btn-submit">
                                    <i class="fas fa-save"></i> Salvar Pedido
                                </button>
                                <a href="index.php" class="btn btn-cancel">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4 text-muted">
                    <p>Sistema de Pedidos de Escuta &copy; <?= date('Y') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Adicionar classe de prioridade às opções do select
        document.addEventListener('DOMContentLoaded', function() {
            const prioridadeSelect = document.getElementById('prioridade');
            
            prioridadeSelect.addEventListener('change', function() {
                // Remover classes de todas as opções
                Array.from(prioridadeSelect.options).forEach(option => {
                    option.classList.remove('priority-high', 'priority-medium', 'priority-low');
                });
                
                // Adicionar classe à opção selecionada
                if (this.value === 'Alta') {
                    this.classList.add('priority-high');
                } else if (this.value === 'Média') {
                    this.classList.add('priority-medium');
                } else if (this.value === 'Baixa') {
                    this.classList.add('priority-low');
                }
            });
        });
    </script>
</body>
</html>