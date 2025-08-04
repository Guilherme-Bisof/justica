<?php
include '../auth.php';
permitir(['admin', 'recepcao_agenda', 'psicologa']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agenda de Escutas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet">
    <!-- CSS da Agenda -->
    <link rel="stylesheet" href="../assets/css/agenda.css">
</head>
<body>

<div class="container">
    <h2 class="mb-4">📅 Agenda de Escutas</h2>
    <div id="calendar"></div>
    <a href="../painel.php" class="btn btn-secondary mt-3">⬅ Voltar ao Painel</a>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventoModalLabel">Detalhes do Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <p><strong>Nome:</strong> <span id="modalNome"></span></p>
        <p><strong>Data:</strong> <span id="modalData"></span></p>
        <p><strong>Hora:</strong> <span id="modalHora"></span></p>
        <p><strong>Psicóloga:</strong> <span id="modalPsicologa"></span></p>
        <p><strong>Prioridade:</strong> <span id="modalPrioridade"></span></p>
        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
      </div>
      <div class="modal-footer">
        <a href="#" id="editarLink" class="btn btn-primary">✏ Editar</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/pt-br.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        events: '../agenda/agenda.php',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        eventDisplay: 'block',
        eventDidMount: function(info) {
            if (info.event.extendedProps.prioridade === 'Alta') {
                info.el.style.backgroundColor = '#dc3545';
                info.el.style.color = 'white';
            } else if (info.event.extendedProps.prioridade === 'Média') {
                info.el.style.backgroundColor = '#ffc107';
                info.el.style.color = 'black';
            } else {
                info.el.style.backgroundColor = '#198754';
                info.el.style.color = 'white';
            }
        },
        eventClick: function(info) {
            var props = info.event.extendedProps;

            document.getElementById('modalNome').innerText = props.nome_completo;
            document.getElementById('modalData').innerText = props.data_agendamento;
            document.getElementById('modalHora').innerText = props.hora_agendamento;
            document.getElementById('modalPsicologa').innerText = props.psicologa;
            document.getElementById('modalPrioridade').innerText = props.prioridade;
            document.getElementById('modalStatus').innerText = props.status;
            document.getElementById('editarLink').href = '../escuta/editar.php?id=' + props.id;

            var modal = new bootstrap.Modal(document.getElementById('eventoModal'));
            modal.show();
        }
    });

    calendar.render();
});
</script>

</body>
</html>
