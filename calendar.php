<?php
// Obtém o mês e ano atuais ou os passados via GET
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date("m");
$currentYear = isset($_GET['year']) ? $_GET['year'] : date("Y");

// Função para gerar o calendário para o mês e ano informados
function gerarCalendario($month, $year) {
    // Número de dias no mês
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDayOfMonth = strtotime("$year-$month-01");
    $firstDayWeekday = date("w", $firstDayOfMonth); // Dia da semana em que começa o mês (0 - Domingo, 1 - Segunda...)

    // Cabeçalho do calendário
    $calendar = '<table class="calendar-table" border="1">';
    $calendar .= '<tr>';
    $calendar .= '<th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>';
    $calendar .= '</tr><tr>';

    // Adiciona os dias em branco no início do mês
    for ($i = 0; $i < $firstDayWeekday; $i++) {
        $calendar .= '<td></td>';
    }

    // Exibe os dias do mês
    $day = 1;
    for ($i = $firstDayWeekday; $i < 7; $i++) {
        $calendar .= '<td>';
        $calendar .= "<span class='day-number'>$day</span>";
        $calendar .= '</td>';
        $day++;
    }
    $calendar .= '</tr>';

    // Preenche o restante dos dias do mês
    while ($day <= $daysInMonth) {
        $calendar .= '<tr>';
        for ($i = 0; $i < 7; $i++) {
            if ($day <= $daysInMonth) {
                $calendar .= "<td><span class='day-number'>$day</span></td>";
                $day++;
            } else {
                $calendar .= '<td></td>';
            }
        }
        $calendar .= '</tr>';
    }
    $calendar .= '</table>';
    return $calendar;
}

// Função para gerar os links de navegação entre meses e anos
function generateNavigation($currentMonth, $currentYear) {
    $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
    $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
    $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
    $nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;

    $prevMonthLink = "?month=$previousMonth&year=$previousYear";
    $nextMonthLink = "?month=$nextMonth&year=$nextYear";

    return [
        'prevMonthLink' => $prevMonthLink,
        'nextMonthLink' => $nextMonthLink
    ];
}

$navigation = generateNavigation($currentMonth, $currentYear);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário Interativo com Filtros</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
   
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        .calendar-container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .calendar {
            width: 75%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .sidebar {
            width: 20%;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-left: 20px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-header button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .calendar-header button:hover {
            background-color: #0056b3;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .calendar-table th, .calendar-table td {
            padding: 15px;
            text-align: center;
            width: 14.28%;
            height: 80px;
        }

        .calendar-table th {
            background-color: #f8f9fa;
        }

        .calendar-table td {
            background-color: #ffffff;
            cursor: pointer;
        }

        .calendar-table td:hover {
            background-color: #f0f0f0;
        }

        .highlight {
            background-color: #ffeb3b; /* Cor de destaque */
        }

        .calendar-table .event {
            background-color: #007bff;
            color: white;
            padding: 5px;
            border-radius: 5px;
            font-size: 12px;
        }
        .novo {
            display: flex;
        }

        .sidebar h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .sidebar .event-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .sidebar .event-item {
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .sidebar .event-item span {
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        select.form-control {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        .category {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="calendar-container">
    <!-- Calendário -->
    <div class="calendar">
        <div class="calendar-header">
            <button id="prev-month">Anterior</button>
            <h2 id="month-year"></h2>
            <button id="next-month">Próximo</button>
        </div>

        <table class="calendar-table">
            <thead>
                <tr>
                    <th>Dom</th>
                    <th>Seg</th>
                    <th>Ter</th>
                    <th>Qua</th>
                    <th>Qui</th>
                    <th>Sex</th>
                    <th>Sáb</th>
                </tr>
            </thead>
            <tbody id="calendar-body">
                <!-- Os dias serão gerados aqui via JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Sidebar para exibir compromissos -->
    <div class="sidebar">
        <h3>Agenda</h3>

        <!-- Controle para colaboradores ou grupos -->
        <div class="form-group">
            <label for="collaborators">Colaboradores ou grupos:</label>
            <select id="collaborators" class="form-control">
                <option value="">Clique para adicionar</option>
                <!-- Adicione opções de colaboradores ou grupos aqui -->
            </select>
        </div>

        <!-- Filtros de Visualização -->
        <div class="form-group">
            <h4>Visualizar:</h4>
            <label>
                <input type="checkbox" id="task-planning" checked> Planejamento de tarefas do prazo
            </label><br>
            <label>
                <input type="checkbox" id="deadline-terms" checked> Término de prazos
            </label><br>
            <label>
                <input type="checkbox" id="canceled-commitments" checked> Compromissos cancelados
            </label><br>
        </div>

        <!-- Categorias -->
        <div class="form-group">
            <h4>Categorias:</h4>
            <div class="novo">
            <label>
                <input type="checkbox" class="category" id="category-geral" checked> Geral
            </label><br>
          
            <a href="">novo
            </a>
           </div>
           <div class="novo">
            <label>
                <input type="checkbox" class="category" id="category-audiencia" checked> AUDIÊNCIA
            </label><br>
                <a href=""><i class="fa-regular fa-pen-to-square"></i></a>
                <i class="fa-regular fa-trash-can"></i>
           </div>
           <div class="novo">
            <label>
                <input type="checkbox" class="category" id="category-escritorio" checked> ESCRITÓRIO
            </label><br>
           </div>
            <label>
                <input type="checkbox" class="category" id="category-ligacao" checked> LIGAÇÃO
            </label><br>
            <label>
                <input type="checkbox" class="category" id="category-oab" checked> OAB
            </label><br>
            <label>
                <input type="checkbox" class="category" id="category-reuniao" checked> REUNIÃO
            </label><br>
            <label>
                <input type="checkbox" class="category" id="category-urgente" checked> URGENTE
            </label><br>
        </div>

        <div class="event-list" id="event-list">
            <!-- Eventos serão exibidos aqui -->
        </div>
    </div>
</div>

<script src="./src/js/calendario.js">

</script>

</body>
</html>
