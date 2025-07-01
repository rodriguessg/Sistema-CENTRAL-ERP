<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$usuarioLogado = $_SESSION['username'];

// Conexão com o banco
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div class='error'>Erro ao conectar ao banco: " . $e->getMessage() . "</div>");
}

// Carrega bondes para o filtro
$bondes = [];
try {
    $stmt = $pdo->query("SELECT modelo FROM bondes ORDER BY modelo ASC");
    $bondes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $bondes = [];
}

// Processa filtro
$bonde = isset($_GET['bonde']) ? $_GET['bonde'] : '';
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'dia';
$dataBase = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');

// Calcula intervalo de datas
switch ($periodo) {
    case 'dia':
        $dataInicio = $dataBase;
        $dataFim = $dataBase;
        $periodoTexto = "Dia: " . date('d/m/Y', strtotime($dataBase));
        break;
    case 'semana':
        $dataInicio = date('Y-m-d', strtotime('monday this week', strtotime($dataBase)));
        $dataFim = date('Y-m-d', strtotime('sunday this week', strtotime($dataBase)));
        $periodoTexto = "Semana: " . date('d/m/Y', strtotime($dataInicio)) . " a " . date('d/m/Y', strtotime($dataFim));
        break;
    case 'mes':
        $dataInicio = date('Y-m-01', strtotime($dataBase));
        $dataFim = date('Y-m-t', strtotime($dataBase));
        $periodoTexto = "Mês: " . date('m/Y', strtotime($dataBase));
        break;
    case 'ano':
        $dataInicio = date('Y-01-01', strtotime($dataBase));
        $dataFim = date('Y-12-31', strtotime($dataBase));
        $periodoTexto = "Ano: " . date('Y', strtotime($dataBase));
        break;
    default:
        $dataInicio = $dataFim = $dataBase;
        $periodoTexto = "Dia: " . date('d/m/Y', strtotime($dataBase));
}

