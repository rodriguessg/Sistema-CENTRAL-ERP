<?php
    session_start();

    // Configuração do banco de dados
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'gm_sicbd';

    // Verifica se é uma requisição AJAX para dados
    if (isset($_GET['api']) && $_GET['api'] === 'data') {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, must-revalidate');
        
        $conn = new mysqli($host, $user, $password, $dbname);
        
        if ($conn->connect_error) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Erro na conexão com o banco']);
            exit;
        }
        
        $conn->set_charset('utf8mb4');
        
        // Pega os filtros
        $periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'diario';
        $ano = isset($_GET['ano']) ? intval($_GET['ano']) : date('Y');
        $mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('m');
        $dia = isset($_GET['dia']) ? intval($_GET['dia']) : date('d');
        $tipoFuncionario = isset($_GET['tipo_funcionario']) ? $_GET['tipo_funcionario'] : 'todos';
        
        // Monta a condição WHERE baseada no período
        $whereClause = "";
        if ($periodo === 'diario') {
            $whereClause = "WHERE DATE(data) = '$ano-$mes-$dia'";
        } elseif ($periodo === 'mensal') {
            $whereClause = "WHERE YEAR(data) = $ano AND MONTH(data) = $mes";
        } elseif ($periodo === 'anual') {
            $whereClause = "WHERE YEAR(data) = $ano";
        }
        
        $response = [];
        
        // KPIs
        $sql = "SELECT 
                    COUNT(DISTINCT bonde) as total_bondes,
                    COUNT(*) as viagens_realizadas,
                    SUM(pagantes) as passageiros_pagantes,
                    SUM(moradores) as moradores,
                    SUM(grat_pcd_idoso) as gratuidades_pcd,
                    SUM(gratuidade) as gratuidades,
                    SUM(passageiros) as total_passageiros
                FROM viagens 
                $whereClause";
        
        $result = $conn->query($sql);
        $kpis = $result->fetch_assoc();
        $response['kpis'] = $kpis;
        
        // Bondes com maior performance
        $sql = "SELECT bonde, COUNT(*) as num_viagens 
                FROM viagens 
                $whereClause 
                GROUP BY bonde 
                ORDER BY num_viagens DESC 
                LIMIT 10";
        $result = $conn->query($sql);
        $bondes_performance = [];
        while ($row = $result->fetch_assoc()) {
            $bondes_performance[] = $row;
        }
        $response['bondes_performance'] = $bondes_performance;
        
        $sql = "SELECT 
                    SUM(pagantes) as pagantes,
                    SUM(moradores) as moradores,
                    SUM(grat_pcd_idoso) as grat_pcd_idoso,
                    SUM(gratuidade) as gratuidade
                FROM viagens 
                $whereClause";
        $result = $conn->query($sql);
        $distribuicao = $result->fetch_assoc();
        $response['distribuicao'] = $distribuicao;
        
        // Padrão semanal de viagens
        $sql = "SELECT 
                    DAYOFWEEK(data) as dia_semana,
                    COUNT(*) as num_viagens
                FROM viagens 
                $whereClause 
                GROUP BY DAYOFWEEK(data)
                ORDER BY DAYOFWEEK(data)";
        $result = $conn->query($sql);
        $padrao_semanal = [];
        while ($row = $result->fetch_assoc()) {
            $padrao_semanal[] = $row;
        }
        $response['padrao_semanal'] = $padrao_semanal;
        
        // Fluxo de passageiros por horário
        $sql = "SELECT 
                    HOUR(hora) as hora,
                    SUM(passageiros) as total_passageiros
                FROM viagens 
                $whereClause 
                GROUP BY HOUR(hora)
                ORDER BY HOUR(hora)";
        $result = $conn->query($sql);
        $fluxo_horario = [];
        while ($row = $result->fetch_assoc()) {
            $fluxo_horario[] = $row;
        }
        $response['fluxo_horario'] = $fluxo_horario;
        
        // Quantidade de passageiros por mês (apenas para visão anual)
        if ($periodo === 'anual') {
            $sql = "SELECT 
                        MONTH(data) as mes,
                        SUM(passageiros) as total_passageiros
                    FROM viagens 
                    WHERE YEAR(data) = $ano
                    GROUP BY MONTH(data)
                    ORDER BY MONTH(data)";
            $result = $conn->query($sql);
            $passageiros_mes = [];
            while ($row = $result->fetch_assoc()) {
                $passageiros_mes[] = $row;
            }
            $response['passageiros_mes'] = $passageiros_mes;
            
            $sql = "SELECT 
                        DAY(data) as dia,
                        MONTH(data) as mes,
                        SUM(passageiros) as total_passageiros
                    FROM viagens 
                    WHERE YEAR(data) = $ano
                    GROUP BY DAY(data), MONTH(data)
                    ORDER BY MONTH(data), DAY(data)";
            $result = $conn->query($sql);
            $passageiros_diarios = [];
            while ($row = $result->fetch_assoc()) {
                $passageiros_diarios[] = $row;
            }
            $response['passageiros_diarios'] = $passageiros_diarios;
        }
        
        // Viagens por maquinista e agente
        if ($tipoFuncionario === 'maquinistas') {
            $sql = "SELECT 
                        maquinista as nome,
                        'Maquinista' as tipo,
                        COUNT(*) as num_viagens
                    FROM viagens 
                    $whereClause 
                    GROUP BY maquinista
                    ORDER BY num_viagens DESC
                    LIMIT 10";
        } elseif ($tipoFuncionario === 'agentes') {
            $sql = "SELECT 
                        agente as nome,
                        'Agente' as tipo,
                        COUNT(*) as num_viagens
                    FROM viagens 
                    $whereClause 
                    GROUP BY agente
                    ORDER BY num_viagens DESC
                    LIMIT 10";
        } else {
            $sql = "SELECT 
                        maquinista,
                        agente,
                        COUNT(*) as num_viagens
                    FROM viagens 
                    $whereClause 
                    GROUP BY maquinista, agente
                    ORDER BY num_viagens DESC
                    LIMIT 10";
        }
        
        $result = $conn->query($sql);
        $viagens_funcionarios = [];
        while ($row = $result->fetch_assoc()) {
            $viagens_funcionarios[] = $row;
        }
        $response['viagens_funcionarios'] = $viagens_funcionarios;
        $response['tipo_funcionario'] = $tipoFuncionario;
        
        $conn->close();
        
        echo json_encode($response);
        exit;
    }

    // Conexão com o banco
    $conn = new mysqli($host, $user, $password, $dbname);

    // Verifica conexão
    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    // Data atual para consultas dinâmicas
    $current_year = date('Y');
    $current_month = date('m');
    $current_day = date('d');

    // Consultas para métricas gerais
    $total_bondes = 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM bondes");
    if ($result) {
        $row = $result->fetch_assoc();
        $total_bondes = $row['total'];
    }

    $total_acidentes = 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM acidentes");
    if ($result) {
        $row = $result->fetch_assoc();
        $total_acidentes = $row['total'];
    }

    $viagens_anual = 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM viagens WHERE YEAR(data) = $current_year");
    if ($result) {
        $row = $result->fetch_assoc();
        $viagens_anual = $row['total'];
    }

    $bondes_ativos = 0;
    $result = $conn->query("SELECT COUNT(*) as total FROM bondes WHERE id NOT IN (SELECT bonde_afetado FROM manutencoes WHERE status = 'Em Andamento')");
    if ($result) {
        $row = $result->fetch_assoc();
        $bondes_ativos = $row['total'];
    }

