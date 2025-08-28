<?php
session_start();

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle AJAX request for table data
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_table_data') {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'gm_sicbd';

    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]);
        exit;
    }

    $sql = "SELECT id, data, descricao, localizacao, usuario, severidade, categoria, cor, modelo, data_registro, status, policia, bombeiros, samu 
            FROM acidentes 
            ORDER BY data_registro DESC 
            LIMIT 4";
    $result = $conn->query($sql);

    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro na consulta SQL: ' . $conn->error]);
        exit;
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $conn->close();
    header('Content-Type: application/json');
    echo json_encode($rows);
    exit;
}

// Handle AJAX request for statistics data
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_stats') {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'gm_sicbd';

    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]);
        exit;
    }

    // Buscar contagem por severidade
    $countSql = "SELECT severidade, COUNT(*) as count FROM acidentes WHERE status != 'resolvido' GROUP BY severidade";
    $countResult = $conn->query($countSql);
    $counts = ['Grave' => 0, 'Moderado' => 0, 'Leve' => 0, 'Moderado a Grave' => 0];
    
    while ($countRow = $countResult->fetch_assoc()) {
        if (isset($counts[$countRow['severidade']])) {
            $counts[$countRow['severidade']] = $countRow['count'];
        }
    }
    
    $totalOcorrencias = array_sum($counts);
    
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode([
        'total' => $totalOcorrencias,
        'grave' => $counts['Grave'],
        'moderado' => $counts['Moderado'],
        'leve' => $counts['Leve']
    ]);
    exit;
}

// Conexão com o banco
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica sessão
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

$sql = "SELECT id, data, descricao, localizacao, usuario, severidade, categoria, cor, modelo, data_registro, status, policia, bombeiros, samu 
        FROM acidentes 
        ORDER BY data_registro DESC 
        LIMIT 4";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}

$corMap = [
    'Verde' => '#d1f5d3',
    'Amarelo' => '#fff3cd',
    'Vermelho' => '#f8d7da',
    'Amarelo/Vermelho' => 'linear-gradient(to right, #fff3cd, #f8d7da)'
];

$countSql = "SELECT severidade, COUNT(*) as count FROM acidentes GROUP BY severidade";
$countResult = $conn->query($countSql);
$counts = ['Grave' => 0, 'Moderado' => 0, 'Leve' => 0, 'Moderado a Grave' => 0];
while ($countRow = $countResult->fetch_assoc()) {
    if (isset($counts[$countRow['severidade']])) {
        $counts[$countRow['severidade']] = $countRow['count'];
    }
}
$totalOcorrencias = array_sum($counts);

// Simulação de dados para viagens do bonde
$viagens = [
    ['id' => 1, 'direcao' => 'Subindo', 'ultima_atualizacao' => '12:15'],
    ['id' => 2, 'direcao' => 'Descendo', 'ultima_atualizacao' => '12:10'],
    ['id' => 3, 'direcao' => 'Subindo', 'ultima_atualizacao' => '12:05']
];

