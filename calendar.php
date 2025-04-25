<?php
session_start();

// Simulação de usuário logado (em um ambiente real, isso viria de um sistema de autenticação)
$_SESSION['username'] = isset($_SESSION['username']) ? $_SESSION['username'] : 'usuario_logado';

// Generate CSRF token for form security
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\Sistema-CENTRAL-ERP\vendor\phpmailer\phpmailer\src/Exception.php';
require 'C:\xampp\htdocs\Sistema-CENTRAL-ERP\vendor\phpmailer\phpmailer\src/PHPMailer.php';
require 'C:\xampp\htdocs\Sistema-CENTRAL-ERP\vendor\phpmailer\phpmailer\src/SMTP.php';

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . htmlspecialchars($e->getMessage()));
}

// Get month, year, and selected day from GET or default to current
$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date("m");
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date("Y");
$selectedDay = isset($_GET['day']) ? (int)$_GET['day'] : (int)date("d");
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Validate month, year, and day
if ($currentMonth < 1 || $currentMonth > 12) $currentMonth = (int)date("m");
if ($currentYear < 1970 || $currentYear > 9999) $currentYear = (int)date("Y");
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
if ($selectedDay < 1 || $selectedDay > $daysInMonth) $selectedDay = (int)date("d");

// Fetch saved emails for the current user
try {
    $sqlEmails = "SELECT email FROM emails_salvos WHERE username = :username ORDER BY criado_em DESC";
    $stmtEmails = $pdo->prepare($sqlEmails);
    $stmtEmails->execute(['username' => $_SESSION['username']]);
    $savedEmails = $stmtEmails->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $savedEmails = [];
    $messages[] = ['type' => 'error', 'text' => 'Erro ao buscar e-mails salvos: ' . htmlspecialchars($e->getMessage())];
}

