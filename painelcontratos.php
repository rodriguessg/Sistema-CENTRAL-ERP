<?php
// Conexão com o banco de dados (ajuste conforme sua configuração)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta para contratos por mês (Ativos, Expirados, Novos)
$sql_contratos_por_mes = "
    SELECT 
        MONTH(validade) AS mes,
        YEAR(validade) AS ano,
        SUM(CASE WHEN validade >= CURDATE() THEN 1 ELSE 0 END) AS ativos,
        SUM(CASE WHEN validade < CURDATE() THEN 1 ELSE 0 END) AS expirados,
        SUM(CASE WHEN data_cadastro >= CURDATE() - INTERVAL 1 MONTH THEN 1 ELSE 0 END) AS novos
    FROM gestao_contratos
    WHERE validade >= CURDATE() - INTERVAL 6 MONTH
    GROUP BY ano, mes
    ORDER BY ano, mes
";

$result_contratos_por_mes = $conn->query($sql_contratos_por_mes);

// Arrays para armazenar os dados do gráfico de contratos por mês
$meses_contratos = [];
$contratos_ativos_por_mes = [];
$contratos_expirados_por_mes = [];
$contratos_novos_por_mes = [];

if ($result_contratos_por_mes->num_rows > 0) {
    while ($row = $result_contratos_por_mes->fetch_assoc()) {
        $meses_contratos[] = date('M', mktime(0, 0, 0, $row['mes'], 1));
        $contratos_ativos_por_mes[] = $row['ativos'] ?? 0;
        $contratos_expirados_por_mes[] = $row['expirados'] ?? 0;
        $contratos_novos_por_mes[] = $row['novos'] ?? 0;
    }
} else {
    $meses_contratos = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'];
    $contratos_ativos_por_mes = array_fill(0, 6, 0);
    $contratos_expirados_por_mes = array_fill(0, 6, 0);
    $contratos_novos_por_mes = array_fill(0, 6, 0);
}

// Consulta para evolução do valor total dos contratos
$sql_valor_total = "
    SELECT 
        MONTH(validade) AS mes,
        YEAR(validade) AS ano,
        SUM(valor_contrato) AS valor_total
    FROM gestao_contratos
    WHERE validade >= CURDATE() - INTERVAL 6 MONTH
    GROUP BY ano, mes
    ORDER BY ano, mes
";

$result_valor_total = $conn->query($sql_valor_total);

// Arrays para armazenar os dados do gráfico de valor total
$meses_valor = [];
$valores_totais = [];

if ($result_valor_total->num_rows > 0) {
    while ($row = $result_valor_total->fetch_assoc()) {
        $meses_valor[] = date('M', mktime(0, 0, 0, $row['mes'], 1));
        $valores_totais[] = $row['valor_total'] ?? 0;
    }
} else {
    $meses_valor = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'];
    $valores_totais = array_fill(0, 6, 0);
}

// Consulta para contratos ativos nos últimos 12 meses
$sql_ativos_12meses = "
    SELECT 
        MONTH(validade) AS mes,
        YEAR(validade) AS ano,
        COUNT(*) AS total_ativos
    FROM gestao_contratos
    WHERE validade >= CURDATE() - INTERVAL 12 MONTH
    GROUP BY ano, mes
    ORDER BY ano DESC, mes DESC
";

$result_ativos_12meses = $conn->query($sql_ativos_12meses);

// Arrays para armazenar os dados do gráfico
$meses = [];
$contratos_ativos = [];

if ($result_ativos_12meses->num_rows > 0) {
    while ($row = $result_ativos_12meses->fetch_assoc()) {
        $meses[] = date('M', mktime(0, 0, 0, $row['mes'], 1));
        $contratos_ativos[] = $row['total_ativos'];
    }
} else {
    $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $contratos_ativos = array_fill(0, 12, 0);
}

// Consulta para contar todos os processos na tabela gestao_contratos
$sql_processos = "SELECT COUNT(*) AS total_processos FROM gestao_contratos";
$result_processos = $conn->query($sql_processos);
$row_processos = $result_processos->fetch_assoc();
$total_processos = $row_processos['total_processos'];

