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
        $meses[] = date('M', mktime(0, 0, 0, $row['mes'], 1)); // Nome do mês
        $contratos_ativos[] = $row['total_ativos']; // Total de contratos ativos
    }
} else {
    $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $contratos_ativos = array_fill(0, 12, 0); // Se não houver dados, preenche com 0
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

// Consulta para listar contratos próximos de expirar
$sql_lista_expirando = "SELECT id, titulo, validade FROM gestao_contratos WHERE validade <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH) AND validade >= CURDATE()";
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

// Consulta para contar os agendamentos
$sql_agendamentos = "SELECT COUNT(*) AS total_agendamentos FROM agendamentos";
$result_agendamentos = $conn->query($sql_agendamentos);
$row_agendamentos = $result_agendamentos->fetch_assoc();
$total_agendamentos = $row_agendamentos['total_agendamentos'];

$conn->close();
include 'header.php';
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
    
</head>
<body>

<!-- Container principal -->
<div class="main-container">
    <div class="dashboard">
        <!-- Cards -->
        <div class="cards-container">
            <div class="card">
                <h3>Total de Processos</h3>
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
                <h3>Contratos Vencendo em 30 dias</h3>
                <p><?php echo $total_vencendo; ?></p>
            </div>

            <!-- Novo Card para Agendamentos -->
            <div class="card">
                <h3>Total de Agendamentos</h3>
                <p><?php echo $total_agendamentos; ?></p>
            </div>
            <!-- <div class="card">
                <h3>Total de Agendamentos</h3>
                <p><?php echo $total_agendamentos; ?></p>
            </div> -->
            </div> <!-- Fim dos Cards -->

            <div class="cards-container2">

            <!-- Novo Card com Gráfico de Pizza -->
            <div class="card5">
                <h3>Distribuição de Contratos por Título</h3>
                <canvas id="tituloChart" width="400" height="400"></canvas> <!-- Gráfico de pizza aqui -->
            </div>

        <!-- Novo Card com Gráfico de Barras e Linha -->
        <div class="card5">
            <h3>Contratos Ativos nos Últimos 12 Meses</h3>
            <canvas id="ativosChart" width="400" height="400"></canvas> <!-- Gráfico de barras e linha -->
        </div>
        <div class="card5">
        <!-- Tabela de Contratos Próximos de Expirar -->
        <div class="table-container">
            <h3>Contratos Próximos de Expirar</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nome do Contrato</th>
                        <th>Data de Validade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Exibe os contratos que estão para expirar
                    if ($result_lista_expirando->num_rows > 0) {
                        while($row = $result_lista_expirando->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row['titulo'] . "</td>
                                    <td>" . date('d/m/Y', strtotime($row['validade'])) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Nenhum contrato encontrado</td></tr>";
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
    // Preparando dados para o gráfico de pizza
    const tituloData = {
        labels: [],
        datasets: [{
            data: [],
            backgroundColor: ['#ff9999', '#66b3ff', '#99ff99', '#ffcc66', '#ff6699'],  // Definindo as cores do gráfico
            hoverBackgroundColor: ['#ff6666', '#3399ff', '#66cc66', '#ffcc33', '#ff3366']
        }]
    };

    <?php
    // Preenche os dados do gráfico com base nos resultados da consulta
    if ($result_titulo->num_rows > 0) {
        while($row = $result_titulo->fetch_assoc()) {
            echo "tituloData.labels.push('" . $row['titulo'] . "');";
            echo "tituloData.datasets[0].data.push(" . $row['total'] . ");";
        }
    } else {
        echo "console.log('Nenhum dado para o gráfico.');";
    }
    ?>

    // Configurações do gráfico de pizza
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

    // Criando o gráfico de pizza no canvas
    const ctxPizza = document.getElementById('tituloChart').getContext('2d');
    new Chart(ctxPizza, configPizza);

    // Dados para o gráfico de barras e linha (contratos ativos nos últimos 12 meses)
    const ativosData = {
        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],  // Meses do ano
        datasets: [{
            type: 'bar',
            label: 'Contratos Ativos',
            data: <?php echo json_encode($contratos_ativos); ?>,
            backgroundColor: '#007bff',  // Cor das barras
            borderColor: '#0056b3',
            borderWidth: 1
        }, {
            type: 'line',
            label: 'Tendência de Contratos Ativos',
            data: <?php echo json_encode($contratos_ativos); ?>,
            borderColor: '#ff5733',  // Cor da linha
            fill: false,
            tension: 0.4
        }]
    };

    // Configurações do gráfico de barras e linha
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

    // Criando o gráfico de barras e linha no canvas
    const ctxAtivos = document.getElementById('ativosChart').getContext('2d');
    new Chart(ctxAtivos, configAtivos);
</script>

</body>
</html>
