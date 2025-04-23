<?php
session_start();

// Generate CSRF token for form security
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . htmlspecialchars($e->getMessage()));
}

// Get month and year from GET or default to current
$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date("m");
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date("Y");
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Validate month and year
if ($currentMonth < 1 || $currentMonth > 12) $currentMonth = (int)date("m");
if ($currentYear < 1970 || $currentYear > 9999) $currentYear = (int)date("Y");

// Handle form submissions (Add/Edit/Delete events)
$messages = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $messages[] = ['type' => 'error', 'text' => 'Erro de validação CSRF.'];
    } else {
        try {
            if ($_POST['action'] === 'add_event' || $_POST['action'] === 'edit_event') {
                $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
                $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING) ?? '';
                $data = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);
                $hora = filter_input(INPUT_POST, 'hora', FILTER_SANITIZE_STRING);
                $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING);
                $cor = filter_input(INPUT_POST, 'cor', FILTER_SANITIZE_STRING);

                if (!$titulo || !$data || !$hora || !$categoria || !$cor) {
                    $messages[] = ['type' => 'error', 'text' => 'Preencha todos os campos obrigatórios.'];
                } else {
                    $datetime = "$data $hora";
                    if ($_POST['action'] === 'add_event') {
                        $sql = "INSERT INTO eventos (titulo, descricao, data, hora, categoria, cor) 
                                VALUES (:titulo, :descricao, :data, :hora, :categoria, :cor)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            'titulo' => $titulo,
                            'descricao' => $descricao,
                            'data' => $datetime,
                            'hora' => $hora,
                            'categoria' => $categoria,
                            'cor' => $cor
                        ]);
                        $messages[] = ['type' => 'success', 'text' => 'Evento adicionado com sucesso!'];
                    } else {
                        $id = (int)$_POST['event_id'];
                        $sql = "UPDATE eventos SET titulo = :titulo, descricao = :descricao, data = :data, 
                                hora = :hora, categoria = :categoria, cor = :cor WHERE id = :id";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            'titulo' => $titulo,
                            'descricao' => $descricao,
                            'data' => $datetime,
                            'hora' => $hora,
                            'categoria' => $categoria,
                            'cor' => $cor,
                            'id' => $id
                        ]);
                        $messages[] = ['type' => 'success', 'text' => 'Evento atualizado com sucesso!'];
                    }
                }
            } elseif ($_POST['action'] === 'delete_event') {
                $id = (int)$_POST['event_id'];
                $sql = "DELETE FROM eventos WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $id]);
                $messages[] = ['type' => 'success', 'text' => 'Evento excluído com sucesso!'];
            }
        } catch (PDOException $e) {
            $messages[] = ['type' => 'error', 'text' => 'Erro no banco de dados: ' . htmlspecialchars($e->getMessage())];
        }
    }
}

// Fetch all parcels and events for the month
try {
    $sql_parcelas = "SELECT titulo, valor_contrato AS valor, validade, num_parcelas 
                     FROM gestao_contratos 
                     WHERE MONTH(validade) = :mes AND YEAR(validade) = :ano AND num_parcelas > 0";
    $stmt_parcelas = $pdo->prepare($sql_parcelas);
    $stmt_parcelas->execute(['mes' => $currentMonth, 'ano' => $currentYear]);
    $contratos = $stmt_parcelas->fetchAll(PDO::FETCH_ASSOC);

    $sql_eventos = "SELECT id, titulo, cor, DAY(data) AS day, categoria 
                    FROM eventos 
                    WHERE MONTH(data) = :mes AND YEAR(data) = :ano";
    if ($categoryFilter) {
        $sql_eventos .= " AND categoria = :categoria";
    }
    if ($searchQuery) {
        $sql_eventos .= " AND (titulo LIKE :search OR descricao LIKE :search)";
    }
    $stmt_eventos = $pdo->prepare($sql_eventos);
    $params = ['mes' => $currentMonth, 'ano' => $currentYear];
    if ($categoryFilter) $params['categoria'] = $categoryFilter;
    if ($searchQuery) $params['search'] = "%$searchQuery%";
    $stmt_eventos->execute($params);
    $eventos = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages[] = ['type' => 'error', 'text' => 'Erro ao buscar dados: ' . htmlspecialchars($e->getMessage())];
}

