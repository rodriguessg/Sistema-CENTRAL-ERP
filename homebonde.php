<?php
include 'header.php';

// Database configuration
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch all bondes from the 'bondes' table
    $stmt = $pdo->query("SELECT id, modelo, capacidade, ativo, ano_fabricacao, descricao FROM bondes ORDER BY modelo ASC");
    $bondes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $bondes = [
        ['id' => 1, 'modelo' => 'BONDE 17', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2010, 'descricao' => 'Bonde padrão'],
        ['id' => 2, 'modelo' => 'BONDE 16', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2009, 'descricao' => 'Bonde clássico'],
        ['id' => 3, 'modelo' => 'BONDE 19', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2011, 'descricao' => 'Bonde renovado'],
        ['id' => 4, 'modelo' => 'BONDE 22', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2013, 'descricao' => 'Bonde moderno'],
        ['id' => 5, 'modelo' => 'BONDE 18', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2010, 'descricao' => 'Bonde intermediário'],
        ['id' => 6, 'modelo' => 'BONDE 20', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2012, 'descricao' => 'Bonde atualizado']
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Viagens - Bondes Santa Teresa</title>
    <link rel="stylesheet" href="./src/bonde/style/bonde.css">
    <style>
        .container {
            display: flex;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-container {
            flex: 2;
            margin-right: 20px;
        }
        .bondes-container {
            flex: 1;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .bonde-item {
            margin: 10px 0;
        }
        .bonde-item input[type="checkbox"] {
            margin-right: 5px;
        }
        .header-section .section-title {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .input-item {
            flex: 1;
        }
        .input-item label {
            display: block;
            margin-bottom: 5px;
        }
        .input-item select, .input-item input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .buttons-section {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .buttons-section button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .buttons-section button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .counts-section {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .total-box {
            flex: 1;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .total-box .section-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .total-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .table-section {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #1e3a8a;
            color: white;
        }
        .progress-container {
            display: flex;
            flex-direction: column;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            background-color: #4CAF50;
            text-align: center;
            color: white;
            transition: width 0.3s;
        }
        /* Estilo do Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            width: 400px;
            max-width: 90%;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .modal-header h3 {
            margin: 0;
        }
        .close-modal {
            cursor: pointer;
            font-size: 20px;
        }
        .modal-form .input-group {
            margin-bottom: 15px;
        }
        .modal-form .input-group label {
            display: block;
            margin-bottom: 5px;
        }
        .modal-form .input-group input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .modal-actions button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container" id="controle-viagem">
            <form id="viagem-form" method="POST" action="add_viagem.php">
                <div class="header-section">
                    <div>
                        <div class="section-title">CADASTRAMENTO DE TRANSAÇÕES</div>
                        <div class="input-group">
                            <div class="input-item">
                                <label for="bonde">BONDE</label>
                                <select id="bonde" name="bonde" required>
                                    <option value="">Selecione</option>
                                    <?php
                                    $activeBondes = false;
                                    foreach ($bondes as $bonde) {
                                        if ($bonde['ativo'] == 1) {
                                            echo "<option value=\"{$bonde['modelo']}\">{$bonde['modelo']}</option>";
                                            $activeBondes = true;
                                        }
                                    }
                                    if (!$activeBondes) {
                                        echo "<option value=\"\" disabled>Nenhum bonde ativo</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-item">
                                <label for="saida">SAÍDA</label>
                                <select id="saida" name="saida" required>
                                    <option value="Carioca">Carioca</option>
                                    <option value="D.Irmãos">D.Irmãos</option>
                                    <option value="Paula Mattos">Paula Mattos</option>
                                    <option value="Silvestre">Silvestre</option>
                                </select>
                            </div>
                            <div class="input-item">
                                <label for="retorno">RETORNO</label>
                                <select id="retorno" name="retorno">
                                    <option value="">Selecione (para retorno)</option>
                                    <option value="Carioca">Carioca</option>
                                    <option value="D.Irmãos">D.Irmãos</option>
                                    <option value="Paula Mattos">Paula Mattos</option>
                                    <option value="Silvestre">Silvestre</option>
                                    <option value="Oficina">Oficina</option>
                                </select>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-item">
                                <label for="maquinistas">MAQUINISTAS</label>
                                <select id="maquinistas" name="maquinistas" required>
                                    <option value="">Selecione</option>
                                    <option value="Sergio Lima">Sergio Lima</option>
                                    <option value="Adriano">Adriano</option>
                                    <option value="Helio">Helio</option>
                                    <option value="M. Celestino">M. Celestino</option>
                                    <option value="Leonardo">Leonardo</option>
                                    <option value="Andre">Andre</option>
                                </select>
                            </div>
                            <div class="input-item">
                                <label for="agentes">AGENTES</label>
                                <select id="agentes" name="agentes" required>
                                    <option value="">Selecione</option>
                                    <option value="Samir">Samir</option>
                                    <option value="Vinicius">Vinicius</option>
                                    <option value="P. Nascimento">P. Nascimento</option>
                                    <option value="Oliveira">Oliveira</option>
                                    <option value="Carlos">Carlos</option>
                                </select>
                            </div>
                            <div class="input-item">
                                <label for="hora">HORA</label>
                                <input type="text" id="hora" name="hora" value="00:00:00" readonly>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-item">
                                <label for="pagantes">PAGANTES</label>
                                <input type="number" id="pagantes" name="pagantes" value="0" min="0" required>
                            </div>
                            <div class="input-item">
                                <label for="moradores">MORADORES</label>
                                <input type="number" id="moradores" name="moradores" value="0" min="0" required>
                            </div>
                            <div class="input-item">
                                <label for="grat_pcd_idoso">GRAT. PCD/IDOSO</label>
                                <input type="number" id="grat_pcd_idoso" name="grat_pcd_idoso" value="0" min="0" required>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-item">
                                <label for="gratuidade">GRATUIDADE</label>
                                <input type="number" id="gratuidade" name="gratuidade" value="0" readonly>
                            </div>
                            <div class="input-item">
                                <label for="passageiros">PASSAGEIROS</label>
                                <input type="number" id="passageiros" name="passageiros" value="0" readonly>
                            </div>
                            <div class="input-item">
                                <label for="viagem">VIAGEM</label>
                                <input type="number" id="viagem" name="viagem" value="1" min="1" required>
                            </div>
                        </div>
                        <div class="input-group">
                            <div class="input-item">
                                <label for="data">DATA</label>
                                <input type="date" id="data" name="data" required>
                            </div>
                            <div class="input-item progress-container">
                                <label>CAPACIDADE DO BONDE (Máx. 32 Passageiros)</label>
                                <div class="progress-bar">
                                    <div class="progress-bar-fill" id="progress-bar-fill">0%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="buttons-section">
                    <button type="submit" id="add-btn">Adicionar</button>
                    <button type="button" id="clear-form-btn">Cancelar</button>
                    <button type="button" id="delete-btn" disabled>Excluir</button>
                    <button type="button" id="alter-btn" disabled>Alterar</button>
                    <button type="button" id="return-btn" style="display: none;">Registrar Retorno</button>
                    <button type="button" id="clear-transactions-btn">Limpar Transações</button>
                    <button type="button" id="add-bonde-btn">Adicionar Bonde</button>
                    <div class="id-input-container">
                        <label for="id-filter">ID:</label>
                        <input type="text" id="id-filter" placeholder="Filtrar por ID">
                    </div>
                </div>
            </form>
            <div class="counts-section">
                <div class="total-box">
                    <div class="section-title">TOTAL BONDES SUBINDO</div>
                    <div class="total-item"><span>Pagantes</span><span id="total-subindo-pagantes">0</span></div>
                    <div class="total-item"><span>Gratuitos</span><span id="total-subindo-gratuitos">0</span></div>
                    <div class="total-item"><span>Moradores</span><span id="total-subindo-moradores">0</span></div>
                    <div class="total-item"><span>Passageiros</span><span id="total-subindo-passageiros">0</span></div>
                    <div class="total-item"><span>Bondes Saída</span><span id="total-bondes-saida">0</span></div>
                </div>
                <div class="total-box">
                    <div class="section-title">TOTAL BONDES RETORNO</div>
                    <div class="total-item"><span>Pagantes</span><span id="total-retorno-pagantes">0</span></div>
                    <div class="total-item"><span>Gratuitos</span><span id="total-retorno-gratuitos">0</span></div>
                    <div class="total-item"><span>Moradores</span><span id="total-retorno-moradores">0</span></div>
                    <div class="total-item"><span>Passageiros</span><span id="total-retorno-passageiros">0</span></div>
                    <div class="total-item"><span>Bondes Retorno</span><span id="total-bondes-retorno">0</span></div>
                </div>
            </div>
            <div class="table-section">
                <table>
                    <thead>
                        <tr>
                            <th>ID-M</th>
                            <th>Bondes</th>
                            <th>Saída</th>
                            <th>Retorno</th>
                            <th>Maquinista</th>
                            <th>Agente</th>
                            <th>Hora</th>
                            <th>Pagantes</th>
                            <th>Gratuidade</th>
                            <th>Moradores</th>
                            <th>Passageiros</th>
                            <th>Tipo Viagem</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody id="transactions-table-body">
                        <!-- Data will be populated here by JavaScript -->
                    </tbody>
                </table>
                <div class="pagination">
                    <button id="prev-page" disabled>Anterior</button>
                    <span id="page-info"></span>
                    <button id="next-page">Próximo</button>
                </div>
            </div>
        </div>
        <div class="bondes-container">
            <h3>Bondes Ativos</h3>
            <p><em>Cadastre aqui os bondes que estão operacionais na atual data: <?php date_default_timezone_set('America/Sao_Paulo'); echo date('H:i d/m/Y'); ?></em></p>
            <?php foreach ($bondes as $bonde): ?>
                <div class="bonde-item">
                    <input type="checkbox" id="bonde_<?php echo $bonde['id']; ?>" 
                           data-id="<?php echo $bonde['id']; ?>" 
                           data-modelo="<?php echo htmlspecialchars($bonde['modelo']); ?>" 
                           <?php echo $bonde['ativo'] ? 'checked' : ''; ?> 
                           onchange="updateBondeStatus(this)">
                    <label for="bonde_<?php echo $bonde['id']; ?>">
                        <?php echo htmlspecialchars($bonde['modelo']); ?> (Cap: <?php echo $bonde['capacidade']; ?>)
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal para Adicionar Bonde -->
    <div id="add-bonde-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Adicionar Novo Bonde</h3>
                <span class="close-modal" onclick="closeAddBondeModal()">&times;</span>
            </div>
            <form id="add-bonde-form" method="POST" action="add_bonde.php">
                <div class="modal-form">
                    <div class="input-group">
                        <label for="modelo">Modelo</label>
                        <input type="text" id="modelo" name="modelo" required>
                    </div>
                    <div class="input-group">
                        <label for="capacidade">Capacidade</label>
                        <input type="number" id="capacidade" name="capacidade" min="1" required>
                    </div>
                    <div class="input-group">
                        <label for="ano_fabricacao">Ano de Fabricação</label>
                        <input type="number" id="ano_fabricacao" name="ano_fabricacao" min="1900" max="<?php echo date('Y'); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="descricao">Descrição</label>
                        <input type="text" id="descricao" name="descricao" required>
                    </div>
                    <div class="input-group">
                        <label for="ativo">Ativo</label>
                        <input type="checkbox" id="ativo" name="ativo" value="1">
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="submit">Salvar</button>
                    <button type="button" onclick="closeAddBondeModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="./src/bonde/js/bonde.js"></script>
    <script>
        function updateBondeStatus(checkbox) {
            const bondeId = checkbox.getAttribute('data-id');
            const modelo = checkbox.getAttribute('data-modelo');
            const ativo = checkbox.checked ? 1 : 0;
            const url = new URL('/Sistema-CENTRAL-ERP/update_bonde_status.php', window.location.origin);

            fetch(url.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: bondeId,
                    ativo: ativo
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateSelectOptions();
                } else {
                    alert('Erro ao atualizar status do bonde: ' + data.message);
                    checkbox.checked = !checkbox.checked;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro na conexão com o servidor.');
                checkbox.checked = !checkbox.checked;
            });
        }

        function updateSelectOptions() {
            const select = document.getElementById('bonde');
            const originalValue = select.value;

            fetch('/Sistema-CENTRAL-ERP/get_active_bondes.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro ao carregar bondes: ' + response.status);
                return response.json();
            })
            .then(data => {
                select.innerHTML = '<option value="">Selecione</option>';

                let hasActiveBondes = false;
                data.forEach(bonde => {
                    if (bonde.ativo == 1) {
                        const option = document.createElement('option');
                        option.value = bonde.modelo;
                        option.textContent = bonde.modelo;
                        select.appendChild(option);
                        hasActiveBondes = true;
                    }
                });

                if (!hasActiveBondes) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Nenhum bonde ativo';
                    option.disabled = true;
                    select.appendChild(option);
                }

                if (originalValue && data.some(bonde => bonde.modelo === originalValue && bonde.ativo == 1)) {
                    select.value = originalValue;
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar opções do select:', error);
                alert('Erro ao carregar a lista de bondes.');
            });
        }

        // Função para calcular e atualizar a barra de progresso
        function updateProgressBar() {
            const pagantes = parseInt(document.getElementById('pagantes').value) || 0;
            const moradores = parseInt(document.getElementById('moradores').value) || 0;
            const gratPcdIdoso = parseInt(document.getElementById('grat_pcd_idoso').value) || 0;
            const totalPassageiros = pagantes + moradores + gratPcdIdoso;
            const maxCapacity = 32;
            const percentage = (totalPassageiros / maxCapacity) * 100;
            const progressFill = document.getElementById('progress-bar-fill');
            progressFill.style.width = Math.min(percentage, 100) + '%';
            progressFill.textContent = Math.min(percentage, 100).toFixed(0) + '%';

            document.getElementById('gratuidade').value = gratPcdIdoso;
            document.getElementById('passageiros').value = totalPassageiros;
        }

        // Adiciona eventos para atualização em tempo real
        ['pagantes', 'moradores', 'grat_pcd_idoso'].forEach(id => {
            document.getElementById(id).addEventListener('input', updateProgressBar);
        });

        // Inicializa a barra de progresso
        updateProgressBar();

        // Inicializa as opções do <select>
        updateSelectOptions();

        // Funções para controlar o modal de adicionar bonde
        const addBondeModal = document.getElementById('add-bonde-modal');
        const addBondeBtn = document.getElementById('add-bonde-btn');

        addBondeBtn.addEventListener('click', () => {
            addBondeModal.style.display = 'flex';
        });

        function closeAddBondeModal() {
            addBondeModal.style.display = 'none';
            document.getElementById('add-bonde-form').reset();
        }

        // Fechar o modal ao clicar fora dele
        window.addEventListener('click', (event) => {
            if (event.target === addBondeModal) {
                closeAddBondeModal();
            }
        });

        // Manipular o envio do formulário via AJAX
        document.getElementById('add-bonde-form').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const data = {
                modelo: formData.get('modelo'),
                capacidade: formData.get('capacidade'),
                ano_fabricacao: formData.get('ano_fabricacao'),
                descricao: formData.get('descricao'),
                ativo: formData.get('ativo') ? 1 : 0
            };

            fetch('/Sistema-CENTRAL-ERP/add_bonde.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Bonde adicionado com sucesso!');
                    closeAddBondeModal();
                    location.reload(); // Recarrega a página para atualizar a lista de bondes
                } else {
                    alert('Erro ao adicionar bonde: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro na conexão com o servidor.');
            });
        });
    </script>
</body>
</html>