// Handle form submissions (Add/Edit/Delete events, Add category)
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
                $enviarEmail = isset($_POST['enviar_email']) ? 1 : 0;
                $emailDestinatario = filter_input(INPUT_POST, 'email_destinatario', FILTER_SANITIZE_EMAIL);
                $salvarEmail = isset($_POST['salvar_email']) ? 1 : 0;

                if (!$titulo || !$data || !$hora || !$categoria || !$cor) {
                    $messages[] = ['type' => 'error', 'text' => 'Preencha todos os campos obrigatórios.'];
                } elseif ($enviarEmail && !$emailDestinatario) {
                    $messages[] = ['type' => 'error', 'text' => 'Digite um e-mail válido para enviar o evento.'];
                } else {
                    $datetime = "$data $hora";
                    $createdAt = date('Y-m-d H:i:s');

                    // Save email if requested
                    if ($enviarEmail && $salvarEmail && $emailDestinatario) {
                        $sqlSaveEmail = "INSERT IGNORE INTO emails_salvos (email, username, criado_em) 
                                         VALUES (:email, :username, :criado_em)";
                        $stmtSaveEmail = $pdo->prepare($sqlSaveEmail);
                        $stmtSaveEmail->execute([
                            'email' => $emailDestinatario,
                            'username' => $_SESSION['username'],
                            'criado_em' => $createdAt
                        ]);
                    }

                    if ($_POST['action'] === 'add_event') {
                        // Insert into eventos
                        $sql = "INSERT INTO eventos (titulo, descricao, data, hora, categoria, cor, criado_em) 
                                VALUES (:titulo, :descricao, :data, :hora, :categoria, :cor, :criado_em)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            'titulo' => $titulo,
                            'descricao' => $descricao,
                            'data' => $datetime,
                            'hora' => $hora,
                            'categoria' => $categoria,
                            'cor' => $cor,
                            'criado_em' => $createdAt
                        ]);

                        // Insert into notificacoes
                        $eventId = $pdo->lastInsertId();
                        $mensagem = "Novo evento criado: $titulo";
                        $sqlNotif = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                                     VALUES (:username, :setor, :mensagem, :situacao, :data_criacao)";
                        $stmtNotif = $pdo->prepare($sqlNotif);
                        $stmtNotif->execute([
                            'username' => $_SESSION['username'],
                            'setor' => 'contratos',
                            'mensagem' => $mensagem,
                            'situacao' => 'Não lida',
                            'data_criacao' => $createdAt
                        ]);

                        $messages[] = ['type' => 'success', 'text' => 'Evento adicionado com sucesso!'];
                    } else {
                        $id = (int)$_POST['event_id'];
                        // Update eventos
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

                        // Insert notification for update
                        $mensagem = "Evento atualizado: $titulo";
                        $sqlNotif = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                                     VALUES (:username, :setor, :mensagem, :situacao, :data_criacao)";
                        $stmtNotif = $pdo->prepare($sqlNotif);
                        $stmtNotif->execute([
                            'username' => $_SESSION['username'],
                            'setor' => 'contratos',
                            'mensagem' => $mensagem,
                            'situacao' => 'Não lida',
                            'data_criacao' => $createdAt
                        ]);

                        $messages[] = ['type' => 'success', 'text' => 'Evento atualizado com sucesso!'];
                    }

                    // Send email if requested
                    if ($enviarEmail && $emailDestinatario) {
                        $mail = new PHPMailer(true);
                        try {
                            // Server settings
                            $mail->isSMTP();
                            $mail->Host = 'central.rj.gov.br';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'impressora@central.rj.gov.br';
                            $mail->Password = 'central@123';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use SSL for port 443
                            $mail->Port = 443;

                            // Recipients
                            $mail->setFrom('impressora@central.rj.gov.br', 'Sistema de Eventos');
                            $mail->addAddress($emailDestinatario);

                            // Content
                            $mail->isHTML(true);
                            $mail->Subject = 'Detalhes do Evento: ' . $titulo;
                            $mail->Body = "
                                <h2>Detalhes do Evento</h2>
                                <p><strong>Título:</strong> $titulo</p>
                                <p><strong>Descrição:</strong> $descricao</p>
                                <p><strong>Data e Hora:</strong> $data às $hora</p>
                                <p><strong>Categoria:</strong> $categoria</p>
                                <p><strong>Cor:</strong> <span style='color: $cor;'>$cor</span></p>
                            ";
                            $mail->AltBody = "Detalhes do Evento\nTítulo: $titulo\nDescrição: $descricao\nData e Hora: $data às $hora\nCategoria: $categoria\nCor: $cor";

                            $mail->send();
                            $messages[] = ['type' => 'success', 'text' => 'E-mail enviado com sucesso para ' . htmlspecialchars($emailDestinatario) . '!'];
                        } catch (Exception $e) {
                            $messages[] = ['type' => 'error', 'text' => 'Erro ao enviar o e-mail: ' . htmlspecialchars($mail->ErrorInfo)];
                        }
                    }
                }
            } elseif ($_POST['action'] === 'delete_event') {
                $id = (int)$_POST['event_id'];
                // Fetch event title before deletion for notification
                $sqlFetch = "SELECT titulo FROM eventos WHERE id = :id";
                $stmtFetch = $pdo->prepare($sqlFetch);
                $stmtFetch->execute(['id' => $id]);
                $event = $stmtFetch->fetch(PDO::FETCH_ASSOC);
                $titulo = $event['titulo'];

                // Delete from eventos
                $sql = "DELETE FROM eventos WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['id' => $id]);

                // Insert notification for deletion
                $mensagem = "Evento excluído: $titulo";
                $sqlNotif = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                             VALUES (:username, :setor, :mensagem, :situacao, :data_criacao)";
                $stmtNotif = $pdo->prepare($sqlNotif);
                $stmtNotif->execute([
                    'username' => $_SESSION['username'],
                    'setor' => 'contratos',
                    'mensagem' => $mensagem,
                    'situacao' => 'Não lida',
                    'data_criacao' => date('Y-m-d H:i:s')
                ]);

                $messages[] = ['type' => 'success', 'text' => 'Evento excluído com sucesso!'];
            } elseif ($_POST['action'] === 'add_category') {
                $newCategory = filter_input(INPUT_POST, 'new_category', FILTER_SANITIZE_STRING);
                if ($newCategory) {
                    // Aqui você pode salvar a nova categoria em uma tabela de categorias, se existir
                    // Como não foi especificada uma tabela de categorias, apenas a usaremos dinamicamente
                    $messages[] = ['type' => 'success', 'text' => "Categoria '$newCategory' adicionada com sucesso!"];
                } else {
                    $messages[] = ['type' => 'error', 'text' => 'Digite o nome da nova categoria.'];
                }
            }
        } catch (PDOException $e) {
            $messages[] = ['type' => 'error', 'text' => 'Erro no banco de dados: ' . htmlspecialchars($e->getMessage())];
        }
    }
}