// Function to generate the calendar
function gerarCalendario($month, $year, $contratos, $eventos) {
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDayOfMonth = strtotime("$year-$month-01");
    $firstDayWeekday = (int)date("w", $firstDayOfMonth);

    $calendar = '<table class="calendar-table" role="grid" aria-label="Calendário de Eventos">';
    $calendar .= '<thead><tr>';
    $calendar .= '<th scope="col">Dom</th><th scope="col">Seg</th><th scope="col">Ter</th>';
    $calendar .= '<th scope="col">Qua</th><th scope="col">Qui</th><th scope="col">Sex</th><th scope="col">Sáb</th>';
    $calendar .= '</tr></thead><tbody><tr>';

    // Add blank days at the start
    for ($i = 0; $i < $firstDayWeekday; $i++) {
        $calendar .= '<td></td>';
    }

    $day = 1;
    $cellCount = $firstDayWeekday;
    while ($day <= $daysInMonth) {
        if ($cellCount % 7 === 0) {
            $calendar .= '</tr><tr>';
        }

        $calendar .= "<td role='gridcell' aria-label='Dia $day'>";
        $calendar .= "<span class='day-number'>$day</span>";

        // Display parcels
        foreach ($contratos as $contrato) {
            $validade = strtotime($contrato['validade']);
            $startMonth = (int)date('m', $validade);
            $startYear = (int)date('Y', $validade);
            $parcelamentoMes = (($year - $startYear) * 12) + ($month - $startMonth);
            if ($parcelamentoMes >= 0 && $parcelamentoMes < $contrato['num_parcelas']) {
                $parcelValue = $contrato['valor'] / $contrato['num_parcelas'];
                $calendar .= "<div class='parcela' aria-label='Parcela do contrato {$contrato['titulo']}'>";
                $calendar .= "<strong>" . htmlspecialchars($contrato['titulo']) . "</strong><br>";
                $calendar .= "Parcela $parcelamentoMes de {$contrato['num_parcelas']}<br>";
                $calendar .= "Valor: R$ " . number_format($parcelValue, 2, ',', '.');
                $calendar .= "</div>";
            }
        }

        // Display events
        foreach ($eventos as $evento) {
            if ($evento['day'] == $day) {
                $calendar .= "<div class='evento' style='background-color: {$evento['cor']}' data-id='{$evento['id']}' role='button' tabindex='0' aria-label='Evento: {$evento['titulo']}'>";
                $calendar .= "<strong>" . htmlspecialchars($evento['titulo']) . "</strong>";
                $calendar .= "</div>";
            }
        }

        $calendar .= '</td>';
        $day++;
        $cellCount++;
    }

    // Fill remaining cells
    while ($cellCount % 7 !== 0) {
        $calendar .= '<td></td>';
        $cellCount++;
    }
    $calendar .= '</tr></tbody></table>';
    return $calendar;
}

// Generate navigation links
function generateNavigation($currentMonth, $currentYear, $categoryFilter, $searchQuery) {
    $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
    $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
    $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
    $nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;

    $params = [];
    if ($categoryFilter) $params['category'] = $categoryFilter;
    if ($searchQuery) $params['search'] = $searchQuery;

    $prevQuery = http_build_query(array_merge($params, ['month' => $previousMonth, 'year' => $previousYear]));
    $nextQuery = http_build_query(array_merge($params, ['month' => $nextMonth, 'year' => $nextYear]));

    return [
        'prevMonthLink' => "?$prevQuery",
        'nextMonthLink' => "?$nextQuery"
    ];
}

