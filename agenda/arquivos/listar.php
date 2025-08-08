<?php
require_once __DIR__ . '/../../includes/conexao.php';
require_once __DIR__ . '/../../includes/auth.php';
permitir(['admin', 'psicologa']);

$sql = "SELECT * FROM arquivamentos ORDER BY envio_relatorio DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$arquivamentos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arquivamentos</title>
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
            padding: 25px 0;
            position: relative;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .btn-new {
            background: linear-gradient(135deg, var(--accent), #16a085);
        }
        
        .btn-back {
            position: absolute;
            left: 20px;
            top: 20px;
        }
        
        .action-btn {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="header text-center mb-4">
        <div class="container">
            <!-- Botão Voltar ao Painel -->
            <a href="../../painel.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar ao Painel
            </a>
            
            <h1><i class="fas fa-archive me-2"></i>Arquivamentos</h1>
            <p class="lead">Gerenciamento de documentos arquivados</p>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Arquivamentos</h5>
                <a href="novo.php" class="btn btn-new text-white">
                    <i class="fas fa-plus me-1"></i> Novo Arquivamento
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Data Nascimento</th>
                                <th>Psicóloga</th>
                                <th>Órgão Origem</th>
                                <th>Envio Relatório</th>
                                <th>Arquivo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arquivamentos as $arq): ?>
                            <tr>
                                <td><?= htmlspecialchars($arq['nome_completo']) ?></td>
                                <td><?= date('d/m/Y', strtotime($arq['data_nascimento'])) ?></td>
                                <td><?= htmlspecialchars($arq['psicologa']) ?></td>
                                <td><?= htmlspecialchars($arq['orgao_origem']) ?></td>
                                <td><?= $arq['envio_relatorio'] ? date('d/m/Y', strtotime($arq['envio_relatorio'])) : 'N/A' ?></td>
                                <td>
                                    <?php if ($arq['arquivo_relatorio']): ?>
                                        <a href="../uploads/<?= $arq['arquivo_relatorio'] ?>" target="_blank">
                                            <i class="fas fa-file-pdf text-danger"></i> Ver
                                        </a>
                                    <?php else: ?>
                                        Nenhum
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="editar.php?id=<?= $arq['id'] ?>" class="btn btn-sm btn-warning action-btn" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="processar.php?action=delete&id=<?= $arq['id'] ?>" 
                                       class="btn btn-sm btn-danger action-btn" 
                                       title="Excluir"
                                       onclick="return confirm('Tem certeza que deseja excluir?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>