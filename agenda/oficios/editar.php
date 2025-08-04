<?php
require_once __DIR__ . '/../../includes/conexao.php';
require_once __DIR__ . '/../../includes/auth.php';
permitir(['admin', 'recepcao_agenda']);

if (!isset($_GET['id'])) {
    header('Location: listar.php');
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM oficios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$oficio = $result->fetch_assoc();

if (!$oficio) {
    header('Location: listar.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Ofício</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #1abc9c;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px 0;
            position: relative;
        }
        
        .card {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            border: none;
        }
        
        .btn-save {
            background: linear-gradient(135deg, var(--accent), #16a085);
            border: none;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(26, 188, 156, 0.3);
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-save:hover {
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
        
        @media (max-width: 768px) {
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
            <a href="listar.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
            <h1><i class="fas fa-edit me-2"></i> Editar Ofício</h1>
        </div>
    </div>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form action="processar.php" method="POST">
                            <input type="hidden" name="id" value="<?= $oficio['id'] ?>">
                            
                            <div class="mb-4">
                                <label for="numero_oficio" class="form-label fw-bold">
                                    <i class="fas fa-hashtag me-2"></i> Número do Ofício
                                </label>
                                <input type="text" class="form-control form-control-lg" 
                                       id="numero_oficio" name="numero_oficio" 
                                       value="<?= htmlspecialchars($oficio['numero_oficio']) ?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="nome_completo" class="form-label fw-bold">
                                    <i class="fas fa-user me-2"></i> Nome Completo
                                </label>
                                <input type="text" class="form-control form-control-lg" 
                                       id="nome_completo" name="nome_completo" 
                                       value="<?= htmlspecialchars($oficio['nome_completo']) ?>" required>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="tipo_oficio" class="form-label fw-bold">
                                        <i class="fas fa-tag me-2"></i> Tipo de Ofício
                                    </label>
                                    <select class="form-select form-select-lg" id="tipo_oficio" name="tipo_oficio" required>
                                        <option value="Busca Ativa" <?= $oficio['tipo_oficio'] === 'Busca Ativa' ? 'selected' : '' ?>>Busca Ativa</option>
                                        <option value="Informativo" <?= $oficio['tipo_oficio'] === 'Informativo' ? 'selected' : '' ?>>Informativo</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="data_criacao" class="form-label fw-bold">
                                        <i class="fas fa-calendar-day me-2"></i> Data de Criação
                                    </label>
                                    <input type="date" class="form-control form-control-lg" 
                                           id="data_criacao" name="data_criacao" 
                                           value="<?= $oficio['data_criacao'] ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="status_oficio" class="form-label fw-bold">
                                    <i class="fas fa-tasks me-2"></i> Status
                                </label>
                                <select class="form-select form-select-lg" id="status_oficio" name="status_oficio" required>
                                    <option value="Aguardando" <?= $oficio['status_oficio'] === 'Aguardando' ? 'selected' : '' ?>>Aguardando</option>
                                    <option value="Realizado" <?= $oficio['status_oficio'] === 'Realizado' ? 'selected' : '' ?>>Realizado</option>
                                </select>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5">
                                <a href="listar.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-save btn-lg">
                                    <i class="fas fa-save me-1"></i> Atualizar Ofício
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>