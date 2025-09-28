<?php
session_start();

if (isset($_GET['api']) && $_GET['api'] === 'data') {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    
    // Verifica sessão
    if (!isset($_SESSION['username'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
        exit;
    }
    
    // Conexão com o banco
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'gm_sicbd';
    $conn = new mysqli($host, $user, $password, $dbname);
    
    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro na conexão com o banco']);
        exit;
    }
    
    $current_year = date('Y');
    $current_month = date('m');
    
    try {
        // Consulta consolidada para melhor performance
        $sql = "
        SELECT 
            -- Métricas gerais
            (SELECT COUNT(*) FROM bondes) as total_bondes,
            (SELECT COUNT(*) FROM acidentes) as total_acidentes,
            (SELECT COUNT(*) FROM viagens) as total_viagens,
            (SELECT COUNT(*) FROM bondes WHERE id NOT IN (SELECT COALESCE(bonde_afetado, 0) FROM manutencoes WHERE status = 'Em Andamento')) as bondes_ativos,
            
            -- Dados diários
            (SELECT COUNT(*) FROM viagens WHERE DATE(data) = CURDATE()) as viagens_hoje,
            (SELECT COALESCE(SUM(pagantes), 0) FROM viagens WHERE DATE(data) = CURDATE()) as pagantes_hoje,
            (SELECT COALESCE(SUM(moradores), 0) FROM viagens WHERE DATE(data) = CURDATE()) as moradores_hoje,
            (SELECT COALESCE(SUM(gratuidade), 0) FROM viagens WHERE DATE(data) = CURDATE()) as gratuidade_hoje,
            (SELECT COALESCE(SUM(grat_pcd_idoso), 0) FROM viagens WHERE DATE(data) = CURDATE()) as grat_pcd_idoso_hoje,
            
            -- Dados mensais
            (SELECT COUNT(*) FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month) as viagens_mes_atual,
            (SELECT COALESCE(SUM(pagantes), 0) FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month) as pagantes_mes_atual,
            (SELECT COALESCE(SUM(moradores), 0) FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month) as moradores_mes_atual,
            (SELECT COALESCE(SUM(gratuidade), 0) FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month) as gratuidade_mes_atual,
            (SELECT COALESCE(SUM(grat_pcd_idoso), 0) FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month) as grat_pcd_idoso,
            
            -- Dados anuais
            (SELECT COALESCE(SUM(pagantes), 0) FROM viagens WHERE YEAR(data) = $current_year) as pagantes_anual,
            (SELECT COALESCE(SUM(moradores), 0) FROM viagens WHERE YEAR(data) = $current_year) as moradores_anual,
            (SELECT COALESCE(SUM(gratuidade), 0) FROM viagens WHERE YEAR(data) = $current_year) as gratuidade_anual,
            (SELECT COALESCE(SUM(grat_pcd_idoso), 0) FROM viagens WHERE YEAR(data) = $current_year) as grat_pcd_idoso_anual
        ";

        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception('Erro na consulta principal: ' . $conn->error);
        }

        $metrics = $result->fetch_assoc();

        // Bondes com mais viagens - todos os períodos
        $bondes_viagens_diario = [];
        $result = $conn->query("SELECT bonde, COUNT(id) as total_viagens FROM viagens WHERE DATE(data) = CURDATE() GROUP BY bonde ORDER BY total_viagens DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $bondes_viagens_diario[] = ['bonde' => 'Bonde ' . $row['bonde'], 'total_viagens' => (int)$row['total_viagens']];
            }
        }

        $bondes_viagens_mensal = [];
        $result = $conn->query("SELECT bonde, COUNT(id) as total_viagens FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month GROUP BY bonde ORDER BY total_viagens DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $bondes_viagens_mensal[] = ['bonde' => 'Bonde ' . $row['bonde'], 'total_viagens' => (int)$row['total_viagens']];
            }
        }

        $bondes_viagens_anual = [];
        $result = $conn->query("SELECT bonde, COUNT(id) as total_viagens FROM viagens WHERE YEAR(data) = $current_year GROUP BY bonde ORDER BY total_viagens DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $bondes_viagens_anual[] = ['bonde' => 'Bonde ' . $row['bonde'], 'total_viagens' => (int)$row['total_viagens']];
            }
        }

        // Dados para gráficos de passageiros por horário
        $passageiros_por_horario_diario = array_fill(6, 15, 0);
        $result = $conn->query("SELECT HOUR(created_at) as hora, COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total_passageiros FROM viagens WHERE DATE(created_at) = CURDATE() AND HOUR(created_at) BETWEEN 6 AND 20 GROUP BY HOUR(created_at)");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $passageiros_por_horario_diario[(int)$row['hora']] = (int)$row['total_passageiros'];
            }
        }

        $passageiros_por_horario_mensal = array_fill(6, 15, 0);
        $result = $conn->query("SELECT HOUR(created_at) as hora, COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total_passageiros FROM viagens WHERE YEAR(created_at) = $current_year AND MONTH(created_at) = $current_month AND HOUR(created_at) BETWEEN 6 AND 20 GROUP BY HOUR(created_at)");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $passageiros_por_horario_mensal[(int)$row['hora']] = (int)$row['total_passageiros'];
            }
        }

        // Dados para recorde de passageiros por mês
        $passageiros_por_mes = array_fill(1, 12, 0);
        $result = $conn->query("SELECT MONTH(data) as mes, COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total_passageiros 
                                FROM viagens 
                                WHERE YEAR(data) = $current_year 
                                GROUP BY MONTH(data)");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $passageiros_por_mes[(int)$row['mes']] = (int)$row['total_passageiros'];
            }
        }

        // Dados para viagens por maquinista e agente
        $viagens_maquinista_agente_diario = [];
        $result = $conn->query("SELECT 
                                    COALESCE(maquinista, 'Desconhecido') as nome, 
                                    'Maquinista' as tipo, 
                                    COUNT(id) as total_viagens 
                                FROM viagens 
                                WHERE DATE(data) = CURDATE() 
                                GROUP BY maquinista
                                UNION
                                SELECT 
                                    COALESCE(agente, 'Desconhecido') as nome, 
                                    'Agente' as tipo, 
                                    COUNT(id) as total_viagens 
                                FROM viagens 
                                WHERE DATE(data) = CURDATE() 
                                GROUP BY agente
                                ORDER BY total_viagens DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $viagens_maquinista_agente_diario[] = [
                    'nome' => $row['nome'],
                    'tipo' => $row['tipo'],
                    'total_viagens' => (int)$row['total_viagens']
                ];
            }
        }

        $viagens_maquinista_agente_mensal = [];
        $result = $conn->query("SELECT 
                                    COALESCE(maquinista, 'Desconhecido') as nome, 
                                    'Maquinista' as tipo, 
                                    COUNT(id) as total_viagens 
                                FROM viagens 
                                WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month 
                                GROUP BY maquinista
                                UNION
                                SELECT 
                                    COALESCE(agente, 'Desconhecido') as nome, 
                                    'Agente' as tipo, 
                                    COUNT(id) as total_viagens 
                                FROM viagens 
                                WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month 
                                GROUP BY agente
                                ORDER BY total_viagens DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $viagens_maquinista_agente_mensal[] = [
                    'nome' => $row['nome'],
                    'tipo' => $row['tipo'],
                    'total_viagens' => (int)$row['total_viagens']
                ];
            }
        }

        $viagens_maquinista_agente_anual = [];
        $result = $conn->query("SELECT 
                                    COALESCE(maquinista, 'Desconhecido') as nome, 
                                    'Maquinista' as tipo, 
                                    COUNT(id) as total_viagens 
                                FROM viagens 
                                WHERE YEAR(data) = $current_year 
                                GROUP BY maquinista
                                UNION
                                SELECT 
                                    COALESCE(agente, 'Desconhecido') as nome, 
                                    'Agente' as tipo, 
                                    COUNT(id) as total_viagens 
                                FROM viagens 
                                WHERE YEAR(data) = $current_year 
                                GROUP BY agente
                                ORDER BY total_viagens DESC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $viagens_maquinista_agente_anual[] = [
                    'nome' => $row['nome'],
                    'tipo' => $row['tipo'],
                    'total_viagens' => (int)$row['total_viagens']
                ];
            }
        }

        // Calcular totais e porcentagens
        $passageiros_hoje = $metrics['pagantes_hoje'] + $metrics['moradores_hoje'] + $metrics['gratuidade_hoje'] + $metrics['grat_pcd_idoso_hoje'];
        $passageiros_mes_atual = $metrics['pagantes_mes_atual'] + $metrics['moradores_mes_atual'] + $metrics['gratuidade_mes_atual'] + $metrics['grat_pcd_idoso'];
        $passageiros_anual = $metrics['pagantes_anual'] + $metrics['moradores_anual'] + $metrics['gratuidade_anual'] + $metrics['grat_pcd_idoso_anual'];

        $total_passageiros_geral = max($passageiros_anual, 1);
        $frota_ativa_percent = round(($metrics['bondes_ativos'] / max($metrics['total_bondes'], 1)) * 100, 1);
        $operacao_andamento_percent = round(($metrics['viagens_mes_atual'] / max($metrics['total_viagens'], 1)) * 100, 1);
        $fluxo_crescente_percent = round(($passageiros_mes_atual / $total_passageiros_geral) * 100, 1);

        $data = [
            'success' => true,
            'timestamp' => time(),
            'total_bondes' => (int)$metrics['total_bondes'],
            'viagens_realizadas' => (int)$metrics['viagens_mes_atual'],
            'total_passageiros' => $passageiros_mes_atual,
            'passageiros_pagantes' => (int)$metrics['pagantes_mes_atual'],
            'moradores' => (int)$metrics['moradores_mes_atual'],
            'gratuidades' => (int)$metrics['gratuidade_mes_atual'],
            'grat_pcd_idoso' => (int)$metrics['grat_pcd_idoso'],
            'frota_ativa_percent' => $frota_ativa_percent,
            'operacao_andamento_percent' => $operacao_andamento_percent,
            'fluxo_crescente_percent' => $fluxo_crescente_percent,
            'dados_cards' => [
                'diario' => [
                    'viagens' => (int)$metrics['viagens_hoje'],
                    'passageiros' => $passageiros_hoje,
                    'pagantes' => (int)$metrics['pagantes_hoje'],
                    'moradores' => (int)$metrics['moradores_hoje'],
                    'gratuidade' => (int)$metrics['gratuidade_hoje'],
                    'grat_pcd_idoso' => (int)$metrics['grat_pcd_idoso_hoje']
                ],
                'mensal' => [
                    'viagens' => (int)$metrics['viagens_mes_atual'],
                    'passageiros' => $passageiros_mes_atual,
                    'pagantes' => (int)$metrics['pagantes_mes_atual'],
                    'moradores' => (int)$metrics['moradores_mes_atual'],
                    'gratuidade' => (int)$metrics['gratuidade_mes_atual'],
                    'grat_pcd_idoso' => (int)$metrics['grat_pcd_idoso']
                ],
                'anual' => [
                    'viagens' => (int)$metrics['total_viagens'],
                    'passageiros' => $passageiros_anual,
                    'pagantes' => (int)$metrics['pagantes_anual'],
                    'moradores' => (int)$metrics['moradores_anual'],
                    'gratuidade' => (int)$metrics['gratuidade_anual'],
                    'grat_pcd_idoso' => (int)$metrics['grat_pcd_idoso_anual']
                ]
            ],
            'graficos' => [
                'bondes_viagens' => [
                    'diario' => [
                        'labels' => array_column($bondes_viagens_diario, 'bonde'),
                        'data' => array_column($bondes_viagens_diario, 'total_viagens')
                    ],
                    'mensal' => [
                        'labels' => array_column($bondes_viagens_mensal, 'bonde'),
                        'data' => array_column($bondes_viagens_mensal, 'total_viagens')
                    ]
                ],
                'passageiros' => [
                    'diario' => [
                        'pagantes' => (int)$metrics['pagantes_hoje'],
                        'moradores' => (int)$metrics['moradores_hoje'],
                        'gratuidade' => (int)$metrics['gratuidade_hoje'],
                        'grat_pcd_idoso' => (int)$metrics['grat_pcd_idoso_hoje']
                    ],
                    'mensal' => [
                        'pagantes' => (int)$metrics['pagantes_mes_atual'],
                        'moradores' => (int)$metrics['moradores_mes_atual'],
                        'gratuidade' => (int)$metrics['gratuidade_mes_atual'],
                        'grat_pcd_idoso' => (int)$metrics['grat_pcd_idoso']
                    ],
                    'anual' => [
                        'pagantes' => (int)$metrics['pagantes_anual'],
                        'moradores' => (int)$metrics['moradores_anual'],
                        'gratuidade' => (int)$metrics['gratuidade_anual'],
                        'grat_pcd_idoso' => (int)$metrics['grat_pcd_idoso_anual']
                    ]
                ],
                'passageiros_horario' => [
                    'diario' => [
                        'labels' => ['6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h'],
                        'data' => array_values(array_slice($passageiros_por_horario_diario, 6, 15, true))
                    ],
                    'mensal' => [
                        'labels' => ['6h', '7h', '8h', '9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h', '19h', '20h'],
                        'data' => array_values(array_slice($passageiros_por_horario_mensal, 6, 15, true))
                    ]
                ],
                'passageiros_por_mes' => [
                    'anual' => [
                        'labels' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                        'data' => array_values($passageiros_por_mes)
                    ]
                ],
                'viagens_maquinista_agente' => [
                    'diario' => [
                        'labels' => array_map(function($item) { return $item['nome'] . ' (' . $item['tipo'] . ')'; }, $viagens_maquinista_agente_diario),
                        'data' => array_column($viagens_maquinista_agente_diario, 'total_viagens'),
                        'colors' => array_map(function($item) { return $item['tipo'] === 'Maquinista' ? 'rgba(102, 126, 234, 0.8)' : 'rgba(75, 192, 192, 0.8)'; }, $viagens_maquinista_agente_diario)
                    ],
                    'mensal' => [
                        'labels' => array_map(function($item) { return $item['nome'] . ' (' . $item['tipo'] . ')'; }, $viagens_maquinista_agente_mensal),
                        'data' => array_column($viagens_maquinista_agente_mensal, 'total_viagens'),
                        'colors' => array_map(function($item) { return $item['tipo'] === 'Maquinista' ? 'rgba(102, 126, 234, 0.8)' : 'rgba(75, 192, 192, 0.8)'; }, $viagens_maquinista_agente_mensal)
                    ],
                    'anual' => [
                        'labels' => array_map(function($item) { return $item['nome'] . ' (' . $item['tipo'] . ')'; }, $viagens_maquinista_agente_anual),
                        'data' => array_column($viagens_maquinista_agente_anual, 'total_viagens'),
                        'colors' => array_map(function($item) { return $item['tipo'] === 'Maquinista' ? 'rgba(102, 126, 234, 0.8)' : 'rgba(75, 192, 192, 0.8)'; }, $viagens_maquinista_agente_anual)
                    ]
                ]
            ]
        ];

        echo json_encode($data);
        $conn->close();
        exit;

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erro interno: ' . $e->getMessage(),
            'timestamp' => time()
        ]);
        $conn->close();
        exit;
    }
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

