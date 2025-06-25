<?php
// Include database configuration with error handling
try {
    include 'bancoo.php';
    if (!isset($pdo) || !$pdo) {
        throw new Exception("Falha na conexão com o banco de dados.");
    }
} catch (Exception $e) {
    // Se for uma requisição AJAX, retornar erro como JSON
    if (isset($_GET['action']) && $_GET['action'] === 'get_trips') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
    die("Erro: " . $e->getMessage());
}

function generateTripId() {
    return 'V-' . strtoupper(uniqid());
}

$new_trip_id = generateTripId();

// Handle AJAX request for trips
if (isset($_GET['action']) && $_GET['action'] === 'get_trips') {
    header('Content-Type: application/json');
    $stmt_trips = $pdo->query("SELECT v.id, v.bonde_id, v.origem, v.destino, v.passageiros_ida, v.passageiros_volta, v.data_ida, v.data_volta, b.modelo 
                        FROM viagens v 
                        LEFT JOIN bondes b ON v.bonde_id = b.id");
    if ($stmt_trips === false) {
        error_log("Erro na consulta de viagens: " . print_r($pdo->errorInfo(), true));
        echo json_encode(['success' => false, 'message' => 'Erro ao carregar viagens.']);
    } else {
        $existing_trips = $stmt_trips->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $existing_trips]);
    }
    exit();
}

