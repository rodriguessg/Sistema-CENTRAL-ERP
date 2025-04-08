<?php
// Conexão com o banco de dados (substitua pelos seus dados)
$pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtém o mês e ano atuais ou os passados via GET
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date("m");
$currentYear = isset($_GET['year']) ? $_GET['year'] : date("Y");

// Função para gerar o calendário para o mês e ano informados
function gerarCalendario($month, $year, $pdo) {
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
        // Exibe as parcelas para o mês
        $sql_parcelas = "SELECT titulo, valor_contrato AS valor, validade, num_parcelas FROM gestao_contratos 
                         WHERE MONTH(validade) <= :mes AND YEAR(validade) <= :ano
                         AND num_parcelas > 0";
        $stmt_parcelas = $pdo->prepare($sql_parcelas);
        $stmt_parcelas->bindParam(':mes', $month);
        $stmt_parcelas->bindParam(':ano', $year);
        $stmt_parcelas->execute();
        $contratos = $stmt_parcelas->fetchAll(PDO::FETCH_ASSOC);

        foreach ($contratos as $contrato) {
            // Calculando a data de início da parcela e distribuindo-a nos meses
            $validade = strtotime($contrato['validade']);
            $startMonth = date('m', $validade);
            $startYear = date('Y', $validade);

            // Verifica se a parcela se encaixa no mês atual
            $parcelamentoMes = (($year - $startYear) * 12) + ($month - $startMonth);
            if ($parcelamentoMes >= 0 && $parcelamentoMes < $contrato['num_parcelas']) {
                $parcelValue = $contrato['valor'] / $contrato['num_parcelas'];
                $calendar .= "<br><strong>" . $contrato['titulo'] . "</strong><br>Parcela $parcelamentoMes de " . $contrato['num_parcelas'] . "<br>Valor: R$ " . number_format($parcelValue, 2, ',', '.');
            }
        }
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
include 'header.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário Interativo com Filtros</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="./src/contratos/style/calendar.css">
</head>
<body>

<div class="calendar-container">
    <!-- Calendário -->
    <div class="calendar">
        <div class="calendar-header">
            <button id="prev-month" onclick="window.location.href='<?= $navigation['prevMonthLink'] ?>'">Anterior</button>
            <h2 id="month-year"><?= date("F Y", strtotime("$currentYear-$currentMonth-01")) ?></h2>
            <button id="next-month" onclick="window.location.href='<?= $navigation['nextMonthLink'] ?>'">Próximo</button>
        </div>

        <?= gerarCalendario($currentMonth, $currentYear, $pdo); ?>

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
                <a href="">novo</a>
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

<script src="./src/js/calendario.js"></script>

</body>
</html>