// Consulta para contar contratos com prazo próximo de expirar
$sql_expirando = "SELECT COUNT(*) AS total_expirando FROM gestao_contratos WHERE validade <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH) AND validade >= CURDATE()";
$result_expirando = $conn->query($sql_expirando);
$row_expirando = $result_expirando->fetch_assoc();
$total_expirando = $row_expirando['total_expirando'];

// Consulta para listar contratos próximos de expirar (com mais detalhes)
$sql_lista_expirando = "
    SELECT 
        id, 
        titulo, 
        validade, 
        valor_contrato,
        CASE 
            WHEN validade >= CURDATE() THEN 'Ativo'
            ELSE 'Expirado'
        END AS status
    FROM gestao_contratos 
    WHERE validade <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH) AND validade >= CURDATE()
";
$result_lista_expirando = $conn->query($sql_lista_expirando);

// Consulta para contar contratos por título
$sql_titulo = "SELECT titulo, COUNT(*) AS total FROM gestao_contratos GROUP BY titulo";
$result_titulo = $conn->query($sql_titulo);

// Consulta para contratos ativos
$sql_ativos = "SELECT COUNT(*) AS total_ativos FROM gestao_contratos WHERE validade >= CURDATE()";
$result_ativos = $conn->query($sql_ativos);
$row_ativos = $result_ativos->fetch_assoc();
$total_ativos = $row_ativos['total_ativos'];

// Consulta para contratos expirados
$sql_expirados = "SELECT COUNT(*) AS total_expirados FROM gestao_contratos WHERE validade < CURDATE()";
$result_expirados = $conn->query($sql_expirados);
$row_expirados = $result_expirados->fetch_assoc();
$total_expirados = $row_expirados['total_expirados'];

// Consulta para contratos vencendo em 30 dias
$sql_vencendo = "SELECT COUNT(*) AS total_vencendo FROM gestao_contratos WHERE validade BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
$result_vencendo = $conn->query($sql_vencendo);
$row_vencendo = $result_vencendo->fetch_assoc();
$total_vencendo = $row_vencendo['total_vencendo'];

// Consulta para contar os agendamentos do dia atual
$sql_agendamentos = "SELECT COUNT(*) AS total_agendamentos 
                     FROM eventos 
                     WHERE DATE(data) = CURDATE()";

$result_agendamentos = $conn->query($sql_agendamentos);

if ($result_agendamentos) {
    $row_agendamentos = $result_agendamentos->fetch_assoc();
    $total_agendamentos = $row_agendamentos['total_agendamentos'];
} else {
    // Em caso de erro, define como 0 e/ou exibe mensagem
    $total_agendamentos = 0;
    echo "Erro ao contar agendamentos: " . $conn->error;
}


$conn->close();
include 'header.php';
?>
<?php
// Conexão com o banco de dados (ajuste conforme sua configuração)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";


