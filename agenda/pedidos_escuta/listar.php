<?php
require_once __DIR__ . '/../../includes/auth.php'; // sobe um nível para pegar o auth.php
permitir(['admin', 'recepcao_agenda', 'recepcao_entrada']); // tipos que podem acessar
?>