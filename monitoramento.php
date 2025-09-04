<?php
session_start();

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()]);
    exit;
}

// Handle AJAX request for table data (accidents)
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_table_data') {
    try {
        $sql = "SELECT id, data, descricao, localizacao, usuario, severidade, categoria, cor, modelo, data_registro, status, policia, bombeiros, samu, vitima 
                FROM acidentes 
                ORDER BY data_registro DESC, id DESC 
                LIMIT 6";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($rows);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro na consulta SQL: ' . $e->getMessage()]);
    }
    exit;
}

// Handle AJAX request for statistics data (accidents)
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_stats') {
    try {
        $sql = "SELECT severidade, COUNT(*) as count FROM acidentes WHERE status != 'resolvido' GROUP BY severidade";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $countResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $counts = ['Grave' => 0, 'Moderado' => 0, 'Leve' => 0, 'Moderado a Grave' => 0];
        foreach ($countResult as $countRow) {
            if (isset($counts[$countRow['severidade']])) {
                $counts[$countRow['severidade']] = $countRow['count'];
            }
        }
        $totalOcorrencias = array_sum($counts);

        header('Content-Type: application/json');
        echo json_encode([
            'total' => $totalOcorrencias,
            'grave' => $counts['Grave'],
            'moderado' => $counts['Moderado'],
            'leve' => $counts['Leve']
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro na consulta SQL: ' . $e->getMessage()]);
    }
    exit;
}

// Handle AJAX request for viagens data
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_viagens') {
    try {
        $sql = "SELECT bonde, pagantes, moradores, gratuidade, passageiros 
                FROM viagens 
                ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($rows);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro na consulta SQL: ' . $e->getMessage()]);
    }
    exit;
}

// Verifica sessão
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

// Buscar dados iniciais para a tabela
try {
    $sql = "SELECT id, data, descricao, localizacao, usuario, severidade, categoria, cor, modelo, data_registro, status, policia, bombeiros, samu, vitima 
            FROM acidentes 
            ORDER BY data_registro DESC, id DESC 
            LIMIT 6";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na consulta SQL: " . $e->getMessage());
}

// Buscar dados de estatísticas
try {
    $sql = "SELECT severidade, COUNT(*) as count FROM acidentes WHERE status != 'resolvido' GROUP BY severidade";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $countResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $counts = ['Grave' => 0, 'Moderado' => 0, 'Leve' => 0, 'Moderado a Grave' => 0];
    foreach ($countResult as $countRow) {
        if (isset($counts[$countRow['severidade']])) {
            $counts[$countRow['severidade']] = $countRow['count'];
        }
    }
    $totalOcorrencias = array_sum($counts);
} catch (PDOException $e) {
    die("Erro na consulta SQL: " . $e->getMessage());
}