// Fetch events for the month and selected day
try {
    $sqlEventos = "SELECT id, titulo, cor, DAY(data) AS day, categoria, hora, descricao 
                   FROM eventos 
                   WHERE MONTH(data) = :mes AND YEAR(data) = :ano";
    if ($categoryFilter) {
        $sqlEventos .= " AND categoria = :categoria";
    }
    if ($searchQuery) {
        $sqlEventos .= " AND (titulo LIKE :search OR descricao LIKE :search)";
    }
    $stmtEventos = $pdo->prepare($sqlEventos);
    $params = ['mes' => $currentMonth, 'ano' => $currentYear];
    if ($categoryFilter) $params['categoria'] = $categoryFilter;
    if ($searchQuery) $params['search'] = "%$searchQuery%";
    $stmtEventos->execute($params);
    $eventos = $stmtEventos->fetchAll(PDO::FETCH_ASSOC);

    // Fetch events for the selected day
    $selectedDate = sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $selectedDay);
    $sqlDailyEvents = "SELECT id, titulo, cor, hora, categoria, descricao 
                       FROM eventos 
                       WHERE DATE(data) = :selected_date";
    $stmtDailyEvents = $pdo->prepare($sqlDailyEvents);
    $stmtDailyEvents->execute(['selected_date' => $selectedDate]);
    $dailyEvents = $stmtDailyEvents->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages[] = ['type' => 'error', 'text' => 'Erro ao buscar eventos: ' . htmlspecialchars($e->getMessage())];
}

// Function to generate the calendar
function gerarCalendario($month, $year, $eventos, $selectedDay) {
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

        $isSelected = $day == $selectedDay ? ' selected-day' : '';
        $calendar .= "<td role='gridcell' class='day-cell$isSelected' data-day='$day' aria-label='Dia $day'>";
        $calendar .= "<span class='day-number'>$day</span>";

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
function generateNavigation($currentMonth, $currentYear, $categoryFilter, $searchQuery, $selectedDay) {
    $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
    $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
    $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
    $nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;

    $params = ['day' => $selectedDay];
    if ($categoryFilter) $params['category'] = $categoryFilter;
    if ($searchQuery) $params['search'] = $searchQuery;

    $prevQuery = http_build_query(array_merge($params, ['month' => $previousMonth, 'year' => $previousYear]));
    $nextQuery = http_build_query(array_merge($params, ['month' => $nextMonth, 'year' => $nextYear]));

    return [
        'prevMonthLink' => "?$prevQuery",
        'nextMonthLink' => "?$nextQuery"
    ];
}

$navigation = generateNavigation($currentMonth, $currentYear, $categoryFilter, $searchQuery, $selectedDay);

// Predefined categories
$categories = [
    'geral' => 'Geral',
    'audiencia' => 'Audiência',
    'escritorio' => 'Escritório',
    'ligacao' => 'Ligação',
    'oab' => 'OAB',
    'reuniao' => 'Reunião',
    'urgente' => 'Urgente'
];

// Add new category if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_category') {
    $newCategory = filter_input(INPUT_POST, 'new_category', FILTER_SANITIZE_STRING);
    if ($newCategory) {
        $categoryKey = strtolower(str_replace(' ', '_', $newCategory));
        $categories[$categoryKey] = $newCategory;
    }
}
 include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário Interativo com Agendamento</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="./src/contratos/style/calendar.css">
