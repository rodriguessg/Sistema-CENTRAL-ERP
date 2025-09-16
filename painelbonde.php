<?php
session_start();

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

// Adiciona depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
} else {
    die("Erro na consulta de total de bondes: " . $conn->error);
}

$total_acidentes = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM acidentes");
if ($result) {
    $row = $result->fetch_assoc();
    $total_acidentes = $row['total'];
} else {
    die("Erro na consulta de total de acidentes: " . $conn->error);
}

$total_viagens = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM viagens");
if ($result) {
    $row = $result->fetch_assoc();
    $total_viagens = $row['total'];
} else {
    die("Erro na consulta de total de viagens: " . $conn->error);
}

$bondes_ativos = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM bondes WHERE id NOT IN (SELECT bonde_afetado FROM manutencoes WHERE status = 'Em Andamento')");
if ($result) {
    $row = $result->fetch_assoc();
    $bondes_ativos = $row['total'];
} else {
    die("Erro na consulta de bondes ativos: " . $conn->error);
}

// Consultas para dados diários (hoje)
$viagens_hoje = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM viagens WHERE DATE(data) = CURDATE()");
if ($result) {
    $row = $result->fetch_assoc();
    $viagens_hoje = $row['total'];
    error_log("Viagens Hoje: " . $viagens_hoje);
} else {
    die("Erro na consulta de viagens hoje: " . $conn->error);
}

$pagantes_hoje = 0;
$result = $conn->query("SELECT COALESCE(SUM(pagantes), 0) as total FROM viagens WHERE DATE(data) = CURDATE()");
if ($result) {
    $row = $result->fetch_assoc();
    $pagantes_hoje = $row['total'];
} else {
    die("Erro na consulta de pagantes diário: " . $conn->error);
}

$moradores_hoje = 0;
$result = $conn->query("SELECT COALESCE(SUM(moradores), 0) as total FROM viagens WHERE DATE(data) = CURDATE()");
if ($result) {
    $row = $result->fetch_assoc();
    $moradores_hoje = $row['total'];
} else {
    die("Erro na consulta de moradores diário: " . $conn->error);
}

$gratuidade_hoje = 0;
$result = $conn->query("SELECT COALESCE(SUM(gratuidade), 0) as total FROM viagens WHERE DATE(data) = CURDATE()");
if ($result) {
    $row = $result->fetch_assoc();
    $gratuidade_hoje = $row['total'];
} else {
    die("Erro na consulta de gratuidade diário: " . $conn->error);
}

$passageiros_hoje = 0;
$result = $conn->query("SELECT COALESCE(SUM(pagantes + moradores + gratuidade), 0) as total FROM viagens WHERE DATE(data) = CURDATE()");
if ($result) {
    $row = $result->fetch_assoc();
    $passageiros_hoje = $row['total'];
} else {
    die("Erro na consulta de passageiros diário: " . $conn->error);
}

// Consultas para dados mensais
$pagantes_mes_atual = 0;
$result = $conn->query("SELECT COALESCE(SUM(pagantes), 0) as total FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month");
if ($result) {
    $row = $result->fetch_assoc();
    $pagantes_mes_atual = $row['total'];
} else {
    die("Erro na consulta de pagantes mensal: " . $conn->error);
}

$moradores_mes_atual = 0;
$result = $conn->query("SELECT COALESCE(SUM(moradores), 0) as total FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month");
if ($result) {
    $row = $result->fetch_assoc();
    $moradores_mes_atual = $row['total'];
} else {
    die("Erro na consulta de moradores mensal: " . $conn->error);
}

$gratuidade_mes_atual = 0;
$result = $conn->query("SELECT COALESCE(SUM(gratuidade), 0) as total FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month");
if ($result) {
    $row = $result->fetch_assoc();
    $gratuidade_mes_atual = $row['total'];
} else {
    die("Erro na consulta de gratuidade mensal: " . $conn->error);
}

$viagens_mes_atual = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month");
if ($result) {
    $row = $result->fetch_assoc();
    $viagens_mes_atual = $row['total'];
} else {
    die("Erro na consulta de viagens do mês atual: " . $conn->error);
}

$passageiros_mes_atual = 0;
$result = $conn->query("SELECT COALESCE(SUM(pagantes + moradores + gratuidade), 0) as total FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month");
if ($result) {
    $row = $result->fetch_assoc();
    $passageiros_mes_atual = $row['total'];
} else {
    die("Erro na consulta de passageiros mensal: " . $conn->error);
}

// Consultas para dados anuais
$pagantes_anual = 0;
$result = $conn->query("SELECT COALESCE(SUM(pagantes), 0) as total FROM viagens WHERE YEAR(data) = $current_year");
if ($result) {
    $row = $result->fetch_assoc();
    $pagantes_anual = $row['total'];
} else {
    die("Erro na consulta de pagantes anual: " . $conn->error);
}

