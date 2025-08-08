<?php
// processos_circulares/excluir.php
require_once __DIR__ . '/../../includes/conexao.php';
require_once __DIR__ . '/../../includes/auth.php';
permitir(['admin', 'recepcao']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Excluir processo
$stmt = $conn->prepare("DELETE FROM processos_circulares WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['sucesso'] = 'Processo excluído com sucesso!';
} else {
    $_SESSION['erro'] = 'Erro ao excluir processo: ' . $stmt->error;
}

$stmt->close();
header('Location: index.php');
exit;