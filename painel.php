<?php 
include 'auth.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Painel - Justiça Restaurativa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="painel-box shadow-sm">
            <h1 class="painel-title">
                Bem vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>!
            </h1>
            <p class="painel-subtitle">Seu perfil: <?php echo htmlspecialchars($_SESSION['usuario_tipo']); ?></p>
            <hr>

            <h3 class="painel-menu-title">Menu Principal</h3>
            <div class="list-group painel-menu">
                <a href="agenda/agenda_view.php" class="list-group-item list-group-item-action">Agenda de Escutas</a>
                <a href="escuta/listar.php" class="list-group-item list-group-item-action">Pedidos de Escuta</a>
                <a href="arquivamentos/listar.php" class="list-group-item list-group-item-action">Arquivamentos</a>
                <a href="oficios/listar.php" class="list-group-item list-group-item-action">Ofícios</a>
                <?php if ($_SESSION['usuario_tipo'] === 'admin'): ?>
                    <a href="usuarios/listar.php" class="list-group-item list-group-item-action">Gerenciar Usuários</a>
                <?php endif; ?>
            </div>

            <a href="logout.php" class="btn btn-danger w-100 mt-3">Sair</a>
        </div>
    </div>
</body>
</html>
