<?php
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
        
        // Pega o ano (obrigatório para análise anual)
        $ano = isset($_GET['ano']) ? intval($_GET['ano']) : date('Y');
        
        $response = [];
        
        // Dados para passageiros diários
        $sql = "SELECT 
                    DAY(data) as dia,
                    MONTH(data) as mes,
                    YEAR(data) as ano,
                    data,
                    DAYOFWEEK(data) as dia_semana,
                    SUM(passageiros) as total_passageiros
                FROM viagens 
                WHERE YEAR(data) = $ano
                GROUP BY data, DAY(data), MONTH(data), YEAR(data), DAYOFWEEK(data)
                ORDER BY MONTH(data), DAY(data)";
        $result = $conn->query($sql);
        $passageiros_diarios = [];
        while ($row = $result->fetch_assoc()) {
            $passageiros_diarios[] = $row;
        }
        $response['passageiros_diarios'] = $passageiros_diarios;
        
        // Calcular estatísticas mensais
        $estatisticas_mensais = [];
        
        for ($mes = 1; $mes <= 12; $mes++) {
            // Total de passageiros do mês
            $sql_total = "SELECT 
                            SUM(passageiros) as total,
                            COUNT(DISTINCT data) as dias_com_dados
                        FROM viagens 
                        WHERE YEAR(data) = $ano AND MONTH(data) = $mes";
            $result_total = $conn->query($sql_total);
            $row_total = $result_total->fetch_assoc();
            
            $total_passageiros = $row_total['total'] ?? 0;
            $dias_com_dados = $row_total['dias_com_dados'] ?? 0;
            
            // Número total de dias do mês
            $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
            
            // Contar dias úteis (segunda a sexta) no mês
            $dias_uteis_calendario = 0;
            for ($dia = 1; $dia <= $dias_no_mes; $dia++) {
                $timestamp = mktime(0, 0, 0, $mes, $dia, $ano);
                $dia_semana = date('N', $timestamp); // 1=segunda, 7=domingo
                if ($dia_semana >= 1 && $dia_semana <= 5) { // Segunda a sexta
                    $dias_uteis_calendario++;
                }
            }
            
            $mdu_mes = $dias_uteis_calendario > 0 ? round($total_passageiros / $dias_uteis_calendario) : 0;
            
            $md_mes = $dias_no_mes > 0 ? round($total_passageiros / $dias_no_mes) : 0;
            
            $estatisticas_mensais[$mes] = [
                'total' => $total_passageiros,
                'mdu_mes' => $mdu_mes,
                'md_mes' => $md_mes,
                'dias_com_dados' => $dias_com_dados,
                'dias_uteis_calendario' => $dias_uteis_calendario,
                'dias_no_mes' => $dias_no_mes
            ];
        }
        
        $acumulado_progressivo = [];
        $acumulado_total = 0;
        for ($mes = 1; $mes <= 12; $mes++) {
            $acumulado_total += $estatisticas_mensais[$mes]['total'];
            $acumulado_progressivo[$mes] = $acumulado_total;
        }
        
        $response['estatisticas_mensais'] = $estatisticas_mensais;
        $response['acumulado_progressivo'] = $acumulado_progressivo;
        
        // Recorde do Ano Parcial (maior dia de todo o ano)
        $sql_recorde_parcial = "SELECT 
                                    data,
                                    DAY(data) as dia,
                                    MONTH(data) as mes,
                                    SUM(passageiros) as total_passageiros
                                FROM viagens 
                                WHERE YEAR(data) = $ano
                                GROUP BY data
                                ORDER BY total_passageiros DESC
                                LIMIT 1";
        $result_recorde = $conn->query($sql_recorde_parcial);
        $recorde_parcial = $result_recorde->fetch_assoc();
        
        $response['recorde_parcial'] = $recorde_parcial;
        
        $sql_total_ano = "SELECT SUM(passageiros) as total_ano
                         FROM viagens 
                         WHERE YEAR(data) = $ano";
        $result_total_ano = $conn->query($sql_total_ano);
        $row_total_ano = $result_total_ano->fetch_assoc();
        
        $response['total_ano'] = $row_total_ano['total_ano'] ?? 0;

        $conn->close();
        
        echo json_encode($response);
        exit;
    }

    // Conexão com o banco para dados gerais
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }
    $conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análise Anual de Passageiros por Dia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            padding: 2rem;
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

        .annual-analysis-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            backdrop-filter: blur(10px);
            transition: var(--transition);
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

        /* Estilos para as linhas de estatísticas (MDU/MÊS e MD/MÊS) */
        .annual-table tr.stats-row td {
            background: rgba(102, 126, 234, 0.2) !important;
            font-weight: 700;
            color: #a5b4fc;
            border: 1px solid rgba(102, 126, 234, 0.4);
        }

        .annual-table tr.stats-row td:first-child {
            background: rgba(102, 126, 234, 0.3) !important;
            color: #c7d2fe;
        }

        .annual-table tr.stats-row:hover td {
            background: rgba(102, 126, 234, 0.3) !important;
        }

        /* Estilos para a linha de acumulado */
        .annual-table tr.accumulated-row td {
            background: rgba(34, 197, 94, 0.2) !important;
            font-weight: 700;
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.4);
        }

        .annual-table tr.accumulated-row td:first-child {
            background: rgba(34, 197, 94, 0.3) !important;
            color: #bbf7d0;
        }

        .annual-table tr.accumulated-row:hover td {
            background: rgba(34, 197, 94, 0.3) !important;
        }

        .legend-container {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: rgba(30, 41, 59, 0.5);
            border-radius: var(--border-radius-sm);
            flex-wrap: wrap;
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

        /* Estilos para os cards de estatísticas do ano */
        .stats-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.25rem;
            backdrop-filter: blur(10px);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
            border-color: #667eea;
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .stat-card-icon.trophy {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .stat-card-icon.total {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card-title {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-card-detail {
            font-size: 0.9rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-card-detail i {
            color: #667eea;
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

        @media print {
            body {
                background: white;
                color: black;
            }
            
            .export-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Análise Anual de Passageiros por Dia</h1>
            
            <div class="period-controls">
                <div class="filter-group">
                    <label for="ano">Ano</label>
                    <select id="ano" class="period-select" onchange="loadData()">
                        <?php
                        $currentYear = date('Y');
                        for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                            echo "<option value='$y' " . ($y == $currentYear ? 'selected' : '') . ">$y</option>";
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
        
        <div class="section" id="annual-analysis-section">
            <h2><i class="fas fa-table"></i> Análise Anual de Passageiros por Dia</h2>
            
            <div class="annual-analysis-card active" id="annualAnalysisCard">
                <div class="legend-container">
                    <div class="legend-item">
                        <div class="legend-box yellow"></div>
                        <span>Recorde Absoluto do Ano</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box red"></div>
                        <span>Recorde Mensal</span>
                    </div>
                </div>
                
                <div class="annual-table-container">
                    <table class="annual-table" id="annualTable">
                        <thead>
                            <tr>
                                <th>DIA / MÊS</th>
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
                            <!-- Será preenchido via JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- Cards de estatísticas do ano parcial -->
                <div class="stats-cards-container">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon trophy">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="stat-card-title">Recorde do Ano Parcial</div>
                        </div>
                        <div class="stat-card-value" id="recordeParcialValor">-</div>
                        <div class="stat-card-detail">
                            <i class="fas fa-calendar-day"></i>
                            <span id="recordeParcialData">-</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon total">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="stat-card-title">Total Acumulado do Ano</div>
                        </div>
                        <div class="stat-card-value" id="totalAnoValor">-</div>
                        <div class="stat-card-detail">
                            <i class="fas fa-info-circle"></i>
                            <span>Soma de todos os meses</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const mesesNomes = ['JAN', 'FEV', 'MAR', 'ABR', 'MAI', 'JUN', 'JUL', 'AGO', 'SET', 'OUT', 'NOV', 'DEZ'];
        
        function loadData() {
            const ano = document.getElementById('ano').value;
            
            const url = `?api=data&ano=${ano}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    updateAnnualTable(data.passageiros_diarios, data.estatisticas_mensais, data.acumulado_progressivo);
                    updateStatCards(data.recorde_parcial, data.total_ano);
                })
                .catch(error => {
                    console.error('Erro ao carregar dados:', error);
                });
        }
        
        function updateAnnualTable(data, estatisticas, acumuladoProgressivo) {
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
            
            // Encontrar o máximo absoluto do ano
            let absoluteMax = 0;
            matrix.forEach(row => {
                row.forEach(val => {
                    if (val > absoluteMax) absoluteMax = val;
                });
            });
            
            // Encontrar o máximo de cada mês (coluna)
            const monthMaxValues = Array(12).fill(0);
            for (let mes = 0; mes < 12; mes++) {
                for (let dia = 0; dia < 31; dia++) {
                    if (matrix[dia][mes] > monthMaxValues[mes]) {
                        monthMaxValues[mes] = matrix[dia][mes];
                    }
                }
            }
            
            // Criar linhas da tabela (dias 1-31)
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
                        // Destacar recorde mensal em vermelho
                        else if (value === monthMaxValues[mes] && value > 0) {
                            td.classList.add('monthly-max');
                        }
                    } else {
                        td.textContent = '';
                        td.style.color = 'var(--text-muted)';
                    }
                    
                    tr.appendChild(td);
                }
                
                tbody.appendChild(tr);
            }
            
            const trTotal = document.createElement('tr');
            trTotal.classList.add('stats-row');
            
            const tdTotalLabel = document.createElement('td');
            tdTotalLabel.textContent = 'TOTAL';
            tdTotalLabel.style.fontWeight = '700';
            trTotal.appendChild(tdTotalLabel);
            
            for (let mes = 1; mes <= 12; mes++) {
                const td = document.createElement('td');
                const totalMes = estatisticas[mes]?.total || 0;
                td.textContent = totalMes > 0 ? totalMes.toLocaleString('pt-BR') : '-';
                trTotal.appendChild(td);
            }
            
            tbody.appendChild(trTotal);
            
            const trMDU = document.createElement('tr');
            trMDU.classList.add('stats-row');
            
            const tdMDULabel = document.createElement('td');
            tdMDULabel.textContent = 'MDU/MÊS';
            tdMDULabel.style.fontWeight = '700';
            trMDU.appendChild(tdMDULabel);
            
            for (let mes = 1; mes <= 12; mes++) {
                const td = document.createElement('td');
                const mduMes = estatisticas[mes]?.mdu_mes || 0;
                td.textContent = mduMes > 0 ? mduMes.toLocaleString('pt-BR') : '-';
                trMDU.appendChild(td);
            }
            
            tbody.appendChild(trMDU);
            
            const trMD = document.createElement('tr');
            trMD.classList.add('stats-row');
            
            const tdMDLabel = document.createElement('td');
            tdMDLabel.textContent = 'MD/MÊS';
            tdMDLabel.style.fontWeight = '700';
            trMD.appendChild(tdMDLabel);
            
            for (let mes = 1; mes <= 12; mes++) {
                const td = document.createElement('td');
                const mdMes = estatisticas[mes]?.md_mes || 0;
                td.textContent = mdMes > 0 ? mdMes.toLocaleString('pt-BR') : '-';
                trMD.appendChild(td);
            }
            
            tbody.appendChild(trMD);
            
            const trAcumulado = document.createElement('tr');
            trAcumulado.classList.add('accumulated-row');
            
            const tdAcumuladoLabel = document.createElement('td');
            tdAcumuladoLabel.textContent = 'ACUMULADO';
            tdAcumuladoLabel.style.fontWeight = '700';
            trAcumulado.appendChild(tdAcumuladoLabel);
            
            for (let mes = 1; mes <= 12; mes++) {
                const td = document.createElement('td');
                const acumulado = acumuladoProgressivo[mes] || 0;
                td.textContent = acumulado > 0 ? acumulado.toLocaleString('pt-BR') : '-';
                trAcumulado.appendChild(td);
            }
            
            tbody.appendChild(trAcumulado);
        }
        
        function updateStatCards(recordeParcial, totalAno) {
            // Atualizar Recorde do Ano Parcial
            if (recordeParcial && recordeParcial.total_passageiros) {
                const valor = parseInt(recordeParcial.total_passageiros);
                const dia = parseInt(recordeParcial.dia);
                const mes = parseInt(recordeParcial.mes);
                const mesNome = mesesNomes[mes - 1];
                
                document.getElementById('recordeParcialValor').textContent = valor.toLocaleString('pt-BR');
                document.getElementById('recordeParcialData').textContent = `${dia}/${mesNome}`;
            } else {
                document.getElementById('recordeParcialValor').textContent = '-';
                document.getElementById('recordeParcialData').textContent = 'Sem dados';
            }
            
            // Atualizar Total Acumulado do Ano
            if (totalAno && totalAno > 0) {
                document.getElementById('totalAnoValor').textContent = parseInt(totalAno).toLocaleString('pt-BR');
            } else {
                document.getElementById('totalAnoValor').textContent = '-';
            }
        }
        
        async function exportToPDF() {
            const button = event.target.closest('.export-button');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Gerando PDF...</span>';
            
            try {
                const { jsPDF } = window.jspdf;
                const content = document.getElementById('annualAnalysisCard');
                
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
                const ano = document.getElementById('ano').value;
                const filename = `analise_anual_${ano}_${now.getFullYear()}-${(now.getMonth()+1).toString().padStart(2,'0')}-${now.getDate().toString().padStart(2,'0')}.pdf`;
                pdf.save(filename);
                
            } catch (error) {
                console.error('Erro ao gerar PDF:', error);
                alert('Erro ao gerar PDF. Por favor, tente novamente.');
            } finally {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-file-pdf"></i><span>Exportar PDF</span>';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });
    </script>
</body>
</html>