// Reiniciar ponteiro do resultado
$result->data_seek(0);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento - Bonde de Santa Teresa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* Efeitos neumórficos */
        .neomorphic {
            background: #e0e5ec;
            border-radius: 20px;
            box-shadow: 
                9px 9px 16px rgba(163, 177, 198, 0.6),
                -9px -9px 16px rgba(255, 255, 255, 0.5);
        }

        .neomorphic-inset {
            background: #e0e5ec;
            border-radius: 15px;
            box-shadow: 
                inset 6px 6px 12px rgba(163, 177, 198, 0.4),
                inset -6px -6px 12px rgba(255, 255, 255, 0.7);
        }

        /* Layout principal adaptado para TV */
        .main-container {
            display: flex;
            height: 100vh;
            position: relative;
            z-index: 2;
        }

        /* Sidebar esquerda com estatísticas verticais */
        .sidebar {
            width: 280px;
            padding: 20px;
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .stat-card {
            background: #e0e5ec;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 
                6px 6px 12px rgba(163, 177, 198, 0.4),
                -6px -6px 12px rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 
                8px 8px 16px rgba(163, 177, 198, 0.5),
                -8px -8px 16px rgba(255, 255, 255, 0.8);
        }

        /* Ícones das estatísticas agora em vermelho para destaque */
        .stat-icon {
            font-size: 28px;
            margin-bottom: 8px;
            color: #dc2626;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Aumentando tamanho das fontes para melhor legibilidade */
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-sublabel {
            font-size: 12px;
            color: #999;
            margin-top: 2px;
            font-style: italic;
        }

        /* Área principal do conteúdo */
        .content-area {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow: hidden;
        }

        /* Cabeçalho com ícones */
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 
                9px 9px 16px rgba(163, 177, 198, 0.6),
                -9px -9px 16px rgba(255, 255, 255, 0.5);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: slide 3s infinite;
        }

        @keyframes slide {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Aumentando tamanho da fonte do header */
        .header h1 {
            font-size: 28px;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            font-weight: bold;
        }

        .header-icon {
            font-size: 32px;
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Layout da área principal */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex: 1;
            overflow: hidden;
        }

        /* Reorganizando layout - tabela ocupa mais espaço na parte superior */
        .top-section {
            display: flex;
            gap: 20px;
            min-height: 0;
        }

        /* Seção da tabela - agora maior */
        .table-section {
            flex: 3;
            background: #e0e5ec;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 
                9px 9px 16px rgba(163, 177, 198, 0.6),
                -9px -9px 16px rgba(255, 255, 255, 0.5);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Aumentando tamanho da fonte dos títulos das seções */
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        /* Ícones das seções agora em vermelho para destaque */
        .section-icon {
            font-size: 24px;
            color: #dc2626;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        /* Tabela com design neumórfico */
        .table-container {
            flex: 1;
            overflow: auto;
            border-radius: 15px;
            background: #e0e5ec;
            box-shadow: 
                inset 6px 6px 12px rgba(163, 177, 198, 0.4),
                inset -6px -6px 12px rgba(255, 255, 255, 0.7);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }

        /* Aumentando tamanho das fontes da tabela e melhorando legibilidade */
        th, td {
            padding: 15px 10px;
            text-align: center;
            border: none;
            font-size: 14px;
            font-weight: 500;
        }

        /* Header da tabela com cor única e uniforme */
        th {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%);
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 15px;
        }

        /* Removendo movimento no hover para evitar deslocamento */
        tr {
            transition: background-color 0.3s ease;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.3);
            cursor: pointer;
        }

        .severity-bg {
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: bold;
            text-align: center;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
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

        /* Detalhes agora no canto direito, mais compacto */
        .details-container {
            flex: 1;
            background: #e0e5ec;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 
                9px 9px 16px rgba(163, 177, 198, 0.6),
                -9px -9px 16px rgba(255, 255, 255, 0.5);
            overflow: auto;
            max-width: 300px;
            min-width: 280px;
        }

        /* Aumentando tamanho da fonte dos detalhes */
        .details-container h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 8px;
        }

        .details-container p {
            font-size: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #555;
        }

        /* Ícones de detalhes agora em vermelho */
        .detail-icon {
            width: 18px;
            color: #dc2626;
        }

        /* Nova seção inferior com mapa e container adicional lado a lado */
        .bottom-section {
            display: flex;
            gap: 20px;
            flex: 1;
            min-height: 0;
        }

        /* Mapa agora ocupa 70% da largura inferior */
        .map-container {
            flex: 2;
            background: #e0e5ec;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 
                9px 9px 16px rgba(163, 177, 198, 0.6),
                -9px -9px 16px rgba(255, 255, 255, 0.5);
            height: 480px;
        }

        .map-container iframe {
            width: 100%;
            height: calc(100% - 0px);
            border: none;
            border-radius: 15px;
            box-shadow: 
                inset 6px 6px 12px rgba(163, 177, 198, 0.4),
                inset -6px -6px 12px rgba(255, 255, 255, 0.7);
        }

        /* Novo container adicional para conteúdo futuro */
        .future-content-container {
            flex: 1;
            background: #e0e5ec;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 
                9px 9px 16px rgba(163, 177, 198, 0.6),
                -9px -9px 16px rgba(255, 255, 255, 0.5);
            height: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #666;
        }

        .future-content-container i {
            font-size: 48px;
            color: #dc2626;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }

        .future-content-container h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .future-content-container p {
            font-size: 14px;
            text-align: center;
            color: #666;
        }

        /* Responsividade para telas menores */
        @media (max-width: 1200px) {
            .sidebar { width: 220px; }
            .top-section { flex-direction: column; }
            .bottom-section { flex-direction: column; }
            .details-container { max-width: none; }
        }

        /* Seleção de linha */
        .selected {
            background: linear-gradient(135deg, #d1e7dd 0%, #a3d9a5 100%) !important;
            font-weight: bold;
            box-shadow: 
                inset 3px 3px 6px rgba(163, 177, 198, 0.3),
                inset -3px -3px 6px rgba(255, 255, 255, 0.8);
        }

        /* Animação de nova ocorrência piscante por 1 hora */
        .new-occurrence {
            animation: blink-red 1s infinite;
        }

        /* Animação específica para campo de tempo piscante */
        .time-blink {
            animation: time-blink-red 1s infinite;
        }

        @keyframes blink-red {
            0%, 50% { background-color: rgba(220, 38, 38, 0.3); }
            51%, 100% { background-color: transparent; }
        }

        @keyframes time-blink-red {
            0%, 50% { color: #dc2626; font-weight: bold; }
            51%, 100% { color: inherit; font-weight: normal; }
        }

        /* Contadores animados */
        .counter {
            display: inline-block;
            animation: countUp 2s ease-out;
        }

        @keyframes countUp {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Efeitos de hover melhorados */
        .stat-card:hover .stat-icon {
            animation: spin 0.5s ease-in-out;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Partículas flutuantes -->
    <div class="particles" id="particles"></div>

    <div class="main-container">
        <!-- Sidebar esquerda com estatísticas -->
        <div class="sidebar">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-number counter" id="total-occurrences"><?= $totalOcorrencias ?></div>
                <div class="stat-label">OCORRÊNCIAS ATIVAS</div>
                <div class="stat-sublabel">Monitoramento em tempo real</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-number counter" id="grave-occurrences"><?= $counts['Grave'] ?></div>
                <div class="stat-label">OCORRÊNCIAS CRÍTICA</div>
                <div class="stat-sublabel">Requer ação imediata</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="stat-number counter" id="moderado-occurrences"><?= $counts['Moderado'] ?></div>
                <div class="stat-label">OCORRÊNCIAS MODERADO</div>
                <div class="stat-sublabel">Situação controlada</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-info-circle"></i></div>
                <div class="stat-number counter" id="leve-occurrences"><?= $counts['Leve'] ?></div>
                <div class="stat-label">OCORRÊNCIAS LEVE</div>
                <div class="stat-sublabel">Baixo risco operacional</div>
            </div>
        </div>

        <!-- Área principal do conteúdo -->
        <div class="content-area">
            <div class="main-content">
                <!-- Nova estrutura: tabela e detalhes na parte superior -->
                <div class="top-section">
                    <!-- Seção da tabela - agora maior -->
                    <div class="table-section">
                        <div class="section-title">
                            <i class="fas fa-table section-icon"></i>
                            Últimas Ocorrências em Tempo Real
                        </div>
                        <div class="table-container">
                            <table id="accidents-table">
                                <thead>
                                    <tr>
                                         <th><i class="fas fa-bus"></i> Modelo</th>
                                        <th><i class="fas fa-thermometer-half"></i> Severidade</th>
                                       
                                        <th><i class="fas fa-tags"></i> Tipo</th>
                                       
                                        <th><i class="fas fa-map-marker-alt"></i> Local</th>
                                        <th><i class="fas fa-clock"></i> Hora</th>
                                      
                                         <th><i class="fas fa-shield-alt"></i> Polícia</th>
                                        <th><i class="fas fa-ambulance"></i> SAMU</th>
                                        <th><i class="fas fa-fire-extinguisher"></i> Bombeiros</th>
                                          <th><i class="fas fa-info"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        $severityClass = 'cor-' . str_replace('/', '-', strtolower($row['cor']));
                                        $hora = date('H:i', strtotime($row['data_registro']));
                                        $status = $row['status'] ?? 'Desconhecido';
                                        ?>
                                        <tr onclick="selectOccurrence(<?= $row['id'] ?>, this, <?= json_encode(['policia' => $row['policia'], 'bombeiros' => $row['bombeiros'], 'samu' => $row['samu'], 'modelo' => $row['modelo']]) ?>)">
                                            <td class="severity-bg <?= $severityClass ?>">
                                                <i class="fas fa-thermometer-half"></i>
                                                <?= htmlspecialchars($row['severidade']) ?>
                                            </td>
                                            <td><i class="fas fa-bus"></i> <?= htmlspecialchars($row['modelo'] ?? 'N/A') ?></td>
                                            <td><i class="fas fa-tag"></i> <?= htmlspecialchars($row['categoria']) ?></td>
                                            <td><?= $row['policia'] == 1 ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>' ?></td>
                                            <td><?= $row['samu'] == 1 ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>' ?></td>
                                            <td><?= $row['bombeiros'] == 1 ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>' ?></td>
                                            <td><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['localizacao']) ?></td>
                                            <td class="time-cell"><i class="fas fa-clock"></i> <?= $hora ?></td>
                                            <td><i class="fas fa-info-circle"></i> <?= htmlspecialchars($status) ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Detalhes mais compactos no canto direito -->
                    <div class="details-container" id="occurrence-details">
                        <h3><i class="fas fa-info-circle"></i> Detalhes da Ocorrência Selecionada</h3>
                         <p><i class="fas fa-bus detail-icon"></i> Modelo: <span>N/A</span></p>
                        <p><i class="fas fa-thermometer-half detail-icon"></i> Severidade: <span>N/A</span></p>
                       
                        <p><i class="fas fa-tag detail-icon"></i> Tipo: <span>N/A</span></p>
                        <p><i class="fas fa-map-marker-alt detail-icon"></i> Local: <span>N/A</span></p>
                        <p><i class="fas fa-shield-alt detail-icon"></i> Polícia: <span>N/A</span></p>
                        <p><i class="fas fa-ambulance detail-icon"></i> SAMU: <span>N/A</span></p>
                        <p><i class="fas fa-fire-extinguisher detail-icon"></i> Bombeiros: <span>N/A</span></p>
                        <p><i class="fas fa-clock detail-icon"></i> Hora: <span>N/A</span></p>
                        <p><i class="fas fa-info-circle detail-icon"></i> Status: <span>N/A</span></p>
                        <p><i class="fas fa-cogs detail-icon"></i> Ações: <span>N/A</span></p>
                    </div>
                </div>

                <!-- Nova seção inferior com mapa e container adicional lado a lado -->
                <div class="bottom-section">
                    <!-- Mapa expandido -->
                    <div class="map-container">
                        <iframe src="https://monitoramento.mobilesat.com.br/locator/index.html?t=4ebee7c35e2e2fbedde92f4b2611c141F0AA094FB415B295867B3BD93520050BB6566DD7" allowfullscreen></iframe>
                    </div>

                    <!-- Container adicional para conteúdo futuro -->
                    <div class="future-content-container">
                        <i class="fas fa-cogs"></i>
                        <h3>Área em Desenvolvimento</h3>
                        <p>Este espaço será utilizado para futuras funcionalidades e recursos adicionais do sistema de monitoramento.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.width = Math.random() * 4 + 2 + 'px';
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
                        tr.setAttribute('onclick', `selectOccurrence(${row.id}, this, ${JSON.stringify({ policia: row.policia, bombeiros: row.bombeiros, samu: row.samu, modelo: row.modelo })})`);
                        
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

                        tr.innerHTML = 
                        `
                          <td><i class="fas fa-bus"></i> ${row.modelo || 'N/A'}</td>
                            <td class="severity-bg ${severityClass}">
                                <i class="fas fa-thermometer-half"></i>
                                ${row.severidade}
                            </td>
                          
                            <td><i class="fas fa-tag"></i> ${row.categoria}</td>
                          <td><i class="fas fa-map-marker-alt"></i> ${row.localizacao}</td>
                            <td class="time-cell ${shouldBlink && status !== 'resolvido' ? 'time-blink' : ''}"><i class="fas fa-clock"></i> ${hora}</td>
                            
                              <td>${row.policia == 1 ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'}</td>
                            <td>${row.samu == 1 ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'}</td>
                            <td>${row.bombeiros == 1 ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'}</td>
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

        setInterval(updateTable, 2000);
        setInterval(updateStats, 2000);

        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            animateCounters();
            updateTable();
            updateStats();
        });

        function selectOccurrence(id, row, emergencyServices) {
            document.querySelectorAll('#accidents-table tr').forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');

            const rowData = row.cells;
            const details = {
                severidade: rowData[0].textContent.trim(),
                modelo: rowData[1].textContent.trim(),
                tipo: rowData[2].textContent.trim(),
                policia: emergencyServices.policia ? 'Sim' : 'Não',
                samu: emergencyServices.samu ? 'Sim' : 'Não',
                bombeiros: emergencyServices.bombeiros ? 'Sim' : 'Não',
                local: rowData[6].textContent.trim(),
                hora: rowData[7].textContent.trim(),
                status: rowData[8].textContent.trim(),
                acoes: rowData[8].textContent.trim() === 'em andamento' ? 'Ações em andamento' : 'Resolvido'
            };

            const detailsDiv = document.getElementById('occurrence-details');
            detailsDiv.innerHTML = `
                <h3><i class="fas fa-info-circle"></i> Detalhes da Ocorrência Selecionada</h3>
                <p><i class="fas fa-thermometer-half detail-icon"></i> Severidade: <span>${details.severidade}</span></p>
                <p><i class="fas fa-bus detail-icon"></i> Modelo: <span>${details.modelo}</span></p>
                <p><i class="fas fa-tag detail-icon"></i> Tipo: <span>${details.tipo}</span></p>
                <p><i class="fas fa-map-marker-alt detail-icon"></i> Local: <span>${details.local}</span></p>
                <p><i class="fas fa-shield-alt detail-icon"></i> Polícia: <span>${details.policia}</span></p>
                <p><i class="fas fa-ambulance detail-icon"></i> SAMU: <span>${details.samu}</span></p>
                <p><i class="fas fa-fire-extinguisher detail-icon"></i> Bombeiros: <span>${details.bombeiros}</span></p>
                <p><i class="fas fa-clock detail-icon"></i> Hora: <span>${details.hora}</span></p>
                <p><i class="fas fa-info-circle detail-icon"></i> Status: <span>${details.status}</span></p>
                <p><i class="fas fa-cogs detail-icon"></i> Ações: <span>${details.acoes}</span></p>
            `;
        }
    </script>

    <?php $conn->close(); ?>
</body>
</html>