// Consulta transações
$sql = "SELECT modelo, data, pagantes, moradores, gratuidade, passageiros FROM transacoes WHERE data BETWEEN :inicio AND :fim";
$params = [':inicio' => $dataInicio, ':fim' => $dataFim];
if ($bonde) {
    $sql .= " AND modelo = :modelo";
    $params[':modelo'] = $bonde;
}
$sql .= " ORDER BY modelo, data ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $resultados = [];
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Bondes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #34495e;
            --gray: #95a5a6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .title {
            color: var(--primary);
            font-size: 28px;
            font-weight: 600;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--light);
            padding: 10px 15px;
            border-radius: 30px;
            font-weight: 500;
        }
        
        .user-info i {
            color: var(--secondary);
            font-size: 18px;
        }
        
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-weight: 500;
            margin-bottom: 6px;
            color: var(--dark);
            font-size: 14px;
        }
        
        .filter-group select, 
        .filter-group input {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background: white;
            transition: all 0.3s ease;
        }
        
        .filter-group select:focus, 
        .filter-group input:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .btn-generate {
            background: var(--success);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 14px 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-generate:hover {
            background: #219653;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.3);
        }
        
        .btn-generate i {
            font-size: 18px;
        }
        
        .period-info {
            background: #e1f5fe;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .period-info i {
            color: var(--secondary);
            font-size: 20px;
        }
        
        .results-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            min-width: 800px;
        }
        
        th {
            background: var(--primary);
            color: white;
            text-align: left;
            padding: 15px;
            font-weight: 600;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #f1f9ff;
        }
        
        .totals-row {
            font-weight: 700;
            background-color: #e3f2fd !important;
        }
        
        .totals-row td {
            border-top: 2px solid var(--secondary);
            border-bottom: none;
        }
        
        .no-results {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }
        
        .no-results i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }
        
        .no-results p {
            font-size: 18px;
        }
        
        .bonde-name {
            font-weight: 600;
            color: var(--secondary);
        }
        
        .data-cell {
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .filters {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 15px;
            }
            
            .card {
                padding: 20px;
            }
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
            color: var(--secondary);
        }
        
        .loading i {
            font-size: 40px;
            margin-bottom: 10px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .summary-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
        }
        
        .summary-card .title {
            font-size: 16px;
            color: var(--gray);
            margin-bottom: 10px;
        }
        
        .summary-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .summary-card .bonde {
            font-size: 14px;
            color: var(--secondary);
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1 class="title">Relatório de Transações dos Bondes</h1>
                <div class="user-info">
                    <i class="fas fa-user"></i>
                    <span>Usuário: <?= htmlspecialchars($usuarioLogado) ?></span>
                </div>
            </div>
            
            <form method="GET" id="reportForm">
                <div class="filters">
                    <div class="filter-group">
                        <label for="bonde"><i class="fas fa-train"></i> Bonde:</label>
                        <select name="bonde" id="bonde">
                            <option value="">Todos os bondes</option>
                            <?php foreach ($bondes as $modelo): ?>
                                <option value="<?= htmlspecialchars($modelo) ?>" <?= ($bonde == $modelo) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($modelo) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="periodo"><i class="fas fa-calendar"></i> Período:</label>
                        <select name="periodo" id="periodo">
                            <option value="dia" <?= ($periodo == 'dia') ? 'selected' : '' ?>>Dia</option>
                            <option value="semana" <?= ($periodo == 'semana') ? 'selected' : '' ?>>Semana</option>
                            <option value="mes" <?= ($periodo == 'mes') ? 'selected' : '' ?>>Mês</option>
                            <option value="ano" <?= ($periodo == 'ano') ? 'selected' : '' ?>>Ano</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="data"><i class="fas fa-calendar-day"></i> Data base:</label>
                        <input type="date" name="data" id="data" value="<?= htmlspecialchars($dataBase) ?>">
                    </div>
                    
                    <div class="filter-group" style="align-self: flex-end;">
                        <button type="submit" class="btn-generate">
                            <i class="fas fa-sync-alt"></i> Gerar Relatório
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="period-info">
                <i class="fas fa-info-circle"></i>
                <span><?= $periodoTexto ?></span>
                <?php if ($bonde): ?>
                    <span> | Bonde selecionado: <strong><?= htmlspecialchars($bonde) ?></strong></span>
                <?php endif; ?>
            </div>
            
            <div class="loading" id="loadingIndicator">
                <i class="fas fa-spinner"></i>
                <p>Carregando dados...</p>
            </div>
            
            <?php if (count($resultados) > 0): ?>
                <!-- Cards Resumo -->
                <div class="summary-cards">
                    <?php
                    $totPagantes = $totMoradores = $totGratuidade = $totPassageiros = 0;
                    foreach ($resultados as $linha) {
                        $totPagantes += $linha['pagantes'];
                        $totMoradores += $linha['moradores'];
                        $totGratuidade += $linha['gratuidade'];
                        $totPassageiros += $linha['passageiros'];
                    }
                    ?>
                    <div class="summary-card">
                        <div class="title">Total de Pagantes</div>
                        <div class="value"><?= number_format($totPagantes, 0, ',', '.') ?></div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="title">Total de Moradores</div>
                        <div class="value"><?= number_format($totMoradores, 0, ',', '.') ?></div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="title">Total de Gratuidades</div>
                        <div class="value"><?= number_format($totGratuidade, 0, ',', '.') ?></div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="title">Total de Passageiros</div>
                        <div class="value"><?= number_format($totPassageiros, 0, ',', '.') ?></div>
                    </div>
                </div>
                
                <!-- Tabela Detalhada -->
                <div class="results-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Bonde</th>
                                <th>Pagantes</th>
                                <th>Moradores</th>
                                <th>Gratuidade</th>
                                <th>Total Passageiros</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totPagantes = $totMoradores = $totGratuidade = $totPassageiros = 0;
                            foreach ($resultados as $linha):
                                $totPagantes += $linha['pagantes'];
                                $totMoradores += $linha['moradores'];
                                $totGratuidade += $linha['gratuidade'];
                                $totPassageiros += $linha['passageiros'];
                                // Formatar data para o padrão brasileiro
                                $dataFormatada = date('d/m/Y', strtotime($linha['data']));
                            ?>
                            <tr>
                                <td class="data-cell"><?= htmlspecialchars($dataFormatada) ?></td>
                                <td><span class="bonde-name"><?= htmlspecialchars($linha['modelo']) ?></span></td>
                                <td><?= number_format($linha['pagantes'], 0, ',', '.') ?></td>
                                <td><?= number_format($linha['moradores'], 0, ',', '.') ?></td>
                                <td><?= number_format($linha['gratuidade'], 0, ',', '.') ?></td>
                                <td><strong><?= number_format($linha['passageiros'], 0, ',', '.') ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="totals-row">
                                <td colspan="2">Totais</td>
                                <td><?= number_format($totPagantes, 0, ',', '.') ?></td>
                                <td><?= number_format($totMoradores, 0, ',', '.') ?></td>
                                <td><?= number_format($totGratuidade, 0, ',', '.') ?></td>
                                <td><strong><?= number_format($totPassageiros, 0, ',', '.') ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-file-excel"></i>
                    <p>Nenhuma transação encontrada para o filtro selecionado</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('reportForm').addEventListener('submit', function() {
            // Mostrar indicador de carregamento
            document.getElementById('loadingIndicator').style.display = 'block';
        });
        
        // Configurar data atual como padrão se não estiver definida
        if(!document.getElementById('data').value) {
            document.getElementById('data').value = new Date().toISOString().split('T')[0];
        }
    </script>
</body>
</html>