$moradores_anual = 0;
$result = $conn->query("SELECT COALESCE(SUM(moradores), 0) as total FROM viagens WHERE YEAR(data) = $current_year");
if ($result) {
    $row = $result->fetch_assoc();
    $moradores_anual = $row['total'];
} else {
    die("Erro na consulta de moradores anual: " . $conn->error);
}

$gratuidade_anual = 0;
$result = $conn->query("SELECT COALESCE(SUM(gratuidade), 0) as total FROM viagens WHERE YEAR(data) = $current_year");
if ($result) {
    $row = $result->fetch_assoc();
    $gratuidade_anual = $row['total'];
} else {
    die("Erro na consulta de gratuidade anual: " . $conn->error);
}

$passageiros_anual = 0;
$result = $conn->query("SELECT COALESCE(SUM(pagantes + moradores + gratuidade), 0) as total FROM viagens WHERE YEAR(data) = $current_year");
if ($result) {
    $row = $result->fetch_assoc();
    $passageiros_anual = $row['total'];
} else {
    die("Erro na consulta de passageiros anual: " . $conn->error);
}

// Consultas para bondes com mais viagens
$bondes_viagens_diario = [];
$result = $conn->query("SELECT bonde, COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE DATE(data) = CURDATE() 
                        GROUP BY bonde 
                        ORDER BY total_viagens DESC 
                        LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bondes_viagens_diario[] = [
            'bonde' => 'Bonde ' . $row['bonde'],
            'total_viagens' => $row['total_viagens']
        ];
    }
    error_log("Bondes com mais viagens (diário): " . json_encode($bondes_viagens_diario));
} else {
    die("Erro na consulta de bondes com mais viagens (diário): " . $conn->error);
}

$bondes_viagens_mensal = [];
$result = $conn->query("SELECT bonde, COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month 
                        GROUP BY bonde 
                        ORDER BY total_viagens DESC 
                        LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bondes_viagens_mensal[] = [
            'bonde' => 'Bonde ' . $row['bonde'],
            'total_viagens' => $row['total_viagens']
        ];
    }
    error_log("Bondes com mais viagens (mensal): " . json_encode($bondes_viagens_mensal));
} else {
    die("Erro na consulta de bondes com mais viagens (mensal): " . $conn->error);
}

$bondes_viagens_anual = [];
$result = $conn->query("SELECT bonde, MONTH(data) as mes, COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE YEAR(data) = $current_year 
                        GROUP BY bonde, MONTH(data)");
if ($result) {
    $viagens_por_bonde_mes = [];
    while ($row = $result->fetch_assoc()) {
        $bonde = 'Bonde ' . $row['bonde'];
        $mes = (int)$row['mes'];
        $total_viagens = (int)$row['total_viagens'];
        if (!isset($viagens_por_bonde_mes[$bonde])) {
            $viagens_por_bonde_mes[$bonde] = array_fill(1, 12, 0);
        }
        $viagens_por_bonde_mes[$bonde][$mes] = $total_viagens;
    }
    $totais_por_bonde = [];
    foreach ($viagens_por_bonde_mes as $bonde => $meses) {
        $totais_por_bonde[$bonde] = array_sum($meses);
    }
    arsort($totais_por_bonde);
    $top_bondes = array_slice($totais_por_bonde, 0, 5, true);
    foreach ($top_bondes as $bonde => $total) {
        $bondes_viagens_anual[] = [
            'bonde' => $bonde,
            'viagens_por_mes' => array_values($viagens_por_bonde_mes[$bonde])
        ];
    }
    error_log("Bondes com mais viagens (anual): " . json_encode($bondes_viagens_anual));
} else {
    die("Erro na consulta de bondes com mais viagens (anual): " . $conn->error);
}

// Consultas para viagens por dia da semana
$viagens_por_dia_semana_diario = array_fill(0, 7, 0);
$result = $conn->query("SELECT WEEKDAY(data) as dia_semana, COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE DATE(data) = CURDATE() 
                        GROUP BY WEEKDAY(data)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dia_semana = (int)$row['dia_semana'];
        $viagens_por_dia_semana_diario[$dia_semana] = (int)$row['total_viagens'];
    }
    error_log("Viagens por dia da semana (diário): " . json_encode($viagens_por_dia_semana_diario));
} else {
    die("Erro na consulta de viagens por dia da semana (diário): " . $conn->error);
}

$viagens_por_dia_semana_mensal = array_fill(0, 7, 0);
$result = $conn->query("SELECT WEEKDAY(data) as dia_semana, COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month 
                        GROUP BY WEEKDAY(data)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dia_semana = (int)$row['dia_semana'];
        $viagens_por_dia_semana_mensal[$dia_semana] = (int)$row['total_viagens'];
    }
    error_log("Viagens por dia da semana (mensal): " . json_encode($viagens_por_dia_semana_mensal));
} else {
    die("Erro na consulta de viagens por dia da semana (mensal): " . $conn->error);
}

