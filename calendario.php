
<!-- Botões de navegação e exibição -->
<div class="navegacao">
    <button class="btn" id="prevMonth" onclick="changeMonth(-1)">◀</button>
    <button class="btn" id="today" onclick="goToToday()">Hoje</button>
    <button class="btn" id="nextMonth" onclick="changeMonth(1)">▶</button>
</div>
<div class="exibicao">
    <button class="btn">Mês</button>
    <button class="btn">Semana</button>
    <button class="btn">Dia</button>
</div>

<!-- Event Modal -->
<div id="eventModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEvent()">&times;</span>
        <h2 id="eventTitle"></h2>
        <p id="eventDescription"></p>
    </div>
</div>

<?php
// Função para gerar o calendário do mês atual
function gerarCalendario($mes, $ano) {
    // Nomes dos meses
    $meses = [
        1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho",
        7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro"
    ];
    $diasDaSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

    // Calculando o primeiro dia do mês e o número de dias do mês
    $primeiroDia = mktime(0, 0, 0, $mes, 1, $ano);
    $diasNoMes = date('t', $primeiroDia);
    $diaDaSemana = date('w', $primeiroDia); // Obtém o dia da semana do primeiro dia do mês

    // Gerando o calendário
    echo "<h2>Calendário - " . $meses[$mes] . " $ano</h2>";
    echo "<table class='calendario'>";
    echo "<tr>";
    foreach ($diasDaSemana as $dia) {
        echo "<th>$dia</th>";
    }
    echo "</tr><tr>";

    // Preenchendo os espaços vazios antes do primeiro dia
    for ($i = 0; $i < $diaDaSemana; $i++) {
        echo "<td></td>";
    }

    // Preenchendo os dias do mês
    for ($dia = 1; $dia <= $diasNoMes; $dia++) {
        // Se chegar ao sábado, pula para a próxima linha
        if (($dia + $diaDaSemana - 1) % 7 == 0) {
            echo "</tr><tr>";
        }

        // Verifica se o dia tem um evento
        if ($dia == 18) {
            echo "<td class='evento' id='dia-$dia' onclick='showEvent($dia)'>$dia<br>Jogatina</td>";
        } else {
            // Exibe o número do dia
            echo "<td id='dia-$dia' onclick='showEvent($dia)'>$dia</td>";
        }
    }

    // Fechando a tabela
    echo "</tr>";
    echo "</table>";
}

// Definindo o mês e o ano atual, ou outro específico
$mes = date('n'); // Mês atual
$ano = date('Y'); // Ano atual

// Chamando a função para gerar o calendário
gerarCalendario($mes, $ano);
?>


<!-- Adicionando estilo para o calendário, botões e modal -->
<style>
    table.calendario {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    table.calendario th, table.calendario td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    table.calendario th {
        background-color: #f2f2f2;
    }

    table.calendario td {
        height: 50px;
    }

    table.calendario td:hover {
        background-color: #f0f0f0;
        cursor: pointer;
    }

    .evento {
        background-color: #f8d7da;
        font-weight: bold;
    }

    .navegacao, .exibicao {
        margin: 10px 0;
        text-align: center;
    }

    .btn {
        padding: 10px 20px;
        margin: 0 5px;
        border: 1px solid #ddd;
        background-color: #f2f2f2;
        cursor: pointer;
        border-radius: 4px;
    }

    .btn:hover {
        background-color: #ddd;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
    }

    .close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        right: 10px;
        top: 0;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<!-- Script para interação -->
<script>
    let currentMonth = <?php echo $mes; ?>;
    let currentYear = <?php echo $ano; ?>;

    function changeMonth(direction) {
        currentMonth += direction;
        if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        } else if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
        updateCalendar();
    }

    function updateCalendar() {
        // Aqui você pode enviar uma solicitação para o servidor para gerar o calendário com o mês correto.
        location.href = '?mes=' + currentMonth + '&ano=' + currentYear;
    }

    function goToToday() {
        const today = new Date();
        currentMonth = today.getMonth() + 1;
        currentYear = today.getFullYear();
        updateCalendar();
    }

    function showEvent(day) {
        const eventTitle = "Evento no dia " + day;
        const eventDescription = "Descrição do evento: Jogatina no dia 18.";

        document.getElementById("eventTitle").innerText = eventTitle;
        document.getElementById("eventDescription").innerText = eventDescription;

        const modal = document.getElementById("eventModal");
        modal.style.display = "block";
    }

    function closeEvent() {
        const modal = document.getElementById("eventModal");
        modal.style.display = "none";
    }
</script>