</head>
<body>
<div class="calendar-container">
    <!-- Messages -->
    <div id="messages" aria-live="polite" style="width: 100%;">
        <?php foreach ($messages as $msg): ?>
            <div class="message message-<?= $msg['type'] ?>">
                <?= htmlspecialchars($msg['text']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Calendar and Daily Events -->
    <div style="display: flex; flex-wrap: wrap; flex: 1; gap: 20px;">
        <!-- Calendar -->
        <div class="calendar-section">
            <div class="calendar-header">
                <button id="prev-month" data-url="<?= $navigation['prevMonthLink'] ?>" aria-label="Mês anterior">Anterior</button>
                <h2 id="month-year" aria-live="polite"><?= date("F Y", strtotime("$currentYear-$currentMonth-01")) ?></h2>
                <button id="next-month" data-url="<?= $navigation['nextMonthLink'] ?>" aria-label="Próximo mês">Próximo</button>
            </div>
            <?= gerarCalendario($currentMonth, $currentYear, $eventos, $selectedDay); ?>
        </div>

        <!-- Daily Events Preview -->
        <div class="daily-events">
            <h3>Eventos do Dia <?= sprintf("%02d/%02d/%04d", $selectedDay, $currentMonth, $currentYear) ?></h3>
            <?php if (empty($dailyEvents)): ?>
                <p>Nenhum evento para este dia.</p>
            <?php else: ?>
                <?php foreach ($dailyEvents as $event): ?>
                    <div class="daily-event">
                        <div class="event-color-bar" style="background-color: <?= htmlspecialchars($event['cor']) ?>;"></div>
                        <div class="daily-event-content">
                            <div class="daily-event-info">
                                <strong><?= htmlspecialchars($event['titulo']) ?></strong>
                                <small>das <?= htmlspecialchars($event['hora']) ?></small>
                            </div>
                            <div class="daily-event-menu">
                                <i class="fas fa-bars menu-icon"></i>
                                <div class="dropdown-menu">
                                    <a href="#" class="edit-link" data-id="<?= $event['id'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <a href="#" class="delete-link" data-id="<?= $event['id'] ?>"><i class="fas fa-trash"></i> Excluir</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar for Adding/Editing Events and Categories -->
    <div class="sidebar">
        <h3 id="form-title">Adicionar Evento</h3>
        <form id="event-form" method="POST">
            <input type="hidden" name="action" id="form-action" value="add_event">
            <input type="hidden" name="event_id" id="event-id">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" required aria-required="true">
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea name="descricao" id="descricao" aria-describedby="desc-help"></textarea>
            </div>
            <div class="form-group input-with-icon">
                <label for="data">Data</label>
                <input type="date" name="data" id="data" required aria-required="true" value="<?= sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $selectedDay) ?>">
                <!-- <i class="fas fa-calendar-alt"></i> -->
            </div>
            <div class="form-group input-with-icon">
                <label for="hora">Tempo</label>
                <input type="time" name="hora" id="hora" required aria-required="true">
                <!-- <i class="fas fa-clock"></i> -->
            </div>
            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select name="categoria" id="categoria" required aria-required="true">
                    <?php foreach ($categories as $key => $label): ?>
                        <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="cor">Cor</label>
                <input type="color" name="cor" id="cor" value="#ff0000" required aria-required="true">
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="enviar_email" id="enviar-email">
                    Enviar por e-mail
                </label>
            </div>
            <div class="form-group" id="email-field">
                <label for="email_destinatario">E-mail do Destinatário</label>
                <select name="email_destinatario" id="email-destinatario">
                    <option value="">Selecione ou digite um e-mail</option>
                    <?php foreach ($savedEmails as $email): ?>
                        <option value="<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="email" name="email_destinatario" id="email-destinatario-input" placeholder="Digite o e-mail">
            </div>
            <div class="form-group" id="salvar-email-field">
                <label>
                    <input type="checkbox" name="salvar_email" id="salvar-email">
                    Salvar este e-mail para eventos futuros
                </label>
            </div>
            <div class="form-actions">
                <button type="submit" id="submit-btn">Adicionar Evento</button>
                <button type="button" class="cancel-btn" id="cancel-btn" style="display: none;">Cancelar</button>
            </div>
        </form>

        <button id="toggle-category-form">Adicionar nova categoria</button>
        <form id="add-category-form" method="POST">
            <input type="hidden" name="action" value="add_category">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label for="new_category">Nova Categoria:</label>
                <input type="text" name="new_category" id="new_category" placeholder="Digite o nome da categoria">
            </div>
            <button type="submit">Adicionar Categoria</button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle category form visibility
        $('#toggle-category-form').click(function() {
            $('#add-category-form').toggle();
        });

        // Navigation buttons
        $('#prev-month, #next-month').click(function() {
            window.location.href = $(this).data('url');
        });

        // Select day to view events
        $('.day-cell').click(function() {
            const day = $(this).data('day');
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('day', day);
            window.location.search = urlParams.toString();
        });

        // Toggle dropdown menu
        $('.menu-icon').click(function(e) {
            e.stopPropagation();
            const $dropdown = $(this).siblings('.dropdown-menu');
            $('.dropdown-menu').not($dropdown).hide();
            $dropdown.toggle();
        });

        // Hide dropdown when clicking outside
        $(document).click(function() {
            $('.dropdown-menu').hide();
        });

        // Toggle email field visibility
        $('#enviar-email').change(function() {
            if ($(this).is(':checked')) {
                $('#email-field, #salvar-email-field').show();
            } else {
                $('#email-field, #salvar-email-field').hide();
                $('#email-destinatario').val('');
                $('#email-destinatario-input').val('');
                $('#salvar-email').prop('checked', false);
            }
        });

        // Toggle between select and input for email
        $('#email-destinatario').change(function() {
            if ($(this).val() === '') {
                $('#email-destinatario-input').show().focus();
            } else {
                $('#email-destinatario-input').hide();
            }
        });

        // Open edit form in sidebar
        $('.edit-link, .evento').click(function(e) {
            e.preventDefault();
            const eventId = $(this).data('id');
            $.ajax({
                url: 'fetch_event.php',
                method: 'POST',
                data: { event_id: eventId },
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        // Update form for editing
                        $('#form-title').text('Editar Evento');
                        $('#form-action').val('edit_event');
                        $('#event-id').val(data.id);
                        $('#titulo').val(data.titulo);
                        $('#descricao').val(data.descricao);
                        $('#data').val(data.data.split(' ')[0]);
                        $('#hora').val(data.hora);
                        $('#categoria').val(data.categoria);
                        $('#cor').val(data.cor);
                        $('#enviar-email').prop('checked', false);
                        $('#email-field, #salvar-email-field').hide();
                        $('#email-destinatario').val('');
                        $('#email-destinatario-input').val('');
                        $('#salvar-email').prop('checked', false);
                        $('#submit-btn').text('Evento de atualização');
                        $('#cancel-btn').show();
                    }
                },
                error: function() {
                    alert('Erro ao carregar o evento.');
                }
            });
        });

        // Reset form to "Add Event" state
        $('#cancel-btn').click(function() {
            $('#form-title').text('Adicionar Evento');
            $('#form-action').val('add_event');
            $('#event-id').val('');
            $('#event-form')[0].reset();
            $('#data').val('<?= sprintf("%04d-%02d-%02d", $currentYear, $currentMonth, $selectedDay) ?>');
            $('#cor').val('#ff0000'); // Reset color to default
            $('#enviar-email').prop('checked', false);
            $('#email-field, #salvar-email-field').hide();
            $('#email-destinatario').val('');
            $('#email-destinatario-input').val('');
            $('#salvar-email').prop('checked', false);
            $('#submit-btn').text('Adicionar Evento');
            $('#cancel-btn').hide();
        });

        // Delete event from daily events preview
        $('.delete-link').click(function(e) {
            e.preventDefault();
            if (confirm('Tem certeza que deseja excluir este evento?')) {
                const eventId = $(this).data('id');
                $('<form>', {
                    method: 'POST',
                    action: window.location.href,
                    html: `
                        <input type="hidden" name="action" value="delete_event">
                        <input type="hidden" name="event_id" value="${eventId}">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    `
                }).appendTo('body').submit();
            }
        });
    });
</script>
</body>
</html>