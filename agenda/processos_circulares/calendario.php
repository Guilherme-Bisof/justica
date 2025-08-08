<?php
// processos_circulares/calendario.php
require_once __DIR__ . '/../../includes/conexao.php';
require_once __DIR__ . '/../../includes/auth.php';
permitir(['admin', 'recepcao', 'psicologa']);

// Consulta usando MySQLi
$sql = "SELECT id, numero_processo, facilitador, data_circulo 
        FROM processos_circulares 
        WHERE data_circulo IS NOT NULL 
        ORDER BY data_circulo";
$result = $conn->query($sql);

$eventos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // CORREÇÃO: Removido o parêntese extra
        if (!empty($row['data_circulo'])) {
            try {
                $data = new DateTime($row['data_circulo']);
                $row['data_circulo_iso'] = $data->format('Y-m-d\TH:i:s');
            } catch (Exception $e) {
                error_log("Erro na data do processo {$row['id']}: " . $e->getMessage());
                continue;
            }
            $eventos[] = $row;
        }
    }
} else {
    die("Erro na consulta: " . $conn->error);
}

// Adicionar evento de teste
$eventos[] = [
    'id' => 0,
    'numero_processo' => 'TESTE',
    'facilitador' => 'Evento de Teste',
    'data_circulo_iso' => date('Y-m-d\TH:i:s', strtotime('+1 day'))
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário de Processos Circulares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
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
            --light-gray: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
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
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            border: none;
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 20px 25px;
            border: none;
        }
        
        .btn-new {
            background: linear-gradient(135deg, var(--accent), #16a085);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(26, 188, 156, 0.3);
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-new:hover {
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
        
        .page-title {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .page-description {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .stats-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        #calendar {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            min-height: 600px;
        }
        
        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 0.9rem;
            border: none !important;
            margin-bottom: 2px;
        }
        
        .fc-event:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }
        
        .fc-daygrid-event {
            border-left: 4px solid var(--accent);
        }
        
        .fc-event-title {
            font-weight: 500;
            white-space: normal !important;
        }
        
        .fc-toolbar-title {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.4rem;
        }
        
        .fc-button {
            background-color: var(--secondary) !important;
            border: none !important;
            color: white !important;
            border-radius: 6px !important;
            padding: 6px 12px !important;
        }
        
        .fc-button:hover {
            background-color: var(--primary) !important;
        }
        
        .fc-dayHeader {
            background-color: var(--light-gray);
            padding: 8px 0;
            font-weight: 600;
        }
        
        .fc-day-sun {
            color: #e74c3c;
        }
        
        .fc-day-sat {
            color: #3498db;
        }
        
        .fc-daygrid-day-top {
            justify-content: center;
            padding-top: 5px;
        }
        
        .fc-daygrid-day-number {
            font-weight: 600;
            font-size: 1.1rem;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto;
        }
        
        .fc-day-today .fc-daygrid-day-number {
            background-color: var(--accent);
            color: white;
        }
        
        .btn-list {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
            color: white;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-list:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(52, 152, 219, 0.4);
            color: white;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }
            
            .fc-header-toolbar {
                flex-wrap: wrap;
            }
            
            .fc-toolbar-chunk {
                margin-bottom: 10px;
            }
            
            #calendar {
                min-height: 400px;
            }
            
            .card-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .card-header > div {
                width: 100%;
                text-align: center;
            }
            
            .stats-card {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <div class="container position-relative">
            <a href="index.php" class="btn btn-outline-light btn-back">
                <i class="fas fa-arrow-left me-1"></i> Voltar aos Processos
            </a>
            
            <h1 class="page-title"><i class="fas fa-calendar-alt me-2"></i>Calendário de Processos Circulares</h1>
            <p class="page-description">Visualize as datas agendadas para os círculos</p>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= count($eventos) ?></div>
                    <div class="stats-label">Eventos Agendados</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= count(array_filter($eventos, function($e) { 
                        return (new DateTime($e['data_circulo_iso'])) > new DateTime(); 
                    })) ?></div>
                    <div class="stats-label">Próximos Eventos</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?= count(array_filter($eventos, function($e) { 
                        return (new DateTime($e['data_circulo_iso'])) <= new DateTime(); 
                    })) ?></div>
                    <div class="stats-label">Eventos Realizados</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-calendar-day me-2"></i>
                    <span>Calendário de Círculos</span>
                </div>
                <div>
                    <a href="index.php" class="btn btn-list me-2">
                        <i class="fas fa-list me-1"></i>Ver Lista Completa
                    </a>
                    <a href="novo.php" class="btn btn-new">
                        <i class="fas fa-plus me-1"></i>Novo Processo
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
        
        <div class="text-center mt-4 text-muted">
            <p>Sistema de Gestão de Processos Circulares</p>
            <p>Escritividade para obtenção da personalidade de pessoas internas</p>
            <p>&copy; <?= date('Y') ?> Departamento de Licença. Todos os direitos reservados.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const eventos = <?php echo json_encode($eventos); ?>;
            
            console.log('Eventos carregados:', eventos);
            
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                timeZone: 'local',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: eventos.map(evento => {
                    return {
                        title: 'Processo ' + evento.numero_processo + ' - ' + evento.facilitador,
                        start: evento.data_circulo_iso,
                        allDay: false,
                        extendedProps: {
                            id: evento.id
                        },
                        backgroundColor: evento.id === 0 ? '#e74c3c' : '#3498db',
                        borderColor: evento.id === 0 ? '#c0392b' : '#2980b9'
                    };
                }),
                eventClick: function(info) {
                    if (info.event.extendedProps.id !== 0) {
                        window.location.href = 'editar.php?id=' + info.event.extendedProps.id;
                    }
                },
                eventContent: function(arg) {
                    const container = document.createElement('div');
                    container.classList.add('fc-event-container');
                    
                    const title = document.createElement('div');
                    title.classList.add('fc-event-title');
                    title.innerHTML = arg.event.title;
                    
                    container.appendChild(title);
                    return { domNodes: [container] };
                },
                dayHeaderClassNames: function(arg) {
                    if (arg.date.getDay() === 0) {
                        return ['fc-day-sun'];
                    }
                    if (arg.date.getDay() === 6) {
                        return ['fc-day-sat'];
                    }
                    return [];
                },
                dayCellClassNames: function(arg) {
                    if (arg.date.getDay() === 0) {
                        return ['fc-day-sun'];
                    }
                    if (arg.date.getDay() === 6) {
                        return ['fc-day-sat'];
                    }
                    return [];
                },
                eventDidMount: function(info) {
                    console.log('Evento montado:', info.event);
                }
            });
            
            calendar.render();
        });
    </script>
</body>
</html>