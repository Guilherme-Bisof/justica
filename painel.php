<?php 
include './includes/auth.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Painel - Justiça Restaurativa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #1abc9c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
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
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .card-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        
        .card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--secondary);
        }
        
        .card-title {
            font-weight: 600;
            color: var(--dark);
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
            color: white;
        }
        .logo {
            height: 150px; /* aumenta o tamanho do logo */
            vertical-align: middle;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <div class="container">
           <div class="header-title d-flex justify-content-center align-items-center gap-3 flex-wrap">
                <h1 class="mb-0">Painel Administrativo</h1>
                <img src="./assets/img/logo.png" alt="Logo Justiça Restaurativa" class="logo">
            </div>
            <p class="lead">Sistema de Gestão de Justiça Restaurativa</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row mb-4">
            <div class="col-md-8 mx-auto text-center">
                <div class="alert alert-primary">
                    <h3 class="mb-0">
                        <i class="fas fa-user me-2"></i> 
                        Bem vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!
                    </h3>
                    <p class="mb-0">Seu perfil: <span class="badge bg-info"><?php echo htmlspecialchars($_SESSION['usuario_tipo']); ?></span></p>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <a href="agenda/index.php" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-calendar-check"></i>
                            <h5 class="card-title">Agenda de Escutas</h5>
                            <p class="text-muted">Agendamentos de atendimentos psicológicos</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4">
                <a href="agenda/pedidos_escuta/index.php" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-headphones"></i>
                            <h5 class="card-title">Pedidos de Escuta</h5>
                            <p class="text-muted">Solicitações de atendimento</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4">
                <a href="agenda/arquivos/listar.php" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-archive"></i>
                            <h5 class="card-title">Arquivamentos</h5>
                            <p class="text-muted">Documentos arquivados</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <?php if ($_SESSION['usuario_tipo'] !== 'psicologa'): ?>
                <div class="col-md-4">
                    <a href="agenda/oficios/listar.php" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body">
                                <i class="fas fa-file-alt"></i>
                                <h5 class="card-title">Ofícios</h5>
                                <p class="text-muted">Gerenciamento de ofícios</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
            
            <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
            <div class="col-md-4">
                <a href="agenda/usuarios/listar.php" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-users-cog"></i>
                            <h5 class="card-title">Gerenciar Usuários</h5>
                            <p class="text-muted">Controle de acesso</p>
                        </div>
                    </div>
                </a>
            </div>
            <?php endif; ?>
            
            <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                <div class="col-md-4">
                    <a href="agenda/processos_circulares/index.php" class="text-decoration-none">
                        <div class="card">
                            <div class="card-body">
                                <i class="fas fa-circle-nodes"></i>
                                <h5 class="card-title">Processos Circulares</h5>
                                <p class="text-muted">Gestão dos círculos restaurativos</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="d-grid gap-2 mt-5">
            <a href="logout.php" class="btn btn-logout btn-lg">
                <i class="fas fa-sign-out-alt me-1"></i> Sair do Sistema
            </a>
        </div>
    </div>
</body>
</html>