?>

<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Controle de Bondes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            
            --bg-primary: #0f0f23;
            --bg-secondary: #1a1a2e;
            --bg-card: #16213e;
            --bg-card-hover: #1e2749;
            
            --text-primary: #ffffff;
            --text-secondary: #b8c5d6;
            --text-muted: #8892b0;
            
            --border-color: #233554;
            --shadow-light: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 10px 25px rgba(0, 0, 0, 0.2);
            --shadow-heavy: 0 20px 40px rgba(0, 0, 0, 0.3);
            
            --border-radius: 12px;
            --border-radius-sm: 6px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.2) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 15, 35, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(102, 126, 234, 0.3);
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
        }

        .section {
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section h2 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
        }

        .section h2::before {
            content: '';
            width: 3px;
            height: 1.5rem;
            background: white;
            border-radius: 2px;
        }

        .section h2 i {
            font-size: 1.2rem;
            background: white;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header {
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #fff;
        }

        .real-time-clock {
            position: fixed;
            top: 10px;
            right: 20px;
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 12px;
            padding: 12px 20px;
            color: #e2e8f0;
            font-size: 14px;
            font-weight: 500;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .real-time-clock:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .real-time-clock i {
            margin-right: 8px;
            color: #3b82f6;
        }

        .realtime-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding: 15px 20px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
        }

        .realtime-info .status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #94a3b8;
        }

        .realtime-info .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .period-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            background: rgba(30, 41, 59, 0.5);
            padding: 10px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 14px;
            font-weight: 500;
            color: #94a3b8;
        }

        .period-select {
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            color: var(--text-primary);
            font-family: inherit;
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
            min-width: 150px;
        }

        .period-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .export-button {
            background: var(--primary-gradient);
            border: none;
            border-radius: var(--border-radius-sm);
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow-light);
            margin-left: auto;
        }

        .export-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .export-button:active {
            transform: translateY(0);
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            cursor: pointer;
            backdrop-filter: blur(10px);
            min-height: 120px;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: var(--transition);
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-heavy);
            border-color: rgba(102, 126, 234, 0.3);
            background: var(--bg-card-hover);
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .card-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            background: var(--primary-gradient);
            box-shadow: var(--shadow-light);
        }

        .card h3 {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .charts-grid2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .chart-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            height: 320px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            transition: var(--transition);
            cursor: pointer;
        }

        .chart-card:hover {
            transform: scale(1.02) translateY(-2px);
            box-shadow: var(--shadow-heavy);
            border-color: rgba(102, 126, 234, 0.4);
            z-index: 10;
        }

        .chart-card h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-card h3 i {
            color: #667eea;
            font-size: 0.9rem;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-header h3 {
            margin-bottom: 0;
        }

        .chart-filter {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 13px;
            cursor: pointer;
            transition: var(--transition);
        }

        .chart-filter:focus {
            outline: none;
            border-color: #667eea;
        }

        .chart-container {
            flex: 1;
            position: relative;
            min-height: 0;
        }

        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            backdrop-filter: blur(10px);
            transition: var(--transition);
            min-height: 280px;
        }

        .table-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .table-card h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-container {
            overflow-x: auto;
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--border-color);
            max-height: 200px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        th {
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: 0.75rem 0.5rem;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-size: 0.7rem;
            border-bottom: 2px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1;
        }

        td {
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
            transition: var(--transition);
        }

        tr:hover td {
            background: var(--bg-card-hover);
            color: var(--text-primary);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.5rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .severity-low {
            color: #10b981;
            font-weight: 600;
        }

        .severity-medium {
            color: #f59e0b;
            font-weight: 600;
        }

        .severity-high {
            color: #ef4444;
            font-weight: 600;
        }

        /* Estilos para a tabela de análise anual */
        .annual-analysis-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            backdrop-filter: blur(10px);
            transition: var(--transition);
            display: none;
        }

        .annual-analysis-card.active {
            display: block;
        }

        .annual-table-container {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 600px;
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--border-color);
        }

        .annual-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
        }

        .annual-table th {
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: 0.5rem;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-size: 0.7rem;
            border: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .annual-table th:first-child {
            left: 0;
            z-index: 3;
        }

        .annual-table td {
            padding: 0.5rem;
            text-align: center;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .annual-table td:first-child {
            background: var(--bg-secondary);
            font-weight: 600;
            position: sticky;
            left: 0;
            z-index: 1;
        }

        .annual-table tr:hover td {
            background: var(--bg-card-hover);
        }

        .annual-table td.absolute-max {
            background: rgba(234, 179, 8, 0.3) !important;
            color: #fbbf24;
            font-weight: 700;
            border: 2px solid #fbbf24;
        }

        .annual-table td.monthly-max {
            background: rgba(239, 68, 68, 0.3) !important;
            color: #ef4444;
            font-weight: 700;
            border: 2px solid #ef4444;
        }

        .legend-container {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(30, 41, 59, 0.5);
            border-radius: var(--border-radius-sm);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
        }

        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .legend-box.yellow {
            background: rgba(234, 179, 8, 0.3);
            border: 2px solid #fbbf24;
        }

        .legend-box.red {
            background: rgba(239, 68, 68, 0.3);
            border: 2px solid #ef4444;
        }

        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-card {
                height: 350px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 0.75rem;
            }
            
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .charts-grid2 {
                grid-template-columns: 1fr;
            }
            
            .period-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .export-button {
                width: 100%;
                justify-content: center;
                margin-left: 0;
            }
            
            .chart-card {
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
    </style>
</head>
<body>
    <div class="real-time-clock">
        <i class="fas fa-clock"></i>
        <span id="realtime-clock">Carregando...</span>
    </div>

    <div class="caderno" id="dashboard-content">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Dashboard - Sistema de Controle de Bondes</h1>

            <div class="cards-grid" id="kpis">
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon"><i class="fas fa-spinner fa-spin"></i></div>
                    </div>
                    <h3>Carregando...</h3>
                    <div class="card-value">...</div>
                </div>
            </div>
            
            <div class="realtime-info" style="display:none;">
                <div class="status">
                    <span class="status-dot"></span>
                    <span>Atualização automática a cada 30 segundos</span>
                </div>
            </div>
            
            <div class="period-controls">
                <div class="filter-group">
                    <label for="periodo">Período</label>
                    <select id="periodo" class="period-select" onchange="updateFilters()">
                        <option value="diario">Diário</option>
                        <option value="mensal">Mensal</option>
                        <option value="anual">Anual</option>
                    </select>
                </div>
                
                <div class="filter-group" id="filter-ano">
                    <label for="ano">Ano</label>
                    <select id="ano" class="period-select" onchange="loadData()">
                        <?php
                        $currentYear = date('Y');
                        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                            echo "<option value='$y'>$y</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-group" id="filter-mes" style="display: none;">
                    <label for="mes">Mês</label>
                    <select id="mes" class="period-select" onchange="loadData()">
                        <option value="1">Janeiro</option>
                        <option value="2">Fevereiro</option>
                        <option value="3">Março</option>
                        <option value="4">Abril</option>
                        <option value="5">Maio</option>
                        <option value="6">Junho</option>
                        <option value="7">Julho</option>
                        <option value="8">Agosto</option>
                        <option value="9" selected>Setembro</option>
                        <option value="10">Outubro</option>
                        <option value="11">Novembro</option>
                        <option value="12">Dezembro</option>
                    </select>
                </div>
                
                <div class="filter-group" id="filter-dia" style="display: none;">
                    <label for="dia">Dia</label>
                    <select id="dia" class="period-select" onchange="loadData()">
                        <?php
                        for ($d = 1; $d <= 31; $d++) {
                            $selected = ($d == date('d')) ? 'selected' : '';
                            echo "<option value='$d' $selected>$d</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <button class="export-button" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf"></i>
                    <span>Exportar PDF</span>
                </button>
            </div>
        </div>
        
         Nova seção de análise anual de passageiros 
        <div class="section" id="annual-analysis-section">
            <h2><i class="fas fa-table"></i> Análise Anual de Passageiros por Dia</h2>
            
            <div class="annual-analysis-card" id="annualAnalysisCard">
                <div class="legend-container">
                    <div class="legend-item">
                        <div class="legend-box yellow"></div>
                        <span>Recorde Absoluto do Ano</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box red"></div>
                        <span>Recorde Mensal do Dia</span>
                    </div>
                </div>
                
                <div class="annual-table-container">
                    <table class="annual-table" id="annualTable">
                        <thead>
                            <tr>
                                <th>Dia</th>
                                <th>JAN</th>
                                <th>FEV</th>
                                <th>MAR</th>
                                <th>ABR</th>
                                <th>MAI</th>
                                <th>JUN</th>
                                <th>JUL</th>
                                <th>AGO</th>
                                <th>SET</th>
                                <th>OUT</th>
                                <th>NOV</th>
                                <th>DEZ</th>
                            </tr>
                        </thead>
                        <tbody id="annualTableBody">
                             Será preenchido via JavaScript 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2><i class="fas fa-chart-bar"></i> Análise Avançada de Operações</h2>
            
            <div class="charts-grid">
                <div class="chart-card">
                    <h3><i class="fas fa-trophy"></i> Bondes com Maior Performance</h3>
                    <div class="chart-container">
                        <canvas id="chartBondesPerformance"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <h3><i class="fas fa-users"></i> Distribuição de Passageiros</h3>
                    <div class="chart-container">
                        <canvas id="chartDistribuicao"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <h3><i class="fas fa-calendar-week"></i> Padrão Semanal de Viagens</h3>
                    <div class="chart-container">
                        <canvas id="chartPadraoSemanal"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <h3><i class="fas fa-clock"></i> Fluxo de Passageiros por Horário</h3>
                    <div class="chart-container">
                        <canvas id="chartFluxoHorario"></canvas>
                    </div>
                </div>
                
                <div class="chart-card" id="chartPassageirosMesCard" style="display: none;">
                    <h3><i class="fas fa-chart-line"></i> Quantidade de Passageiros por Mês</h3>
                    <div class="chart-container">
                        <canvas id="chartPassageirosMes"></canvas>
                    </div>
                </div>
                
                <div class="chart-card">
                    <div class="chart-header">
                        <h3><i class="fas fa-user-tie"></i> Viagens por Maquinista e Agente</h3>
                        <select id="tipoFuncionario" class="chart-filter" onchange="loadData()">
                            <option value="todos">Todos</option>
                            <option value="maquinistas">Apenas Maquinistas</option>
                            <option value="agentes">Apenas Agentes</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartViagensFunc"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2><i class="fas fa-cogs"></i> Detalhes Operacionais e Monitoramento</h2>
            <div class="charts-grid2">
                <div class="table-card">
                    <h3><i class="fas fa-exclamation-triangle"></i> Acidentes Recentes</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar"></i> Data</th>
                                    <th><i class="fas fa-file-alt"></i> Tipo</th>
                                    <th><i class="fas fa-map-marker-alt"></i> Localização</th>
                                    <th><i class="fas fa-thermometer-half"></i> Severidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_acidentes = "SELECT data_registro, categoria, localizacao, severidade FROM acidentes ORDER BY data_registro DESC LIMIT 5";
                                $result_acidentes = $conn->query($sql_acidentes);
                                if ($result_acidentes && $result_acidentes->num_rows > 0) {
                                    while ($row = $result_acidentes->fetch_assoc()) {
                                        $severityClass = '';
                                        switch(strtolower($row['severidade'])) {
                                            case 'baixa': $severityClass = 'severity-low'; break;
                                            case 'média': case 'media': $severityClass = 'severity-medium'; break;
                                            case 'alta': $severityClass = 'severity-high'; break;
                                            default: $severityClass = 'severity-medium';
                                        }
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data_registro']))) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['categoria']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['localizacao'] ?? 'N/A') . "</td>";
                                        echo "<td><span class='{$severityClass}'>" . htmlspecialchars($row['severidade']) . "</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' style='text-align: center; color: var(--text-muted);'><i class='fas fa-check-circle'></i> Nenhum acidente registrado</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-card">
                    <h3><i class="fas fa-history"></i> Viagens Recentes</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar"></i> Data</th>
                                    <th><i class="fas fa-train"></i> Bonde</th>
                                    <th><i class="fas fa-play"></i> Saída</th>
                                    <th><i class="fas fa-flag-checkered"></i> Destino</th>
                                    <th><i class="fas fa-users"></i> Passageiros</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_viagens = "SELECT v.data, v.bonde, v.saida, v.retorno as destino, (v.pagantes + v.gratuidade + v.moradores) as passageiros 
                                                FROM viagens v 
                                                ORDER BY v.data DESC LIMIT 5";
                                $result_viagens = $conn->query($sql_viagens);
                                if ($result_viagens && $result_viagens->num_rows > 0) {
                                    while ($row = $result_viagens->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data']))) . "</td>";
                                        echo "<td><span class='status-badge status-active'><i class='fas fa-train'></i> " . htmlspecialchars($row['bonde']) . "</span></td>";
                                        echo "<td>" . htmlspecialchars($row['saida']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['destino'] ?? 'N/A') . "</td>";
                                        echo "<td><strong>" . htmlspecialchars($row['passageiros']) . "</strong></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align: center; color: var(--text-muted);'><i class='fas fa-info-circle'></i> Nenhuma viagem registrada</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-card">
                    <h3><i class="fas fa-heartbeat"></i> Status da Frota</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-clock"></i> Última Atualização</th>
                                    <th><i class="fas fa-train"></i> Bonde</th>
                                    <th><i class="fas fa-signal"></i> Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_status = "SELECT id, modelo, ativo FROM bondes ORDER BY id ASC";
                                $result_status = $conn->query($sql_status);
                                if ($result_status && $result_status->num_rows > 0) {
                                    while ($row = $result_status->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . date('d/m/Y') . "</td>";
                                        echo "<td><strong>" . (empty($row['modelo']) ? 'Sem modelo' : htmlspecialchars($row['modelo'])) . "</strong></td>";
                                        echo "<td><span class='status-badge " . ($row['ativo'] == '1' ? 'status-active' : 'status-inactive') . "'>";
                                        echo "<i class='fas fa-" . ($row['ativo'] == '1' ? 'check-circle' : 'times-circle') . "'></i> ";
                                        echo ($row['ativo'] == '1' ? 'Operacional' : 'Inoperante-Manutenção') . "</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' style='text-align: center; color: var(--text-muted);'><i class='fas fa-info-circle'></i> Nenhum bonde cadastrado</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-card">
                    <h3><i class="fas fa-tools"></i> Manutenções Programadas</h3>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-calendar"></i> Data</th>
                                    <th><i class="fas fa-train"></i> Bonde</th>
                                    <th><i class="fas fa-wrench"></i> Tipo</th>
                                    <th><i class="fas fa-info-circle"></i> Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
                                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                    $stmt = $pdo->prepare("
                                        SELECT m.data, m.tipo, b.modelo AS bonde, m.status
                                        FROM manutencoes m
                                        JOIN bondes b ON m.bonde_afetado = b.id
                                        WHERE m.status IN ('pendente', 'em_andamento')
                                        ORDER BY m.data ASC
                                    ");
                                    $stmt->execute();
                                    $manutencoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($manutencoes) > 0) {
                                        foreach ($manutencoes as $manutencao) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($manutencao['data']) . "</td>";
                                            echo "<td>" . htmlspecialchars($manutencao['bonde']) . "</td>";
                                            echo "<td>" . htmlspecialchars($manutencao['tipo']) . "</td>";
                                            echo "<td>" . htmlspecialchars($manutencao['status']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' style='text-align: center; color: var(--text-muted); padding: 2rem;'>";
                                        echo "<i class='fas fa-check-circle' style='font-size: 2rem; margin-bottom: 1rem; display: block;'></i>";
                                        echo "Nenhuma manutenção programada</td></tr>";
                                    }
                                } catch (PDOException $e) {
                                    echo "<tr><td colspan='4' style='text-align: center; color: red; padding: 2rem;'>Erro: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let charts = {};
        let autoRefreshInterval;
        
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.1)';
        Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        
        function updateClock() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            const dateTimeString = now.toLocaleDateString('pt-BR', options);
            document.getElementById('realtime-clock').textContent = dateTimeString;
        }
        
        function startAutoRefresh() {
            autoRefreshInterval = setInterval(() => {
                loadData();
            }, 30000);
        }
        
        function updateFilters() {
            const periodo = document.getElementById('periodo').value;
            const filterMes = document.getElementById('filter-mes');
            const filterDia = document.getElementById('filter-dia');
            const chartPassageirosMesCard = document.getElementById('chartPassageirosMesCard');
            const annualAnalysisCard = document.getElementById('annualAnalysisCard');
            
            if (periodo === 'diario') {
                filterMes.style.display = 'flex';
                filterDia.style.display = 'flex';
                chartPassageirosMesCard.style.display = 'none';
                annualAnalysisCard.classList.remove('active');
            } else if (periodo === 'mensal') {
                filterMes.style.display = 'flex';
                filterDia.style.display = 'none';
                chartPassageirosMesCard.style.display = 'none';
                annualAnalysisCard.classList.remove('active');
            } else {
                filterMes.style.display = 'none';
                filterDia.style.display = 'none';
                chartPassageirosMesCard.style.display = 'block';
                annualAnalysisCard.classList.add('active');
            }
            
            loadData();
        }
        
        function loadData() {
            const periodo = document.getElementById('periodo').value;
            const ano = document.getElementById('ano').value;
            const mes = document.getElementById('mes').value;
            const dia = document.getElementById('dia').value;
            const tipoFuncionario = document.getElementById('tipoFuncionario').value;
            
            const url = `?api=data&periodo=${periodo}&ano=${ano}&mes=${mes}&dia=${dia}&tipo_funcionario=${tipoFuncionario}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    updateKPIs(data.kpis);
                    updateChartBondesPerformance(data.bondes_performance);
                    updateChartDistribuicao(data.distribuicao);
                    updateChartPadraoSemanal(data.padrao_semanal);
                    updateChartFluxoHorario(data.fluxo_horario);
                    if (periodo === 'anual') {
                        updateChartPassageirosMes(data.passageiros_mes);
                        updateAnnualTable(data.passageiros_diarios);
                    }
                    updateChartViagensFunc(data.viagens_funcionarios, data.tipo_funcionario);
                })
                .catch(error => {
                    console.error('Erro ao carregar dados:', error);
                });
        }
        
        function updateAnnualTable(data) {
            const tbody = document.getElementById('annualTableBody');
            tbody.innerHTML = '';
            
            // Criar matriz 31x12 (dias x meses)
            const matrix = Array(31).fill(null).map(() => Array(12).fill(0));
            
            // Preencher matriz com dados
            data.forEach(item => {
                const dia = parseInt(item.dia) - 1;
                const mes = parseInt(item.mes) - 1;
                const total = parseInt(item.total_passageiros);
                matrix[dia][mes] = total;
            });
            
            // Encontrar o máximo absoluto
            let absoluteMax = 0;
            matrix.forEach(row => {
                row.forEach(val => {
                    if (val > absoluteMax) absoluteMax = val;
                });
            });
            
            // Encontrar o máximo de cada dia (linha)
            const dayMaxValues = matrix.map(row => Math.max(...row));
            
            // Criar linhas da tabela
            for (let dia = 0; dia < 31; dia++) {
                const tr = document.createElement('tr');
                
                // Coluna do dia
                const tdDia = document.createElement('td');
                tdDia.textContent = dia + 1;
                tr.appendChild(tdDia);
                
                // Colunas dos meses
                for (let mes = 0; mes < 12; mes++) {
                    const td = document.createElement('td');
                    const value = matrix[dia][mes];
                    
                    if (value > 0) {
                        td.textContent = value.toLocaleString('pt-BR');
                        
                        // Destacar recorde absoluto em amarelo
                        if (value === absoluteMax) {
                            td.classList.add('absolute-max');
                        }
                        // Destacar recorde mensal do dia em vermelho
                        else if (value === dayMaxValues[dia] && value > 0) {
                            td.classList.add('monthly-max');
                        }
                    } else {
                        td.textContent = '-';
                        td.style.color = 'var(--text-muted)';
                    }
                    
                    tr.appendChild(td);
                }
                
                tbody.appendChild(tr);
            }
        }
        
        async function exportToPDF() {
            const button = event.target.closest('.export-button');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Gerando PDF...</span>';
            
            try {
                const { jsPDF } = window.jspdf;
                const content = document.getElementById('dashboard-content');
                
                const canvas = await html2canvas(content, {
                    scale: 1.5,
                    backgroundColor: '#0f172a',
                    logging: false,
                    windowWidth: content.scrollWidth,
                    windowHeight: content.scrollHeight
                });
                
                const imgData = canvas.toDataURL('image/png');
                
                const pdfWidth = 297;
                const pdfHeight = 210;
                
                const imgWidth = canvas.width;
                const imgHeight = canvas.height;
                const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight);
                
                const scaledWidth = imgWidth * ratio;
                const scaledHeight = imgHeight * ratio;
                
                const xOffset = (pdfWidth - scaledWidth) / 2;
                const yOffset = (pdfHeight - scaledHeight) / 2;
                
                const pdf = new jsPDF('landscape', 'mm', 'a4');
                pdf.addImage(imgData, 'PNG', xOffset, yOffset, scaledWidth, scaledHeight);
                
                const now = new Date();
                const filename = `dashboard_${now.getFullYear()}-${(now.getMonth()+1).toString().padStart(2,'0')}-${now.getDate().toString().padStart(2,'0')}_${now.getHours()}${now.getMinutes()}.pdf`;
                pdf.save(filename);
                
            } catch (error) {
                console.error('Erro ao gerar PDF:', error);
                alert('Erro ao gerar PDF. Por favor, tente novamente.');
            } finally {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-file-pdf"></i><span>Exportar PDF</span>';
            }
        }
        
        function updateKPIs(kpis) {
            const kpisContainer = document.getElementById('kpis');
            
            const kpiData = [
                { title: 'Total de Bondes', value: kpis.total_bondes, icon: 'fa-train', color: '#3b82f6' },
                { title: 'Viagens Realizadas', value: kpis.viagens_realizadas, icon: 'fa-route', color: '#10b981' },
                { title: 'Passageiros Pagantes', value: kpis.passageiros_pagantes, icon: 'fa-money-bill-wave', color: '#f59e0b' },
                { title: 'Moradores', value: kpis.moradores, icon: 'fa-home', color: '#06b6d4' },
                { title: 'Gratuidades PCD/Idoso', value: kpis.gratuidades_pcd, icon: 'fa-wheelchair', color: '#8b5cf6' },
                { title: 'Gratuidades', value: kpis.gratuidades, icon: 'fa-gift', color: '#ec4899' },
                { title: 'Total de Passageiros', value: kpis.total_passageiros, icon: 'fa-users', color: '#14b8a6' }
            ];
            
            kpisContainer.innerHTML = kpiData.map(kpi => `
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon" style="background: ${kpi.color}20; color: ${kpi.color};">
                            <i class="fas ${kpi.icon}"></i>
                        </div>
                    </div>
                    <h3>${kpi.title}</h3>
                    <div class="card-value">${Number(kpi.value).toLocaleString('pt-BR')}</div>
                </div>
            `).join('');
        }
        
        function updateChartBondesPerformance(data) {
            const ctx = document.getElementById('chartBondesPerformance');
            
            if (charts.bondesPerformance) {
                charts.bondesPerformance.destroy();
            }
            
            const colors = [
                'rgba(102, 126, 234, 0.7)',
                'rgba(139, 92, 246, 0.7)',
                'rgba(59, 130, 246, 0.7)',
                'rgba(20, 184, 166, 0.7)',
                'rgba(16, 185, 129, 0.7)',
                'rgba(236, 72, 153, 0.7)',
                'rgba(245, 158, 11, 0.7)',
                'rgba(239, 68, 68, 0.7)',
                'rgba(99, 102, 241, 0.7)',
                'rgba(168, 85, 247, 0.7)'
            ];
            
            charts.bondesPerformance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(d => d.bonde),
                    datasets: [{
                        label: 'Número de Viagens',
                        data: data.map(d => d.num_viagens),
                        backgroundColor: data.map((_, i) => colors[i % colors.length]),
                        borderColor: data.map((_, i) => colors[i % colors.length].replace('0.7', '1')),
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 30,
                            bottom: 10,
                            left: 10,
                            right: 10
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                },
                plugins: [{
                    afterDatasetsDraw: function(chart) {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const data = dataset.data[index];
                                ctx.fillStyle = '#fff';
                                ctx.font = 'bold 14px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';
                                ctx.fillText(data, bar.x, bar.y - 8);
                            });
                        });
                    }
                }]
            });
        }
        
        function updateChartDistribuicao(data) {
            const ctx = document.getElementById('chartDistribuicao');
            
            if (charts.distribuicao) {
                charts.distribuicao.destroy();
            }
            
            const total = parseInt(data.pagantes) + parseInt(data.moradores) + parseInt(data.grat_pcd_idoso) + parseInt(data.gratuidade);
            
            charts.distribuicao = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pagantes', 'Moradores', 'PCD/Idoso', 'Gratuidade'],
                    datasets: [{
                        data: [data.pagantes, data.moradores, data.grat_pcd_idoso, data.gratuidade],
                        backgroundColor: [
                            'rgba(102, 126, 234, 0.7)',
                            'rgba(20, 184, 166, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                            'rgba(236, 72, 153, 0.7)'
                        ],
                        borderColor: [
                            'rgba(102, 126, 234, 1)',
                            'rgba(20, 184, 166, 1)',
                            'rgba(139, 92, 246, 1)',
                            'rgba(236, 72, 153, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 15, font: { size: 12 } },
                            onClick: function(e, legendItem, legend) {
                                const index = legendItem.index;
                                const chart = legend.chart;
                                const meta = chart.getDatasetMeta(0);
                                
                                meta.data[index].hidden = !meta.data[index].hidden;
                                chart.update();
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${value.toLocaleString('pt-BR')} (${percentage}%)`;
                                }
                            }
                        }
                    }
                },
                plugins: [{
                    afterDatasetsDraw: function(chart) {
                        const ctx = chart.ctx;
                        const dataset = chart.data.datasets[0];
                        const meta = chart.getDatasetMeta(0);
                        
                        let visibleTotal = 0;
                        meta.data.forEach((arc, index) => {
                            if (!arc.hidden) {
                                visibleTotal += parseInt(dataset.data[index]);
                            }
                        });
                        
                        meta.data.forEach((arc, index) => {
                            if (!arc.hidden) {
                                const data = parseInt(dataset.data[index]);
                                const percentage = visibleTotal > 0 ? ((data / visibleTotal) * 100).toFixed(1) : 0;
                                
                                const midAngle = (arc.startAngle + arc.endAngle) / 2;
                                const x = arc.x + Math.cos(midAngle) * (arc.outerRadius * 0.7);
                                const y = arc.y + Math.sin(midAngle) * (arc.outerRadius * 0.7);
                                
                                ctx.fillStyle = '#fff';
                                ctx.font = 'bold 16px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                ctx.fillText(percentage + '%', x, y);
                            }
                        });
                    }
                }]
            });
        }
        
        function updateChartPadraoSemanal(data) {
            const ctx = document.getElementById('chartPadraoSemanal');
            
            if (charts.padraoSemanal) {
                charts.padraoSemanal.destroy();
            }
            
            const diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
            const totalViagens = data.reduce((sum, d) => sum + parseInt(d.num_viagens), 0);
            
            const dadosCompletos = Array(7).fill(0);
            data.forEach(d => {
                dadosCompletos[d.dia_semana - 1] = parseInt(d.num_viagens);
            });
            
            charts.padraoSemanal = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: diasSemana,
                    datasets: [{
                        label: 'Viagens',
                        data: dadosCompletos,
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 30,
                            bottom: 10,
                            left: 10,
                            right: 10
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                },
                plugins: [{
                    afterDatasetsDraw: function(chart) {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const data = dataset.data[index];
                                const percentage = totalViagens > 0 ? ((data / totalViagens) * 100).toFixed(1) : 0;
                                
                                ctx.fillStyle = '#fff';
                                ctx.font = 'bold 12px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';
                                ctx.fillText(percentage + '%', bar.x, bar.y - 8);
                            });
                        });
                    }
                }]
            });
        }
        
        function updateChartFluxoHorario(data) {
            const ctx = document.getElementById('chartFluxoHorario');
            
            if (charts.fluxoHorario) {
                charts.fluxoHorario.destroy();
            }
            
            const dadosCompletos = Array(24).fill(0);
            data.forEach(d => {
                dadosCompletos[parseInt(d.hora)] = parseInt(d.total_passageiros);
            });
            
            const labels = Array.from({length: 24}, (_, i) => `${i}h`);
            const maxValue = Math.max(...dadosCompletos);
            
            charts.fluxoHorario = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Passageiros',
                        data: dadosCompletos,
                        borderColor: 'rgba(139, 92, 246, 1)',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: dadosCompletos.map(val => val > 0 ? 5 : 3),
                        pointBackgroundColor: dadosCompletos.map(val => 
                            val === maxValue && val > 0 ? 'rgba(239, 68, 68, 1)' : 'rgba(139, 92, 246, 1)'
                        ),
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 35,
                            bottom: 10,
                            left: 10,
                            right: 10
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                },
                plugins: [{
                    afterDatasetsDraw: function(chart) {
                        const ctx = chart.ctx;
                        const dataset = chart.data.datasets[0];
                        const meta = chart.getDatasetMeta(0);
                        
                        meta.data.forEach((point, index) => {
                            const data = dataset.data[index];
                            if (data > 0) {
                                const isMax = data === maxValue;
                                
                                const textWidth = ctx.measureText(data.toString()).width;
                                const padding = 6;
                                const boxHeight = 22;
                                
                                ctx.fillStyle = 'rgba(15, 23, 42, 0.9)';
                                ctx.fillRect(
                                    point.x - textWidth/2 - padding,
                                    point.y - boxHeight - 8,
                                    textWidth + padding * 2,
                                    boxHeight
                                );
                                
                                ctx.fillStyle = isMax ? '#ef4444' : '#fff';
                                ctx.font = isMax ? 'bold 14px sans-serif' : 'bold 12px sans-serif';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                ctx.fillText(data.toString(), point.x, point.y - boxHeight/2 - 8);
                            }
                        });
                    }
                }]
            });
        }
        
        function updateChartPassageirosMes(data) {
            const ctx = document.getElementById('chartPassageirosMes');
            
            if (charts.passageirosMes) {
                charts.passageirosMes.destroy();
            }
            
            const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            
            const dadosCompletos = Array(12).fill(0);
            data.forEach(d => {
                dadosCompletos[parseInt(d.mes) - 1] = parseInt(d.total_passageiros);
            });
            
            const monthColors = [
                'rgba(102, 126, 234, 0.7)', 'rgba(139, 92, 246, 0.7)', 'rgba(59, 130, 246, 0.7)',
                'rgba(20, 184, 166, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(236, 72, 153, 0.7)',
                'rgba(245, 158, 11, 0.7)', 'rgba(239, 68, 68, 0.7)', 'rgba(99, 102, 241, 0.7)',
                'rgba(168, 85, 247, 0.7)', 'rgba(236, 72, 153, 0.7)', 'rgba(59, 130, 246, 0.7)'
            ];
            
            charts.passageirosMes = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: meses,
                    datasets: [{
                        label: 'Passageiros',
                        data: dadosCompletos,
                        backgroundColor: monthColors,
                        borderColor: monthColors.map(c => c.replace('0.7', '1')),
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 30,
                            bottom: 10,
                            left: 10,
                            right: 10
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                },
                plugins: [{
                    afterDatasetsDraw: function(chart) {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const data = dataset.data[index];
                                if (data > 0) {
                                    ctx.fillStyle = '#fff';
                                    ctx.font = 'bold 12px sans-serif';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    ctx.fillText(data.toLocaleString('pt-BR'), bar.x, bar.y - 8);
                                }
                            });
                        });
                    }
                }]
            });
        }
        
        function updateChartViagensFunc(data, tipoFuncionario) {
            const ctx = document.getElementById('chartViagensFunc');
            
            if (charts.viagensFunc) {
                charts.viagensFunc.destroy();
            }
            
            let labels;
            if (tipoFuncionario === 'maquinistas' || tipoFuncionario === 'agentes') {
                labels = data.map(d => d.nome);
            } else {
                labels = data.map(d => `${d.maquinista} / ${d.agente}`);
            }
            
            const colors = [
                'rgba(102, 126, 234, 0.7)', 'rgba(139, 92, 246, 0.7)', 'rgba(59, 130, 246, 0.7)',
                'rgba(20, 184, 166, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(236, 72, 153, 0.7)',
                'rgba(245, 158, 11, 0.7)', 'rgba(239, 68, 68, 0.7)', 'rgba(99, 102, 241, 0.7)',
                'rgba(168, 85, 247, 0.7)'
            ];
            
            charts.viagensFunc = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Viagens',
                        data: data.map(d => d.num_viagens),
                        backgroundColor: data.map((_, i) => colors[i % colors.length]),
                        borderColor: data.map((_, i) => colors[i % colors.length].replace('0.7', '1')),
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 10,
                            left: 10,
                            right: 50
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.1)' }
                        },
                        y: {
                            grid: { display: false }
                        }
                    }
                },
                plugins: [{
                    afterDatasetsDraw: function(chart) {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach((bar, index) => {
                                const data = dataset.data[index];
                                ctx.fillStyle = '#fff';
                                ctx.font = 'bold 12px sans-serif';
                                ctx.textAlign = 'left';
                                ctx.textBaseline = 'middle';
                                ctx.fillText(data, bar.x + 8, bar.y);
                            });
                        });
                    }
                }]
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            updateClock();
            setInterval(updateClock, 1000);
            updateFilters();
            startAutoRefresh();
        });
    </script>
</body>
</html>