// Fetch existing trips for initial render
$stmt_trips = $pdo->query("SELECT v.id, v.bonde_id, v.origem, v.destino, v.passageiros_ida, v.passageiros_volta, v.data_ida, v.data_volta, b.modelo 
                    FROM viagens v 
                    LEFT JOIN bondes b ON v.bonde_id = b.id");
if ($stmt_trips === false) {
    error_log("Erro na consulta inicial de viagens: " . print_r($pdo->errorInfo(), true));
    $existing_trips = [];
} else {
    $existing_trips = $stmt_trips->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch bondes for dropdowns and layout section
$stmt_bondes = $pdo->query("SELECT id, modelo, descricao, capacidade FROM bondes");
if ($stmt_bondes === false) {
    error_log("Erro na consulta de bondes: " . print_r($pdo->errorInfo(), true));
    $bondes = [];
} else {
    $bondes = $stmt_bondes->fetchAll(PDO::FETCH_ASSOC);
}

// Calculate average travel time
function calculateAverageTravelTime($trips) {
    $total_duration = 0;
    $count = 0;
    foreach ($trips as $trip) {
        if (isset($trip['data_ida']) && $trip['data_ida'] && isset($trip['data_volta']) && $trip['data_volta']) {
            $start = new DateTime($trip['data_ida']);
            $end = new DateTime($trip['data_volta']);
            $duration = $end->getTimestamp() - $start->getTimestamp();
            if ($duration > 0) {
                $total_duration += $duration;
                $count++;
            }
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

include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Bondes - Santa Teresa</title>
    <link rel="stylesheet" href="src/bonde/style/bonde.css">
    <link rel="stylesheet" href="src/style/tabs.css">
 
</head>
<body>
    <div class="caderno">
        <div class="tabs">
            <div class="tab" data-tab="cadastrar" onclick="showTab('cadastrar')">Cadastrar Bonde</div>
            <div class="tab" data-tab="passageiros" onclick="showTab('passageiros')">Gerenciar Passageiros (Ida)</div>
            <div class="tab" data-tab="controle" onclick="showTab('controle')">Controle de Viagens</div>
            <div class="tab" data-tab="manutencao" onclick="showTab('manutencao')">Manutenção</div>
            <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">Relatórios</div>
        </div>

        <!-- Cadastrar Bonde -->
        <div class="form-container" id="cadastrar">
            <h2>Cadastrar Bonde</h2>
            <form action="process_bonde.php" method="POST">
                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" id="modelo" name="modelo" required>
                </div>
                <div class="form-group">
                    <label for="capacidade">Capacidade (passageiros)</label>
                    <input type="number" id="capacidade" name="capacidade" required min="1">
                </div>
                <div class="form-group">
                    <label for="ano_fabricacao">Ano de Fabricação</label>
                    <input type="number" id="ano_fabricacao" name="ano_fabricacao" required min="1900" max="<?php echo date('Y'); ?>">
                </div>
                <div class="form-group">
                    <label for="descricao-cadastro-bonde">Descrição</label>
                    <textarea id="descricao-cadastro-bonde" name="descricao-cadastro-bonde" required></textarea>
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
                        <?php foreach ($bondes as $bonde): ?>
                            <option value="<?php echo $bonde['id']; ?>">B<?php echo $bonde['id']; ?> - <?php echo htmlspecialchars($bonde['modelo']); ?></option>
                        <?php endforeach; ?>
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
            <p>Tempo médio das viagens: <?php echo htmlspecialchars($average_time); ?></p>
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
                    if (empty($existing_trips)) {
                        echo "<tr><td colspan='7'>Nenhuma viagem registrada. Verifique se há dados na tabela 'viagens'.</td></tr>";
                    } else {
                        error_log("Renderizando " . count($existing_trips) . " viagens."); // Depuração
                        foreach ($existing_trips as $viagem) {
                            $bonde_id = 'B' . ($viagem['bonde_id'] ?? 'N/A');
                            $volta = $viagem['passageiros_volta'] !== null ? $viagem['passageiros_volta'] : 'N/A';
                            $status = $viagem['passageiros_volta'] !== null ? 'Concluída' : 'Pendente';
                            echo "<tr onclick=\"showModal('" . htmlspecialchars($viagem['id']) . "')\">";
                            echo "<td>" . htmlspecialchars($viagem['id'] ?? 'N/A') . "</td>";
                            echo "<td>" . $bonde_id . ' - ' . htmlspecialchars($viagem['modelo'] ?? 'Desconhecido') . "</td>";
                            echo "<td>" . htmlspecialchars($viagem['origem'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($viagem['destino'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($viagem['passageiros_ida'] ?? '0') . "</td>";
                            echo "<td>" . $volta . "</td>";
                            echo "<td>" . $status . "</td>";
                            echo "</tr>";
                        }
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
                        <?php foreach ($bondes as $bonde): ?>
                            <option value="<?php echo $bonde['id']; ?>">B<?php echo $bonde['id']; ?> - <?php echo htmlspecialchars($bonde['modelo']); ?></option>
                        <?php endforeach; ?>
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
            <div id="relatorio-resultado"></div>
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
            const tab = document.querySelector(`.tab[data-tab="${tabId}"]`);
            const container = document.getElementById(tabId);
            if (!tab || !container) {
                console.error(`Tab or container with ID ${tabId} not found`);
                return;
            }
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.form-container').forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            container.classList.add('active');
            if (tabId === 'controle') {
                loadTrips();
            }
        }

        function registerVolta() {
            const tripId = $('#voltaModal').data('tripId');
            const volta = $('#volta_passengers').val();
            if (volta && parseInt(volta) >= 0) {
                $.post('process_volta.php', { trip_id: tripId, volta: volta }, function(response) {
                    if (response && response.success) {
                        loadTrips();
                        closeModal();
                    } else {
                        alert('Erro ao registrar volta: ' + (response.message || ''));
                    }
                }, 'json').fail(function(xhr, status, error) {
                    alert('Erro na conexão com o servidor: ' + error);
                });
            } else {
                alert('Insira um número válido de passageiros.');
            }
        }

        function loadTrips() {
            $.get('homebonde.php', { action: 'get_trips' }, function(data) {
                const tbody = $('#tripsTable tbody');
                tbody.empty();
                console.log('Dados recebidos da AJAX:', data); // Depuração
                if (data && data.success && Array.isArray(data.data) && data.data.length > 0) {
                    data.data.forEach(trip => {
                        const bonde_id = 'B' + (trip.bonde_id || 'N/A');
                        const volta = trip.passageiros_volta !== null ? trip.passageiros_volta : 'N/A';
                        const status = trip.passageiros_volta !== null ? 'Concluída' : 'Pendente';
                        tbody.append(`<tr onclick="showModal('${htmlspecialchars(trip.id)}')">
                            <td>${htmlspecialchars(trip.id || 'N/A')}</td>
                            <td>${bonde_id} - ${htmlspecialchars(trip.modelo || 'Desconhecido')}</td>
                            <td>${htmlspecialchars(trip.origem || 'N/A')}</td>
                            <td>${htmlspecialchars(trip.destino || 'N/A')}</td>
                            <td>${htmlspecialchars(trip.passageiros_ida || '0')}</td>
                            <td>${volta}</td>
                            <td>${status}</td>
                        </tr>`);
                    });
                } else {
                    tbody.append('<tr><td colspan="7">Nenhuma viagem registrada ou erro ao carregar dados.</td></tr>');
                    console.log('Dados inválidos ou ausentes:', data);
                }
            }, 'json').fail(function(xhr, status, error) {
                console.error('Erro na requisição AJAX:', error, 'Resposta:', xhr.responseText);
                $('#tripsTable tbody').html('<tr><td colspan="7">Erro ao carregar viagens. Verifique a conexão ou o servidor.</td></tr>');
            });
        }

        function showModal(tripId) {
            $('#voltaModal').data('tripId', tripId).css('display', 'flex');
        }

        function closeModal() {
            $('.modal').css('display', 'none');
            $('#volta_passengers').val('');
        }

        function gerarRelatorio() {
            const tipo = $('#tipo_relatorio').val();
            const resultadoDiv = $('#relatorio-resultado');
            if (tipo === 'passageiros') {
                resultadoDiv.html('<h3>Relatório de Passageiros</h3><p>Total de passageiros (ida): ' + (<?php echo array_sum(array_column($existing_trips, 'passageiros_ida')); ?> || 0) + '<br>Total de passageiros (volta): ' + (<?php echo array_sum(array_column($existing_trips, 'passageiros_volta')); ?> || 0) + '<br>Data: <?php echo date('d/m/Y H:i'); ?></p>');
            } else if (tipo === 'manutencao') {
                resultadoDiv.html('<h3>Relatório de Manutenção</h3><p>Manutenções realizadas: 3<br>Última manutenção: 10/06/2025<br>Data: <?php echo date('d/m/Y H:i'); ?></p>');
            }
        }

        function validateIdaForm(event) {
            event.preventDefault();
            const bonde = $('#bonde_id_pass').val();
            const origin = $('#origin').val();
            const destination = $('#destination').val();
            const motorman = $('#motorman').val().trim();
            const auxiliary = $('#auxiliary').val().trim();
            const validator = $('#validator').val().trim();
            const ida = $('#passageiros_ida').val();

            if (!bonde || !origin || !destination || !motorman || !auxiliary || !validator || !ida) {
                alert('Todos os campos são obrigatórios.');
                return false;
            }
            if (parseInt(ida) < 0) {
                alert('O número de passageiros não pode ser negativo.');
                return false;
            }
            event.target.submit();
            return true;
        }

        function htmlspecialchars(str) {
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab') || 'passageiros';
            showTab(tab);
        });
    </script>
    <?php $pdo = null; // Fecha a conexão ?>
</body>
</html>