$viagens_por_dia_semana_anual = array_fill(0, 7, 0);
$result = $conn->query("SELECT WEEKDAY(data) as dia_semana, COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE YEAR(data) = $current_year 
                        GROUP BY WEEKDAY(data)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dia_semana = (int)$row['dia_semana'];
        $viagens_por_dia_semana_anual[$dia_semana] = (int)$row['total_viagens'];
    }
    error_log("Viagens por dia da semana (anual): " . json_encode($viagens_por_dia_semana_anual));
} else {
    die("Erro na consulta de viagens por dia da semana (anual): " . $conn->error);
}

// Consultas para fluxo de passageiros por horário
$passageiros_por_horario_diario = array_fill(6, 15, 0);
$result = $conn->query("SELECT HOUR(created_at) as hora, COALESCE(SUM(pagantes + moradores + gratuidade), 0) as total_passageiros 
                        FROM viagens 
                        WHERE DATE(created_at) = CURDATE() AND HOUR(created_at) BETWEEN 6 AND 20 
                        GROUP BY HOUR(created_at)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $hora = (int)$row['hora'];
        $passageiros_por_horario_diario[$hora] = (int)$row['total_passageiros'];
    }
    error_log("Passageiros por horário (diário): " . json_encode($passageiros_por_horario_diario));
} else {
    die("Erro na consulta de passageiros por horário (diário): " . $conn->error);
}

$passageiros_por_horario_mensal = array_fill(6, 15, 0);
$result = $conn->query("SELECT HOUR(created_at) as hora, COALESCE(SUM(pagantes + moradores + gratuidade), 0) as total_passageiros 
                        FROM viagens 
                        WHERE YEAR(created_at) = $current_year AND MONTH(created_at) = $current_month AND HOUR(created_at) BETWEEN 6 AND 20 
                        GROUP BY HOUR(created_at)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $hora = (int)$row['hora'];
        $passageiros_por_horario_mensal[$hora] = (int)$row['total_passageiros'];
    }
    error_log("Passageiros por horário (mensal): " . json_encode($passageiros_por_horario_mensal));
} else {
    die("Erro na consulta de passageiros por horário (mensal): " . $conn->error);
}

