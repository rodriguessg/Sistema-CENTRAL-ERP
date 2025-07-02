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

    // Query to fetch bondes from the 'modelo' column in the 'bondes' table
    $stmt = $pdo->query("SELECT modelo FROM bondes ORDER BY modelo ASC");
    $bondes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // Fallback to hardcoded options if database query fails
    $bondes = ['BONDE 17', 'BONDE 16', 'BONDE 19', 'BONDE 22', 'BONDE 18', 'BONDE 20'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Viagens - Bondes Santa Teresa</title>
    <link rel="stylesheet" href="./src/bonde/style/bonde.css">
</head>
<body>
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
                                foreach ($bondes as $bonde) {
                                    echo "<option value=\"{$bonde}\">{$bonde}</option>";
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
                <button type="button" id="clear-transactions-btn">Limpar Transações</button>
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
    <script src="./src/bonde/js/bonde.js"></script>
</body>
</html>