try {
    // Criação da conexão com PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta apenas os eventos do dia atual
    $sql = "SELECT id, titulo, descricao, data, hora, categoria 
            FROM eventos 
            WHERE data = CURDATE()";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Obtem os resultados
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Contratos</title>
    <link rel="stylesheet" href="./src/style/painel_contratos.css">
    <!-- Inclusão do FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Inclusão do Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .main-container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
        }
        .dashboard {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }
        .cards-container2 {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1 1 150px;
            text-align: center;
        }
        .card5 {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1 1 400px;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .card h3, .card5 h3 {
            margin: 0 0 10px;
            font-size: 1.2rem;
            color: #333;
        }
        .card p {
            margin: 0;
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }
        .table-container {
            flex: 1 1 100%;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            overflow-x: auto;
        }
        .table-container h3 {
            margin: 0 0 10px;
            font-size: 1.2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: #fff;
        }
        .status-ativo {
            color: green;
            font-weight: bold;
        }
        .status-expirado {
            color: red;
            font-weight: bold;
        }
        .warning-icon {
            color: orange;
            margin-left: 5px;
        }
        @media (max-width: 768px) {
            .cards-container, .cards-container2 {
                flex-direction: column;
            }
            .card, .card5 {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>

<!-- Container principal -->
<div class="caderno">
    <div class="dashboard">
        <!-- Cards -->
        <div class="cards-container">
            <div class="card">
                <h3>Total de Contratos</h3>
                <p><?php echo $total_processos; ?></p>
            </div>
            <div class="card">
                <h3>Contratos Ativos</h3>
                <p><?php echo $total_ativos; ?></p>
            </div>
            <div class="card">
                <h3>Contratos Expirados</h3>
                <p><?php echo $total_expirados; ?></p>
            </div>
            <div class="card">
                <h3>Contratos Vencendo em 6 meses</h3>
                <p><?php echo $total_vencendo; ?></p>
            </div>
            <div class="card">
                <h3>Eventos de Hoje</h3>
                <p><?php echo $total_agendamentos; ?></p>
            </div>
        </div>

        <div class="cards-container2">
            <!-- Gráfico de Contratos por Mês -->
            <div class="card5">
                <h3> Contratos </h3>
                <canvas id="contratosPorMesChart" width="400" height="200"></canvas>
            </div>

            <!-- Gráfico de Evolução do Valor Total -->
            <div class="card5">
                <h3>Evolução do Valor Total dos Contratos</h3>
                <canvas id="valorTotalChart" width="400" height="200"></canvas>
            </div>

            <!-- Gráfico de Distribuição de Contratos por Título -->
            <div class="card5">
                <h3>Distribuição de Contratos por Título</h3>
                <canvas id="tituloChart" width="400" height="200"></canvas>
            </div>

            <!-- Gráfico de Contratos Ativos nos Últimos 12 Meses -->
            <div class="card5">
                <h3>Contratos Ativos nos Últimos 12 Meses</h3>
                <canvas id="ativosChart" width="400" height="200"></canvas>
            </div>

            <!-- Tabela de Contratos Próximos de Expirar -->
            <div class="card5">
                <div class="table-container">
                    <h3>Contratos por Vencer <i class="fas fa-exclamation-triangle warning-icon"></i></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Contrato</th>
                                <th>Fornecedor</th>
                                <!-- <th>Tipo</th> -->
                                <th>Término</th>
                                <th>Valência</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_lista_expirando->num_rows > 0) {
                                while ($row = $result_lista_expirando->fetch_assoc()) {
                                    $status_class = $row['status'] == 'Ativo' ? 'status-ativo' : 'status-expirado';
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row['id']) . "</td>
                                            <td>" . htmlspecialchars($row['titulo']) . "</td>
                                           
                                            <td>" . date('d/m/Y', strtotime($row['validade'])) . "</td>
                                            <td>R$ " . number_format($row['valor_contrato'], 2, ',', '.') . "</td>
                                            <td class='$status_class'>" . $row['status'] . "</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>Nenhum contrato encontrado</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <card class="card5">
                 <!-- Tabela para exibir os eventos -->
                 <h3>Eventos agendados</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descrição</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Categoria</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Verifica se há eventos
            if ($eventos) {
                // Exibe cada evento como uma linha na tabela
                foreach ($eventos as $evento) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($evento['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($evento['titulo']) . "</td>";
                    echo "<td>" . htmlspecialchars($evento['descricao']) . "</td>";
                    echo "<td>" . htmlspecialchars($evento['data']) . "</td>";
                    echo "<td>" . htmlspecialchars($evento['hora']) . "</td>";
                    echo "<td>" . htmlspecialchars($evento['categoria']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhum evento encontrado.</td></tr>";
            }
            ?>
        </tbody>
    </table>
            </card>
        </div>
    </div>
</div>

<script>
// Gráfico de Contratos por Mês (Histograma)
const contratosPorMesData = {
    labels: <?php echo json_encode($meses_contratos); ?>,
    datasets: [
        {
            label: 'Ativos',
            data: <?php echo json_encode($contratos_ativos_por_mes); ?>,
            backgroundColor: '#99ff99',
            borderColor: '#66cc66',
            borderWidth: 1
        },
        {
            label: 'Expirados',
            data: <?php echo json_encode($contratos_expirados_por_mes); ?>,
            backgroundColor: '#ff9999',
            borderColor: '#ff6666',
            borderWidth: 1
        },
        {
            label: 'Novos',
            data: <?php echo json_encode($contratos_novos_por_mes); ?>,
            backgroundColor: '#66b3ff',
            borderColor: '#3399ff',
            borderWidth: 1
        }
    ]
};

const configContratosPorMes = {
    type: 'bar',
    data: contratosPorMesData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.dataset.label + ': ' + tooltipItem.raw + ' contratos';
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Meses'
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Número de Contratos'
                }
            }
        }
    }
};

const ctxContratosPorMes = document.getElementById('contratosPorMesChart').getContext('2d');
new Chart(ctxContratosPorMes, configContratosPorMes);

// Gráfico de Evolução do Valor Total (Linha)
const valorTotalData = {
    labels: <?php echo json_encode($meses_valor); ?>,
    datasets: [{
        label: 'Valor Total',
        data: <?php echo json_encode($valores_totais); ?>,
        borderColor: '#66b3ff',
        fill: false,
        tension: 0.1
    }]
};

const configValorTotal = {
    type: 'line',
    data: valorTotalData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return 'Valor Total: R$ ' + tooltipItem.raw.toLocaleString('pt-BR', { minimumFractionDigits: 2 });
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Meses'
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Valor Total (R$)'
                },
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
};

const ctxValorTotal = document.getElementById('valorTotalChart').getContext('2d');
new Chart(ctxValorTotal, configValorTotal);

// Gráfico de Distribuição de Contratos por Título (Pizza)
const tituloData = {
    labels: [],
    datasets: [{
        data: [],
        backgroundColor: ['#ff9999', '#66b3ff', '#99ff99', '#ffcc66', '#ff6699'],
        hoverBackgroundColor: ['#ff6666', '#3399ff', '#66cc66', '#ffcc33', '#ff3366']
    }]
};

<?php
if ($result_titulo->num_rows > 0) {
    while($row = $result_titulo->fetch_assoc()) {
        echo "tituloData.labels.push('" . $row['titulo'] . "');";
        echo "tituloData.datasets[0].data.push(" . $row['total'] . ");";
    }
} else {
    echo "console.log('Nenhum dado para o gráfico de títulos.');";
}
?>

const configPizza = {
    type: 'pie',
    data: tituloData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.label + ': ' + tooltipItem.raw + ' contratos';
                    }
                }
            }
        }
    }
};

const ctxPizza = document.getElementById('tituloChart').getContext('2d');
new Chart(ctxPizza, configPizza);

// Gráfico de Contratos Ativos nos Últimos 12 Meses (Barras e Linha)
const ativosData = {
    labels: <?php echo json_encode($meses); ?>,
    datasets: [
        {
            type: 'bar',
            label: 'Contratos Ativos',
            data: <?php echo json_encode($contratos_ativos); ?>,
            backgroundColor: '#007bff',
            borderColor: '#0056b3',
            borderWidth: 1
        },
        {
            type: 'line',
            label: 'Tendência de Contratos Ativos',
            data: <?php echo json_encode($contratos_ativos); ?>,
            borderColor: '#ff5733',
            fill: false,
            tension: 0.4
        }
    ]
};

const configAtivos = {
    type: 'bar',
    data: ativosData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.dataset.label + ': ' + tooltipItem.raw + ' contratos';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Número de Contratos Ativos'
                }
            }
        }
    }
};

const ctxAtivos = document.getElementById('ativosChart').getContext('2d');
new Chart(ctxAtivos, configAtivos);
</script>
<?php
include 'footer.php'
?>
</body>
</html>