$passageiros_por_horario_anual = array_fill(6, 15, 0);
$result = $conn->query("SELECT HOUR(created_at) as hora, COALESCE(SUM(pagantes + moradores + gratuidade), 0) as total_passageiros 
                        FROM viagens 
                        WHERE YEAR(created_at) = $current_year AND HOUR(created_at) BETWEEN 6 AND 20 
                        GROUP BY HOUR(created_at)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $hora = (int)$row['hora'];
        $passageiros_por_horario_anual[$hora] = (int)$row['total_passageiros'];
    }
    error_log("Passageiros por horário (anual): " . json_encode($passageiros_por_horario_anual));
} else {
    die("Erro na consulta de passageiros por horário (anual): " . $conn->error);
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Bonde</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
    <link rel="stylesheet" href="src/bonde/style/painelbonde.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- jsPDF CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Fallback para versão local do jsPDF se o CDN falhar
        if (typeof jspdf === 'undefined') {
            document.write('<script src="/src/js/jspdf.umd.min.js"><\/script>');
        }
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f6f8;
            color: #333;
            overflow-x: hidden;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: all 0.3s ease;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            font-size: 1.1rem;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .chart-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 400px;
            display: flex;
            flex-direction: column;
        }

        .chart-container {
            flex: 1;
            max-width: 100%;
            max-height: 100%;
            position: relative;
        }

        .table-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #ecf0f1;
            color: #2c3e50;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        #mapaBonde {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .iframe-container {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%;
            margin-top: 20px;
        }

        .iframe-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
            border-radius: 10px;
        }

        .period-select {
            margin-bottom: 15px;
            padding: 8px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 200px;
        }

        .export-button {
            margin-left: 10px;
            padding: 8px 16px;
            font-size: 1rem;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .export-button:hover {
            background-color: #34495e;
        }

        .info-paragraph {
            margin-top: 20px;
            font-size: 1.2rem;
            color: #2c3e50;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="main-content">
            <div class="section">
                <h2>Métricas Gerais</h2>
                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                    <select id="globalPeriodSelect" class="period-select">
                        <option value="diario">Diário</option>
                        <option value="mensal" selected>Mensal</option>
                        <option value="anual">Anual</option>
                    </select>
                    <button class="export-button" onclick="exportarParaPDF()">Exportar para PDF</button>
                </div>
                <div class="cards-grid">
                    <div class="card">
                        <h3>Total de Bondes</h3>
                        <p id="totalBondes"><?php echo $total_bondes; ?></p>
                    </div>
                    <div class="card">
                        <h3>Viagens</h3>
                        <p id="viagensPeriodo"><?php echo $viagens_mes_atual; ?></p>
                    </div>
                    <div class="card">
                        <h3>Passageiros</h3>
                        <p id="passageirosPeriodo"><?php echo $passageiros_mes_atual; ?></p>
                    </div>
                    <div class="card">
                        <h3>Pagantes</h3>
                        <p id="pagantesPeriodo"><?php echo $pagantes_mes_atual; ?></p>
                    </div>
                    <div class="card">
                        <h3>Moradores</h3>
                        <p id="moradoresPeriodo"><?php echo $moradores_mes_atual; ?></p>
                    </div>
                    <div class="card">
                        <h3>Gratuidade</h3>
                        <p id="gratuidadePeriodo"><?php echo $gratuidade_mes_atual; ?></p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Análise de Viagens e Passageiros</h2>
                <div class="charts-grid">
                    <div class="chart-card">
                        <h3>Bondes com Mais Viagens</h3>
                        <div id="noDataBondesMessage" style="display: none; text-align: center; color: #e74c3c; margin-top: 10px;">
                            Nenhum dado de viagens disponível para o período selecionado.
                        </div>
                        <div class="chart-container">
                            <canvas id="bondesViagensChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3>Distribuição de Passageiros</h3>
                        <div id="noDataMessage" style="display: none; text-align: center; color: #e74c3c; margin-top: 10px;">
                            Nenhum dado de passageiros disponível para o período selecionado.
                        </div>
                        <div class="chart-container">
                            <canvas id="passageirosChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3>Viagens por Dia da Semana</h3>
                        <div id="noDataViagensDiaSemanaMessage" style="display: none; text-align: center; color: #e74c3c; margin-top: 10px;">
                            Nenhum dado de viagens disponível para o período selecionado.
                        </div>
                        <div class="chart-container">
                            <canvas id="viagensDiaSemanaChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3>Fluxo Médio de Passageiros por Horário</h3>
                        <div id="noDataPassageirosHorarioMessage" style="display: none; text-align: center; color: #e74c3c; margin-top: 10px;">
                            Nenhum dado de passageiros disponível para o período selecionado.
                        </div>
                        <div class="chart-container">
                            <canvas id="passageirosHorarioChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Detalhes Operacionais</h2>
                <div class="cards-grid">
                    <div class="table-card">
                        <h3>Acidentes Recentes</h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Descrição</th>
                                        <th>Localização</th>
                                        <th>Severidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_acidentes = "SELECT data, descricao, localizacao, severidade FROM acidentes ORDER BY data_registro DESC LIMIT 5";
                                    $result_acidentes = $conn->query($sql_acidentes);
                                    if ($result_acidentes === false) {
                                        echo "<tr><td colspan='4'>Erro na consulta de acidentes: " . $conn->error . "</td></tr>";
                                    } elseif ($result_acidentes->num_rows > 0) {
                                        while ($row = $result_acidentes->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data']))) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['localizacao'] ?? 'N/A') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['severidade']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>Nenhum acidente registrado</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-card">
                        <h3>Viagens Recentes</h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Retorno</th>
                                        <th>Bonde</th>
                                        <th>Saída</th>
                                        <th>Destino</th>
                                        <th>Passageiros</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_viagens = "SELECT v.data, v.retorno, v.bonde, v.saida, v.retorno as destino, (v.pagantes + v.gratuidade + v.moradores) as passageiros 
                                                    FROM viagens v 
                                                    ORDER BY v.data DESC LIMIT 5";
                                    $result_viagens = $conn->query($sql_viagens);
                                    if ($result_viagens === false) {
                                        echo "<tr><td colspan='6'>Erro na consulta de viagens: " . $conn->error . "</td></tr>";
                                    } elseif ($result_viagens->num_rows > 0) {
                                        while ($row = $result_viagens->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data']))) . "</td>";
                                            echo "<td>" . ($row['retorno'] ? htmlspecialchars(date('d/m/Y', strtotime($row['retorno']))) : 'N/A') . "</td>";
                                            echo "<td>" . htmlspecialchars('Bonde ' . $row['bonde']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['saida']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['destino'] ?? 'N/A') . "</td>";
                                            echo "<td>" . htmlspecialchars($row['passageiros']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>Nenhuma viagem registrada</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-card">
                        <h3>Status dos Bondes</h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Bonde</th>
                                        <th>Status</th>
                                        <th>Última Atualização</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_status = "SELECT id FROM bondes ORDER BY id ASC";
                                    $result_status = $conn->query($sql_status);
                                    if ($result_status === false) {
                                        echo "<tr><td colspan='3'>Erro na consulta de status: " . $conn->error . "</td></tr>";
                                    } elseif ($result_status->num_rows > 0) {
                                        while ($row = $result_status->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars('Bonde ' . $row['id']) . "</td>";
                                            echo "<td>Ativo</td>";
                                            echo "<td>" . date('d/m/Y H:i') . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3'>Nenhum bonde cadastrado</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-card">
                        <h3>Manutenções Agendadas</h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Título</th>
                                        <th>Bonde</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_manutencoes = "SELECT m.data_manutencao, m.titulo, m.bonde_id, m.status 
                                                        FROM manutencoes m 
                                                        ORDER BY m.data_manutencao DESC LIMIT 5";
                                    $result_manutencoes = $conn->query($sql_manutencoes);
                                    if ($result_manutencoes === false) {
                                        echo "<tr><td colspan='4'>Erro na consulta de manutenções: " . $conn->error . "</td></tr>";
                                    } elseif ($result_manutencoes->num_rows > 0) {
                                        while ($row = $result_manutencoes->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data_manutencao']))) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['titulo']) . "</td>";
                                            echo "<td>" . htmlspecialchars('Bonde ' . $row['bonde_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>Nenhuma manutenção registrada</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Dados para os cards
        const dadosCards = {
            diario: {
                viagens: <?php echo $viagens_hoje; ?>,
                passageiros: <?php echo $passageiros_hoje; ?>,
                pagantes: <?php echo $pagantes_hoje; ?>,
                moradores: <?php echo $moradores_hoje; ?>,
                gratuidade: <?php echo $gratuidade_hoje; ?>
            },
            mensal: {
                viagens: <?php echo $viagens_mes_atual; ?>,
                passageiros: <?php echo $passageiros_mes_atual; ?>,
                pagantes: <?php echo $pagantes_mes_atual; ?>,
                moradores: <?php echo $moradores_mes_atual; ?>,
                gratuidade: <?php echo $gratuidade_mes_atual; ?>
            },
            anual: {
                viagens: <?php echo $total_viagens; ?>,
                passageiros: <?php echo $passageiros_anual; ?>,
                pagantes: <?php echo $pagantes_anual; ?>,
                moradores: <?php echo $moradores_anual; ?>,
                gratuidade: <?php echo $gratuidade_anual; ?>
            }
        };

        // Dados para o gráfico de passageiros
        const dadosPassageiros = {
            diario: {
                pagantes: <?php echo $pagantes_hoje; ?>,
                moradores: <?php echo $moradores_hoje; ?>,
                gratuidade: <?php echo $gratuidade_hoje; ?>
            },
            mensal: {
                pagantes: <?php echo $pagantes_mes_atual; ?>,
                moradores: <?php echo $moradores_mes_atual; ?>,
                gratuidade: <?php echo $gratuidade_mes_atual; ?>
            },
            anual: {
                pagantes: <?php echo $pagantes_anual; ?>,
                moradores: <?php echo $moradores_anual; ?>,
                gratuidade: <?php echo $gratuidade_anual; ?>
            }
        };

        // Dados para o gráfico de bondes com mais viagens
        const dadosBondesViagens = {
            diario: {
                labels: [<?php echo "'" . implode("','", array_column($bondes_viagens_diario, 'bonde')) . "'"; ?>],
                data: [<?php echo implode(',', array_column($bondes_viagens_diario, 'total_viagens')); ?>]
            },
            mensal: {
                labels: [<?php echo "'" . implode("','", array_column($bondes_viagens_mensal, 'bonde')) . "'"; ?>],
                data: [<?php echo implode(',', array_column($bondes_viagens_mensal, 'total_viagens')); ?>]
            },
            anual: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [
                    <?php
                    $colors = [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ];
                    $borderColors = [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(153, 102, 255, 1)'
                    ];
                    foreach ($bondes_viagens_anual as $index => $bonde) {
                        echo "{
                            label: '" . htmlspecialchars($bonde['bonde']) . "',
                            data: [" . implode(',', $bonde['viagens_por_mes']) . "],
                            backgroundColor: '" . $colors[$index % count($colors)] . "',
                            borderColor: '" . $borderColors[$index % count($borderColors)] . "',
                            borderWidth: 1
                        },";
                    }
                    ?>
                ]
            }
        };

        // Dados para o gráfico de viagens por dia da semana
        const dadosViagensDiaSemana = {
            diario: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                data: [<?php echo implode(',', $viagens_por_dia_semana_diario); ?>]
            },
            mensal: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                data: [<?php echo implode(',', $viagens_por_dia_semana_mensal); ?>]
            },
            anual: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
                data: [<?php echo implode(',', $viagens_por_dia_semana_anual); ?>]
            }
        };

        // Dados para o gráfico de fluxo de passageiros por horário
        const dadosPassageirosHorario = {
            diario: {
                labels: ['6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h'],
                data: [<?php echo implode(',', array_slice($passageiros_por_horario_diario, 6, 15, true)); ?>]
            },
            mensal: {
                labels: ['6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h'],
                data: [<?php echo implode(',', array_slice($passageiros_por_horario_mensal, 6, 15, true)); ?>]
            },
            anual: {
                labels: ['6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h'],
                data: [<?php echo implode(',', array_slice($passageiros_por_horario_anual, 6, 15, true)); ?>]
            }
        };

        // Função para atualizar os cards
        function atualizarCards(periodo) {
            const dados = dadosCards[periodo];
            document.getElementById('totalBondes').textContent = <?php echo $total_bondes; ?>;
            document.getElementById('viagensPeriodo').textContent = dados.viagens.toLocaleString('pt-BR');
            document.getElementById('passageirosPeriodo').textContent = dados.passageiros.toLocaleString('pt-BR');
            document.getElementById('pagantesPeriodo').textContent = dados.pagantes.toLocaleString('pt-BR');
            document.getElementById('moradoresPeriodo').textContent = dados.moradores.toLocaleString('pt-BR');
            document.getElementById('gratuidadePeriodo').textContent = dados.gratuidade.toLocaleString('pt-BR');
        }

        // Função para atualizar o gráfico de passageiros
        function atualizarGraficoPassageiros(periodo) {
            const dados = dadosPassageiros[periodo];
            const total = dados.pagantes + dados.moradores + dados.gratuidade;
            const noDataMessage = document.getElementById('noDataMessage');
            const canvas = document.getElementById('passageirosChart');

            if (total === 0) {
                noDataMessage.style.display = 'block';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            passageirosChart.data.datasets[0].data = [dados.pagantes, dados.moradores, dados.gratuidade];
            passageirosChart.options.plugins.title.text = `Distribuição de Passageiros (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual'})`;
            passageirosChart.update();
        }

        // Função para atualizar o gráfico de bondes com mais viagens
        function atualizarGraficoBondesViagens(periodo) {
            const dados = dadosBondesViagens[periodo];
            const noDataMessage = document.getElementById('noDataBondesMessage');
            const canvas = document.getElementById('bondesViagensChart');

            let total = 0;
            if (periodo === 'anual') {
                total = dados.datasets.reduce((sum, dataset) => sum + dataset.data.reduce((s, v) => s + v, 0), 0);
                bondesViagensChart.data.labels = dados.labels;
                bondesViagensChart.data.datasets = dados.datasets;
                bondesViagensChart.options.scales.x.title.text = 'Meses';
                bondesViagensChart.options.scales.y.title.text = 'Número de Viagens';
                bondesViagensChart.options.plugins.legend.display = true;
            } else {
                total = dados.data.reduce((sum, value) => sum + value, 0);
                bondesViagensChart.data.labels = dados.labels;
                bondesViagensChart.data.datasets = [{
                    label: 'Viagens',
                    data: dados.data,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }];
                bondesViagensChart.options.scales.x.title.text = 'Bondes';
                bondesViagensChart.options.scales.y.title.text = 'Número de Viagens';
                bondesViagensChart.options.plugins.legend.display = false;
            }

            if (total === 0) {
                noDataMessage.style.display = 'block';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            bondesViagensChart.options.plugins.title.text = `Bondes com Mais Viagens (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual por Mês'})`;
            bondesViagensChart.update();
        }

        // Função para atualizar o gráfico de viagens por dia da semana
        function atualizarGraficoViagensDiaSemana(periodo) {
            const dados = dadosViagensDiaSemana[periodo];
            const total = dados.data.reduce((sum, value) => sum + value, 0);
            const noDataMessage = document.getElementById('noDataViagensDiaSemanaMessage');
            const canvas = document.getElementById('viagensDiaSemanaChart');

            if (total === 0) {
                noDataMessage.style.display = 'block';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            viagensDiaSemanaChart.data.datasets[0].data = dados.data;
            viagensDiaSemanaChart.options.plugins.title.text = `Viagens por Dia da Semana (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual'})`;
            viagensDiaSemanaChart.update();
        }

        // Função para atualizar o gráfico de fluxo de passageiros por horário
        function atualizarGraficoPassageirosHorario(periodo) {
            const dados = dadosPassageirosHorario[periodo];
            const total = dados.data.reduce((sum, value) => sum + value, 0);
            const noDataMessage = document.getElementById('noDataPassageirosHorarioMessage');
            const canvas = document.getElementById('passageirosHorarioChart');

            if (total === 0) {
                noDataMessage.style.display = 'block';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            passageirosHorarioChart.data.datasets[0].data = dados.data;
            passageirosHorarioChart.options.plugins.title.text = `Fluxo Médio de Passageiros por Horário (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual'})`;
            passageirosHorarioChart.update();
        }

        // Função para atualizar todo o painel
        function atualizarPainel(periodo) {
            atualizarCards(periodo);
            atualizarGraficoPassageiros(periodo);
            atualizarGraficoBondesViagens(periodo);
            atualizarGraficoViagensDiaSemana(periodo);
            atualizarGraficoPassageirosHorario(periodo);
        }

        // Função para exportar os dados para PDF
        function exportarParaPDF() {
            const { jsPDF } = window.jspdf;
            if (!jsPDF) {
                console.error('jsPDF library is not loaded.');
                alert('Erro: A biblioteca de exportação para PDF não foi carregada corretamente. Tente novamente mais tarde.');
                return;
            }

            const doc = new jsPDF();
            const periodo = document.getElementById('globalPeriodSelect').value;
            const periodoNome = periodo === 'diario' ? 'Diário' : periodo === 'mensal' ? 'Mensal' : 'Anual';
            const dataAtual = new Date().toISOString().slice(0, 10).replace(/-/g, '');
            let y = 10;

            // Título do documento
            doc.setFontSize(16);
            doc.text(`Relatório de Painel Bonde - ${periodoNome}`, 10, y);
            y += 10;

            // Seção: Métricas Gerais
            doc.setFontSize(14);
            doc.text('Métricas Gerais', 10, y);
            y += 10;
            const metricasGerais = [
                ['Métrica', 'Valor'],
                ['Total de Bondes', <?php echo $total_bondes; ?>],
                ['Viagens', dadosCards[periodo].viagens],
                ['Passageiros', dadosCards[periodo].passageiros],
                ['Pagantes', dadosCards[periodo].pagantes],
                ['Moradores', dadosCards[periodo].moradores],
                ['Gratuidade', dadosCards[periodo].gratuidade]
            ];
            doc.autoTable({
                startY: y,
                head: [metricasGerais[0]],
                body: metricasGerais.slice(1),
                theme: 'grid',
                headStyles: { fillColor: [44, 62, 80] },
                styles: { fontSize: 10, cellPadding: 2 }
            });
            y = doc.lastAutoTable.finalY + 10;

            // Seção: Distribuição de Passageiros
            doc.setFontSize(14);
            doc.text('Distribuição de Passageiros', 10, y);
            y += 10;
            const distPassageiros = [
                ['Categoria', 'Quantidade'],
                ['Pagantes', dadosPassageiros[periodo].pagantes],
                ['Moradores', dadosPassageiros[periodo].moradores],
                ['Gratuidade', dadosPassageiros[periodo].gratuidade]
            ];
            doc.autoTable({
                startY: y,
                head: [distPassageiros[0]],
                body: distPassageiros.slice(1),
                theme: 'grid',
                headStyles: { fillColor: [44, 62, 80] },
                styles: { fontSize: 10, cellPadding: 2 }
            });
            y = doc.lastAutoTable.finalY + 10;

            // Seção: Bondes com Mais Viagens
            doc.setFontSize(14);
            doc.text('Bondes com Mais Viagens', 10, y);
            y += 10;
            if (periodo === 'anual') {
                const bondesViagensData = [['Bonde', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']];
                dadosBondesViagens.anual.datasets.forEach(dataset => {
                    bondesViagensData.push([dataset.label, ...dataset.data]);
                });
                doc.autoTable({
                    startY: y,
                    head: [bondesViagensData[0]],
                    body: bondesViagensData.slice(1),
                    theme: 'grid',
                    headStyles: { fillColor: [44, 62, 80] },
                    styles: { fontSize: 10, cellPadding: 2 }
                });
            } else {
                const bondesViagensData = [['Bonde', 'Viagens']];
                dadosBondesViagens[periodo].labels.forEach((label, index) => {
                    bondesViagensData.push([label, dadosBondesViagens[periodo].data[index]]);
                });
                doc.autoTable({
                    startY: y,
                    head: [bondesViagensData[0]],
                    body: bondesViagensData.slice(1),
                    theme: 'grid',
                    headStyles: { fillColor: [44, 62, 80] },
                    styles: { fontSize: 10, cellPadding: 2 }
                });
            }
            y = doc.lastAutoTable.finalY + 10;

            // Seção: Viagens por Dia da Semana
            doc.setFontSize(14);
            doc.text('Viagens por Dia da Semana', 10, y);
            y += 10;
            const viagensDiaSemanaData = [['Dia da Semana', 'Viagens']];
            dadosViagensDiaSemana[periodo].labels.forEach((label, index) => {
                viagensDiaSemanaData.push([label, dadosViagensDiaSemana[periodo].data[index]]);
            });
            doc.autoTable({
                startY: y,
                head: [viagensDiaSemanaData[0]],
                body: viagensDiaSemanaData.slice(1),
                theme: 'grid',
                headStyles: { fillColor: [44, 62, 80] },
                styles: { fontSize: 10, cellPadding: 2 }
            });
            y = doc.lastAutoTable.finalY + 10;

            // Seção: Fluxo de Passageiros por Horário
            doc.setFontSize(14);
            doc.text('Fluxo de Passageiros por Horário', 10, y);
            y += 10;
            const passageirosHorarioData = [['Horário', 'Passageiros']];
            dadosPassageirosHorario[periodo].labels.forEach((label, index) => {
                passageirosHorarioData.push([label, dadosPassageirosHorario[periodo].data[index]]);
            });
            doc.autoTable({
                startY: y,
                head: [passageirosHorarioData[0]],
                body: passageirosHorarioData.slice(1),
                theme: 'grid',
                headStyles: { fillColor: [44, 62, 80] },
                styles: { fontSize: 10, cellPadding: 2 }
            });

            // Gera o arquivo PDF com nome dinâmico baseado no período e na data
            try {
                doc.save(`Painel_Bonde_${periodoNome}_${dataAtual}.pdf`);
            } catch (error) {
                console.error('Erro ao gerar o arquivo PDF:', error);
                alert('Erro ao exportar para PDF. Por favor, tente novamente.');
            }
        }

        // Gráfico de barras: Bondes com mais viagens
        const bondesViagensCtx = document.getElementById('bondesViagensChart').getContext('2d');
        const bondesViagensChart = new Chart(bondesViagensCtx, {
            type: 'bar',
            data: {
                labels: dadosBondesViagens.mensal.labels,
                datasets: [{
                    label: 'Viagens',
                    data: dadosBondesViagens.mensal.data,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Viagens'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bondes'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Bondes com Mais Viagens (Mês Atual)'
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Gráfico de pizza: Distribuição de passageiros
        const passageirosCtx = document.getElementById('passageirosChart').getContext('2d');
        const passageirosChart = new Chart(passageirosCtx, {
            type: 'pie',
            data: {
                labels: ['Pagantes', 'Moradores', 'Gratuidade'],
                datasets: [{
                    label: 'Passageiros',
                    data: [<?php echo $pagantes_mes_atual; ?>, <?php echo $moradores_mes_atual; ?>, <?php echo $gratuidade_mes_atual; ?>],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Distribuição de Passageiros (Mês Atual)'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return `${label}: ${value.toLocaleString('pt-BR')}`;
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Gráfico de barras: Viagens por dia da semana
        const viagensDiaSemanaCtx = document.getElementById('viagensDiaSemanaChart').getContext('2d');
        const viagensDiaSemanaChart = new Chart(viagensDiaSemanaCtx, {
            type: 'bar',
            data: {
                labels: dadosViagensDiaSemana.mensal.labels,
                datasets: [{
                    label: 'Viagens',
                    data: dadosViagensDiaSemana.mensal.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Viagens'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Dias da Semana'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'Viagens por Dia da Semana (Mês Atual)'
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Gráfico de linha: Fluxo de passageiros por horário
        const passageirosHorarioCtx = document.getElementById('passageirosHorarioChart').getContext('2d');
        const passageirosHorarioChart = new Chart(passageirosHorarioCtx, {
            type: 'line',
            data: {
                labels: dadosPassageirosHorario.mensal.labels,
                datasets: [{
                    label: 'Passageiros (Partida + Chegada)',
                    data: dadosPassageirosHorario.mensal.data,
                    backgroundColor: 'rgba(169, 169, 169, 0.5)',
                    borderColor: 'rgba(0, 0, 255, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Passageiros'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Horário'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: true,
                        text: 'Fluxo Médio de Passageiros por Horário (Mês Atual)'
                    },
                    subtitle: {
                        display: true,
                        text: 'Distribuição de passageiros ao longo do dia'
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Verifica se há dados para exibir os gráficos inicialmente
        const totalPassageirosHorario = dadosPassageirosHorario.mensal.data.reduce((sum, value) => sum + value, 0);
        if (totalPassageirosHorario === 0) {
            document.getElementById('noDataPassageirosHorarioMessage').style.display = 'block';
            document.getElementById('passageirosHorarioChart').style.display = 'none';
        } else {
            document.getElementById('noDataPassageirosHorarioMessage').style.display = 'none';
            document.getElementById('passageirosHorarioChart').style.display = 'block';
        }

        // Evento para o select global
        document.getElementById('globalPeriodSelect').addEventListener('change', function() {
            atualizarPainel(this.value);
        });

        // Inicializa o painel com o período mensal
        atualizarPainel('mensal');
    </script>

    <?php $conn->close(); ?>
</body>
</html>