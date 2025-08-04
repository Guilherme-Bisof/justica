<?php
require_once __DIR__ . '/../includes/conexao.php';
require_once __DIR__ . '/../includes/auth.php';
permitir(['admin', 'recepcao_agenda', 'psicologa']);

// Buscar pedido se existir na URL
$pedido = null;
if (isset($_GET['pedido_id'])) {
    $sql = "SELECT * FROM pedidos_escuta WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['pedido_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $pedido = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Escutas</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
    
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
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 25px 0;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        #calendar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .fc-event {
            cursor: pointer;
            border: none;
            font-weight: 500;
        }
        
        .priority-high .fc-event {
            background-color: var(--danger);
            border-color: var(--danger);
        }
        
        .priority-medium .fc-event {
            background-color: var(--warning);
            border-color: var(--warning);
            color: #000;
        }
        
        .priority-low .fc-event {
            background-color: var(--success);
            border-color: var(--success);
        }
        
        .modal-content {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .btn-new {
            background: linear-gradient(135deg, var(--accent), #16a085);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            color: white;
        }
        
        .btn-new:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }
        
        .badge-priority {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <div class="container">
            <h1><i class="fas fa-calendar-check me-3"></i> Agenda de Escutas</h1>
            <p class="lead">Controle de agendamentos para atendimentos psicológicos</p>
        </div>
    </div>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="far fa-calendar-alt me-2"></i> Calendário de Agendamentos</h2>
            <a href="../painel.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar ao Painel
            </a>
        </div>
        
        <!-- Área de debug -->
        <div id="debug" class="alert alert-info mb-3" style="display: none;">
            <strong>Debug:</strong> <span id="debugMsg"></span>
        </div>
        
        <div id="calendar"></div>
    </div>

    <!-- Modal Novo Evento -->
    <div class="modal fade" id="novoEventoModal" tabindex="-1" aria-labelledby="novoEventoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novoEventoModalLabel">
                        <i class="fas fa-plus-circle me-2"></i> Novo Agendamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formNovoEvento">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="novoNome" class="form-label">
                                        <i class="fas fa-user me-1"></i> Nome Completo
                                    </label>
                                    <input type="text" class="form-control" id="novoNome" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="novaData" class="form-label">
                                                <i class="far fa-calendar me-1"></i> Data
                                            </label>
                                            <input type="date" class="form-control" id="novaData" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="novaHora" class="form-label">
                                                <i class="far fa-clock me-1"></i> Hora
                                            </label>
                                            <input type="time" class="form-control" id="novaHora" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="novaPsicologa" class="form-label">
                                        <i class="fas fa-user-md me-1"></i> Psicóloga
                                    </label>
                                    <select class="form-control" id="novaPsicologa" required>
                                        <option value="">Selecione...</option>
                                        <option value="Psic. Aline">Psic. Aline</option>
                                        <option value="Psic. Hugo">Psic. Hugo</option>
                                        <option value="Psic. Laura">Psic. Laura</option>
                                        <option value="Psic. Carla">Psic. Carla</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="novaPrioridade" class="form-label">
                                        <i class="fas fa-exclamation-circle me-1"></i> Prioridade
                                    </label>
                                    <select class="form-control" id="novaPrioridade" required>
                                        <option value="">Selecione...</option>
                                        <option value="Alta">Alta</option>
                                        <option value="Média">Média</option>
                                        <option value="Baixa">Baixa</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="novaObservacoes" class="form-label">
                                        <i class="fas fa-sticky-note me-1"></i> Observações
                                    </label>
                                    <textarea class="form-control" id="novaObservacoes" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Salvar Agendamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detalhes -->
    <div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventoModalLabel">
                        <i class="fas fa-info-circle me-2"></i> Detalhes do Agendamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="fas fa-user me-2"></i> Nome
                                </div>
                                <div class="detail-value fs-5" id="modalNome"></div>
                            </div>
                            
                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="far fa-calendar me-2"></i> Data
                                </div>
                                <div class="detail-value fs-5" id="modalData"></div>
                            </div>
                            
                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="far fa-clock me-2"></i> Hora
                                </div>
                                <div class="detail-value fs-5" id="modalHora"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="fas fa-user-md me-2"></i> Psicóloga
                                </div>
                                <div class="detail-value fs-5" id="modalPsicologa"></div>
                            </div>
                            
                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="fas fa-exclamation-circle me-2"></i> Prioridade
                                </div>
                                <div class="detail-value fs-5" id="modalPrioridade"></div>
                            </div>
                            
                            <div class="detail-item mb-4">
                                <div class="detail-label">
                                    <i class="fas fa-tasks me-2"></i> Status
                                </div>
                                <div class="detail-value fs-5" id="modalStatus"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <div class="detail-label">
                            <i class="fas fa-sticky-note me-2"></i> Observações
                        </div>
                        <div class="detail-value" id="modalObservacoes"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="editarLink" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" id="excluirBtn">
                        <i class="fas fa-trash-alt me-1"></i> Excluir
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/pt-br.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

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
                events: '../agenda/eventos.php',
                eventTimeFormat: { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    hour12: false 
                },
                eventDisplay: 'block',
                selectable: true,
                dateClick: function(info) {
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
                    // Adiciona classe de prioridade ao evento
                    const prioridade = info.event.extendedProps.prioridade;
                    if (prioridade === 'Alta') {
                        info.el.classList.add('priority-high');
                    } else if (prioridade === 'Média') {
                        info.el.classList.add('priority-medium');
                    } else {
                        info.el.classList.add('priority-low');
                    }
                },
                eventClick: function(info) {

                    currentEventId = info.event.id;
                    const props = info.event.extendedProps;

                    document.getElementById('modalNome').innerText = props.nome_completo;
                    document.getElementById('modalData').innerText = props.data_agendamento;
                    document.getElementById('modalHora').innerText = props.hora_agendamento;
                    document.getElementById('modalPsicologa').innerText = props.psicologa;
                    document.getElementById('modalObservacoes').innerText = props.observacoes || 'Nenhuma observação';
                    
                    // Prioridade
                    const prioridadeElement = document.getElementById('modalPrioridade');
                    prioridadeElement.innerText = props.prioridade;
                    prioridadeElement.className = 'detail-value fs-5';
                    if (props.prioridade === 'Alta') {
                        prioridadeElement.classList.add('text-danger');
                    } else if (props.prioridade === 'Média') {
                        prioridadeElement.classList.add('text-warning');
                    } else {
                        prioridadeElement.classList.add('text-success');
                    }
                    
                    // Status
                    const statusElement = document.getElementById('modalStatus');
                    statusElement.innerText = props.status;
                    statusElement.className = 'detail-value fs-5 badge bg-primary';
                    
                    // Link de edição
                    document.getElementById('editarLink').href = `../escuta/editar.php?id=${props.id}`;

                    const modal = new bootstrap.Modal(document.getElementById('eventoModal'));
                    modal.show();
                }
            });

            calendar.render();
            showDebug('Calendário renderizado!');
            
            // Formulário para novo evento
            document.getElementById('formNovoEvento').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    nome_completo: document.getElementById('novoNome').value,
                    data_agendamento: document.getElementById('novaData').value,
                    hora_agendamento: document.getElementById('novaHora').value,
                    psicologa: document.getElementById('novaPsicologa').value,
                    prioridade: document.getElementById('novaPrioridade').value,
                    observacoes: document.getElementById('novaObservacoes').value,
                    status: 'Agendado'
                };
                
                // Se veio de um pedido de escuta
                <?php if(isset($pedido)): ?>
                    formData.pedido_id = <?= $pedido['id'] ?>;
                <?php endif; ?>
                
                showDebug('Enviando novo agendamento...');
                
                // Envia via AJAX
                axios.post('../agenda/agenda.php?action=create', formData)
                    .then(response => {
                        showDebug('Agendamento criado com sucesso!');
                        
                        // Fechar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('novoEventoModal'));
                        modal.hide();
                        
                        // Recarregar eventos
                        calendar.refetchEvents();
                        
                        // Limpar formulário
                        document.getElementById('formNovoEvento').reset();
                    })
                    .catch(error => {
                        showDebug('Erro ao criar agendamento: ' + error.message);
                        console.error(error);
                    });
            });
            
            // Botão excluir
            document.getElementById('excluirBtn').addEventListener('click', function() {
                const eventId = currentEventId;
                
                if (currentEventId) {
                    if (confirm('Tem certeza que deseja excluir este agendamento?')) {
                        axios.post('../agenda/agenda.php?action=delete', { id: eventId })
                            .then(() => {
                                calendar.refetchEvents();
                                bootstrap.Modal.getInstance(document.getElementById('eventoModal')).hide();
                            })
                            .catch(error => {
                                showDebug('Erro ao excluir: ' + error.message);
                                console.error(error);
                            });
                    }
                } else {
                    showDebug('Nenhum evento selecionado para exclusão')
                }
            });
            
            // Preencher com dados do pedido, se existir
            <?php if($pedido): ?>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('novoNome').value = <?= json_encode($pedido['nome_completo']) ?>;
                    document.getElementById('novaPrioridade').value = <?= json_encode($pedido['prioridade']) ?>;
                    
                    const obs = <?= json_encode($pedido['observacoes']) ?>;
                    if (obs) {
                        document.getElementById('novaObservacoes').value = obs;
                    }
                    
                    const modal = new bootstrap.Modal(document.getElementById('novoEventoModal'));
                    modal.show();
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>