$grat_pcd_idoso_hoje = 0;
$result = $conn->query("SELECT COALESCE(SUM(grat_pcd_idoso), 0) as total FROM viagens WHERE DATE(data) = CURDATE()");
if ($result) {
    $row = $result->fetch_assoc();
    $grat_pcd_idoso_hoje = $row['total'];
} else {
    die("Erro na consulta de gratuidades PCD/Idoso diário: " . $conn->error);
}

$passageiros_hoje = 0;
$result = $conn->query("SELECT COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total FROM viagens WHERE DATE(data) = CURDATE()");
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

$grat_pcd_idoso = 0;
$result = $conn->query("SELECT COALESCE(SUM(grat_pcd_idoso), 0) as total FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month");
if ($result) {
    $row = $result->fetch_assoc();
    $grat_pcd_idoso = $row['total'];
} else {
    die("Erro na consulta de gratuidades PCD/Idoso mensal: " . $conn->error);
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
$result = $conn->query("SELECT COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total FROM viagens WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month");
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

$grat_pcd_idoso_anual = 0;
$result = $conn->query("SELECT COALESCE(SUM(grat_pcd_idoso), 0) as total FROM viagens WHERE YEAR(data) = $current_year");
if ($result) {
    $row = $result->fetch_assoc();
    $grat_pcd_idoso_anual = $row['total'];
} else {
    die("Erro na consulta de gratuidades PCD/Idoso anual: " . $conn->error);
}

$passageiros_anual = 0;
$result = $conn->query("SELECT COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total FROM viagens WHERE YEAR(data) = $current_year");
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
                        ORDER BY total_viagens DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bondes_viagens_diario[] = [
            'bonde' => ' ' . $row['bonde'],
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
                        ORDER BY total_viagens DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bondes_viagens_mensal[] = [
            'bonde' => ' ' . $row['bonde'],
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
        $bonde = '          ' . $row['bonde'];
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
$result = $conn->query("SELECT HOUR(created_at) as hora, COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total_passageiros 
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
$result = $conn->query("SELECT HOUR(created_at) as hora, COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total_passageiros 
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
$result = $conn->query("SELECT HOUR(created_at) as hora, COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total_passageiros 
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

// Consultas para viagens por maquinista e agente
$viagens_maquinista_agente_diario = [];
$result = $conn->query("SELECT 
                            COALESCE(maquinista, 'Desconhecido') as nome, 
                            'Maquinista' as tipo, 
                            COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE DATE(data) = CURDATE() 
                        GROUP BY maquinista
                        UNION
                        SELECT 
                            COALESCE(agente, 'Desconhecido') as nome, 
                            'Agente' as tipo, 
                            COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE DATE(data) = CURDATE() 
                        GROUP BY agente
                        ORDER BY total_viagens DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $viagens_maquinista_agente_diario[] = [
            'nome' => $row['nome'],
            'tipo' => $row['tipo'],
            'total_viagens' => (int)$row['total_viagens']
        ];
    }
    error_log("Viagens por maquinista/agente (diário): " . json_encode($viagens_maquinista_agente_diario));
} else {
    die("Erro na consulta de viagens por maquinista/agente (diário): " . $conn->error);
}

$viagens_maquinista_agente_mensal = [];
$result = $conn->query("SELECT 
                            COALESCE(maquinista, 'Desconhecido') as nome, 
                            'Maquinista' as tipo, 
                            COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month 
                        GROUP BY maquinista
                        UNION
                        SELECT 
                            COALESCE(agente, 'Desconhecido') as nome, 
                            'Agente' as tipo, 
                            COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE YEAR(data) = $current_year AND MONTH(data) = $current_month 
                        GROUP BY agente
                        ORDER BY total_viagens DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $viagens_maquinista_agente_mensal[] = [
            'nome' => $row['nome'],
            'tipo' => $row['tipo'],
            'total_viagens' => (int)$row['total_viagens']
        ];
    }
    error_log("Viagens por maquinista/agente (mensal): " . json_encode($viagens_maquinista_agente_mensal));
} else {
    die("Erro na consulta de viagens por maquinista/agente (mensal): " . $conn->error);
}

$viagens_maquinista_agente_anual = [];
$result = $conn->query("SELECT 
                            COALESCE(maquinista, 'Desconhecido') as nome, 
                            'Maquinista' as tipo, 
                            COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE YEAR(data) = $current_year 
                        GROUP BY maquinista
                        UNION
                        SELECT 
                            COALESCE(agente, 'Desconhecido') as nome, 
                            'Agente' as tipo, 
                            COUNT(id) as total_viagens 
                        FROM viagens 
                        WHERE YEAR(data) = $current_year 
                        GROUP BY agente
                        ORDER BY total_viagens DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $viagens_maquinista_agente_anual[] = [
            'nome' => $row['nome'],
            'tipo' => $row['tipo'],
            'total_viagens' => (int)$row['total_viagens']
        ];
    }
    error_log("Viagens por maquinista/agente (anual): " . json_encode($viagens_maquinista_agente_anual));
} else {
    die("Erro na consulta de viagens por maquinista/agente (anual): " . $conn->error);
}

// Consultas para recorde de passageiros por mês
$passageiros_por_mes = array_fill(1, 12, 0);
$result = $conn->query("SELECT MONTH(data) as mes, COALESCE(SUM(pagantes + moradores + gratuidade + grat_pcd_idoso), 0) as total_passageiros 
                        FROM viagens 
                        WHERE YEAR(data) = $current_year 
                        GROUP BY MONTH(data)");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $passageiros_por_mes[(int)$row['mes']] = (int)$row['total_passageiros'];
    }
    error_log("Passageiros por mês (anual): " . json_encode($passageiros_por_mes));
} else {
    die("Erro na consulta de passageiros por mês (anual): " . $conn->error);
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
    <!-- Adicionando html2pdf para melhor captura de gráficos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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

        /* Adicionado loading overlay e spinner */
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
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
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
            /* Increased margin-bottom for better spacing between title and chart */
            margin-bottom: 1.5rem;
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

        /* Melhorado sistema de notificações */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1rem;
            color: var(--text-primary);
            box-shadow: var(--shadow-heavy);
            z-index: 1000;
            transform: translateX(400px);
            transition: var(--transition);
            max-width: 300px;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            border-left: 4px solid #10b981;
        }

        .notification.error {
            border-left: 4px solid #ef4444;
        }

        .notification.info {
            border-left: 4px solid #667eea;
        }

        /* Atualizado CSS para o relógio em tempo real */
        .real-time-clock {
            position: fixed;
            top: 20px;
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

        /* Adicionando estilos para o botão de refresh */
        .refresh-button {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
            position: relative;
            overflow: hidden;
        }

        .refresh-button:hover {
            background: linear-gradient(135deg, #218838, #1ea085);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        .refresh-button:active {
            transform: translateY(0);
        }

        .refresh-button.refreshing {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            pointer-events: none;
        }

        .refresh-button.refreshing .fa-sync-alt {
            animation: spin 1s linear infinite;
        }

        .refresh-countdown {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Adicionando estilos para exportação PDF sem cor de fundo */
        @media print {
            body {
                background: white !important;
                color: #333 !important;
            }
            
            .dashboard-container {
                background: white !important;
            }
            
            .sidebar {
                background: white !important;
                border-right: 1px solid #ddd !important;
            }
            
            .main-content {
                background: white !important;
            }
            
            .card {
                background: white !important;
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }
            
            .section {
                background: white !important;
            }
            
            .chart-container {
                background: white !important;
            }
            
            .table-container {
                background: white !important;
            }
            
            .table {
                background: white !important;
            }
            
            .table th {
                background: #f8f9fa !important;
                color: #333 !important;
            }
            
            .table td {
                background: white !important;
                color: #333 !important;
            }
            
            .refresh-button {
                display: none !important;
            }
            
            .export-button {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Adicionado loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

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
                    <!-- Adicionando botão de refresh automático -->
                    <button class="refresh-button" id="refreshButton" onclick="atualizarDashboard()">
                        <i class="fas fa-sync-alt"></i>
                        <span class="refresh-text">Atualizar</span>
                        <span class="refresh-countdown" id="refreshCountdown">30s</span>
                    </button>
                    <!-- Substituído indicador de última atualização por relógio em tempo real -->
                    <div class="real-time-clock" id="realTimeClock">
                        <i class="fas fa-clock"></i>
                        <span id="currentTime"></span>
                    </div>
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
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon" style="background: var(--primary-gradient);">
                                <i class="fas fa-wheelchair"></i>
                            </div>
                        </div>
                        <h3>Gratuidades PCD/Idoso</h3>
                        <div class="card-value" id="gratPcdIdosoPeriodo"><?php echo number_format($grat_pcd_idoso, 0, ',', '.'); ?></div>
                        <div class="metric-comparison">
                            <span><?php echo $passageiros_mes_atual > 0 ? round(($grat_pcd_idoso / $passageiros_mes_atual) * 100, 1) : 0; ?>% do total</span>
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
                    <div class="chart-card">
                        <h3>
                            <i class="fas fa-users"></i>
                            Quantidade de  Passageiros por Mês
                        </h3>
                        <div class="chart-hover-info">
                            Passe o mouse sobre as barras para ver detalhes
                        </div>
                        <div id="noDataPassageirosMesMessage" class="no-data-message" style="display: none;">
                            <i class="fas fa-chart-bar"></i>
                            <span>Nenhum dado de passageiros disponível para o ano atual</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="passageirosMesChart"></canvas>
                        </div>
                    </div>
                      <div class="chart-card">
                        <h3>
                            <i class="fas fa-chart-bar"></i>
                            Viagens por Maquinista e Agente
                        </h3>
                        <div class="chart-hover-info">
                            Passe o mouse sobre as barras para ver detalhes
                        </div>
                        <!-- Adicionando filtros separados para Agente e Maquinista -->
                        <div class="filter-buttons" style="margin-bottom: 15px; display: flex; gap: 10px; justify-content: center;">
                            <button id="filterAgente" class="filter-btn active" onclick="filtrarMaquinistaAgente('Agente')" style="background: rgba(75, 192, 192, 0.8); color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-size: 12px;">
                                Agente
                            </button>
                            <button id="filterMaquinista" class="filter-btn" onclick="filtrarMaquinistaAgente('Maquinista')" style="background: rgba(102, 126, 234, 0.3); color: #b8c5d6; border: 1px solid rgba(102, 126, 234, 0.5); padding: 8px 16px; border-radius: 5px; cursor: pointer; font-size: 12px;">
                                Maquinista
                            </button>
                        </div>
                        <div id="noDataMaquinistaAgenteMessage" class="no-data-message" style="display: none;">
                            <i class="fas fa-chart-bar"></i>
                            <span>Nenhum dado disponível para o período selecionado</span>
                        </div>
                        <div class="chart-container">
                            <canvas id="maquinistaAgenteChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>
                    <i class="fas fa-cogs"></i>
                    Detalhes Operacionais e Monitoramento
                </h2>
                <div class="charts-grid2">
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
                                        <th><i class="fas fa-file-alt"></i> Tipo</th>
                                        <th><i class="fas fa-map-marker-alt"></i> Localização</th>
                                        <th><i class="fas fa-thermometer-half"></i> Severidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_acidentes = "SELECT data_registro, categoria, localizacao, severidade FROM acidentes ORDER BY data_registro DESC LIMIT 5";
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
                        <h3>
                            <i class="fas fa-history"></i>
                            Viagens Recentes
                        </h3>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar"></i> Data</th>
                                        <!-- <th><i class="fas fa-undo"></i> Retorno</th> -->
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
                                            // echo "<td>" . ($row['retorno'] ? htmlspecialchars(date('d/m/Y', strtotime($row['retorno']))) : '<span style="color: var(--text-muted);">N/A</span>') . "</td>";
                                            echo "<td><span class='status-badge status-active'><i class='fas fa-train'></i>  " . htmlspecialchars($row['bonde']) . "</span></td>";
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
                    <th><i class="fas fa-clock"></i> Última Atualização</th>
                <th><i class="fas fa-train"></i> Bonde</th>
                <th><i class="fas fa-signal"></i> Status</th>
            
            </tr>
        </thead>
        <tbody>
            <?php
            // Ajuste na consulta SQL para incluir as colunas 'modelo' e 'ativo'
            $sql_status = "SELECT id, modelo, ativo FROM bondes ORDER BY id ASC";
            $result_status = $conn->query($sql_status);
            if ($result_status === false) {
                echo "<tr><td colspan='3'>Erro na consulta de status: " . $conn->error . "</td></tr>";
            } elseif ($result_status->num_rows > 0) {
                while ($row = $result_status->fetch_assoc()) {
                    echo "<tr>";
                         echo "<td>" . date('d/m/Y ') . "</td>";
                    // Exibe o valor da coluna 'modelo' na coluna Bonde
                    echo "<td><strong>" . (empty($row['modelo']) ? 'Sem modelo' : htmlspecialchars($row['modelo'])) . "</strong></td>";
                    // Verifica o valor da coluna 'ativo' e exibe 'Operacional' para 1 ou 'Inoperante-Manutenção' para 0
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
    <h3>
        <i class="fas fa-tools"></i>
        Manutenções Programadas
    </h3>
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
                // Configuração de conexão (exemplo com PDO)
                $host = 'localhost';
                $dbname = 'gm_sicbd';
                $username = 'root';
                $password = '';

                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Consulta para pegar manutenções programadas (status 'pendente' ou 'em_andamento')
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
                        echo "<tr>";
                        echo "<td colspan='4' style='text-align: center; color: var(--text-muted); padding: 2rem;'>";
                        echo "<i class='fas fa-check-circle' style='font-size: 2rem; margin-bottom: 1rem; display: block;'></i>";
                        echo "Nenhuma manutenção programada";
                        echo "</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr>";
                    echo "<td colspan='4' style='text-align: center; color: red; padding: 2rem;'>Erro: " . htmlspecialchars($e->getMessage()) . "</td>";
                    echo "</tr>";
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

        // Dados para o gráfico de recorde de passageiros por mês
        const dadosPassageirosMes = {
            anual: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                data: [<?php echo implode(',', array_values($passageiros_por_mes)); ?>]
            }
        };

        // Dados para o gráfico de viagens por maquinista e agente
        const dadosViagensMaquinistaAgente = {
            diario: {
                labels: [<?php echo "'" . implode("','", array_map(function($item) { return $item['nome'] . ' (' . $item['tipo'] . ')'; }, $viagens_maquinista_agente_diario)) . "'"; ?>],
                data: [<?php echo implode(',', array_column($viagens_maquinista_agente_diario, 'total_viagens')); ?>],
                colors: [<?php echo "'" . implode("','", array_map(function($item) { return $item['tipo'] === 'Maquinista' ? 'rgba(102, 126, 234, 0.8)' : 'rgba(75, 192, 192, 0.8)'; }, $viagens_maquinista_agente_diario)) . "'"; ?>]
            },
            mensal: {
                labels: [<?php echo "'" . implode("','", array_map(function($item) { return $item['nome'] . ' (' . $item['tipo'] . ')'; }, $viagens_maquinista_agente_mensal)) . "'"; ?>],
                data: [<?php echo implode(',', array_column($viagens_maquinista_agente_mensal, 'total_viagens')); ?>],
                colors: [<?php echo "'" . implode("','", array_map(function($item) { return $item['tipo'] === 'Maquinista' ? 'rgba(102, 126, 234, 0.8)' : 'rgba(75, 192, 192, 0.8)'; }, $viagens_maquinista_agente_mensal)) . "'"; ?>]
            },
            anual: {
                labels: [<?php echo "'" . implode("','", array_map(function($item) { return $item['nome'] . ' (' . $item['tipo'] . ')'; }, $viagens_maquinista_agente_anual)) . "'"; ?>],
                data: [<?php echo implode(',', array_column($viagens_maquinista_agente_anual, 'total_viagens')); ?>],
                colors: [<?php echo "'" . implode("','", array_map(function($item) { return $item['tipo'] === 'Maquinista' ? 'rgba(102, 126, 234, 0.8)' : 'rgba(75, 192, 192, 0.8)'; }, $viagens_maquinista_agente_anual)) . "'"; ?>]
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
            // passageirosChart.options.plugins.title.text = `Distribuição de Passageiros (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual'})`;
            
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
                bondesViagensChart.options.scales.x.title.text = '';
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

            // bondesViagensChart.options.plugins.title.text = `Bondes com Maior Performance (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual por Mês'})`;
            
            // Configurar datalabels para mostrar total de viagens
            bondesViagensChart.options.plugins.datalabels.formatter = function(value, context) {
                return formatNumber(value); // Mostra o total de viagens
            };
            
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
            // viagensDiaSemanaChart.options.plugins.title.text = `Padrão Semanal de Viagens (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual'})`;
            
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
            // passageirosHorarioChart.options.plugins.title.text = `Fluxo de Passageiros por Horário (${periodo === 'diario' ? 'Hoje' : periodo === 'mensal' ? 'Mês Atual' : 'Anual'})`;
            
            // Atualizar tooltips com porcentagens
            passageirosHorarioChart.options.plugins.tooltip.callbacks.label = function(context) {
                const value = context.raw || 0;
                const percentage = calculatePercentage(value, total);
                return `Passageiros: ${formatNumber(value)} (${percentage}%)`;
            };
            
            passageirosHorarioChart.update();
        }

        // Função para atualizar o gráfico de recorde de passageiros por mês
        function atualizarGraficoPassageirosMes(periodo) {
            const dados = dadosPassageirosMes.anual; // Apenas anual para este gráfico
            const total = dados.data.reduce((sum, value) => sum + value, 0);
            const noDataMessage = document.getElementById('noDataPassageirosMesMessage');
            const canvas = document.getElementById('passageirosMesChart');

            if (total === 0) {
                noDataMessage.style.display = 'flex';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            passageirosMesChart.data.datasets[0].data = dados.data;
            // passageirosMesChart.options.plugins.title.text = `Recorde de Passageiros por Mês (Ano ${<?php echo $current_year; ?>})`;
            
            // Atualizar tooltips com porcentagens
            passageirosMesChart.options.plugins.tooltip.callbacks.label = function(context) {
                const value = context.raw || 0;
                const percentage = calculatePercentage(value, total);
                return `Passageiros: ${formatNumber(value)} (${percentage}%)`;
            };
            
            passageirosMesChart.update();
        }

        // Função para atualizar o gráfico de viagens por maquinista e agente
        function atualizarGraficoMaquinistaAgente(periodo) {
            const dados = dadosViagensMaquinistaAgente[periodo];
            const total = dados.data.reduce((sum, value) => sum + value, 0);
            const noDataMessage = document.getElementById('noDataMaquinistaAgenteMessage');
            const canvas = document.getElementById('maquinistaAgenteChart');

            if (total === 0) {
                noDataMessage.style.display = 'flex';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            maquinistaAgenteChart.data.labels = dados.labels;
            maquinistaAgenteChart.data.datasets[0].data = dados.data;
            maquinistaAgenteChart.data.datasets[0].backgroundColor = dados.colors;
            maquinistaAgenteChart.data.datasets[0].borderColor = dados.colors.map(color => color.replace('0.8', '1'));
            // maquinistaAgenteChart.options.plugins.title.text = `Viagens por Maquinista e Agente (${periodo.charAt(0).toUpperCase() + periodo.slice(1)})`;
            
            // Atualizar tooltips com porcentagens
            maquinistaAgenteChart.options.plugins.tooltip.callbacks.label = function(context) {
                const value = context.raw || 0;
                const percentage = calculatePercentage(value, total);
                return `Viagens: ${formatNumber(value)} (${percentage}%)`;
            };
            
            maquinistaAgenteChart.update();
        }

        // Função para atualizar todo o painel
        function atualizarPainel(periodo) {
            atualizarCards(periodo);
            atualizarGraficoPassageiros(periodo);
            atualizarGraficoBondesViagens(periodo);
            atualizarGraficoViagensDiaSemana(periodo);
            atualizarGraficoPassageirosHorario(periodo);
            atualizarGraficoPassageirosMes(periodo);
            atualizarGraficoMaquinistaAgente(periodo);
        }

        function exportarParaPDF() {
            // Mostrar indicador de carregamento
            const exportButton = document.querySelector('.export-button');
            const originalText = exportButton.innerHTML;
            exportButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gerando PDF...';
            exportButton.disabled = true;

            // Configurar elemento para exportação
            const element = document.querySelector('.main-content');
            
            // Aplicar classe para estilos de exportação
            document.body.classList.add('pdf-export');
            
            const opt = {
                margin: [10, 10, 10, 10],
                filename: `Dashboard_Bonde_${new Date().toISOString().slice(0, 10).replace(/-/g, '')}.pdf`,
                image: { 
                    type: 'jpeg', 
                    quality: 0.98 
                },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    scrollX: 0,
                    scrollY: 0,
                    width: element.scrollWidth,
                    height: element.scrollHeight
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'landscape' 
                },
                pagebreak: { 
                    mode: ['avoid-all', 'css', 'legacy'] 
                }
            };

            // Aguardar um momento para garantir que os gráficos estejam renderizados
            setTimeout(() => {
                html2pdf().set(opt).from(element).save().then(() => {
                    // Remover classe de exportação
                    document.body.classList.remove('pdf-export');
                    
                    // Restaurar botão
                    exportButton.innerHTML = originalText;
                    exportButton.disabled = false;
                    
                    console.log('PDF exportado com sucesso!');
                }).catch((error) => {
                    console.error('Erro ao exportar PDF:', error);
                    
                    // Remover classe de exportação
                    document.body.classList.remove('pdf-export');
                    
                    // Restaurar botão
                    exportButton.innerHTML = originalText;
                    exportButton.disabled = false;
                    
                    alert('Erro ao exportar PDF. Tente novamente.');
                });
            }, 1000);
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
                layout: {
                    padding: {
                        top: 25 // Espaço extra para evitar sobreposição do título
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        // text: 'Bondes com Maior Performance (Mês Atual)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#ffffff',
                        padding: {
                            bottom: 15
                        }
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
                                return `Viagens: ${formatNumber(context.raw)}`;
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
                            return formatNumber(value); // Mostra o total de viagens
                        },
                        anchor: 'end',
                        align: 'top',
                        offset: 4 // Espaçamento para evitar sobreposição
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
                layout: {
                    padding: {
                        top: 25
                    }
                },
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
                        // text: 'Distribuição de Passageiros (Mês Atual)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#ffffff',
                        padding: {
                            bottom: 15
                        }
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
                        },
                        anchor: 'center',
                        align: 'center'
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
                layout: {
                    padding: {
                        top: 25
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        // text: 'Padrão Semanal de Viagens (Mês Atual)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#ffffff',
                        padding: {
                            bottom: 15
                        }
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
                        align: 'top',
                        offset: 4
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
                layout: {
                    padding: {
                        top: 35 // Mais espaço para evitar sobreposição
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        // text: 'Fluxo de Passageiros por Horário (Mês Atual)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#ffffff',
                        padding: {
                            bottom: 15
                        }
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
                            const value = context.dataset.data[context.dataIndex];
                            // Só mostra labels nos pontos com valores mais altos para evitar sobreposição
                            const maxValue = Math.max(...context.dataset.data);
                            return value > 0 && value >= maxValue * 0.3; // Mostra apenas se for 30% do valor máximo
                        },
                        color: '#ffffff',
                        backgroundColor: 'rgba(255, 99, 132, 0.9)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        padding: {
                            top: 2,
                            bottom: 2,
                            left: 4,
                            right: 4
                        },
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
                        align: 'top',
                        offset: 8
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

        // Gráfico de barras: Recorde de passageiros por mês
        const passageirosMesCtx = document.getElementById('passageirosMesChart').getContext('2d');
        const passageirosMesChart = new Chart(passageirosMesCtx, {
            type: 'bar',
            data: {
                labels: dadosPassageirosMes.anual.labels,
                datasets: [{
                    label: 'Passageiros',
                    data: dadosPassageirosMes.anual.data,
                    backgroundColor: 'rgba(153, 102, 255, 0.8)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 25
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        // text: 'Recorde de Passageiros por Mês (Ano <?php echo $current_year; ?>)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#ffffff',
                        padding: {
                            bottom: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 15, 35, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#b8c5d6',
                        borderColor: 'rgba(153, 102, 255, 0.3)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `Passageiros: ${formatNumber(context.raw)}`;
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
                            return formatNumber(value);
                        },
                        anchor: 'end',
                        align: 'top',
                        offset: 4
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
                            text: 'Meses',
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

        // Gráfico de barras: Viagens por maquinista e agente
  let maquinistaAgenteChart;
        const maquinistaAgenteCtx = document.getElementById('maquinistaAgenteChart').getContext('2d');
        maquinistaAgenteChart = new Chart(maquinistaAgenteCtx, {
            type: 'bar',
            data: {
                labels: dadosViagensMaquinistaAgente.mensal.labels,
                datasets: [{
                    label: 'Viagens',
                    data: dadosViagensMaquinistaAgente.mensal.data,
                    backgroundColor: dadosViagensMaquinistaAgente.mensal.colors,
                    borderColor: dadosViagensMaquinistaAgente.mensal.colors.map(color => color.replace('0.8', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // Altera para barras horizontais, movendo labels (maquinistas e agentes) para o eixo Y
                scales: {
                    x: {
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
                    y: {
                        title: {
                            display: true,
                            text: 'Maquinistas e Agentes',
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
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Viagens por Maquinista e Agente (Mês Atual)',
                        font: {
                            size: 12,
                            weight: 'bold'
                        },
                        color: '#ffffff',
                        padding: {
                            bottom: 15
                        }
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
                                const percentage = calculatePercentage(value, total);
                                return `Viagens: ${formatNumber(value)} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        formatter: function(value) {
                            return formatNumber(value);
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

        const totalMaquinistaAgente = dadosViagensMaquinistaAgente.mensal.data.reduce((sum, value) => sum + value, 0);
        if (totalMaquinistaAgente === 0) {
            document.getElementById('noDataMaquinistaAgenteMessage').style.display = 'flex';
            document.getElementById('maquinistaAgenteChart').style.display = 'none';
        } else {
            document.getElementById('noDataMaquinistaAgenteMessage').style.display = 'none';
            document.getElementById('maquinistaAgenteChart').style.display = 'block';
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

        // Função para atualizar relógio em tempo real
        function atualizarRelogio() {
            const agora = new Date();
            const opcoes = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'America/Sao_Paulo'
            };
            const tempoFormatado = agora.toLocaleDateString('pt-BR', opcoes);
            document.getElementById('currentTime').textContent = tempoFormatado;
        }

        // Atualizar relógio a cada segundo
        setInterval(atualizarRelogio, 1000);
        atualizarRelogio(); // Executar imediatamente

      async function atualizarDadosAutomaticamente() {
            try {
                console.log('[v0] Iniciando atualização automática dos dados...');
                const timestamp = new Date().getTime();
                const response = await fetch(`?api=data&t=${timestamp}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const dados = await response.json();
                console.log('[v0] Dados recebidos:', dados);
                
                if (dados.success) {
                    // Atualizar cards principais
                    const totalBondesEl = document.getElementById('totalBondes');
                    const viagensPeriodoEl = document.getElementById('viagensPeriodo');
                    const passageirosPeriodoEl = document.getElementById('passageirosPeriodo');
                    const pagantesPeriodoEl = document.getElementById('pagantesPeriodo');
                    const moradoresPeriodoEl = document.getElementById('moradoresPeriodo');
                    const gratuidadePeriodoEl = document.getElementById('gratuidadePeriodo');
                    
                    if (totalBondesEl) totalBondesEl.textContent = formatNumber(dados.total_bondes);
                    if (viagensPeriodoEl) viagensPeriodoEl.textContent = formatNumber(dados.viagens_realizadas);
                    if (passageirosPeriodoEl) passageirosPeriodoEl.textContent = formatNumber(dados.total_passageiros);
                    if (pagantesPeriodoEl) pagantesPeriodoEl.textContent = formatNumber(dados.passageiros_pagantes);
                    if (moradoresPeriodoEl) moradoresPeriodoEl.textContent = formatNumber(dados.moradores);
                    if (gratuidadePeriodoEl) gratuidadePeriodoEl.textContent = formatNumber(dados.gratuidades);
                    
                    // Atualizar porcentagens dos cards
                    const metricComparisons = document.querySelectorAll('.metric-comparison span');
                    if (metricComparisons.length >= 3) {
                        metricComparisons[0].textContent = dados.frota_ativa_percent + '% do total';
                        metricComparisons[1].textContent = dados.operacao_andamento_percent + '% do total';
                        metricComparisons[2].textContent = dados.fluxo_crescente_percent + '% do total';
                    }
                    
                    // Atualizar dados globais para os gráficos
                    if (dados.dados_cards) {
                        dadosCards = dados.dados_cards;
                    }
                    
                    if (dados.graficos) {
                        dadosBondesViagens = dados.graficos.bondes_viagens;
                        dadosPassageiros = dados.graficos.passageiros;
                        dadosPassageirosHorario = dados.graficos.passageiros_horario;
                        dadosPassageirosMes = dados.graficos.passageiros_por_mes;
                        dadosViagensMaquinistaAgente = dados.graficos.viagens_maquinista_agente;
                    }
                    
                    // Recriar gráficos com novos dados
                    const periodoAtual = document.getElementById('globalPeriodSelect').value;
                    
                    // Destruir e recriar todos os gráficos
                    if (bondesViagensChart) {
                        bondesViagensChart.destroy();
                    }
                    if (passageirosChart) {
                        passageirosChart.destroy();
                    }
                    if (viagensDiaSemanaChart) {
                        viagensDiaSemanaChart.destroy();
                    }
                    if (passageirosHorarioChart) {
                        passageirosHorarioChart.destroy();
                    }
                    if (passageirosMesChart) {
                        passageirosMesChart.destroy();
                    }
                    if (maquinistaAgenteChart) {
                        maquinistaAgenteChart.destroy();
                    }
                    
                    // Recriar gráficos
                    setTimeout(() => {
                        // Recriar gráfico de bondes com configuração completa
                        bondesViagensChart = new Chart(bondesViagensCtx, {
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
                                layout: {
                                    padding: {
                                        top: 25
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    title: {
                                        display: true,
                                        text: 'Bondes com Maior Performance (Mês Atual)',
                                        font: {
                                            size: 12,
                                            weight: 'bold'
                                        },
                                        color: '#ffffff',
                                        padding: {
                                            bottom: 15
                                        }
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
                                                return `Viagens: ${formatNumber(context.raw)}`;
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
                                            return formatNumber(value);
                                        },
                                        anchor: 'end',
                                        align: 'top',
                                        offset: 4
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

                        // Recriar gráfico de passageiros com configuração completa
                        passageirosChart = new Chart(passageirosCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Pagantes', 'Moradores', 'Gratuidade'],
                                datasets: [{
                                    data: [dadosPassageiros.mensal.pagantes, dadosPassageiros.mensal.moradores, dadosPassageiros.mensal.gratuidade],
                                    backgroundColor: [
                                        'rgba(102, 126, 234, 0.8)',
                                        'rgba(75, 192, 192, 0.8)',
                                        'rgba(255, 159, 64, 0.8)'
                                    ],
                                    borderColor: [
                                        'rgba(102, 126, 234, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(255, 159, 64, 1)'
                                    ],
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '55%',
                                layout: {
                                    padding: {
                                        top: 25
                                    }
                                },
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            color: '#b8c5d6',
                                            font: { size: 11 },
                                            padding: 15,
                                            usePointStyle: true,
                                            pointStyle: 'circle'
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Distribuição de Passageiros (Mês Atual)',
                                        font: {
                                            size: 12,
                                            weight: 'bold'
                                        },
                                        color: '#ffffff',
                                        padding: {
                                            bottom: 15
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 15, 35, 0.95)',
                                        titleColor: '#ffffff',
                                        bodyColor: '#b8c5d6',
                                        borderColor: 'rgba(102, 126, 234, 0.3)',
                                        borderWidth: 1,
                                        cornerRadius: 8,
                                        displayColors: true,
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const value = context.raw || 0;
                                                const total = dadosPassageiros.mensal.pagantes + dadosPassageiros.mensal.moradores + dadosPassageiros.mensal.gratuidade;
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
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = calculatePercentage(value, total);
                                            return `${percentage}%`;
                                        }
                                    }
                                }
                            }
                        });

                        // Recriar outros gráficos com suas configurações completas
                        viagensDiaSemanaChart = new Chart(viagensDiaSemanaCtx, {
                            // Configuração completa do gráfico de viagens por dia da semana
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
                                    legend: { display: false },
                                    title: {
                                        display: true,
                                        text: 'Padrão Semanal de Viagens (Mês Atual)',
                                        font: { size: 12, weight: 'bold' },
                                        color: '#ffffff',
                                        padding: { bottom: 15 }
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
                                                const total = dadosViagensDiaSemana.mensal.data.reduce((a, b) => a + b, 0);
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
                                        font: { weight: 'bold', size: 10 },
                                        formatter: function(value) {
                                            return formatNumber(value);
                                        },
                                        anchor: 'end',
                                        align: 'top',
                                        offset: 4
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
                                        grid: { color: 'rgba(35, 53, 84, 0.5)' },
                                        ticks: { color: '#b8c5d6', font: { size: 10 } }
                                    },
                                    x: {
                                        grid: { color: 'rgba(35, 53, 84, 0.5)' },
                                        ticks: { color: '#b8c5d6', font: { size: 10 } }
                                    }
                                }
                            }
                        });

                        // Recriar gráfico de passageiros por horário
                        passageirosHorarioChart = new Chart(passageirosHorarioCtx, {
                            type: 'line',
                            data: {
                                labels: dadosPassageirosHorario.mensal.labels,
                                datasets: [{
                                    label: 'Passageiros',
                                    data: dadosPassageirosHorario.mensal.data,
                                    borderColor: 'rgba(255, 159, 64, 1)',
                                    backgroundColor: 'rgba(255, 159, 64, 0.1)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: 'rgba(255, 159, 64, 1)',
                                    pointBorderColor: '#ffffff',
                                    pointBorderWidth: 2,
                                    pointRadius: 5,
                                    pointHoverRadius: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    title: {
                                        display: true,
                                        text: 'Fluxo de Passageiros por Horário (Mês Atual)',
                                        font: { size: 12, weight: 'bold' },
                                        color: '#ffffff',
                                        padding: { bottom: 15 }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 15, 35, 0.95)',
                                        titleColor: '#ffffff',
                                        bodyColor: '#b8c5d6',
                                        borderColor: 'rgba(255, 159, 64, 0.3)',
                                        borderWidth: 1,
                                        cornerRadius: 8,
                                        displayColors: false,
                                        callbacks: {
                                            label: function(context) {
                                                return `Passageiros: ${formatNumber(context.raw)}`;
                                            }
                                        }
                                    },
                                    datalabels: { display: false }
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
                                        grid: { color: 'rgba(35, 53, 84, 0.5)' },
                                        ticks: { color: '#b8c5d6', font: { size: 10 } }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Horário',
                                            color: '#b8c5d6',
                                            font: { size: 11 }
                                        },
                                        grid: { color: 'rgba(35, 53, 84, 0.5)' },
                                        ticks: { color: '#b8c5d6', font: { size: 10 } }
                                    }
                                }
                            }
                        });

                        // Recriar gráfico de passageiros por mês
                        passageirosMesChart = new Chart(passageirosMesCtx, {
                            type: 'bar',
                            data: {
                                labels: dadosPassageirosMes.labels,
                                datasets: [{
                                    label: 'Passageiros',
                                    data: dadosPassageirosMes.data,
                                    backgroundColor: 'rgba(153, 102, 255, 0.8)',
                                    borderColor: 'rgba(153, 102, 255, 1)',
                                    borderWidth: 2,
                                    borderRadius: 6,
                                    borderSkipped: false,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    title: {
                                        display: true,
                                        text: 'Recorde de Passageiros por Mês',
                                        font: { size: 12, weight: 'bold' },
                                        color: '#ffffff',
                                        padding: { bottom: 15 }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 15, 35, 0.95)',
                                        titleColor: '#ffffff',
                                        bodyColor: '#b8c5d6',
                                        borderColor: 'rgba(153, 102, 255, 0.3)',
                                        borderWidth: 1,
                                        cornerRadius: 8,
                                        displayColors: false,
                                        callbacks: {
                                            label: function(context) {
                                                return `Passageiros: ${formatNumber(context.raw)}`;
                                            }
                                        }
                                    },
                                    datalabels: {
                                        display: function(context) {
                                            return context.dataset.data[context.dataIndex] > 0;
                                        },
                                        color: '#ffffff',
                                        font: { weight: 'bold', size: 10 },
                                        formatter: function(value) {
                                            return formatNumber(value);
                                        },
                                        anchor: 'end',
                                        align: 'top',
                                        offset: 4
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
                                        grid: { color: 'rgba(35, 53, 84, 0.5)' },
                                        ticks: { color: '#b8c5d6', font: { size: 10 } }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Meses',
                                            color: '#b8c5d6',
                                            font: { size: 11 }
                                        },
                                        grid: { color: 'rgba(35, 53, 84, 0.5)' },
                                        ticks: { color: '#b8c5d6', font: { size: 10 } }
                                    }
                                }
                            }
                        });

                        // Recriar gráfico de maquinista e agente
                        maquinistaAgenteChart = new Chart(maquinistaAgenteCtx, {
                            type: 'bar',
                            data: {
                                labels: dadosViagensMaquinistaAgente.mensal.labels,
                                datasets: [{
                                    label: 'Viagens',
                                    data: dadosViagensMaquinistaAgente.mensal.data,
                                    backgroundColor: dadosViagensMaquinistaAgente.mensal.colors,
                                    borderColor: dadosViagensMaquinistaAgente.mensal.colors.map(color => color.replace('0.8', '1')),
                                    borderWidth: 2,
                                    borderRadius: 6,
                                    borderSkipped: false,
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    title: {
                                        display: true,
                                        text: 'Viagens por Maquinista e Agente (Mês Atual)',
                                        font: { size: 12, weight: 'bold' },
                                        color: '#ffffff',
                                        padding: { bottom: 15 }
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
                                                const value = context.raw || 0;
                                                const total = dadosViagensMaquinistaAgente.mensal.data.reduce((a, b) => a + b, 0);
                                                const percentage = calculatePercentage(value, total);
                                                return `Viagens: ${formatNumber(value)} (${percentage}%)`;
                                            }
                                        }
                                    },
                                    datalabels: {
                                        display: function(context) {
                                            return context.dataset.data[context.dataIndex] > 0;
                                        },
                                        color: '#ffffff',
                                        font: { weight: 'bold', size: 10 },
                                        formatter: function(value) {
                                            return formatNumber(value);
                                        },
                                        anchor: 'end',
                                        align: 'right',
                                        offset: 4
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Número de Viagens',
                                            color: '#b8c5d6',
                                            font: { size: 11 }
                                        },
                                        grid: { color: 'rgba(35, 53, 84, 0.5)' },
                                        ticks: { color: '#b8c5d6', font: { size: 10 } }
                                    },
                                    y: {
                                        grid: { color: 'rgba(35, 53, 84, 0.5)' },
                                        ticks: { color: '#b8c5d6', font: { size: 10 } }
                                    }
                                }
                            }
                        });
                        
                        // Aplicar filtro padrão (Agente) após recriar o gráfico
                        filtrarMaquinistaAgente('Agente');
                        
                        atualizarPainel(periodoAtual);
                    }, 100);

                    // Indicador visual de atualização bem-sucedida
                    const clockIndicator = document.getElementById('realTimeClock');
                    if (clockIndicator) {
                        clockIndicator.style.backgroundColor = 'rgba(34, 197, 94, 0.1)';
                        clockIndicator.style.borderColor = '#22c55e';
                        setTimeout(() => {
                            clockIndicator.style.backgroundColor = '';
                            clockIndicator.style.borderColor = '';
                        }, 2000);
                    }
                    
                    console.log('[v0] Todos os dados atualizados com sucesso (cards, gráficos e tabelas)');
                    
                } else {
                    console.error('[v0] Erro ao atualizar dados:', dados.error);
                    // Indicador visual de erro
                    const clockIndicator = document.getElementById('realTimeClock');
                    if (clockIndicator) {
                        clockIndicator.style.backgroundColor = 'rgba(239, 68, 68, 0.1)';
                        clockIndicator.style.borderColor = '#ef4444';
                        setTimeout(() => {
                            clockIndicator.style.backgroundColor = '';
                            clockIndicator.style.borderColor = '';
                        }, 3000);
                    }
                }
                
            } catch (error) {
                console.error('[v0] Erro na atualização automática:', error);
                // Indicador visual de erro de conexão
                const clockIndicator = document.getElementById('realTimeClock');
                if (clockIndicator) {
                    clockIndicator.style.backgroundColor = 'rgba(239, 68, 68, 0.1)';
                    clockIndicator.style.borderColor = '#ef4444';
                    setTimeout(() => {
                        clockIndicator.style.backgroundColor = '';
                        clockIndicator.style.borderColor = '';
                    }, 3000);
                }
            }
        }

        let intervalId = setInterval(atualizarDadosAutomaticamente, 10000);
        
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(intervalId);
                console.log('[v0] Atualização automática pausada - página não visível');
            } else {
                intervalId = setInterval(atualizarDadosAutomaticamente, 10000);
                atualizarDadosAutomaticamente(); // Atualizar imediatamente
                console.log('[v0] Atualização automática retomada - página visível');
            }
        });

        // Executar primeira atualização após 2 segundos
        setTimeout(atualizarDadosAutomaticamente, 2000);

        function atualizarDashboard() {
            const refreshButton = document.getElementById('refreshButton');
            const refreshText = refreshButton.querySelector('.refresh-text');
            const countdownElement = document.getElementById('refreshCountdown');
            
            // Parar contador atual
            clearInterval(countdownInterval);
            clearInterval(autoRefreshInterval);
            
            // Indicar que está atualizando
            refreshButton.classList.add('refreshing');
            refreshText.textContent = 'Atualizando...';
            countdownElement.textContent = '';
            
            console.log('[v0] Iniciando atualização do dashboard');
            
            // Recarregar a página para garantir dados atualizados
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        let refreshCountdown = 30;
        let countdownInterval;
        let autoRefreshInterval;

        function iniciarContadorRefresh() {
            refreshCountdown = 30;
            const countdownElement = document.getElementById('refreshCountdown');
            
            countdownInterval = setInterval(() => {
                refreshCountdown--;
                if (countdownElement) {
                    countdownElement.textContent = refreshCountdown + 's';
                }
                
                if (refreshCountdown <= 0) {
                    atualizarDashboard();
                }
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('[v0] Dashboard carregado, iniciando contador de refresh');
            iniciarContadorRefresh();
        });

        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(countdownInterval);
                clearInterval(autoRefreshInterval);
                console.log('[v0] Atualização pausada - página não visível');
            } else {
                iniciarContadorRefresh();
                console.log('[v0] Atualização retomada - página visível');
            }
        });

       function atualizarGraficoMaquinistaAgente(periodo) {
    // Remove qualquer filtro ativo no carregamento inicial
    const botoesFiltro = document.querySelectorAll('.filter-btn');
    botoesFiltro.forEach(btn => btn.classList.remove('active'));

    const dados = dadosViagensMaquinistaAgente[periodo];
    const total = dados.data.reduce((sum, value) => sum + value, 0);
    const noDataMessage = document.getElementById('noDataMaquinistaAgenteMessage');
    const canvas = document.getElementById('maquinistaAgenteChart');

    // Verifica se há um filtro ativo após a limpeza
    const filtroAtivo = document.querySelector('.filter-btn.active');
    const tipoFiltro = filtroAtivo ? (filtroAtivo.id === 'filterAgente' ? 'Agente' : 'Maquinista') : null;

    if (tipoFiltro) {
        filtrarMaquinistaAgente(tipoFiltro);
    } else {
        if (total === 0) {
            noDataMessage.style.display = 'flex';
            canvas.style.display = 'none';
        } else {
            noDataMessage.style.display = 'none';
            canvas.style.display = 'block';
        }

        maquinistaAgenteChart.data.labels = dados.labels;
        maquinistaAgenteChart.data.datasets[0].data = dados.data;
        maquinistaAgenteChart.data.datasets[0].backgroundColor = dados.colors;
        maquinistaAgenteChart.data.datasets[0].borderColor = dados.colors.map(color => color.replace('0.8', '1'));
        maquinistaAgenteChart.options.plugins.title.text = `Viagens por Maquinista e Agente (${periodo.charAt(0).toUpperCase() + periodo.slice(1)})`;
        
        maquinistaAgenteChart.options.plugins.tooltip.callbacks.label = function(context) {
            const value = context.raw || 0;
            const percentage = calculatePercentage(value, total);
            return `Viagens: ${formatNumber(value)} (${percentage}%)`;
        };
        
        maquinistaAgenteChart.update();
    }
}
    function filtrarMaquinistaAgente(tipoFiltro) {
            const periodoAtual = document.getElementById('globalPeriodSelect').value;
            const dados = dadosViagensMaquinistaAgente[periodoAtual];
            
            // Filtrar dados baseado no tipo selecionado
            const dadosFiltrados = {
                labels: [],
                data: [],
                colors: []
            };
            
            dados.labels.forEach((label, index) => {
                if (label.includes(`(${tipoFiltro})`)) {
                    dadosFiltrados.labels.push(label);
                    dadosFiltrados.data.push(dados.data[index]);
                    dadosFiltrados.colors.push(dados.colors[index]);
                }
            });
            
            // Atualizar botões de filtro
            document.getElementById('filterAgente').classList.remove('active');
            document.getElementById('filterMaquinista').classList.remove('active');
            document.getElementById(`filter${tipoFiltro}`).classList.add('active');
            
            // Atualizar estilos dos botões
            const botoes = document.querySelectorAll('.filter-btn');
            botoes.forEach(btn => {
                if (btn.classList.contains('active')) {
                    if (btn.id === 'filterAgente') {
                        btn.style.background = 'rgba(75, 192, 192, 0.8)';
                        btn.style.color = 'white';
                        btn.style.border = 'none';
                    } else {
                        btn.style.background = 'rgba(102, 126, 234, 0.8)';
                        btn.style.color = 'white';
                        btn.style.border = 'none';
                    }
                } else {
                    btn.style.background = 'rgba(102, 126, 234, 0.3)';
                    btn.style.color = '#b8c5d6';
                    btn.style.border = '1px solid rgba(102, 126, 234, 0.5)';
                }
            });
            
            const total = dadosFiltrados.data.reduce((sum, value) => sum + value, 0);
            const noDataMessage = document.getElementById('noDataMaquinistaAgenteMessage');
            const canvas = document.getElementById('maquinistaAgenteChart');

            if (total === 0) {
                noDataMessage.style.display = 'flex';
                canvas.style.display = 'none';
            } else {
                noDataMessage.style.display = 'none';
                canvas.style.display = 'block';
            }

            // Atualizar gráfico com dados filtrados
            maquinistaAgenteChart.data.labels = dadosFiltrados.labels;
            maquinistaAgenteChart.data.datasets[0].data = dadosFiltrados.data;
            maquinistaAgenteChart.data.datasets[0].backgroundColor = dadosFiltrados.colors;
            maquinistaAgenteChart.data.datasets[0].borderColor = dadosFiltrados.colors.map(color => color.replace('0.8', '1'));
            maquinistaAgenteChart.options.plugins.title.text = `Viagens por ${tipoFiltro} (${periodoAtual.charAt(0).toUpperCase() + periodoAtual.slice(1)})`;
            
            // Atualizar tooltips com porcentagens
            maquinistaAgenteChart.options.plugins.tooltip.callbacks.label = function(context) {
                const value = context.raw || 0;
                const percentage = calculatePercentage(value, total);
                return `Viagens: ${formatNumber(value)} (${percentage}%)`;
            };
            
            maquinistaAgenteChart.update();
        }
    </script>

    <?php $conn->close(); ?>
</body>
</html>