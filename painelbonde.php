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

// Consultas para métricas com verificação de erro
$total_bondes = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM bondes");
if ($result === false) {
    die("Erro na consulta de total de bondes: " . $conn->error);
} elseif ($result) {
    $row = $result->fetch_assoc();
    $total_bondes = $row['total'];
}

$total_acidentes = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM acidentes");
if ($result === false) {
    die("Erro na consulta de total de acidentes: " . $conn->error);
} elseif ($result) {
    $row = $result->fetch_assoc();
    $total_acidentes = $row['total'];
}

$total_viagens = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM viagens");
if ($result === false) {
    die("Erro na consulta de total de viagens: " . $conn->error);
} elseif ($result) {
    $row = $result->fetch_assoc();
    $total_viagens = $row['total'];
}

$bondes_ativos = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM bondes WHERE id NOT IN (SELECT bonde_afetado FROM manutencoes WHERE status = 'Em Andamento')");
if ($result === false) {
    die("Erro na consulta de bondes ativos: " . $conn->error);
} elseif ($result) {
    $row = $result->fetch_assoc();
    $bondes_ativos = $row['total'];
}

$viagens_hoje = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM viagens WHERE DATE(data_ida) = CURDATE() OR DATE(data_volta) = CURDATE()");
if ($result === false) {
    die("Erro na consulta de viagens hoje: " . $conn->error);
} elseif ($result) {
    $row = $result->fetch_assoc();
    $viagens_hoje = $row['total'];
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Bonde</title>
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
    <link rel="stylesheet" href="src/bonde/style/painelbonde.css">
    <!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
    
    </style>
</head>
<body>
<div class="caderno">
    <div class="dashboard">
        <!-- Cards -->
        <div class="cards-container">
            <div class="card">
                <h3>Total de Bondes</h3>
                <p><?php echo $total_bondes; ?></p>
            </div>
            <div class="card">
                <h3>Bondes Ativos</h3>
                <p><?php echo $bondes_ativos; ?></p>
            </div>
            <div class="card">
                <h3>Total de Acidentes</h3>
                <p><?php echo $total_acidentes; ?></p>
            </div>
            <div class="card">
                <h3>Viagens Hoje</h3>
                <p><?php echo $viagens_hoje; ?></p>
            </div>
            <div class="card">
                <h3>Total de Viagens</h3>
                <p><?php echo $total_viagens; ?></p>
            </div>
        </div>

        <div class="cards-container2">
            <!-- Gráfico de Acidentes por Severidade -->
            <div class="card5">
                <h3>Acidentes por Severidade</h3>
                <canvas id="acidentesSeveridadeChart" width="400" height="200"></canvas>
            </div>

            <!-- Gráfico de Passageiros por Viagem -->
            <div class="card5">
                <h3>Passageiros por Viagem</h3>
                <canvas id="passageirosPorViagemChart" width="400" height="200"></canvas>
            </div>

            <!-- Tabela de Acidentes Recentes -->
            <div class="card5">
                <div class="table-container">
                    <h3>Acidentes Recentes</h3>
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

            <!-- Tabela de Viagens Recentes -->
            <div class="card5">
                <div class="table-container">
                    <h3>Viagens Recentes</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Data Ida</th>
                                <th>Data Volta</th>
                                <th>Bonde</th>
                                <th>Origem</th>
                                <th>Destino</th>
                                <th>Passageiros Ida</th>
                                <th>Passageiros Volta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_viagens = "SELECT v.data_ida, v.data_volta, b.modelo, v.origem, v.destino, v.passageiros_ida, v.passageiros_volta 
                                            FROM viagens v 
                                            JOIN bondes b ON v.bonde_id = b.id 
                                            ORDER BY v.data_ida DESC LIMIT 5";
                            $result_viagens = $conn->query($sql_viagens);
                            if ($result_viagens === false) {
                                echo "<tr><td colspan='7'>Erro na consulta de viagens: " . $conn->error . "</td></tr>";
                            } elseif ($result_viagens->num_rows > 0) {
                                while ($row = $result_viagens->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data_ida']))) . "</td>";
                                    echo "<td>" . ($row['data_volta'] ? htmlspecialchars(date('d/m/Y', strtotime($row['data_volta']))) : 'N/A') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['origem']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['destino']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['passageiros_ida']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['passageiros_volta'] ?? '0') . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>Nenhuma viagem registrada</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
<!-- Card de Status dos Bondes -->
<div class="card5">
    <h3>Status dos Bondes</h3>
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
            $sql_status = "SELECT modelo FROM bondes ORDER BY modelo ASC";
            $result_status = $conn->query($sql_status);
            if ($result_status === false) {
                echo "<tr><td colspan='3'>Erro na consulta de status: " . $conn->error . "</td></tr>";
            } elseif ($result_status->num_rows > 0) {
                while ($row = $result_status->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                    // echo "<td>" . htmlspecialchars($row['status_movimento']) . "</td>";
                    // echo "<td>" . htmlspecialchars(date('d/m/Y H:i', strtotime($row['ultima_atualizacao']))) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Nenhum bonde cadastrado</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Card com Mapa do Bonde -->
<div class="card5">
    <h3>Status e Localização do Bonde</h3>
    <div id="mapaBonde" style="height: 300px; width: 100%; border-radius: 10px;"></div>

    <?php
    // Substitua pela placa real cadastrada no seu sistema
    $placa = 'BONDE 22';
    $token = '4944cad387734128a9efdecfa3d5d0e1';

    $url = "https://api.mobilesat.com.br/localizacao_veiculo/$placa";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $dados = json_decode($response, true);

    if (isset($dados['latitude']) && isset($dados['longitude'])) {
        $latitude = $dados['latitude'];
        $longitude = $dados['longitude'];
        $status = ($dados['ignicao'] === 'ligada') ? 'Em movimento' : 'Parado';
        $velocidade = $dados['velocidade'];

        echo "<script>
            var latitude = $latitude;
            var longitude = $longitude;
            var status = '$status';
            var velocidade = $velocidade;
        </script>";
    } else {
        echo "<p style='color:red;'>Erro ao obter dados da Mobilesat.</p>";
    }
    ?>
</div>

            <!-- Card de Manutenções Agendadas -->
<div class="card5">
    <div class="table-container">
        <h3>Manutenções Agendadas</h3>
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
                $sql_manutencoes = "SELECT m.data_manutencao, m.titulo, b.modelo, m.status 
                                    FROM manutencoes m 
                                    JOIN bondes b ON m.bonde_id = b.id 
                                    ORDER BY m.data_manutencao DESC LIMIT 5";
                $result_manutencoes = $conn->query($sql_manutencoes);
                if ($result_manutencoes === false) {
                    echo "<tr><td colspan='4'>Erro na consulta de manutenções: " . $conn->error . "</td></tr>";
                } elseif ($result_manutencoes->num_rows > 0) {
                    while ($row = $result_manutencoes->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data_manutencao']))) . "</td>";
                        echo "<td>" . htmlspecialchars($row['titulo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
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

<!-- Scripts para Gráficos (Chart.js) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de Acidentes por Severidade
    const ctxSeveridade = document.getElementById('acidentesSeveridadeChart').getContext('2d');
    new Chart(ctxSeveridade, {
        type: 'bar',
        data: {
            labels: ['Leve', 'Moderado', 'Grave'],
            datasets: [{
                label: 'Número de Acidentes',
                data: [
                    <?php
                    $sql_severidade = "SELECT severidade, COUNT(*) as total FROM acidentes GROUP BY severidade";
                    $result_severidade = $conn->query($sql_severidade);
                    if ($result_severidade === false) {
                        echo "0, 0, 0";
                    } else {
                        $severidades = ['Leve' => 0, 'Moderado' => 0, 'Grave' => 0];
                        while ($row = $result_severidade->fetch_assoc()) {
                            $severidades[$row['severidade']] = $row['total'];
                        }
                        echo $severidades['Leve'] . ', ' . $severidades['Moderado'] . ', ' . $severidades['Grave'];
                    }
                    ?>
                ],
                backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c']
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // Gráfico de Passageiros por Viagem
    const ctxPassageiros = document.getElementById('passageirosPorViagemChart').getContext('2d');
    new Chart(ctxPassageiros, {
        type: 'bar',
        data: {
            labels: ['Ida', 'Volta'],
            datasets: [{
                label: 'Média de Passageiros',
                data: [
                    <?php
                    $sql_passageiros_ida = "SELECT AVG(passageiros_ida) as media FROM viagens";
                    $result_ida = $conn->query($sql_passageiros_ida);
                    $media_ida = $result_ida && $result_ida->fetch_assoc() ? round($result_ida->fetch_assoc()['media'], 2) : 0;

                    $sql_passageiros_volta = "SELECT AVG(passageiros_volta) as media FROM viagens";
                    $result_volta = $conn->query($sql_passageiros_volta);
                    $media_volta = $result_volta && $result_volta->fetch_assoc() ? round($result_volta->fetch_assoc()['media'], 2) : 0;

                    echo $media_ida . ', ' . $media_volta;
                    ?>
                ],
                backgroundColor: ['#3498db', '#e67e22']
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    if (typeof latitude !== 'undefined' && typeof longitude !== 'undefined') {
        var map = L.map('mapaBonde').setView([latitude, longitude], 15);

        // Adiciona camada do mapa OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Adiciona marcador
        L.marker([latitude, longitude]).addTo(map)
            .bindPopup("Status: " + status + "<br>Velocidade: " + velocidade + " km/h")
            .openPopup();
    }
});
</script>


<?php $conn->close(); ?>
</body>
</html>