$corMap = [
    'Verde' => '#d1f5d3',
    'Amarelo' => '#fff3cd',
    'Vermelho' => '#f8d7da',
    'Amarelo/Vermelho' => 'linear-gradient(to right, #fff3cd, #f8d7da)'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento - Bonde de Santa Teresa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Partículas flutuantes no fundo */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Layout principal adaptado */
        .main-container {
            display: flex;
            height: 100vh;
            position: relative;
            z-index: 2;
        }

        /* Sidebar compacta */
        .sidebar {
            width: 160px;
            padding: 15px 10px;
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .stat-card {
            background: #e0e5ec;
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            box-shadow: 
                4px 4px 8px rgba(163, 177, 198, 0.4),
                -4px -4px 8px rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
            min-height: 65px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-card:hover {
            transform: translateY(-1px);
            box-shadow: 
                6px 6px 12px rgba(163, 177, 198, 0.5),
                -6px -6px 12px rgba(255, 255, 255, 0.8);
        }

        .stat-icon {
            font-size: 20px;
            margin-bottom: 4px;
            color: #dc2626;
        } 
        .stat-icon2 {
            font-size: 20px;
            margin-bottom: 4px;
            color: #f59e0b;
        }
        .stat-icon3 {
            font-size: 20px;
            margin-bottom: 4px;
            color: #10b981;
        }
        .stat-icon4 {
            font-size: 20px;
            margin-bottom: 4px;
            color: #3b82f6;
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stat-sublabel {
            font-size: 12px;
            color: #999;
            font-weight: bold;
            margin-top: 1px;
        }

        /* Área principal do conteúdo */
        .content-area {
            flex: 1;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow: hidden;
        }

        /* Layout da área principal */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 15px;
            flex: 1;
        }

        /* Seção superior - tabela e detalhes */
        .top-section {
            display: flex;
            gap: 15px;
            height: 35%;
            min-height: 350px;
        }

        /* Seção da tabela */
        .table-section {
            flex: 2.5;
            background: #e0e5ec;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 
                6px 6px 12px rgba(163, 177, 198, 0.4),
                -6px -6px 12px rgba(255, 255, 255, 0.7);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .section-icon {
            font-size: 18px;
            color: #3b82f6;
        }

        /* Tabela com design neumórfico */
        .table-container {
            flex: 1;
            overflow: auto;
            border-radius: 12px;
            background: #e0e5ec;
            box-shadow: 
                inset 4px 4px 8px rgba(163, 177, 198, 0.4),
                inset -4px -4px 8px rgba(255, 255, 255, 0.7);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }

        th, td {
            padding: 10px 8px;
            text-align: center;
            border: none;
            font-size: 12px;
            font-weight: 500;
        }

        .borda-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        th {
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 11px;
        }

        tr {
            transition: background-color 0.3s ease;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.3);
            cursor: pointer;
        }

        .severity-bg {
            padding: 4px 8px;
            border-radius: 15px;
            font-weight: bold;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: 10px;
            white-space: nowrap;
        }

        .cor-verde { 
            background: linear-gradient(135deg, #d1f5d3 0%, #a7f3d0 100%); 
            color: #2f855a; 
        }
        .cor-amarelo { 
            background: linear-gradient(135deg, #fff3cd 0%, #fde68a 100%); 
            color: #d97706; 
        }
        .cor-vermelho { 
            background: linear-gradient(135deg, #f8d7da 0%, #fca5a5 100%); 
            color: #991b1b; 
        }
        .cor-amarelo-vermelho { 
            background: linear-gradient(135deg, #fff3cd 0%, #f8d7da 100%); 
            color: #fff; 
        }

        .tram-highlight {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #3730a3;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: 600;
        }

        .location-highlight {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: 600;
        }

        .details-container {
            flex: 1;
            background: #e0e5ec;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 
                6px 6px 12px rgba(163, 177, 198, 0.4),
                -6px -6px 12px rgba(255, 255, 255, 0.7);
            overflow: auto;
            border-top: 3px solid #3b82f6;
        }

        .details-container h3 {
            font-size: 14px;
            margin-bottom: 12px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .details-container p {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .details-container p .detail-icon {
            color: #007bff;
        }

        .details-container p span {
            color: #555;
        }

        .bottom-section {
            display: flex;
            gap: 15px;
            flex: 1;
            height: 65%;
        }

        .map-container {
            flex: 2.5;
            background: #e0e5ec;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 
                6px 6px 12px rgba(163, 177, 198, 0.4),
                -6px -6px 12px rgba(255, 255, 255, 0.7);
        }

        .map-container iframe {
            width: 100%;
            height: calc(100% - 40px);
            border: none;
            border-radius: 12px;
            box-shadow: 
                inset 4px 4px 8px rgba(163, 177, 198, 0.4),
                inset -4px -4px 8px rgba(255, 255, 255, 0.7);
        }

        /* Dashboard styling */
        .dashboard-container {
            flex: 1;
            background: #e0e5ec;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 
                6px 6px 12px rgba(163, 177, 198, 0.4),
                -6px -6px 12px rgba(255, 255, 255, 0.7);
            display: flex;
            flex-direction: column;
        }

        .dashboard-table th {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            cursor: pointer;
            font-size: 11px;
            padding: 8px;
        }

        .dashboard-table th:hover {
            background: linear-gradient(135deg, #2b4cb3 0%, #2b4cb3 100%);
        }

        .dashboard-table td {
            font-size: 12px;
            padding: 8px;
        }

        .sort-icon::after {
            content: '↕';
            margin-left: 4px;
            font-size: 10px;
        }

        .sort-asc::after {
            content: '↑';
        }

        .sort-desc::after {
            content: '↓';
        }

        .bonde-filter {
            padding: 8px;
            font-size: 12px;
            border: none;
            border-radius: 8px;
            background: #e0e5ec;
            box-shadow: 
                inset 4px 4px 8px rgba(163, 177, 198, 0.4),
                inset -4px -4px 8px rgba(255, 255, 255, 0.7);
            width: 100%;
            margin-bottom: 12px;
        }

        .bonde-filter:focus {
            outline: none;
            box-shadow: 
                inset 2px 2px 4px rgba(163, 177, 198, 0.6),
                inset -2px -2px 4px rgba(255, 255, 255, 0.9);
        }

        /* Responsividade */
        @media (max-width: 1200px) {
            .sidebar { width: 140px; }
            .top-section { 
                flex-direction: column; 
                height: auto;
                min-height: 250px;
            }
            .bottom-section { 
                flex-direction: column; 
                height: auto;
            }
            .details-container { max-width: none; }
            .dashboard-container { height: 400px; }
        }

        @media (max-width: 768px) {
            .main-container { flex-direction: column; }
            .sidebar { 
                width: 100%; 
                flex-direction: row; 
                padding: 10px;
                gap: 8px;
                overflow-x: auto;
            }
            .stat-card { min-width: 120px; }
            .content-area { padding: 10px; }
            .top-section { height: auto; min-height: 200px; }
            .bottom-section { height: 400px; }
            .map-container { flex: 1; }
            .dashboard-container { display: block; height: 400px; }
            .particles .particle { display: none; }
            .dashboard-table th, .dashboard-table td { font-size: 10px; padding: 6px; }
            .bonde-filter { font-size: 10px; padding: 6px; }
        }

        @media (max-width: 480px) {
            .sidebar { gap: 5px; }
            .stat-card { 
                min-width: 100px; 
                padding: 8px 6px;
                min-height: 50px;
            }
            .stat-number { font-size: 18px; }
            .stat-label { font-size: 8px; }
            .stat-sublabel { display: none; }
            th, td { padding: 6px 4px; font-size: 10px; }
            .severity-bg { font-size: 8px; padding: 2px 4px; }
            .dashboard-table th, .dashboard-table td { font-size: 8px; padding: 4px; }
        }

        .selected {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
            font-weight: bold;
        }

        .new-occurrence {
            animation: blink-blue 1s infinite;
        }

        .time-blink {
            animation: time-blink-blue 1s infinite;
        }

        @keyframes blink-blue {
            0%, 50% { background-color: rgba(59, 130, 246, 0.3); }
            51%, 100% { background-color: transparent; }
        }

        @keyframes time-blink-blue {
            0%, 50% { color: #3b82f6; font-weight: bold; }
            51%, 100% { color: inherit; font-weight: normal; }
        }

        .counter {
            display: inline-block;
            animation: countUp 2s ease-out;
        }

        @keyframes countUp {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Partículas flutuantes -->
    <div class="particles" id="particles"></div>

    <div class="main-container">
        <!-- Sidebar compacta -->
        <div class="sidebar">
            <div class="stat-card">
                <div class="stat-icon4"><i class="fas fa-chart-line"></i></div>
                <div class="stat-number counter" id="total-occurrences"><?= $totalOcorrencias ?></div>
                <div class="stat-label">ATIVAS</div>
                <div class="stat-sublabel">Tempo real</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-number counter" id="grave-occurrences"><?= $counts['Grave'] ?></div>
                <div class="stat-label">CRÍTICAS</div>
                <div class="stat-sublabel">Urgente</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon2"><i class="fas fa-exclamation-circle"></i></div>
                <div class="stat-number counter" id="moderado-occurrences"><?= $counts['Moderado'] ?></div>
                <div class="stat-label">MODERADAS</div>
                <div class="stat-sublabel">Atenção</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon3"><i class="fas fa-info-circle"></i></div>
                <div class="stat-number counter" id="leve-occurrences"><?= $counts['Leve'] ?></div>
                <div class="stat-label">LEVES</div>
                <div class="stat-sublabel">Baixo risco</div>
            </div>
        </div>

        <!-- Área principal do conteúdo -->
        <div class="content-area">
            <div class="main-content">
                <!-- Seção superior: tabela e detalhes -->
                <div class="top-section">
                    <!-- Seção da tabela -->
                    <div class="table-section">
                        <div class="section-title">
                            <i class="fas fa-table section-icon"></i>
                            Últimas Ocorrências
                        </div>
                        <div class="table-container">
                            <table id="accidents-table">
                                <thead class="borda-header">
                                    <tr>
                                        <th><i class="fas fa-thermometer-half"></i> Severidade</th>
                                        <th><i class="fas fa-bus"></i> Bonde</th>
                                        <th><i class="fas fa-tags"></i> Tipo</th>
                                        <th><i class="fas fa-map-marker-alt"></i> Local</th>
                                        <th><i class="fas fa-clock"></i> Hora</th>
                                        <th><i class="fas fa-user-injured"></i> Vítima</th>
                                        <th><i class="fas fa-shield-alt"></i> Polícia</th>
                                        <th><i class="fas fa-ambulance"></i> SAMU</th>
                                        <th><i class="fas fa-fire-extinguisher"></i> Bombeiros</th>
                                        <th><i class="fas fa-info"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($result as $row) {
                                        $severityClass = 'cor-' . str_replace('/', '-', strtolower($row['cor']));
                                        $hora = date('H:i', strtotime($row['data_registro']));
                                        $status = $row['status'] ?? 'em andamento';
                                        ?>
                                        <tr onclick="selectOccurrence(<?= $row['id'] ?>, this, <?= json_encode(['policia' => $row['policia'], 'bombeiros' => $row['bombeiros'], 'samu' => $row['samu'], 'modelo' => $row['modelo'], 'categoria' => $row['categoria'], 'localizacao' => $row['localizacao'], 'severidade' => $row['severidade'], 'status' => $status, 'hora' => $hora, 'vitima' => $row['vitima']]) ?>)">
                                            <td><span class="severity-bg <?= $severityClass ?>"><i class="fas fa-thermometer-half"></i> <?= htmlspecialchars($row['severidade']) ?></span></td>
                                            <td><span class="tram-highlight"><i class="fas fa-bus"></i> <?= htmlspecialchars($row['modelo'] ?? 'N/A') ?></span></td>
                                            <td><i class="fas fa-tag"></i> <?= htmlspecialchars($row['categoria']) ?></td>
                                            <td><span class="location-highlight"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['localizacao']) ?></span></td>
                                            <td class="time-cell"><i class="fas fa-clock"></i> <?= $hora ?></td>
                                            <td><?= htmlspecialchars($row['vitima'] ?? 'Não') ?></td>
                                            <td><?= $row['policia'] == 1 ? '<i class="fas fa-check" style="color: #10b981;"></i>' : '<i class="fas fa-times" style="color: #ef4444;"></i>' ?></td>
                                            <td><?= $row['samu'] == 1 ? '<i class="fas fa-check" style="color: #10b981;"></i>' : '<i class="fas fa-times" style="color: #ef4444;"></i>' ?></td>
                                            <td><?= $row['bombeiros'] == 1 ? '<i class="fas fa-check" style="color: #10b981;"></i>' : '<i class="fas fa-times" style="color: #ef4444;"></i>' ?></td>
                                            <td><i class="fas fa-info-circle"></i> <?= htmlspecialchars($status) ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Detalhes compactos -->
                    <div class="details-container" id="occurrence-details">
                        <h3><i class="fas fa-info-circle"></i> Detalhes da Ocorrência</h3>
                        <p><i class="fas fa-thermometer-half detail-icon"></i> Severidade: <span>Selecione uma ocorrência</span></p>
                        <p><i class="fas fa-bus detail-icon"></i> Bonde: <span>-</span></p>
                        <p><i class="fas fa-tag detail-icon"></i> Tipo: <span>-</span></p>
                        <p><i class="fas fa-map-marker-alt detail-icon"></i> Local: <span>-</span></p>
                        <p><i class="fas fa-clock detail-icon"></i> Hora: <span>-</span></p>
                        <p><i class="fas fa-user-injured detail-icon"></i> Vítima: <span>-</span></p>
                        <p><i class="fas fa-shield-alt detail-icon"></i> Polícia: <span>-</span></p>
                        <p><i class="fas fa-ambulance detail-icon"></i> SAMU: <span>-</span></p>
                        <p><i class="fas fa-fire-extinguisher detail-icon"></i> Bombeiros: <span>-</span></p>
                        <p><i class="fas fa-info-circle detail-icon"></i> Status: <span>-</span></p>
                        <p><i class="fas fa-user detail-icon"></i> Responsável: <span>Sistema Automático</span></p>
                    </div>
                </div>

                <!-- Seção inferior: mapa e dashboard -->
                <div class="bottom-section">
                    <div class="map-container">
                        <div class="section-title">
                            <i class="fas fa-map-marked-alt section-icon"></i>
                            Locator
                        </div>
                        <iframe src="https://hosting.wialon.us/locator/index.html?t=4ebee7c35e2e2fbedde92f4b2611c141F0AA094FB415B295867B3BD93520050BB6566DD7" allowfullscreen></iframe>
                    </div>

                    <!-- Dashboard de passageiros por bonde -->
                    <div class="dashboard-container">
                        <div class="section-title">
                            <i class="fas fa-chart-bar section-icon"></i>
                            Passageiros por Bonde
                        </div>
                        <input type="text" id="bonde-filter" placeholder="Filtrar por Bonde" class="bonde-filter">
                        <div class="flex-1 overflow-auto">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th data-sort="bonde"><i class="fas fa-bus"></i> Bonde <span class="sort-icon"></span></th>
                                        <th data-sort="pagantes">Pagantes <span class="sort-icon"></span></th>
                                        <th data-sort="moradores">Moradores <span class="sort-icon"></span></th>
                                        <th data-sort="gratuidade">Gratuidade <span class="sort-icon"></span></th>
                                        <th data-sort="passageiros">Passageiros <span class="sort-icon"></span></th>
                                    </tr>
                                </thead>
                                <tbody id="dashboard-table-body"></tbody>
                            </table>
                        </div>
                        <div style="height: 150px; margin-top: 12px;">
                            <canvas id="passenger-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = window.innerWidth < 768 ? 15 : window.innerWidth < 1200 ? 25 : 35;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.width = Math.random() * 3 + 1 + 'px';
                particle.style.height = particle.style.width;
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        function animateCounters() {
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                let current = 0;
                const increment = target / 30;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 50);
            });
        }

        let blinkingOccurrences = new Map();
        let previousOccurrenceIds = [];

        function updateStats() {
            fetch('?ajax=get_stats')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Erro ao atualizar estatísticas:', data.error);
                        return;
                    }

                    const counters = document.querySelectorAll('.counter');
                    counters[0].textContent = data.total;
                    counters[1].textContent = data.grave;
                    counters[2].textContent = data.moderado;
                    counters[3].textContent = data.leve;

                    counters.forEach(counter => {
                        counter.style.transform = 'scale(1.1)';
                        setTimeout(() => {
                            counter.style.transform = 'scale(1)';
                        }, 200);
                    });
                })
                .catch(error => console.error('Erro ao atualizar estatísticas:', error));
        }

        function updateTable() {
            fetch('?ajax=get_table_data')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Erro ao atualizar tabela:', data.error);
                        return;
                    }

                    const tbody = document.querySelector('#accidents-table tbody');
                    tbody.innerHTML = '';

                    const currentIds = data.map(row => row.id);
                    const newOccurrences = currentIds.filter(id => !previousOccurrenceIds.includes(id));
                    
                    data.forEach((row, index) => {
                        const severityClass = 'cor-' + row.cor.toLowerCase().replace('/', '-');
                        const hora = new Date(row.data_registro).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                        const status = row.status || 'em andamento';

                        const tr = document.createElement('tr');
                        const emergencyData = {
                            policia: row.policia,
                            bombeiros: row.bombeiros,
                            samu: row.samu,
                            modelo: row.modelo,
                            categoria: row.categoria,
                            localizacao: row.localizacao,
                            severidade: row.severidade,
                            status: status,
                            hora: hora,
                            vitima: row.vitima || 'Não'
                        };
                        
                        tr.setAttribute('onclick', `selectOccurrence(${row.id}, this, ${JSON.stringify(emergencyData)})`);
                        
                        const isNewOccurrence = newOccurrences.includes(row.id);
                        const shouldBlink = isNewOccurrence || (blinkingOccurrences.has(row.id) && status !== 'resolvido');
                        
                        if (isNewOccurrence && status !== 'resolvido') {
                            blinkingOccurrences.set(row.id, setTimeout(() => {
                                blinkingOccurrences.delete(row.id);
                            }, 3600000));
                        } else if (status === 'resolvido' && blinkingOccurrences.has(row.id)) {
                            clearTimeout(blinkingOccurrences.get(row.id));
                            blinkingOccurrences.delete(row.id);
                        }

                        tr.innerHTML = `
                            <td><span class="severity-bg ${severityClass}"><i class="fas fa-thermometer-half"></i> ${row.severidade}</span></td>
                            <td><span class="tram-highlight"><i class="fas fa-bus"></i> ${row.modelo || 'N/A'}</span></td>
                            <td><i class="fas fa-tag"></i> ${row.categoria}</td>
                            <td><span class="location-highlight"><i class="fas fa-map-marker-alt"></i> ${row.localizacao}</span></td>
                            <td class="time-cell ${shouldBlink && status !== 'resolvido' ? 'time-blink' : ''}"><i class="fas fa-clock"></i> ${hora}</td>
                            <td>${row.vitima || 'Não'}</td>
                            <td>${row.policia == 1 ? '<i class="fas fa-check" style="color: #10b981;"></i>' : '<i class="fas fa-times" style="color: #ef4444;"></i>'}</td>
                            <td>${row.samu == 1 ? '<i class="fas fa-check" style="color: #10b981;"></i>' : '<i class="fas fa-times" style="color: #ef4444;"></i>'}</td>
                            <td>${row.bombeiros == 1 ? '<i class="fas fa-check" style="color: #10b981;"></i>' : '<i class="fas fa-times" style="color: #ef4444;"></i>'}</td>
                            <td><i class="fas fa-info-circle"></i> ${status}</td>
                        `;
                        
                        if (shouldBlink && status !== 'resolvido') {
                            tr.classList.add('new-occurrence');
                        }
                        
                        tbody.appendChild(tr);
                    });

                    previousOccurrenceIds = currentIds;
                })
                .catch(error => console.error('Erro ao atualizar tabela:', error));
        }

        // Dashboard functionality
        let viagensData = [];
        let sortColumn = 'bonde';
        let sortDirection = 'asc';
        let passengerChart = null;

        function aggregatePassengerData() {
            const bondeData = {};
            viagensData.forEach(t => {
                if (!bondeData[t.bonde]) {
                    bondeData[t.bonde] = {
                        pagantes: 0,
                        moradores: 0,
                        gratuidade: 0,
                        passageiros: 0
                    };
                }
                bondeData[t.bonde].pagantes += parseInt(t.pagantes) || 0;
                bondeData[t.bonde].moradores += parseInt(t.moradores) || 0;
                bondeData[t.bonde].gratuidade += parseInt(t.gratuidade) || 0;
                bondeData[t.bonde].passageiros += parseInt(t.passageiros) || 0;
            });
            return Object.entries(bondeData).map(([bonde, data]) => ({
                bonde,
                ...data
            }));
        }

        function sortData(data, column, direction) {
            return data.sort((a, b) => {
                const valA = a[column];
                const valB = b[column];
                if (typeof valA === 'string') {
                    return direction === 'asc'
                        ? valA.localeCompare(valB)
                        : valB.localeCompare(valA);
                }
                return direction === 'asc'
                    ? valA - valB
                    : valB - valA;
            });
        }

        function renderPassengerChart(data) {
            if (passengerChart) {
                passengerChart.destroy();
            }
            const ctx = document.getElementById('passenger-chart').getContext('2d');
            passengerChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.bonde),
                    datasets: [
                        {
                            label: 'Pagantes',
                            data: data.map(item => item.pagantes),
                            backgroundColor: '#10b981',
                            borderColor: '#059669',
                            borderWidth: 1
                        },
                        {
                            label: 'Moradores',
                            data: data.map(item => item.moradores),
                            backgroundColor: '#f59e0b',
                            borderColor: '#d97706',
                            borderWidth: 1
                        },
                        {
                            label: 'Gratuidade',
                            data: data.map(item => item.gratuidade),
                            backgroundColor: '#3b82f6',
                            borderColor: '#1e40af',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: { display: true, text: 'Bonde', color: '#333', font: { size: 12 } }
                        },
                        y: {
                            title: { display: true, text: 'Quantidade', color: '#333', font: { size: 12 } },
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: { 
                            position: 'top',
                            labels: { font: { size: 10 }, color: '#333' }
                        }
                    }
                }
            });
        }

        function updatePassengerDashboard() {
            const filterBonde = document.getElementById('bonde-filter').value.trim().toLowerCase();
            const tbody = document.getElementById('dashboard-table-body');
            tbody.innerHTML = '';

            let bondeData = aggregatePassengerData();
            
            if (filterBonde) {
                bondeData = bondeData.filter(item => item.bonde.toLowerCase().includes(filterBonde));
            }
            
            bondeData = sortData(bondeData, sortColumn, sortDirection);
            
            if (bondeData.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center">Nenhum dado encontrado.</td></tr>`;
            } else {
                bondeData.forEach(item => {
                    const row = tbody.insertRow();
                    row.innerHTML = `
                        <td><i class="fas fa-bus"></i> ${item.bonde}</td>
                        <td>${item.pagantes}</td>
                        <td>${item.moradores}</td>
                        <td>${item.gratuidade}</td>
                        <td>${item.passageiros}</td>
                    `;
                });
            }
            
            renderPassengerChart(bondeData);
        }

        function fetchViagensData() {
            fetch('?ajax=get_viagens')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Erro ao atualizar dados de viagens:', data.error);
                        return;
                    }
                    viagensData = Array.isArray(data) ? data : [];
                    updatePassengerDashboard();
                })
                .catch(error => console.error('Erro ao buscar dados de viagens:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            animateCounters();
            updateTable();
            updateStats();
            fetchViagensData();

            // Initialize dashboard sorting
            document.querySelectorAll('.dashboard-table th[data-sort]').forEach(th => {
                th.addEventListener('click', () => {
                    const column = th.getAttribute('data-sort');
                    if (sortColumn === column) {
                        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        sortColumn = column;
                        sortDirection = 'asc';
                    }
                    document.querySelectorAll('.dashboard-table th').forEach(header => {
                        header.querySelector('.sort-icon').classList.remove('sort-asc', 'sort-desc');
                    });
                    th.querySelector('.sort-icon').classList.add(sortDirection === 'asc' ? 'sort-asc' : 'sort-desc');
                    updatePassengerDashboard();
                });
            });

            // Initialize dashboard filter
            document.getElementById('bonde-filter').addEventListener('input', updatePassengerDashboard);
        });

        setInterval(updateTable, 2000);
        setInterval(updateStats, 2000);
        setInterval(fetchViagensData, 2000);

        function selectOccurrence(id, row, emergencyServices) {
            document.querySelectorAll('#accidents-table tr').forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');

            const details = {
                severidade: emergencyServices.severidade || 'N/A',
                modelo: emergencyServices.modelo || 'N/A',
                tipo: emergencyServices.categoria || 'N/A',
                local: emergencyServices.localizacao || 'N/A',
                hora: emergencyServices.hora || 'N/A',
                vitima: emergencyServices.vitima || 'Não',
                policia: emergencyServices.policia ? 'Acionada' : 'Não acionada',
                samu: emergencyServices.samu ? 'Acionado' : 'Não acionado',
                bombeiros: emergencyServices.bombeiros ? 'Acionados' : 'Não acionados',
                status: emergencyServices.status || 'N/A'
            };

            const detailsDiv = document.getElementById('occurrence-details');
            detailsDiv.innerHTML = `
                <h3><i class="fas fa-info-circle"></i> Detalhes da Ocorrência #${id}</h3>
                <p><i class="fas fa-thermometer-half detail-icon"></i> Severidade: <span>${details.severidade}</span></p>
                <p><i class="fas fa-bus detail-icon"></i> Bonde: <span>${details.modelo}</span></p>
                <p><i class="fas fa-tag detail-icon"></i> Tipo: <span>${details.tipo}</span></p>
                <p><i class="fas fa-map-marker-alt detail-icon"></i> Local: <span>${details.local}</span></p>
                <p><i class="fas fa-clock detail-icon"></i> Hora: <span>${details.hora}</span></p>
                <p><i class="fas fa-user-injured detail-icon"></i> Vítima: <span>${details.vitima}</span></p>
                <p><i class="fas fa-shield-alt detail-icon"></i> Polícia: <span>${details.policia}</span></p>
                <p><i class="fas fa-ambulance detail-icon"></i> SAMU: <span>${details.samu}</span></p>
                <p><i class="fas fa-fire-extinguisher detail-icon"></i> Bombeiros: <span>${details.bombeiros}</span></p>
                <p><i class="fas fa-info-circle detail-icon"></i> Status: <span>${details.status}</span></p>
                <p><i class="fas fa-user detail-icon"></i> Responsável: <span>Sistema Automático</span></p>
            `;
        }
    </script>
</body>
</html>