$navigation = generateNavigation($currentMonth, $currentYear, $categoryFilter, $searchQuery);
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
    <!-- Messages -->
    <div id="messages" aria-live="polite">
        <?php foreach ($messages as $msg): ?>
            <div class="message message-<?= $msg['type'] ?>">
                <?= htmlspecialchars($msg['text']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Filters -->
    <div class="filters">
        <form id="filter-form" method="GET">
            <div class="form-group">
                <label for="category">Filtrar por Categoria:</label>
                <select name="category" id="category">
                    <option value="">Todas</option>
                    <option value="geral" <?= $categoryFilter === 'geral' ? 'selected' : '' ?>>Geral</option>
                    <option value="audiencia" <?= $categoryFilter === 'audiencia' ? 'selected' : '' ?>>Audiência</option>
                    <option value="escritorio" <?= $categoryFilter === 'escritorio' ? 'selected' : '' ?>>Escritório</option>
                    <option value="ligacao" <?= $categoryFilter === 'ligacao' ? 'selected' : '' ?>>Ligação</option>
                    <option value="oab" <?= $categoryFilter === 'oab' ? 'selected' : '' ?>>OAB</option>
                    <option value="reuniao" <?= $categoryFilter === 'reuniao' ? 'selected' : '' ?>>Reunião</option>
                    <option value="urgente" <?= $categoryFilter === 'urgente' ? 'selected' : '' ?>>Urgente</option>
                </select>
            </div>
            <div class="form-group">
                <label for="search">Pesquisar:</label>
                <input type="text" name="search" id="search" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Título ou descrição">
            </div>
            <input type="hidden" name="month" value="<?= $currentMonth ?>">
            <input type="hidden" name="year" value="<?= $currentYear ?>">
            <button type="submit">Filtrar</button>
        </form>
    </div>

    <!-- Calendar -->
    <div class="calendar">
        <div class="calendar-header">
            <button id="prev-month" data-url="<?= $navigation['prevMonthLink'] ?>" aria-label="Mês anterior">Anterior</button>
            <h2 id="month-year" aria-live="polite"><?= date("F Y", strtotime("$currentYear-$currentMonth-01")) ?></h2>
            <button id="next-month" data-url="<?= $navigation['nextMonthLink'] ?>" aria-label="Próximo mês">Próximo</button>
        </div>
        <?= gerarCalendario($currentMonth, $currentYear, $contratos, $eventos); ?>
    </div>

    <!-- Sidebar for adding events -->
    <div class="sidebar">
        <h3>Adicionar Evento</h3>
        <form id="add-event-form" method="POST">
            <input type="hidden" name="action" value="add_event">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" name="titulo" id="titulo" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <textarea name="descricao" id="descricao" aria-describedby="desc-help"></textarea>
                <small id="desc-help">Opcional</small>
            </div>
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" name="data" id="data" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="hora">Hora:</label>
                <input type="time" name="hora" id="hora" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="categoria">Categoria:</label>
                <select name="categoria" id="categoria" required aria-required="true">
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
                <label for="cor">Cor:</label>
                <input type="color" name="cor" id="cor" value="#ff0000" aria-describedby="color-help">
                <small id="color-help">Escolha uma cor para o evento</small>
            </div>
            <button type="submit">Adicionar Evento</button>
        </form>
    </div>

    <!-- Modal for editing events -->
    <div id="edit-event-modal" class="modal" role="dialog" aria-labelledby="edit-modal-title" aria-hidden="true">
        <div class="modal-content">
            <h3 id="edit-modal-title">Editar Evento</h3>
            <form id="edit-event-form" method="POST">
                <input type="hidden" name="action" value="edit_event">
                <input type="hidden" name="event_id" id="edit-event-id">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="form-group">
                    <label for="edit-titulo">Título:</label>
                    <input type="text" name="titulo" id="edit-titulo" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="edit-descricao">Descrição:</label>
                    <textarea name="descricao" id="edit-descricao"></textarea>
                </div>
                <div class="form-group">
                    <label for="edit-data">Data:</label>
                    <input type="date" name="data" id="edit-data" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="edit-hora">Hora:</label>
                    <input type="time" name="hora" id="edit-hora" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="edit-categoria">Categoria:</label>
                    <select name="categoria" id="edit-categoria" required aria-required="true">
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
                    <label for="edit-cor">Cor:</label>
                    <input type="color" name="cor" id="edit-cor" value="#ff0000">
                </div>
                <button type="submit">Salvar Alterações</button>
                <button type="button" id="delete-event-btn">Excluir Evento</button>
                <button type="button" id="close-modal-btn">Fechar</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="./src/js/calendario.js"></script>
</body>
</html>