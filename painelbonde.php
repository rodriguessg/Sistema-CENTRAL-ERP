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
    <title>Painel Bonde - Dashboard Tecnológico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        :root {
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
            line-height: 1.5;
            overflow-x: hidden;
            min-height: 100vh;
            font-size: 14px;
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

        .dashboard-container {
            display: flex;
            min-height: 100vh;
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
            font-size: 1.5rem;
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
            background: var(--primary-gradient);
            border-radius: 2px;
        }

        .section h2 i {
            font-size: 1.2rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .period-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
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
            min-width: 180px;
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
        }

        .export-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .export-button:active {
            transform: translateY(0);
        }

        /* Reduced card sizes and improved grid layout */
        .cards-grid {
               display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 0.5fr));
    gap: 0.5rem;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.25rem;
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
            padding:0px;
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-trend {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .trend-up {
            color: #10b981;
        }

        .trend-down {
            color: #ef4444;
        }

        .trend-neutral {
            color: var(--text-muted);
        }

        /* Improved charts grid with hover zoom effects */
        .charts-grid {
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

        /* Added hover zoom effect for chart containers */
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
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-card h3 i {
            color: #667eea;
            font-size: 0.9rem;
        }

        .chart-container {
            flex: 1;
            position: relative;
            min-height: 0;
        }

        .no-data-message {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--text-muted);
            font-size: 0.9rem;
            gap: 0.75rem;
        }

        .no-data-message i {
            font-size: 2rem;
            opacity: 0.5;
        }

        /* Improved table layout with better spacing */
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

        .table-card h3 i {
            color: #667eea;
            font-size: 0.9rem;
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

        .status-maintenance {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.2);
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

        .percentage-display {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .metric-comparison {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.25rem;
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Responsive design improvements */
        @media (max-width: 1200px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-card {
                height: 350px;
            }
        }

        @media (max-width: 768px) {
            .caderno {
                padding: 0.75rem;
            }
            
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .period-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .period-select,
            .export-button {
                width: 100%;
                justify-content: center;
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

        /* Custom Scrollbar */
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

        /* Added chart hover effects and percentage display improvements */
        .chart-hover-info {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(15, 15, 35, 0.9);
            color: var(--text-primary);
            padding: 0.5rem;
            border-radius: var(--border-radius-sm);
            font-size: 0.8rem;
            opacity: 0;
            transition: var(--transition);
            pointer-events: none;
            z-index: 100;
        }

        .chart-card:hover .chart-hover-info {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="caderno">
        <div class="main-content">
            <div class="section">
                <h2>
                    <i class="fas fa-chart-line"></i>
                    Métricas Gerais do Sistema
                </h2>
                <div class="period-controls">
                    <select id="globalPeriodSelect" class="period-select">
                        <option value="diario">📅 Visualização Diária</option>
                        <option value="mensal" selected>📊 Visualização Mensal</option>
                        <option value="anual">📈 Visualização Anual</option>
                    </select>
                    <button class="export-button" onclick="exportarParaPDF()">
                        <i class="fas fa-download"></i>
                        Exportar Relatório PDF
                    </button>
                </div>
                <div class="cards-grid">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="fas fa-train"></i>
                            </div>
                        </div>
                        <h3>Total de Bondes</h3>
                        <div class="card-value" id="totalBondes"><?php echo number_format($total_bondes, 0, ',', '.'); ?></div>
                        <div class="card-trend trend-neutral">
                            <i class="fas fa-circle"></i>
                            Frota completa ativa
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: var(--success-gradient);">
                                <i class="fas fa-route"></i>
                            </div>
                        </div>
                        <h3>Viagens Realizadas</h3>
                        <div class="card-value" id="viagensPeriodo"><?php echo number_format($viagens_mes_atual, 0, ',', '.'); ?></div>
                        <div class="card-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            Operação em andamento
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: var(--warning-gradient);">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <h3>Total de Passageiros</h3>
                        <div class="card-value" id="passageirosPeriodo"><?php echo number_format($passageiros_mes_atual, 0, ',', '.'); ?></div>
                        <div class="card-trend trend-up">
                            <i class="fas fa-arrow-up"></i>
                            Fluxo crescente
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: var(--secondary-gradient);">
                                <i class="fas fa-credit-card"></i>
                            </div>
                        </div>
                        <h3>Passageiros Pagantes</h3>
                        <div class="card-value" id="pagantesPeriodo"><?php echo number_format($pagantes_mes_atual, 0, ',', '.'); ?></div>
                        <div class="metric-comparison">
                            <span><?php echo $passageiros_mes_atual > 0 ? round(($pagantes_mes_atual / $passageiros_mes_atual) * 100, 1) : 0; ?>% do total</span>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: var(--info-gradient);">
                                <i class="fas fa-home"></i>
                            </div>
                        </div>
                        <h3>Moradores</h3>
                        <div class="card-value" id="moradoresPeriodo"><?php echo number_format($moradores_mes_atual, 0, ',', '.'); ?></div>
                        <div class="metric-comparison">
                            <span><?php echo $passageiros_mes_atual > 0 ? round(($moradores_mes_atual / $passageiros_mes_atual) * 100, 1) : 0; ?>% do total</span>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: var(--danger-gradient);">
                                <i class="fas fa-gift"></i>
                            </div>
                        </div>
                        <h3>Gratuidades</h3>
                        <div class="card-value" id="gratuidadePeriodo"><?php echo number_format($gratuidade_mes_atual, 0, ',', '.'); ?></div>
                        <div class="metric-comparison">
                            <span><?php echo $passageiros_mes_atual > 0 ? round(($gratuidade_mes_atual / $passageiros_mes_atual) * 100, 1) : 0; ?>% do total</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>
                    <i class="fas fa-analytics"></i>
                    Análise Avançada de Operações
                </h2>
                <div class="charts-grid">
                    <div class="chart-card">
                        <h3>
                            <i class="fas fa-trophy"></i>
                            Bondes com Maior Performance
                        </h3>
                        <!-- Added hover info display -->
                        <div class="chart-hover-info">
                            Passe o mouse sobre as barras para ver detalhes
                        </div>
                        <div id="noDataBondesMessage" class="no-data-message" style="display: none;">
                            <i class="fas fa-chart-bar"></i>
                            <span>Nenhum dado de viagens disponível para o período selecionado</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="bondesViagensChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3>
                            <i class="fas fa-chart-pie"></i>
                            Distribuição de Passageiros
                        </h3>
                        <div class="chart-hover-info">
                            Passe o mouse sobre os segmentos para ver porcentagens
                        </div>
                        <div id="noDataMessage" class="no-data-message" style="display: none;">
                            <i class="fas fa-users"></i>
                            <span>Nenhum dado de passageiros disponível para o período selecionado</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="passageirosChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3>
                            <i class="fas fa-calendar-week"></i>
                            Padrão Semanal de Viagens
                        </h3>
                        <div class="chart-hover-info">
                            Passe o mouse sobre as barras para ver porcentagens
                        </div>
                        <div id="noDataViagensDiaSemanaMessage" class="no-data-message" style="display: none;">
                            <i class="fas fa-calendar"></i>
                            <span>Nenhum dado de viagens disponível para o período selecionado</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="viagensDiaSemanaChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3>
                            <i class="fas fa-clock"></i>
                            Fluxo de Passageiros por Horário
                        </h3>
                        <div class="chart-hover-info">
                            Passe o mouse sobre a linha para ver detalhes
                        </div>
                        <div id="noDataPassageirosHorarioMessage" class="no-data-message" style="display: none;">
                            <i class="fas fa-chart-line"></i>
                            <span>Nenhum dado de passageiros disponível para o período selecionado</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="passageirosHorarioChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>
                    <i class="fas fa-cogs"></i>
                    Detalhes Operacionais e Monitoramento
                </h2>
                <div class="charts-grid">
                    <div class="table-card">
                        <h3>
                            <i class="fas fa-exclamation-triangle"></i>
                            Acidentes Recentes
                        </h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar"></i> Data</th>
                                        <th><i class="fas fa-file-alt"></i> Descrição</th>
                                        <th><i class="fas fa-map-marker-alt"></i> Localização</th>
                                        <th><i class="fas fa-thermometer-half"></i> Severidade</th>
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
                                            $severityClass = '';
                                            switch(strtolower($row['severidade'])) {
                                                case 'baixa': $severityClass = 'severity-low'; break;
                                                case 'média': case 'media': $severityClass = 'severity-medium'; break;
                                                case 'alta': $severityClass = 'severity-high'; break;
                                                default: $severityClass = 'severity-medium';
                                            }
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data']))) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
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
                        <h3>
                            <i class="fas fa-history"></i>
                            Viagens Recentes
                        </h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar"></i> Data</th>
                                        <th><i class="fas fa-undo"></i> Retorno</th>
                                        <th><i class="fas fa-train"></i> Bonde</th>
                                        <th><i class="fas fa-play"></i> Saída</th>
                                        <th><i class="fas fa-flag-checkered"></i> Destino</th>
                                        <th><i class="fas fa-users"></i> Passageiros</th>
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
                                            echo "<td>" . ($row['retorno'] ? htmlspecialchars(date('d/m/Y', strtotime($row['retorno']))) : '<span style="color: var(--text-muted);">N/A</span>') . "</td>";
                                            echo "<td><span class='status-badge status-active'><i class='fas fa-train'></i> Bonde " . htmlspecialchars($row['bonde']) . "</span></td>";
                                            echo "<td>" . htmlspecialchars($row['saida']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['destino'] ?? 'N/A') . "</td>";
                                            echo "<td><strong>" . htmlspecialchars($row['passageiros']) . "</strong></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' style='text-align: center; color: var(--text-muted);'><i class='fas fa-info-circle'></i> Nenhuma viagem registrada</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-card">
                        <h3>
                            <i class="fas fa-heartbeat"></i>
                            Status da Frota
                        </h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-train"></i> Bonde</th>
                                        <th><i class="fas fa-signal"></i> Status</th>
                                        <th><i class="fas fa-clock"></i> Última Atualização</th>
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
                                            echo "<td><strong>Bonde " . htmlspecialchars($row['id']) . "</strong></td>";
                                            echo "<td><span class='status-badge status-active'><i class='fas fa-check-circle'></i> Operacional</span></td>";
                                            echo "<td>" . date('d/m/Y H:i') . "</td>";
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
                        <h3>
                            <i class="fas fa-tools"></i>
                            Manutenções Programadas
                        </h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar"></i> Data</th>
                                        <th><i class="fas fa-wrench"></i> Tipo</th>
                                        <th><i class="fas fa-train"></i> Bonde</th>
                                        <th><i class="fas fa-info-circle"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan='4' style='text-align: center; color: var(--text-muted); padding: 2rem;'>
                                            <i class='fas fa-check-circle' style='font-size: 2rem; margin-bottom: 1rem; display: block;'></i>
                                            Nenhuma manutenção programada
                                        </td>
                                    </tr>
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
    <!-- Added Chart.js datalabels plugin for showing percentages directly on charts -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
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
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ];
                    $borderColors = [
                        'rgba(102, 126, 234, 1)',
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
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
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

        // Função para formatar números
        function formatNumber(num) {
            return new Intl.NumberFormat('pt-BR').format(num);
        }

        // Função para calcular porcentagem
        function calculatePercentage(value, total) {
            return total > 0 ? ((value / total) * 100).toFixed(1) : 0;
        }

        // Função para atualizar os cards
        function atualizarCards(periodo) {
            const dados = dadosCards[periodo];
            document.getElementById('totalBondes').textContent = formatNumber(<?php echo $total_bondes; ?>);
            document.getElementById('viagensPeriodo').textContent = formatNumber(dados.viagens);
            document.getElementById('passageirosPeriodo').textContent = formatNumber(dados.passageiros);
            document.getElementById('pagantesPeriodo').textContent = formatNumber(dados.pagantes);
            document.getElementById('moradoresPeriodo').textContent = formatNumber(dados.moradores);
            document.getElementById('gratuidadePeriodo').textContent = formatNumber(dados.gratuidade);
        }

        // Função para atualizar o gráfico de passageiros com porcentagens
        function atualizarGraficoPassageiros(periodo) {
            const dados = dadosPassageiros[periodo];
            const total = dados.pagantes + dados.moradores + dados.gratuidade;
            const noDataMessage = document.getElementById('noDataMessage');
            const canvas = document.getElementById('passageirosChart');

            if (total === 0) {
                noDataMessage.style.display = 'flex';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            passageirosChart.data.datasets[0].data = [dados.pagantes, dados.moradores, dados.gratuidade];
            passageirosChart.options.plugins.title.text = `Distribuição de Passageiros (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual'})`;
            
            // Atualizar tooltips com porcentagens
            passageirosChart.options.plugins.tooltip.callbacks.label = function(context) {
                const label = context.label || '';
                const value = context.raw || 0;
                const percentage = calculatePercentage(value, total);
                return `${label}: ${formatNumber(value)} (${percentage}%)`;
            };
            
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
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }];
                bondesViagensChart.options.scales.x.title.text = 'Bondes';
                bondesViagensChart.options.scales.y.title.text = 'Número de Viagens';
                bondesViagensChart.options.plugins.legend.display = false;
            }

            if (total === 0) {
                noDataMessage.style.display = 'flex';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            bondesViagensChart.options.plugins.title.text = `Bondes com Maior Performance (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual por Mês'})`;
            bondesViagensChart.update();
        }

        // Função para atualizar o gráfico de viagens por dia da semana
        function atualizarGraficoViagensDiaSemana(periodo) {
            const dados = dadosViagensDiaSemana[periodo];
            const total = dados.data.reduce((sum, value) => sum + value, 0);
            const noDataMessage = document.getElementById('noDataViagensDiaSemanaMessage');
            const canvas = document.getElementById('viagensDiaSemanaChart');

            if (total === 0) {
                noDataMessage.style.display = 'flex';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            viagensDiaSemanaChart.data.datasets[0].data = dados.data;
            viagensDiaSemanaChart.options.plugins.title.text = `Padrão Semanal de Viagens (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual'})`;
            
            // Atualizar tooltips com porcentagens
            viagensDiaSemanaChart.options.plugins.tooltip.callbacks.label = function(context) {
                const value = context.raw || 0;
                const percentage = calculatePercentage(value, total);
                return `Viagens: ${formatNumber(value)} (${percentage}%)`;
            };
            
            viagensDiaSemanaChart.update();
        }

        // Função para atualizar o gráfico de fluxo de passageiros por horário
        function atualizarGraficoPassageirosHorario(periodo) {
            const dados = dadosPassageirosHorario[periodo];
            const total = dados.data.reduce((sum, value) => sum + value, 0);
            const noDataMessage = document.getElementById('noDataPassageirosHorarioMessage');
            const canvas = document.getElementById('passageirosHorarioChart');

            if (total === 0) {
                noDataMessage.style.display = 'flex';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            passageirosHorarioChart.data.datasets[0].data = dados.data;
            passageirosHorarioChart.options.plugins.title.text = `Fluxo de Passageiros por Horário (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual'})`;
            
            // Atualizar tooltips com porcentagens
            passageirosHorarioChart.options.plugins.tooltip.callbacks.label = function(context) {
                const value = context.raw || 0;
                const percentage = calculatePercentage(value, total);
                return `Passageiros: ${formatNumber(value)} (${percentage}%)`;
            };
            
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
                ['Categoria', 'Quantidade', 'Porcentagem'],
                ['Pagantes', dadosPassageiros[periodo].pagantes, calculatePercentage(dadosPassageiros[periodo].pagantes, dadosPassageiros[periodo].pagantes + dadosPassageiros[periodo].moradores + dadosPassageiros[periodo].gratuidade) + '%'],
                ['Moradores', dadosPassageiros[periodo].moradores, calculatePercentage(dadosPassageiros[periodo].moradores, dadosPassageiros[periodo].pagantes + dadosPassageiros[periodo].moradores + dadosPassageiros[periodo].gratuidade) + '%'],
                ['Gratuidade', dadosPassageiros[periodo].gratuidade, calculatePercentage(dadosPassageiros[periodo].gratuidade, dadosPassageiros[periodo].pagantes + dadosPassageiros[periodo].moradores + dadosPassageiros[periodo].gratuidade) + '%']
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
            const viagensDiaSemanaData = [['Dia da Semana', 'Viagens', 'Porcentagem']];
            const totalViagensSemana = dadosViagensDiaSemana[periodo].data.reduce((sum, value) => sum + value, 0);
            dadosViagensDiaSemana[periodo].labels.forEach((label, index) => {
                const viagens = dadosViagensDiaSemana[periodo].data[index];
                viagensDiaSemanaData.push([label, viagens, calculatePercentage(viagens, totalViagensSemana) + '%']);
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
            const passageirosHorarioData = [['Horário', 'Passageiros', 'Porcentagem']];
            const totalPassageirosHorario = dadosPassageirosHorario[periodo].data.reduce((sum, value) => sum + value, 0);
            dadosPassageirosHorario[periodo].labels.forEach((label, index) => {
                const passageiros = dadosPassageirosHorario[periodo].data[index];
                passageirosHorarioData.push([label, passageiros, calculatePercentage(passageiros, totalPassageirosHorario) + '%']);
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

        // Configurações globais do Chart.js
        Chart.defaults.font.family = 'Inter';
        Chart.defaults.color = '#b8c5d6';
        Chart.defaults.backgroundColor = 'rgba(102, 126, 234, 0.1)';

        // Register the datalabels plugin
        Chart.register(ChartDataLabels);

        // Gráfico de barras: Bondes com mais viagens
        const bondesViagensCtx = document.getElementById('bondesViagensChart').getContext('2d');
        const bondesViagensChart = new Chart(bondesViagensCtx, {
            type: 'bar',
            data: {
                labels: dadosBondesViagens.mensal.labels,
                datasets: [{
                    label: 'Viagens',
                    data: dadosBondesViagens.mensal.data,
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Bondes com Maior Performance (Mês Atual)',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#ffffff'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 15, 35, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#b8c5d6',
                        borderColor: 'rgba(102, 126, 234, 0.3)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                const percentage = calculatePercentage(context.raw, total);
                                return `Viagens: ${formatNumber(context.raw)} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: function(context) {
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        color: '#ffffff',
                        font: {
                            weight: 'bold',
                            size: 10
                        },
                        formatter: function(value, context) {
                            const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                            const percentage = calculatePercentage(value, total);
                            return percentage + '%';
                        },
                        anchor: 'end',
                        align: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Viagens',
                            color: '#b8c5d6',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(35, 53, 84, 0.5)'
                        },
                        ticks: {
                            color: '#b8c5d6',
                            font: { size: 10 }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bondes',
                            color: '#b8c5d6',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(35, 53, 84, 0.5)'
                        },
                        ticks: {
                            color: '#b8c5d6',
                            font: { size: 10 }
                        }
                    }
                }
            }
        });

        // Gráfico de pizza: Distribuição de passageiros
        const passageirosCtx = document.getElementById('passageirosChart').getContext('2d');
        const passageirosChart = new Chart(passageirosCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pagantes', 'Moradores', 'Gratuidade'],
                datasets: [{
                    label: 'Passageiros',
                    data: [<?php echo $pagantes_mes_atual; ?>, <?php echo $moradores_mes_atual; ?>, <?php echo $gratuidade_mes_atual; ?>],
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderColor: [
                        'rgba(102, 126, 234, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            color: '#b8c5d6',
                            font: { size: 10 }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Distribuição de Passageiros (Mês Atual)',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#ffffff'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 15, 35, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#b8c5d6',
                        borderColor: 'rgba(102, 126, 234, 0.3)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                const percentage = calculatePercentage(value, total);
                                return `${label}: ${formatNumber(value)} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: function(context) {
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        color: '#ffffff',
                        font: {
                            weight: 'bold',
                            size: 11
                        },
                        formatter: function(value, context) {
                            const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                            const percentage = calculatePercentage(value, total);
                            return percentage + '%';
                        }
                    }
                }
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
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Padrão Semanal de Viagens (Mês Atual)',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#ffffff'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 15, 35, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#b8c5d6',
                        borderColor: 'rgba(75, 192, 192, 0.3)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                const percentage = calculatePercentage(context.raw, total);
                                return `Viagens: ${formatNumber(context.raw)} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: function(context) {
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        color: '#ffffff',
                        font: {
                            weight: 'bold',
                            size: 10
                        },
                        formatter: function(value, context) {
                            const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                            const percentage = calculatePercentage(value, total);
                            return percentage + '%';
                        },
                        anchor: 'end',
                        align: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Viagens',
                            color: '#b8c5d6',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(35, 53, 84, 0.5)'
                        },
                        ticks: {
                            color: '#b8c5d6',
                            font: { size: 10 }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Dias da Semana',
                            color: '#b8c5d6',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(35, 53, 84, 0.5)'
                        },
                        ticks: {
                            color: '#b8c5d6',
                            font: { size: 10 }
                        }
                    }
                }
            }
        });

        // Gráfico de linha: Fluxo de passageiros por horário
        const passageirosHorarioCtx = document.getElementById('passageirosHorarioChart').getContext('2d');
        const passageirosHorarioChart = new Chart(passageirosHorarioCtx, {
            type: 'line',
            data: {
                labels: dadosPassageirosHorario.mensal.labels,
                datasets: [{
                    label: 'Passageiros',
                    data: dadosPassageirosHorario.mensal.data,
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Fluxo de Passageiros por Horário (Mês Atual)',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#ffffff'
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 15, 35, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#b8c5d6',
                        borderColor: 'rgba(255, 99, 132, 0.3)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                                const percentage = calculatePercentage(context.raw, total);
                                return `Passageiros: ${formatNumber(context.raw)} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: function(context) {
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        color: '#ffffff',
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        padding: 2,
                        font: {
                            weight: 'bold',
                            size: 9
                        },
                        formatter: function(value, context) {
                            const total = context.dataset.data.reduce((sum, val) => sum + val, 0);
                            const percentage = calculatePercentage(value, total);
                            return percentage + '%';
                        },
                        anchor: 'end',
                        align: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Passageiros',
                            color: '#b8c5d6',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(35, 53, 84, 0.5)'
                        },
                        ticks: {
                            color: '#b8c5d6',
                            font: { size: 10 }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Horário',
                            color: '#b8c5d6',
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(35, 53, 84, 0.5)'
                        },
                        ticks: {
                            color: '#b8c5d6',
                            font: { size: 10 }
                        }
                    }
                }
            }
        });

        // Verifica se há dados para exibir os gráficos inicialmente
        const totalPassageirosHorario = dadosPassageirosHorario.mensal.data.reduce((sum, value) => sum + value, 0);
        if (totalPassageirosHorario === 0) {
            document.getElementById('noDataPassageirosHorarioMessage').style.display = 'flex';
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

        // Animação de entrada dos cards
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });

        // Atualização automática dos dados a cada 5 minutos
        setInterval(() => {
            console.log('Atualizando dados do dashboard...');
            // Aqui você pode adicionar uma chamada AJAX para atualizar os dados
        }, 300000); // 5 minutos
    </script>

    <?php $conn->close(); ?>
</body>
</html>
