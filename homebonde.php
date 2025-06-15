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

function generateTripId() {
    return 'V-' . strtoupper(uniqid());
}

$new_trip_id = generateTripId();

// Fetch existing trips
$stmt_trips = $pdo->query("SELECT v.id, v.bonde_id, v.origem, v.destino, v.passageiros_ida, v.passageiros_volta, v.data_ida, v.data_volta, b.modelo 
                    FROM viagens v 
                    LEFT JOIN bondes b ON v.bonde_id = b.id");
$existing_trips = $stmt_trips->fetchAll(PDO::FETCH_ASSOC);

// Fetch bondes for dropdowns and layout section
$stmt_bondes = $pdo->query("SELECT id, modelo, descricao, capacidade FROM bondes");
$bondes = $stmt_bondes->fetchAll(PDO::FETCH_ASSOC);

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

// Search functionality for Layout tab
$search_query = isset($_GET['search']) ? strtolower($_GET['search']) : '';
$filtered_bondes = $search_query ? array_filter($bondes, fn($bonde) => strpos(strtolower($bonde['modelo']), $search_query) !== false) : $bondes;

if (isset($_GET['action']) && $_GET['action'] === 'get_trips') {
    echo json_encode($existing_trips);
    exit();
}
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
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center; }
        .modal-content { background: #fff; padding: 20px; border-radius: 5px; width: 300px; text-align: center; }
        .modal-content input, .modal-content select, .modal-content textarea { margin-bottom: 10px; width: 100%; padding: 8px; }
        .photo-upload { width: 300px; padding: 20px; border: 1px dashed #ccc; text-align: center; margin-left: 20px; vertical-align: top; display: inline-block; }
        .photo-upload img { max-width: 100%; height: auto; margin-top: 10px; }
        textarea { resize: vertical; min-height: 60px; }
    </style>
</head>
<body>
    <div class="caderno">
        <div class="tabs">
            <div class="tab" data-tab="cadastrar" onclick="showTab('cadastrar')">
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
            <div class="tab active" data-tab="layout" onclick="showTab('layout')">
                <i class="fas fa-th"></i> Layout do Bonde
            </div>
            <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">
                <i class="fas fa-file-alt"></i> Relatórios
            </div>
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
                    <input type="number" id="capacidade" name="capacidade" required>
                </div>
                <div class="form-group">
                    <label for="ano_fabricacao">Ano de Fabricação</label>
                    <input type="number" id="ano_fabricacao" name="ano_fabricacao" required>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" required></textarea>
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
                    <?php foreach ($existing_trips as $viagem): ?>
                        <?php $bonde_id = 'B' . $viagem['bonde_id']; ?>
                        <?php $volta = isset($viagem['passageiros_volta']) ? $viagem['passageiros_volta'] : 'N/A'; ?>
                        <?php $status = isset($viagem['passageiros_volta']) ? 'Concluída' : 'Pendente'; ?>
                        <tr onclick="showModal('<?php echo $viagem['id']; ?>')">
                            <td><?php echo $viagem['id']; ?></td>
                            <td><?php echo $bonde_id . ' - ' . $viagem['modelo']; ?></td>
                            <td><?php echo $viagem['origem']; ?></td>
                            <td><?php echo $viagem['destino']; ?></td>
                            <td><?php echo $viagem['passageiros_ida']; ?></td>
                            <td><?php echo $volta; ?></td>
                            <td><?php echo $status; ?></td>
                        </tr>
                    <?php endforeach; ?>
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

        <!-- Layout do Bonde -->
        <div class="form-container active" id="layout">
            <h2>Layout do Bonde</h2>
            <div class="form-group">
                <input type="text" id="searchBonde" placeholder="Pesquisar por modelo..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" onkeyup="searchBondes()">
            </div>
            <table id="bondesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Modelo</th>
                        <th>Descrição</th>
                        <th>Capacidade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filtered_bondes as $bonde): ?>
                        <tr onclick="editBonde(<?php echo $bonde['id']; ?>, '<?php echo addslashes($bonde['modelo']); ?>', '<?php echo addslashes($bonde['descricao'] ?? ''); ?>', <?php echo $bonde['capacidade']; ?>)">
                            <td>B<?php echo $bonde['id']; ?></td>
                            <td><?php echo htmlspecialchars($bonde['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($bonde['descricao'] ?? 'Sem descrição'); ?></td>
                            <td><?php echo $bonde['capacidade']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p>Total de registros encontrados: <?php echo count($filtered_bondes); ?></p>

            <!-- Modal for Editing Bonde -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <h3>Editar Bonde</h3>
                    <input type="hidden" id="editBondeId">
                    <div class="form-group">
                        <label for="editModelo">Modelo</label>
                        <input type="text" id="editModelo" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescricao">Descrição</label>
                        <textarea id="editDescricao" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editCapacidade">Capacidade</label>
                        <select id="editCapacidade" required>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="32">32</option>
                            <option value="36">36</option>
                        </select>
                    </div>
                    <button onclick="saveBonde()">Salvar</button>
                    <button onclick="closeModal()">Cancelar</button>
                </div>
            </div>

            <!-- Photo Upload Section -->
            <div class="photo-upload">
                <h3>Foto do Layout</h3>
                <input type="file" id="layoutPhoto" accept="image/*">
                <img id="previewPhoto" src="" alt="Pré-visualização da foto">
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

        function searchBondes() {
            const query = $('#searchBonde').val();
            window.location.href = `homebonde.php?tab=layout&search=${encodeURIComponent(query)}`;
        }

        function editBonde(id, modelo, descricao, capacidade) {
            $('#editBondeId').val(id);
            $('#editModelo').val(modelo);
            $('#editDescricao').val(descricao);
            $('#editCapacidade').val(capacidade);
            $('#editModal').css('display', 'flex');

            // Load or clear photo preview
            const photoInput = $('#layoutPhoto')[0];
            if (photoInput.files && photoInput.files.length > 0) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewPhoto').attr('src', e.target.result);
                };
                reader.readAsDataURL(photoInput.files[0]);
            } else {
                $('#previewPhoto').attr('src', ''); // Clear if no file
            }
        }

        function saveBonde() {
            const id = $('#editBondeId').val();
            const modelo = $('#editModelo').val();
            const descricao = $('#editDescricao').val();
            const capacidade = $('#editCapacidade').val();

            $.post('update_bonde.php', { id: id, modelo: modelo, descricao: descricao, capacidade: capacidade }, function(response) {
                if (response.success) {
                    location.reload(); // Reload to reflect changes
                } else {
                    alert('Erro ao atualizar bonde.');
                }
            }, 'json').fail(function() {
                alert('Erro na conexão com o servidor.');
            });
        }

        function closeModal() {
            $('#editModal, #voltaModal').css('display', 'none');
            $('#editModelo').val('');
            $('#editDescricao').val('');
            $('#editCapacidade').val('');
            $('#editBondeId').val('');
            $('#volta_passengers').val('');
        }

        $('#layoutPhoto').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewPhoto').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        function registerVolta() {
            const tripId = $('#voltaModal').data('tripId');
            const volta = $('#volta_passengers').val();
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
                    const bonde_id = 'B' + trip.bonde_id;
                    const volta = trip.passageiros_volta !== null ? trip.passageiros_volta : 'N/A';
                    const status = trip.passageiros_volta !== null ? 'Concluída' : 'Pendente';
                    tbody.append(`<tr onclick="showModal('${trip.id}')">
                        <td>${trip.id}</td>
                        <td>${bonde_id} - ${trip.modelo}</td>
                        <td>${trip.origem}</td>
                        <td>${trip.destino}</td>
                        <td>${trip.passageiros_ida}</td>
                        <td>${volta}</td>
                        <td>${status}</td>
                    </tr>`);
                });
            }, 'json');
        }

        function showModal(tripId) {
            $('#voltaModal').data('tripId', tripId).css('display', 'flex');
        }

        function gerarRelatorio() {
            const tipo = $('#tipo_relatorio').val();
            const resultadoDiv = $('#relatorio-resultado');
            if (tipo === 'passageiros') {
                resultadoDiv.html('<h3>Relatório de Passageiros</h3><p>Total de passageiros (ida): 55<br>Total de passageiros (volta): 48<br>Data: 13/06/2025 18:22</p>');
            } else {
                resultadoDiv.html('<h3>Relatório de Manutenção</h3><p>Manutenções realizadas: 3<br>Última manutenção: 10/06/2025<br>Data: 13/06/2025 18:22</p>');
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
            if (ida < 0) {
                alert('O número de passageiros não pode ser negativo.');
                return false;
            }
            $(event.target).submit();
            return true;
        }

        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab')) {
                showTab(urlParams.get('tab'));
            } else {
                showTab('layout');
            }
            if ($('#controle').hasClass('active')) {
                loadTrips();
            }
        });
    </script>
</body>
</html>