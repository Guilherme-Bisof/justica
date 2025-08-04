<?php
include '../auth.php'; // sobe um nível para pegar o auth.php
permitir(['admin', 'recepcao_agenda', 'recepcao_entrada']); // tipos que podem acessar
?>