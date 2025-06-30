<?php
// ====================================================================
//  viagens_completo.php   –   Bonde de Santa Teresa
//  Versão FINAL (ATUALIZADA) – 2025-06-27
// --------------------------------------------------------------------
//  Funcionalidades
//    • Cadastro de viagens (subindo/retorno) com barra de capacidade
//    • Tabela paginada (7 linhas) + setas após 5 botões
//    • Clique na linha insere linha abaixo com dados do retorno
//    • Totais consolidados para SUBINDO e RETORNO sempre corretos
//    • Excluir, Alterar e Limpar Transações (via AJAX)
//    • Busca por ID
// ====================================================================

/* ======================= CONEXÃO BD ================================ */
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "gm_sicbd";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

/* ==================== DADOS PARA SELECTS =========================== */
$bondes           = $conn->query("SELECT id, modelo FROM bondes ORDER BY modelo")->fetchAll(PDO::FETCH_ASSOC);
$destinos_subida  = $conn->query("SELECT id, nome FROM destinos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC); // Todos os destinos
$destinos_descida = $conn->query("SELECT id, nome FROM destinos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC); // Todos os destinos
$maquinistas      = $conn->query("SELECT id, nome FROM maquinistas ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$agentes          = $conn->query("SELECT id, nome FROM agentes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Os dados da tabela e totais serão carregados via AJAX na inicialização da página

include 'header.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Viagens - Bonde de Santa Teresa</title>
    <link rel="icon" type="image/png" href="Bondes Santa Teresa Logo.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f2f5;
            color: #333;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .header-section h2 {
            margin: 0;
            color: #004a99;
            font-size: 1.8em;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-section img {
            max-width: 60px;
            height: auto;
        }

        .section-title {
            background-color: #e0e0e0;
            padding: 8px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-weight: bold;
            color: #333;
            text-align: center;
            font-size: 1.1em;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .input-item {
            display: flex;
            flex-direction: column;
        }

        .input-item label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
            font-size: 0.9em;
        }

        .input-item select,
        .input-item input[type="text"],
        .input-item input[type="number"],
        .input-item input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 0.95em;
        }

        .input-item input[readonly] {
            background-color: #eee;
        }

        .counts-section {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            margin-bottom: 20px;
        }

        .total-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            flex: 1;
            text-align: center;
            min-width: 250px;
        }

        .total-box .section-title {
            background-color: #d0d0d0;
            margin-top: 0;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 1.1em;
            padding: 3px 0;
        }

        .total-item span:first-child {
            font-weight: bold;
            color: #444;
        }

        .total-item span:last-child {
            color: #007bff;
        }

        .buttons-row {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }

        .buttons-row button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            color: white;
            transition: background-color 0.3s ease, opacity 0.3s ease;
        }

        .buttons-row button:hover:not(:disabled) {
            opacity: 0.9;
        }

        .buttons-row button:disabled {
            background-color: #cccccc !important;
            cursor: not-allowed;
        }

        #adicionar-btn { background-color: #28a745; } /* Verde */
        #limpar-form-btn { background-color: #ffc107; color: #333;} /* Amarelo */
        #excluir-btn { background-color: #dc3545; } /* Vermelho */
        #alterar-btn { background-color: #007bff; } /* Azul */
        #limpar-transacoes-btn { background-color: #6c757d; } /* Cinza */

        .id-filter-container {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: 20px;
        }

        .id-filter-container input {
            width: 80px;
        }

        .table-section {
            margin-top: 20px;
        }

        .table-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table-section th,
        .table-section td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 0.9em;
        }

        .table-section th {
            background-color: #e9ecef;
            font-weight: bold;
            color: #495057;
        }

        .table-section tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table-section tbody tr:hover {
            background-color: #e2e6ea;
            cursor: pointer;
        }

        .table-section .retorno-line {
            background: #e7f3ff;
            font-style: italic;
            color: #004a99;
        }

        .table-section .return-form-row td {
            padding: 5px;
        }

        .table-section .return-form-row form {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            padding: 5px 0;
        }

        .table-section .return-form-row label {
            font-weight: normal;
            margin-bottom: 0;
            font-size: 0.9em;
        }

        .table-section .return-form-row input[type="number"] {
            width: 70px;
            padding: 3px;
            font-size: 0.85em;
        }

        .table-section .return-form-row button {
            padding: 5px 12px;
            font-size: 0.85em;
            margin: 0;
        }

        .progress-container {
            margin-top: 15px;
            margin-bottom: 20px;
            background-color: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-bar {
            width: 100%;
            background: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 0.9em;
            font-weight: bold;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: #4caf50;
            width: 0;
            transition: width .3s ease, background-color .3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: 0;
            top: 0;
            color: #fff;
        }

        .progress-text {
            z-index: 1;
            color: #333; /* Cor do texto padrão */
            position: relative;
        }
        .progress-fill .progress-text {
             color: white; /* Cor do texto quando dentro da barra preenchida */
        }


        .pagination {
            text-align: center;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }

        .pagination button {
            margin: 0;
            padding: 8px 12px;
            border: 1px solid #ccc;
            background: #fff;
            cursor: pointer;
            border-radius: 4px;
            font-size: 0.9em;
            transition: background-color 0.2s, color 0.2s;
        }

        .pagination button:hover:not(.active):not(.arrow):not(:disabled) {
            background-color: #e9ecef;
        }

        .pagination .active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
            font-weight: bold;
        }

        .pagination .arrow {
            font-weight: bold;
            padding: 8px 15px;
        }
        .pagination button:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <h2>CADASTRAMENTO DE TRANSAÇÕES</h2>
           
        </div>

        <div class="counts-section">
            <div class="total-box">
                <div class="section-title">TOTAL BONDES SUBIDA</div>
                <div class="total-item"><span>Pagantes</span><span id="total-subida-pagantes">0</span></div>
                <div class="total-item"><span>Gratuitos</span><span id="total-subida-gratuitos">0</span></div>
                <div class="total-item"><span>Moradores</span><span id="total-subida-moradores">0</span></div>
                <div class="total-item"><span>Passageiros</span><span id="total-subida-passageiros">0</span></div>
                <div class="total-item"><span>Bondes</span><span id="total-subida-bondes">0</span></div>
            </div>
            <div class="total-box">
                <div class="section-title">TOTAL BONDES DESCIDA</div>
                <div class="total-item"><span>Pagantes</span><span id="total-descida-pagantes">0</span></div>
                <div class="total-item"><span>Gratuitos</span><span id="total-descida-gratuitos">0</span></div>
                <div class="total-item"><span>Moradores</span><span id="total-descida-moradores">0</span></div>
                <div class="total-item"><span>Passageiros</span><span id="total-descida-passageiros">0</span></div>
                <div class="total-item"><span>Bondes</span><span id="total-descida-bondes">0</span></div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">REGISTRAR NOVA VIAGEM</div>
            <form id="viagemForm">
                <div class="form-grid">
                    <div class="input-item">
                        <label for="bonde_id">BONDE:</label>
                        <select name="bonde_id" id="bonde_id">
                            <option value="">Selecione</option>
                            <?php foreach ($bondes as $b) echo "<option value='{$b['id']}'>" . htmlspecialchars($b['modelo']) . "</option>"; ?>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="origem_id">ORIGEM:</label>
                        <select name="origem_id" id="origem_id">
                            <option value="">Selecione</option>
                            <?php foreach ($destinos_subida as $d) echo "<option value='{$d['id']}'>" . htmlspecialchars($d['nome']) . "</option>"; ?>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="destino_id">DESTINO (opcional):</label>
                        <select name="destino_id" id="destino_id">
                            <option value="">Selecione (somente para retorno)</option>
                            <?php foreach ($destinos_descida as $d) echo "<option value='{$d['id']}'>" . htmlspecialchars($d['nome']) . "</option>"; ?>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="maquinista_id">MAQUINISTA:</label>
                        <select name="maquinista_id" id="maquinista_id">
                            <option value="">Selecione</option>
                            <?php foreach ($maquinistas as $m) echo "<option value='{$m['id']}'>" . htmlspecialchars($m['nome']) . "</option>"; ?>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="agente_id">AGENTE:</label>
                        <select name="agente_id" id="agente_id">
                            <option value="">Selecione</option>
                            <?php foreach ($agentes as $a) echo "<option value='{$a['id']}'>" . htmlspecialchars($a['nome']) . "</option>"; ?>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="hora">HORA:</label>
                        <input type="text" name="hora" id="hora" value="<?php echo date('H:i:s'); ?>" readonly>
                    </div>
                    <div class="input-item">
                        <label for="pagantes">PAGANTES:</label>
                        <input type="number" name="pagantes" id="pagantes" min="0" value="0" required>
                    </div>
                    <div class="input-item">
                        <label for="moradores">MORADORES:</label>
                        <input type="number" name="moradores" id="moradores" min="0" value="0" required>
                    </div>
                    <div class="input-item">
                        <label for="gratuidade">GRATUIDADE:</label>
                        <input type="number" name="gratuidade" id="gratuidade" min="0" value="0" required>
                    </div>
                     <div class="input-item">
                        <label for="tipo_viagem">TIPO DE VIAGEM:</label>
                        <select name="tipo_viagem" id="tipo_viagem" disabled>
                            <option value="subida">Subida</option>
                            <option value="descida">Descida</option>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="data_viagem">DATA:</label>
                        <input type="date" name="data_viagem" id="data_viagem" value="<?php echo date('Y-m-d'); ?>" readonly>
                    </div>
                </div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div id="progressFill" class="progress-fill"></div>
                        <span class="progress-text" id="progressText">0 passageiros (32 lugares restantes)</span>
                    </div>
                </div>
                <div class="buttons-row">
                    <button type="submit" id="adicionar-btn" name="adicionar">Adicionar</button>
                    <button type="button" id="limpar-form-btn">Limpar Formulário</button>
                    <button type="button" id="alterar-btn" disabled>Alterar</button>
                    <button type="button" id="limpar-transacoes-btn">Limpar Todas as Transações</button>
                    <div class="id-filter-container">
                        <label for="id-filter">ID:</label>
                        <input type="number" id="id-filter" placeholder="Buscar ID">
                    </div>
                </div>
            </form>
        </div>

        <div class="section table-section">
            <h3>Viagens Registradas</h3>
            <table id="viagens-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Bonde</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Maquinista</th>
                        <th>Agente</th>
                        <th>Hora</th>
                        <th>Pagantes</th>
                        <th>Moradores</th>
                        <th>Gratuidade</th>
                        <th>Total Passageiros</th>
                        <th>Tipo Viagem</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
                <tfoot>
                    </tfoot>
            </table>
            <div class="pagination" id="pagination-controls">
                </div>
        </div>
    </div>

    <script src=".src/bonde/js/bonde-t.js">
         const MAX_CAPACITY = 32;
        let currentPage = 1;
        let selectedRowId = null; // Para alterar e excluir
        let formMode = 'add'; // 'add' ou 'edit'

        // Funções AJAX
        function fetchData(page = 1, searchId = null) {
            $.ajax({
                url: 'api.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'get_viagens', page: page, search_id: searchId },
                success: function(response) {
                    if (response.success) {
                        populateTable(response.viagens);
                        updatePagination(response.totalPages, response.currentPage);
                        currentPage = response.currentPage;
                    } else {
                        alert('Erro ao carregar viagens: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro na requisição AJAX para get_viagens:", status, error, xhr.responseText);
                    alert('Erro de comunicação ao carregar viagens.');
                }
            });
            updateTotals(); // Sempre atualiza os totais
        }

        function updateTotals() {
            $.ajax({
                url: 'api.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'get_totals' },
                success: function(response) {
                    if (response.success) {
                        $('#total-subida-pagantes').text(response.totals.subida.pagantes);
                        $('#total-subida-gratuitos').text(response.totals.subida.gratuitos);
                        $('#total-subida-moradores').text(response.totals.subida.moradores);
                        $('#total-subida-passageiros').text(response.totals.subida.passageiros);
                        $('#total-subida-bondes').text(response.totals.subida.bondes + ' bondes');

                        $('#total-descida-pagantes').text(response.totals.descida.pagantes);
                        $('#total-descida-gratuitos').text(response.totals.descida.gratuitos);
                        $('#total-descida-moradores').text(response.totals.descida.moradores);
                        $('#total-descida-passageiros').text(response.totals.descida.passageiros);
                        $('#total-descida-bondes').text(response.totals.descida.bondes + ' bondes');
                    } else {
                        console.error('Erro ao carregar totais: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro na requisição AJAX para get_totals:", status, error, xhr.responseText);
                }
            });
        }

        function populateTable(viagens) {
            const tbody = $('#viagens-table tbody');
            tbody.empty();
            if (viagens.length === 0) {
                tbody.append('<tr><td colspan="12" class="no-data">Nenhuma viagem encontrada.</td></tr>');
                return;
            }
            viagens.forEach(viagem => {
                const rowClass = viagem.tipo_viagem === 'descida' ? 'retorno-line' : '';
                const row = `
                    <tr data-viagem-id="${viagem.id}" class="${rowClass}">
                        <td>${viagem.id}</td>
                        <td>${viagem.bonde}</td>
                        <td>${viagem.origem}</td>
                        <td>${viagem.destino || ''}</td>
                        <td>${viagem.maquinista}</td>
                        <td>${viagem.agente}</td>
                        <td>${viagem.hora}</td>
                        <td>${viagem.pagantes}</td>
                        <td>${viagem.moradores}</td>
                        <td>${viagem.gratuidade}</td>
                        <td>${viagem.passageiros}</td>
                        <td>${viagem.tipo_viagem}</td>
                    </tr>
                `;
                tbody.append(row);
            });
            // Re-bind click event to new rows
            bindTableRowClick();
        }

        function updatePagination(totalPages, currentPage) {
            const paginationControls = $('#pagination-controls');
            paginationControls.empty();

            const visiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(visiblePages / 2));
            let endPage = Math.min(totalPages, startPage + visiblePages - 1);

            if (endPage - startPage + 1 < visiblePages) {
                startPage = Math.max(1, endPage - visiblePages + 1);
            }

            // Botão "Anterior"
            paginationControls.append(`<button class="arrow" ${currentPage === 1 ? 'disabled' : ''} data-page="${currentPage - 1}">&lt;</button>`);

            // Botões de números
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                paginationControls.append(`<button class="${activeClass}" data-page="${i}">${i}</button>`);
            }

            // Botão "Próximo"
            paginationControls.append(`<button class="arrow" ${currentPage === totalPages ? 'disabled' : ''} data-page="${currentPage + 1}">&gt;</button>`);

            // Atribui evento de clique aos botões de paginação
            paginationControls.find('button').off('click').on('click', function() {
                if ($(this).is(':disabled')) return;
                const page = parseInt($(this).data('page'));
                fetchData(page, $('#id-filter').val());
            });
        }


        // Barra de Progresso
        function updateProgressBar() {
            const pagantes = parseInt($('#pagantes').val()) || 0;
            const moradores = parseInt($('#moradores').val()) || 0;
            const gratuidade = parseInt($('#gratuidade').val()) || 0;
            const total = pagantes + moradores + gratuidade;

            const barFill = $('#progressFill');
            const progressText = $('#progressText');

            let perc = (total / MAX_CAPACITY) * 100;
            if (perc > 100) perc = 100;

            barFill.css('width', perc + '%');
            barFill.css('background-color', total > MAX_CAPACITY ? '#dc3545' : '#4caf50');

            if (total > MAX_CAPACITY) {
                progressText.text(`Capacidade máxima atingida! (${total}/${MAX_CAPACITY})`);
                progressText.css('color', '#fff'); // Texto branco para contraste no vermelho
            } else if (total === 0) {
                progressText.text(`0 passageiros (${MAX_CAPACITY} lugares restantes)`);
                 progressText.css('color', '#333'); // Texto preto para fundo branco
            } else {
                const restantes = MAX_CAPACITY - total;
                progressText.text(`${total} passageiros (${restantes} lugar${restantes !== 1 ? 'es' : ''} restantes)`);
                // Ajusta a cor do texto dependendo de onde ele fica
                if (perc < 50) { // Se a barra estiver pequena, o texto fica fora dela
                     progressText.css('color', '#333');
                } else { // Se a barra estiver grande, o texto fica dentro
                    progressText.css('color', '#fff');
                }
            }
        }


        // Limpar formulário (botão "Limpar Formulário")
        $('#limpar-form-btn').on('click', function() {
            $('#viagemForm')[0].reset();
            $('#hora').val('<?php echo date('H:i:s'); ?>');
            $('#data_viagem').val('<?php echo date('Y-m-d'); ?>');
            updateProgressBar();
            selectedRowId = null;
            formMode = 'add';
            $('#adicionar-btn').text('Adicionar').prop('disabled', false);
            $('#alterar-btn, #excluir-btn').prop('disabled', true);
            // Reativa campos que podem ter sido desabilitados para 'retorno'
            $('#origem_id').prop('disabled', false);
            $('#tipo_viagem').val('subida').prop('disabled', true); // Volta para subida e desabilita
            $('#destino_id').val('').prop('disabled', false); // Limpa e habilita para nova entrada
            $('#id-filter').val(''); // Limpa o filtro de ID
            fetchData(1); // Recarrega a tabela para a primeira página
            highlightRow(null); // Remove qualquer destaque de linha
        });

        // Limpar todas as transações (botão "Limpar Todas as Transações")
        $('#limpar-transacoes-btn').on('click', function() {
            if (confirm('Tem certeza que deseja LIMPAR TODAS as transações? Esta ação é irreversível!')) {
                $.ajax({
                    url: 'api.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'clear_transactions' },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            fetchData(1); // Recarrega a tabela
                            updateTotals(); // Atualiza os totais
                        } else {
                            alert('Erro ao limpar transações: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Erro de comunicação ao limpar transações.');
                    }
                });
            }
        });

        // Submeter formulário (Adicionar/Alterar)
        $('#viagemForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serializeArray();
            let action = formMode === 'add' ? 'add_viagem' : 'update_viagem';
            let data = {};
            formData.forEach(item => {
                data[item.name] = item.value;
            });

            // Se for alteração, adiciona o ID
            if (formMode === 'edit') {
                data['id'] = selectedRowId;
            }

            // Garante que o tipo_viagem esteja correto ao adicionar (sempre 'subida' inicialmente)
            if (formMode === 'add') {
                 data['tipo_viagem'] = 'subida';
                 data['destino_id'] = null; // Garante que destino_id seja nulo para subidas
            }


            data['action'] = action;

            $.ajax({
                url: 'api.php',
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#limpar-form-btn').click(); // Limpa o formulário e reseta o modo
                    } else {
                        alert('Erro: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro na requisição AJAX para submit do formulário:", status, error, xhr.responseText);
                    alert('Erro de comunicação com o servidor.');
                }
            });
        });

        // Excluir viagem (botão "Excluir")
        $('#excluir-btn').on('click', function() {
            if (selectedRowId && confirm('Tem certeza que deseja excluir esta viagem?')) {
                $.ajax({
                    url: 'api.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { action: 'delete_viagem', id: selectedRowId },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#limpar-form-btn').click(); // Limpa o formulário e reseta
                        } else {
                            alert('Erro ao excluir: ' + response.message);
                        }
                    },
                    error: function() {
                        alert('Erro de comunicação ao excluir.');
                    }
                });
            } else if (!selectedRowId) {
                alert('Selecione uma viagem na tabela para excluir.');
            }
        });

        // Função para preencher o formulário ao clicar na linha da tabela
        function fillFormWithRowData($row) {
            selectedRowId = $row.data('viagem-id');
            formMode = 'edit';

            $('#adicionar-btn').text('Atualizar').prop('disabled', false); // Muda texto do botão para "Atualizar"
            $('#alterar-btn, #excluir-btn').prop('disabled', false); // Habilita botões de alterar e excluir

            const bonde_text = $row.find('td:nth-child(2)').text(); // Bonde
            const origem_text = $row.find('td:nth-child(3)').text(); // Origem
            const destino_text = $row.find('td:nth-child(4)').text(); // Destino
            const maquinista_text = $row.find('td:nth-child(5)').text(); // Maquinista
            const agente_text = $row.find('td:nth-child(6)').text(); // Agente
            const hora_text = $row.find('td:nth-child(7)').text(); // Hora
            const pagantes_val = parseInt($row.find('td:nth-child(8)').text()); // Pagantes
            const moradores_val = parseInt($row.find('td:nth-child(9)').text()); // Moradores
            const gratuidade_val = parseInt($row.find('td:nth-child(10)').text()); // Gratuidade
            const tipo_viagem_text = $row.find('td:nth-child(12)').text(); // Tipo Viagem

            // Preencher selects pelo texto
            $('#bonde_id option').filter(function() { return $(this).text() === bonde_text; }).prop('selected', true);
            $('#origem_id option').filter(function() { return $(this).text() === origem_text; }).prop('selected', true);
            $('#destino_id option').filter(function() { return $(this).text() === destino_text; }).prop('selected', true);
            $('#maquinista_id option').filter(function() { return $(this).text() === maquinista_text; }).prop('selected', true);
            $('#agente_id option').filter(function() { return $(this).text() === agente_text; }).prop('selected', true);

            $('#hora').val(hora_text);
            $('#pagantes').val(pagantes_val);
            $('#moradores').val(moradores_val);
            $('#gratuidade').val(gratuidade_val);
            $('#tipo_viagem').val(tipo_viagem_text).prop('disabled', true); // Desabilita alteração do tipo de viagem

            // Para uma linha já existente, o campo de origem e destino pode precisar de tratamento especial
            // Se for uma viagem 'subida' clicada, permite preencher o destino para um possível retorno.
            if (tipo_viagem_text === 'subida') {
                $('#destino_id').prop('disabled', false);
            } else {
                $('#destino_id').prop('disabled', false); // Se já for descida, mantém habilitado mas não faz sentido alterar o destino
            }
             $('#origem_id').prop('disabled', true); // Para edição, não se altera a origem da viagem já feita

            updateProgressBar();
            highlightRow(selectedRowId);
        }

        function highlightRow(id) {
            $('#viagens-table tbody tr').removeClass('highlighted-row');
            if (id) {
                $(`tr[data-viagem-id="${id}"]`).addClass('highlighted-row');
            }
        }

        // Click na linha da tabela para preencher o formulário ou adicionar retorno
        function bindTableRowClick() {
            $('#viagens-table tbody tr').off('click').on('click', function() {
                const $clickedRow = $(this);
                const viagemId = $clickedRow.data('viagem-id');
                const tipoViagem = $clickedRow.find('td:nth-child(12)').text();

                // Remove qualquer linha de retorno existente
                $('.return-form-row').remove();
                $('#viagens-table tbody tr').removeClass('highlighted-row'); // Remove destaque de todas as linhas

                // Se for uma linha de "subida", permite adicionar uma linha de retorno
                if (tipoViagem === 'subida') {
                    // Preenche o formulário principal
                    fillFormWithRowData($clickedRow);

                    // Adiciona a linha para registrar o retorno logo abaixo
                    const bonde = $clickedRow.find('td:nth-child(2)').text();
                    const maquinista = $clickedRow.find('td:nth-child(5)').text();
                    const agente = $clickedRow.find('td:nth-child(6)').text();
                    const hora = '<?php echo date('H:i:s'); ?>'; // Hora atual para o retorno

                    const newRow = `
                        <tr class="return-form-row">
                            <td colspan="12">
                                <form class="return-subform" data-viagem-id="${viagemId}">
                                    <input type="hidden" name="action" value="update_viagem">
                                    <input type="hidden" name="id" value="${viagemId}">
                                    <label>Tipo: <input type="text" value="Descida" readonly style="width:70px;"></label>
                                    <label>Bonde: <input type="text" value="${bonde}" readonly style="width:100px;"></label>
                                    <label>Maquinista: <input type="text" value="${maquinista}" readonly style="width:120px;"></label>
                                    <label>Agente: <input type="text" value="${agente}" readonly style="width:120px;"></label>
                                    <label>Hora: <input type="text" value="${hora}" readonly style="width:80px;"></label>
                                    <label>Destino:
                                        <select name="destino_id" required style="width:120px;">
                                            <option value="">Selecione</option>
                                            <?php foreach ($destinos_descida as $d) echo "<option value='{$d['id']}'>" . htmlspecialchars($d['nome']) . "</option>"; ?>
                                        </select>
                                    </label>
                                    <label>Pagantes: <input type="number" name="pagantes" min="0" value="0" required></label>
                                    <label>Moradores: <input type="number" name="moradores" min="0" value="0" required></label>
                                    <label>Gratuidade: <input type="number" name="gratuidade" min="0" value="0" required></label>
                                    <button type="submit" style="background:#007bff;color:#fff;">Salvar Retorno</button>
                                    <button type="button" class="cancel-return-form-btn" style="background:#dc3545;color:#fff;">Cancelar</button>
                                </form>
                            </td>
                        </tr>
                    `;
                    $clickedRow.after(newRow);

                    // Adicionar destaque à linha clicada
                    $clickedRow.addClass('highlighted-row');
                } else {
                    // Se for uma linha de "descida" (retorno), apenas preenche o formulário principal
                    fillFormWithRowData($clickedRow);
                }
            });

            // Handle submission of return sub-form
            $('#viagens-table').on('submit', '.return-subform', function(e) {
                e.preventDefault();
                const $form = $(this);
                const formData = $form.serializeArray();
                let data = {};
                formData.forEach(item => {
                    data[item.name] = item.value;
                });
                data['action'] = 'update_viagem'; // Ação para atualizar a viagem existente com dados de retorno
                data['tipo_viagem'] = 'descida'; // Define explicitamente como descida
                data['hora'] = $('#hora').val(); // Usa a hora atual do formulário principal ou gera uma nova se necessário

                $.ajax({
                    url: 'api.php',
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            alert('Retorno registrado com sucesso!');
                            $('#limpar-form-btn').click(); // Limpa o formulário e recarrega
                        } else {
                            alert('Erro ao registrar retorno: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erro na requisição AJAX para submeter retorno:", status, error, xhr.responseText);
                        alert('Erro de comunicação ao registrar retorno.');
                    }
                });
            });

            // Handle cancel button for return sub-form
            $('#viagens-table').on('click', '.cancel-return-form-btn', function() {
                $(this).closest('.return-form-row').remove();
                $('#viagens-table tbody tr').removeClass('highlighted-row');
                $('#limpar-form-btn').click(); // Limpa o formulário principal também
            });
        }


        // Inicialização
        $(document).ready(function() {
            fetchData();
            updateProgressBar();

            // Atribui eventos de input para a barra de progresso
            $('#pagantes, #moradores, #gratuidade').on('input', updateProgressBar);
            $('#bonde_id').on('change', function() {
                const bondeSelecionado = $(this).val() !== "";
                $('#origem_id, #destino_id, #maquinista_id, #agente_id, #pagantes, #moradores, #gratuidade').prop('disabled', !bondeSelecionado);
                $('#adicionar-btn').prop('disabled', !bondeSelecionado);
                updateProgressBar(); // Atualiza a barra ao mudar o bonde
            });

            // Auto-atualizar hora
            setInterval(function() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                $('#hora').val(`${hours}:${minutes}:${seconds}`);
            }, 1000);

            // Filtro por ID
            let searchTimeout;
            $('#id-filter').on('keyup', function() {
                clearTimeout(searchTimeout);
                const searchId = $(this).val();
                searchTimeout = setTimeout(() => {
                    fetchData(1, searchId || null); // Passa null se o campo estiver vazio
                }, 500); // Atraso de 500ms para evitar muitas requisições
            });

            // Define o estado inicial dos botões e campos
            $('#alterar-btn, #excluir-btn').prop('disabled', true);
            $('#origem_id, #destino_id, #maquinista_id, #agente_id, #pagantes, #moradores, #gratuidade, #tipo_viagem').prop('disabled', true);
        });
    </script>
</body>
</html>