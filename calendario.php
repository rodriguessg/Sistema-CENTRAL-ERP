<?php
// Conexão com o banco de dados (substitua pelos seus dados)
$pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtém o mês e ano atuais ou os passados via GET
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date("m");
$currentYear = isset($_GET['year']) ? $_GET['year'] : date("Y");

// Função para gerar o calendário para o mês e ano informados
function gerarCalendario($month, $year, $pdo) {
    // Calcula o primeiro dia do mês
    $firstDayOfMonth = strtotime("$year-$month-01");
    $firstDayOfWeek = date("w", $firstDayOfMonth); // Dia da semana do primeiro dia do mês

    // Número total de dias do mês
    $daysInMonth = date("t", $firstDayOfMonth);

    $calendar = "<div class='calendar-days'>";
    $calendar .= "<div class='day-name'>Dom</div>";
    $calendar .= "<div class='day-name'>Seg</div>";
    $calendar .= "<div class='day-name'>Ter</div>";
    $calendar .= "<div class='day-name'>Qua</div>";
    $calendar .= "<div class='day-name'>Qui</div>";
    $calendar .= "<div class='day-name'>Sex</div>";
    $calendar .= "<div class='day-name'>Sáb</div>";

    // Preenche os espaços antes do primeiro dia do mês
    $day = 1;
    for ($i = 0; $i < $firstDayOfWeek; $i++) {
        $calendar .= "<div class='calendar-day empty'></div>";
    }

    // Adiciona os dias do mês
    for ($i = $firstDayOfWeek; $i < 7; $i++) {
        $calendar .= "<div class='calendar-day'>" . $day . "</div>";
        $day++;
    }

    // Preenche os dias do restante do mês
    while ($day <= $daysInMonth) {
        for ($i = 0; $i < 7; $i++) {
            if ($day > $daysInMonth) {
                break;
            }
            $calendar .= "<div class='calendar-day'>" . $day . "</div>";
            $day++;
        }
    }
    $calendar .= "</div>";

    // Exibe os eventos no mês
    $sql_eventos = "SELECT * FROM eventos WHERE MONTH(data) = :mes AND YEAR(data) = :ano";
    $stmt_eventos = $pdo->prepare($sql_eventos);
    $stmt_eventos->bindParam(':mes', $month);
    $stmt_eventos->bindParam(':ano', $year);
    $stmt_eventos->execute();
    $eventos = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($eventos as $evento) {
        $data_evento = date('j', strtotime($evento['data']));
        // Encontra o dia correspondente ao evento
        $calendar .= "<div class='evento' style='position: absolute; top: " . ($data_evento * 40) . "px; left: 0; background-color: {$evento['cor']};'>";
        $calendar .= "<span>{$evento['titulo']} (" . $evento['hora'] . ")</span>";
        $calendar .= "</div>";
    }

    return $calendar;
}

// Função para gerar os links de navegação entre meses e anos
function generateNavigation($currentMonth, $currentYear) {
    $prevMonth = ($currentMonth == 1) ? 12 : $currentMonth - 1;
    $prevYear = ($currentMonth == 1) ? $currentYear - 1 : $currentYear;
    $nextMonth = ($currentMonth == 12) ? 1 : $currentMonth + 1;
    $nextYear = ($currentMonth == 12) ? $currentYear + 1 : $currentYear;

    $prevMonthLink = "?month=$prevMonth&year=$prevYear";
    $nextMonthLink = "?month=$nextMonth&year=$nextYear";

    return ['prevMonthLink' => $prevMonthLink, 'nextMonthLink' => $nextMonthLink];
}


// Função para adicionar evento ao banco de dados
function addEvent($titulo, $descricao, $data, $hora, $categoria, $cor, $pdo) {
    $sql = "INSERT INTO eventos (titulo, descricao, data, hora, categoria, cor) VALUES (:titulo, :descricao, :data, :hora, :categoria, :cor)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':data', $data);
    $stmt->bindParam(':hora', $hora);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':cor', $cor);
    $stmt->execute();
}

// Adicionar evento se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];
    $categoria = $_POST['categoria'];
    $cor = $_POST['cor'];

    addEvent($titulo, $descricao, $data, $hora, $categoria, $cor, $pdo);
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

   

    <!-- Formulário para adicionar evento -->
    <div class="sidebar">
        <h3>Adicionar Evento</h3>
        <form method="POST">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" id="titulo" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea name="descricao" id="descricao"></textarea>
            </div>
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" name="data" id="data" required>
            </div>
            <div class="form-group">
                <label for="hora">Hora:</label>
                <input type="time" name="hora" id="hora" required>
            </div>
            <div class="form-group">
                <label for="categoria">Categoria:</label>
                <select name="categoria" id="categoria">
                    <option value="geral">Geral</option>
                    <option value="audiencia">Audiência</option>
                    <option value="escritorio">Escritório</option>
                    <option value="ligacao">Ligação</option>
                    <option value="oab">OAB</option>
                    <option value="reuniao">Reunião</option>
                    <option value="urgente">Urgente</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cor">Cor (em formato hexadecimal):</label>
                <input type="color" name="cor" id="cor" value="#ff0000">
            </div>
            <button type="submit" name="add_event">Adicionar Evento</button>
        </form>
    </div>
</div>

<script src="./src/js/calendario.js"></script>
</body>
</html>
