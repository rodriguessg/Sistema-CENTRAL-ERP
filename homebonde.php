<?php
// Include database configuration with error handling
try {
    include 'bancoo.php';
    if (!isset($pdo) || !$pdo) {
        throw new Exception("Falha na conexão com o banco de dados.");
    }
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}

// Generate a unique trip ID
function generateTripId() {
    return 'V-' . strtoupper(uniqid());
}

$new_trip_id = generateTripId();

// Fetch existing trips
$stmt = $pdo->query("SELECT v.id, v.bonde_id, v.origem, v.destino, v.passageiros_ida, v.passageiros_volta, v.data_ida, v.data_volta, b.modelo 
                    FROM viagens v 
                    LEFT JOIN bondes b ON v.bonde_id = b.id");
$existing_trips = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate average travel time
function calculateAverageTravelTime($trips) {
    $total_duration = 0;
    $count = 0;
    foreach ($trips as $trip) {
        if (isset($trip['data_ida']) && isset($trip['data_volta'])) {
            $start = new DateTime($trip['data_ida']);
            $end = new DateTime($trip['data_volta']);
            $duration = $end->getTimestamp() - $start->getTimestamp();
            $total_duration += $duration;
            $count++;
        }
    }
    if ($count > 0) {
        $average_duration = $total_duration / $count;
        $hours = floor($average_duration / 3600);
        $minutes = floor(($average_duration % 3600) / 60);
        return sprintf("%d horas e %d minutos", $hours, $minutes);
    }
    return "Não há dados suficientes.";
}
$average_time = calculateAverageTravelTime($existing_trips);

// Simulate get_trips action (replace with real endpoint)
if (isset($_GET['action']) && $_GET['action'] === 'get_trips') {
    echo json_encode($existing_trips);
    exit();
}
include 'header.php';
?>

<!DOCTYPE html>
<html lang="Pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Bondes - Santa Teresa</title>
    <link rel="stylesheet" href="src/bonde/style/bonde.css">
    <link rel="stylesheet" href="src/style/tabs.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .caderno { max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .tabs { display: flex; border-bottom: 2px solid #ccc; margin-bottom: 20px; }
        .tab { flex: 1; padding: 15px; text-align: center; cursor: pointer; background: #e0e0e0; transition: background 0.3s; }
        .tab:hover, .tab.active { background: #007bff; color: #fff; }
        .form-container { display: none; padding: 20px; }
        .form-container.active { display: block; }
        .form-container h2 { margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .form-group input[readonly] { background: #e9ecef; }
        .form-group button { padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        .form-group button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: left; }
        th { background: #007bff; color: #fff; }
        .tram-layout { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; padding: 20px; }
        .tram-layout-editor { margin-top: 20px; border: 1px solid #ccc; padding: 10px; background: #f9f9f9; border-radius: 4px; }
        .tram-layout-editor .seat-layout { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
        .seat { width: 40px; height: 40px; background: #007bff; color: #fff; border: 1px solid #ccc; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .seat.occupied { background: #dc3545; }
        .seat.selected { background: #ffc107; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center; }
        .modal-content { background: #fff; padding: 20px; border-radius: 5px; width: 300px; text-align: center; }
        .modal-content input { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="caderno">
        <div class="tabs">
            <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">
                <i class="fas fa-plus-circle"></i> Cadastrar Bonde
            </div>
            <div class="tab" data-tab="passageiros" onclick="showTab('passageiros')">
                <i class="fas fa-users"></i> Gerenciar Passageiros (Ida)
            </div>
            <div class="tab" data-tab="controle" onclick="showTab('controle')">
                <i class="fas fa-route"></i> Controle de Viagens
            </div>
            <div class="tab" data-tab="manutencao" onclick="showTab('manutencao')">
                <i class="fas fa-tools"></i> Manutenção
            </div>
            <div class="tab" data-tab="layout" onclick="showTab('layout')">
                <i class="fas fa-th"></i> Layout do Bonde
            </div>
            <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">
                <i class="fas fa-file-alt"></i> Relatórios
            </div>
        </div>

        <!-- Cadastrar Bonde -->
        <div class="form-container active" id="cadastrar">
            <h2>Cadastrar Bonde</h2>
            <form action="process_bonde.php" method="POST">
                <div class="form-group">
                    <label for="bonde_id">ID do Bonde</label>
                    <input type="text" id="bonde_id" name="bonde_id" required>
                </div>
                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" id="modelo" name="modelo" required>
                </div>
                <div class="form-group">
                    <label for="capacidade">Capacidade (passageiros)</label>
                    <input type="number" id="capacidade" name="capacidade" required>
                </div>
                <div class="form-group">
                    <label for="ano_fabricacao">Ano de Fabricação</label>
                    <input type="number" id="ano_fabricacao" name="ano_fabricacao" required>
                </div>
                <div class="form-group">
                    <button type="submit">Cadastrar</button>
                </div>
            </form>
        </div>

        <!-- Gerenciar Passageiros (Ida) -->
        <div class="form-container" id="passageiros">
            <h2>Gerenciar Passageiros (Ida)</h2>
            <form action="process_passageiros.php" method="POST" onsubmit="return validateIdaForm(event)">
                <div class="form-group">
                    <label for="bonde_id_pass">Selecione o Bonde</label>
                    <select id="bonde_id_pass" name="bonde_id_pass" required>
                        <option value="">Selecione</option>
                        <?php
                        $stmt = $pdo->query("SELECT id, modelo FROM bondes");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['id']} - {$row['modelo']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="viagem_id">ID da Viagem</label>
                    <input type="text" id="viagem_id" name="viagem_id" value="<?php echo $new_trip_id; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="origin">Origem</label>
                    <select id="origin" name="origin" required>
                        <option value="">Selecione</option>
                        <option value="Santa Teresa">Santa Teresa</option>
                        <option value="Lapa">Lapa</option>
                        <option value="Centro">Centro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="destination">Destino</label>
                    <select id="destination" name="destination" required>
                        <option value="">Selecione</option>
                        <option value="Lapa">Lapa</option>
                        <option value="Centro">Centro</option>
                        <option value="Gloria">Glória</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="motorman">Motorneiro</label>
                    <input type="text" id="motorman" name="motorman" required>
                </div>
                <div class="form-group">
                    <label for="auxiliary">Auxiliar</label>
                    <input type="text" id="auxiliary" name="auxiliary" required>
                </div>
                <div class="form-group">
                    <label for="validator">Quem Efetuou a Validação</label>
                    <input type="text" id="validator" name="validator" required>
                </div>
                <div class="form-group">
                    <label for="passageiros_ida">Passageiros (Ida)</label>
                    <input type="number" id="passageiros_ida" name="passageiros_ida" required min="0">
                </div>
                <div class="form-group">
                    <button type="submit">Registrar Ida</button>
                </div>
            </form>
        </div>

        <!-- Controle de Viagens -->
        <div class="form-container" id="controle">
            <h2>Controle de Viagens</h2>
            <p>Tempo médio das viagens: <?php echo $average_time; ?></p>
            <table id="tripsTable">
                <thead>
                    <tr>
                        <th>ID Viagem</th>
                        <th>Bonde</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Passageiros (Ida)</th>
                        <th>Passageiros (Volta)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($existing_trips as $viagem) {
                        $volta = isset($viagem['passageiros_volta']) ? $viagem['passageiros_volta'] : 'N/A';
                        $status = isset($viagem['passageiros_volta']) ? 'Concluída' : 'Pendente';
                        echo "<tr onclick='showModal(\"{$viagem['id']}\")'>
                            <td>{$viagem['id']}</td>
                            <td>{$viagem['bonde_id']} - {$viagem['modelo']}</td>
                            <td>{$viagem['origem']}</td>
                            <td>{$viagem['destino']}</td>
                            <td>{$viagem['passageiros_ida']}</td>
                            <td>$volta</td>
                            <td>$status</td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Manutenção -->
        <div class="form-container" id="manutencao">
            <h2>Gerenciar Manutenção</h2>
            <form action="process_manutencao.php" method="POST">
                <div class="form-group">
                    <label for="bonde_id_manut">Selecione o Bonde</label>
                    <select id="bonde_id_manut" name="bonde_id_manut" required>
                        <option value="">Selecione</option>
                        <?php
                        $stmt = $pdo->query("SELECT id, modelo FROM bondes");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['id']}'>{$row['id']} - {$row['modelo']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_manutencao">Data da Manutenção</label>
                    <input type="date" id="data_manutencao" name="data_manutencao" required>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" required></textarea>
                </div>
                <div class="form-group">
                    <button type="submit">Registrar</button>
                </div>
            </form>
        </div>

        <!-- Layout do Bonde -->
        <div class="form-container" id="layout">
            <h2>Layout do Bonde</h2>
            <div class="form-group">
                <label for="bonde_id_layout">Selecione o Bonde</label>
                <select id="bonde_id_layout" name="bonde_id_layout" onchange="loadLayout()">
                    <option value="">Selecione</option>
                    <?php
                    $stmt = $pdo->query("SELECT id, modelo FROM bondes");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}'>{$row['id']} - {$row['modelo']}</option>";
                    }
                    ?>
                </select>
            </div>
            <form action="process_layout.php" method="POST" id="layoutForm">
                <div class="form-group">
                    <label for="layout_name">Nome</label>
                    <input type="text" id="layout_name" name="layout_name" value="LAYOUT 46 EXECUTIVO" readonly>
                </div>
                <div class="form-group">
                    <label for="layout_type">Layout do Assento</label>
                    <select id="layout_type" name="layout_type">
                        <option value="EXECUTIVO">EXECUTIVO</option>
                        <option value="PADRAO">PADRÃO</option>
                    </select>
                    <input type="number" id="seat_count" name="seat_count" value="46" readonly style="width: 50px; display: inline-block; margin-left: 10px;">
                </div>
                <div class="form-group">
                    <label for="group">Grupo</label>
                    <input type="text" id="group" name="group" value="-----">
                </div>
                <div class="form-group">
                    <label for="cost_center">Centro de Custo</label>
                    <input type="text" id="cost_center" name="cost_center" value="-----">
                </div>
                <div class="form-group">
                    <button type="button" onclick="editLayout()">Editar Layout</button>
                    <button type="submit">Salvar</button>
                </div>
            </form>
            <div class="tram-layout" id="tram-layout">
                <!-- Layout dinâmico gerado por JS -->
            </div>
        </div>

        <!-- Relatórios -->
        <div class="form-container" id="relatorio">
            <h2>Relatórios</h2>
            <div class="form-group">
                <label for="tipo_relatorio">Tipo de Relatório</label>
                <select id="tipo_relatorio" name="tipo_relatorio">
                    <option value="passageiros">Passageiros por Viagem</option>
                    <option value="manutencao">Histórico de Manutenção</option>
                </select>
            </div>
            <div class="form-group">
                <button onclick="gerarRelatorio()">Gerar Relatório</button>
            </div>
            <div id="relatorio-resultado">
                <!-- Resultado do relatório será inserido aqui -->
            </div>
        </div>

        <!-- Modal for Volta Registration -->
        <div id="voltaModal" class="modal">
            <div class="modal-content">
                <h3>Registrar Volta</h3>
                <input type="number" id="volta_passengers" placeholder="Passageiros (Volta)" min="0" required>
                <button onclick="registerVolta()">Salvar</button>
                <button onclick="closeModal()">Cancelar</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.form-container').forEach(container => container.classList.remove('active'));
            document.querySelector(`.tab[data-tab="${tabId}"]`).classList.add('active');
            document.getElementById(tabId).classList.add('active');
            if (tabId === 'controle' && window.location.search.includes('tab=controle')) {
                loadTrips();
            }
        }

        function loadLayout() {
            const bondeId = document.getElementById('bonde_id_layout').value;
            const layoutDiv = document.getElementById('tram-layout');
            layoutDiv.innerHTML = '';
            if (bondeId) {
                for (let i = 1; i <= 46; i++) {
                    const seat = document.createElement('div');
                    seat.className = 'seat';
                    seat.textContent = i;
                    seat.onclick = () => toggleSeat(seat);
                    layoutDiv.appendChild(seat);
                }
            }
        }

        function toggleSeat(seat) {
            seat.classList.toggle('occupied');
        }

        function editLayout() {
            const layoutForm = document.getElementById('layoutForm');
            const inputs = layoutForm.querySelectorAll('input[readonly], select');
            inputs.forEach(input => input.removeAttribute('readonly'));
            document.getElementById('seat_count').style.display = 'none';
            const layoutDiv = document.getElementById('tram-layout');
            layoutDiv.classList.add('tram-layout-editor');
            layoutDiv.innerHTML = '<p>Inclua ajustes de layout na vertical para uma melhor visualização.</p><div class="seat-layout"></div>';
            for (let i = 1; i <= 46; i++) {
                const seat = document.createElement('div');
                seat.className = 'seat' + (i === 1 ? ' selected' : '');
                seat.textContent = i;
                seat.onclick = () => selectSeat(seat);
                layoutDiv.querySelector('.seat-layout').appendChild(seat);
            }
        }

        function selectSeat(seat) {
            document.querySelectorAll('.seat').forEach(s => s.classList.remove('selected'));
            seat.classList.add('selected');
        }

        function gerarRelatorio() {
            const tipo = document.getElementById('tipo_relatorio').value;
            const resultadoDiv = document.getElementById('relatorio-resultado');
            if (tipo === 'passageiros') {
                resultadoDiv.innerHTML = '<h3>Relatório de Passageiros</h3><p>Total de passageiros (ida): 55<br>Total de passageiros (volta): 48</p>';
            } else {
                resultadoDiv.innerHTML = '<h3>Relatório de Manutenção</h3><p>Manutenções realizadas: 3<br>Última manutenção: 2025-06-10</p>';
            }
        }

        function validateIdaForm(event) {
            event.preventDefault();
            const bonde = document.getElementById('bonde_id_pass').value;
            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;
            const motorman = document.getElementById('motorman').value.trim();
            const auxiliary = document.getElementById('auxiliary').value.trim();
            const validator = document.getElementById('validator').value.trim();
            const ida = document.getElementById('passageiros_ida').value;

            if (!bonde || !origin || !destination || !motorman || !auxiliary || !validator || !ida) {
                alert('Todos os campos são obrigatórios.');
                return false;
            }
            if (ida < 0) {
                alert('O número de passageiros não pode ser negativo.');
                return false;
            }
            const form = event.target;
            form.submit();
            return true;
        }

        function showModal(tripId) {
            const modal = document.getElementById('voltaModal');
            modal.style.display = 'flex';
            modal.dataset.tripId = tripId;
        }

        function closeModal() {
            const modal = document.getElementById('voltaModal');
            modal.style.display = 'none';
            document.getElementById('volta_passengers').value = '';
        }

        function registerVolta() {
            const tripId = document.getElementById('voltaModal').dataset.tripId;
            const volta = document.getElementById('volta_passengers').value;
            if (volta && volta >= 0) {
                $.post('process_volta.php', { trip_id: tripId, volta: volta }, function(response) {
                    if (response.success) {
                        loadTrips();
                        closeModal();
                    } else {
                        alert('Erro ao registrar volta.');
                    }
                }, 'json').fail(function() {
                    alert('Erro na conexão com o servidor.');
                });
            } else {
                alert('Insira um número válido de passageiros.');
            }
        }

        function loadTrips() {
            $.get('homebonde.php', { action: 'get_trips' }, function(data) {
                const tbody = $('#tripsTable tbody');
                tbody.empty();
                data.forEach(trip => {
                    const volta = trip.passageiros_volta !== null ? trip.passageiros_volta : 'N/A';
                    const status = trip.passageiros_volta !== null ? 'Concluída' : 'Pendente';
                    tbody.append(`<tr onclick='showModal("${trip.id}")'>
                        <td>${trip.id}</td>
                        <td>${trip.bonde_id} - ${trip.modelo}</td>
                        <td>${trip.origem}</td>
                        <td>${trip.destino}</td>
                        <td>${trip.passageiros_ida}</td>
                        <td>${volta}</td>
                        <td>${status}</td>
                    </tr>`);
                });
            }, 'json');
        }

        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab')) {
                showTab(urlParams.get('tab'));
            }
            if ($('#controle').hasClass('active')) {
                loadTrips();
            }
        });
    </script>
</body>
</html>