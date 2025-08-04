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
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
    <!-- CSS da Agenda -->
    <link rel="stylesheet" href="../assets/css/agenda.css">
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📅 Agenda de Escutas</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoEventoModal">
            ➕ Novo Agendamento
        </button>
    </div>
    
    <!-- Área de debug -->
    <div id="debug" class="alert alert-info mb-3" style="display: none;">
        <strong>Debug:</strong> <span id="debugMsg"></span>
    </div>
    
    <div id="calendar"></div>
    <a href="../painel.php" class="btn btn-secondary mt-3">⬅ Voltar ao Painel</a>
</div>

<!-- Modal Novo Evento -->
<div class="modal fade" id="novoEventoModal" tabindex="-1" aria-labelledby="novoEventoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="novoEventoModalLabel">➕ Novo Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <form id="formNovoEvento">
        <div class="modal-body">
          <div class="mb-3">
            <label for="novoNome" class="form-label">Nome Completo:</label>
            <input type="text" class="form-control" id="novoNome" required>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="novaData" class="form-label">Data:</label>
              <input type="date" class="form-control" id="novaData" required>
            </div>
            <div class="col-md-6">
              <label for="novaHora" class="form-label">Hora:</label>
              <input type="time" class="form-control" id="novaHora" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="novaPsicologa" class="form-label">Psicóloga:</label>
            <select class="form-control" id="novaPsicologa" required>
              <option value="">Selecione...</option>
              <option value="Psic. Aline">Psic. Aline</option>
              <option value="Psic. Hugo">Psic. Hugo</option>
              <option value="Psic. Laura">Psic. Laura</option>
              <option value="Psic. Carla">Psic. Carla</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="novaPrioridade" class="form-label">Prioridade:</label>
            <select class="form-control" id="novaPrioridade" required>
              <option value="">Selecione...</option>
              <option value="Alta">Alta</option>
              <option value="Média">Média</option>
              <option value="Baixa">Baixa</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">💾 Salvar Agendamento</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="eventoModalLabel">📋 Detalhes do Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <p><strong>👤 Nome:</strong> <span id="modalNome"></span></p>
        <p><strong>📅 Data:</strong> <span id="modalData"></span></p>
        <p><strong>🕐 Hora:</strong> <span id="modalHora"></span></p>
        <p><strong>👩‍⚕️ Psicóloga:</strong> <span id="modalPsicologa"></span></p>
        <p><strong>⚠️ Prioridade:</strong> <span id="modalPrioridade" class="badge"></span></p>
        <p><strong>📊 Status:</strong> <span id="modalStatus" class="badge"></span></p>
      </div>
      <div class="modal-footer">
        <a href="#" id="editarLink" class="btn btn-warning">✏️ Editar</a>
        <button type="button" class="btn btn-danger" id="excluirBtn">🗑️ Excluir</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/pt-br.js"></script>

<script>
function showDebug(msg) {
    document.getElementById('debugMsg').innerText = msg;
    document.getElementById('debug').style.display = 'block';
    console.log('Debug:', msg);
}

document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    
    showDebug('Iniciando carregamento do calendário...');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        height: 600,
        events: '../agenda/agenda.php',
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        eventDisplay: 'block',
        selectable: true, // Permite seleção de datas
        dateClick: function(info) {
            // Quando clicar em uma data vazia, abre modal para novo evento
            document.getElementById('novaData').value = info.dateStr;
            var modal = new bootstrap.Modal(document.getElementById('novoEventoModal'));
            modal.show();
        },
        loading: function(isLoading) {
            if (isLoading) {
                showDebug('Carregando eventos...');
            } else {
                showDebug('Eventos carregados com sucesso!');
            }
        },
        eventDidMount: function(info) {
            console.log('Evento montado:', info.event);
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
            
            // Estiliza prioridade
            var prioridadeSpan = document.getElementById('modalPrioridade');
            prioridadeSpan.innerText = props.prioridade;
            prioridadeSpan.className = 'badge ';
            if (props.prioridade === 'Alta') {
                prioridadeSpan.className += 'bg-danger';
            } else if (props.prioridade === 'Média') {
                prioridadeSpan.className += 'bg-warning text-dark';
            } else {
                prioridadeSpan.className += 'bg-success';
            }
            
            // Estiliza status
            var statusSpan = document.getElementById('modalStatus');
            statusSpan.innerText = props.status;
            statusSpan.className = 'badge bg-primary';
            
            document.getElementById('editarLink').href = '../escuta/editar.php?id=' + props.id;

            var modal = new bootstrap.Modal(document.getElementById('eventoModal'));
            modal.show();
        }
    });

    calendar.render();
    showDebug('Calendário renderizado!');
    
    // Formulário para novo evento
    document.getElementById('formNovoEvento').addEventListener('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            nome_completo: document.getElementById('novoNome').value,
            data_agendamento: document.getElementById('novaData').value,
            hora_agendamento: document.getElementById('novaHora').value,
            psicologa: document.getElementById('novaPsicologa').value,
            prioridade: document.getElementById('novaPrioridade').value,
            status: 'Agendado'
        };
        
        // Aqui você faria uma requisição AJAX para salvar no banco
        // Por enquanto, vamos apenas mostrar uma mensagem
        showDebug('Novo evento criado: ' + formData.nome_completo + ' em ' + formData.data_agendamento);
        
        // Fechar modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('novoEventoModal'));
        modal.hide();
        
        // Recarregar eventos (você pode implementar adição dinâmica depois)
        calendar.refetchEvents();
    });
    
    // Teste adicional - fazer requisição manual
    fetch('../agenda/agenda.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            showDebug('Dados recebidos: ' + data.length + ' eventos encontrados');
            console.log('Dados da agenda:', data);
        })
        .catch(error => {
            showDebug('Erro na requisição: ' + error.message);
            console.error('Erro:', error);
        });
});
</script>

</body>
</html>