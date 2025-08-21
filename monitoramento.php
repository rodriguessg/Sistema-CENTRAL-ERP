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

// Consulta acidentes
$sql = "SELECT id, data, descricao, localizacao, usuario, severidade, categoria, cor, data_registro FROM acidentes ORDER BY data_registro DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}

// Map severidade to nível
$severityMap = [
    'Leve' => 'Nível I',
    'Moderado' => 'Nível II',
    'Grave' => 'Nível III',
    'Moderado a Grave' => 'Nível II/III'
];
$corMap = [
    'Verde' => '#d1f5d3',
    'Amarelo' => '#fff3cd',
    'Vermelho' => '#f8d7da',
    'Amarelo/Vermelho' => 'linear-gradient(to right, #fff3cd, #f8d7da)'
];

// Contagem de ocorrências por gravidade
$counts = ['Grave' => 0, 'Moderado' => 0, 'Leve' => 0, 'Moderado a Grave' => 0];
while ($row = $result->fetch_assoc()) {
    if (isset($severityMap[$row['severidade']])) {
        $counts[$row['severidade']]++;
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
    <title>Monitoramento - Bonde de Santa Teresa</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #1e3a8a;
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
        .summary {
            display: flex;
            justify-content: space-around;
            background-color: #e0e7ff;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .summary div {
            padding: 10px;
            text-align: center;
        }
        .summary .grave { background-color: #f8d7da; }
        .summary .moderado { background-color: #fff3cd; }
        .summary .leve { background-color: #d1f5d3; }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .table-container {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
            cursor: pointer;
        }
        .nivel-emerg {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .nivel-i { background-color: #d1f5d3; color: #2f855a; }
        .nivel-ii { background-color: #fff3cd; color: #d97706; }
        .nivel-iii { background-color: #f8d7da; color: #991b1b; }
        .nivel-ii-iii { background: linear-gradient(to right, #fff3cd, #f8d7da); color: #fff; }
        .severity-bg {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
            display: inline-block;
        }
        .cor-verde { background-color: #d1f5d3; color: #2f855a; }
        .cor-amarelo { background-color: #fff3cd; color: #d97706; }
        .cor-vermelho { background-color: #f8d7da; color: #991b1b; }
        .cor-amarelo-vermelho { background: linear-gradient(to right, #fff3cd, #f8d7da); color: #fff; }
        .map-details {
            display: flex;
            gap: 20px;
        }
        .map-section, .details-section {
            flex: 1;
            padding: 20px;
            border-radius: 5px;
            min-height: 200px;
        }
        .map-section {
            background-color: #d1f5d3;
            color: #2f855a;
        }
        .map-section iframe {
            width: 100%;
            height: 100%;
            min-height: 400px;
            border: none;
            border-radius: 5px;
        }
        .details-section {
            background-color: #f8d7da;
            color: #991b1b;
        }
        .details-section h3 {
            margin-top: 0;
        }
        .viagens-container {
            margin-top: 20px;
            padding: 20px;
            background-color: #e0e7ff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .viagens-container h3 {
            margin-top: 0;
            color: #1e3a8a;
        }
        .viagens-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .viagens-table th, .viagens-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .viagens-table th {
            background-color: #1e3a8a;
            color: white;
            font-weight: bold;
        }
        .viagens-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .viagens-table .subindo { background-color: #d1f5d3; color: #2f855a; }
        .viagens-table .descendo { background-color: #a3bffa; color: #1e40af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PAINEL DE MONITORAMENTO - BONDE DE SANTA TERESA</h1>
    </div>

    <div class="container">
        <div class="summary">
            <div>Total Ocorrências: <?= $totalOcorrencias ?></div>
            <div class="grave">Grave: <?= $counts['Grave'] ?></div>
            <div class="moderado">Moderado: <?= $counts['Moderado'] ?></div>
            <div class="leve">Leve: <?= $counts['Leve'] ?></div>
            <?php if ($counts['Moderado a Grave'] > 0): ?>
                <div class="moderado">Moderado a Grave: <?= $counts['Moderado a Grave'] ?></div>
            <?php endif; ?>
        </div>

        <div class="table-container">
            <table>
                <tr>
                    <th>Nível Emerg.</th>
                    <th>Gravidade</th>
                    <th>Tipo</th>
                    <th>Local</th>
                    <th>Hora</th>
                    <th>Status</th>
                </tr>
                <?php
                while ($row = $result->fetch_assoc()) {
                    $nivel = $severityMap[$row['severidade']] ?? 'Nível Desconhecido';
                    $corClass = str_replace(' ', '-', strtolower($nivel));
                    $severityClass = 'cor-' . str_replace('/', '-', strtolower($row['cor']));
                    $hora = date('H:i', strtotime($row['data_registro']));
                    $status = (strtotime($row['data_registro']) > strtotime('-1 hour')) ? 'Aberta' : 'Resolvida';
                    ?>
                    <tr onclick="selectOccurrence(<?= $row['id'] ?>, this)">
                        <td class="nivel-emerg <?= $corClass ?>"><?= $nivel ?></td>
                        <td class="severity-bg <?= $severityClass ?>"><?= htmlspecialchars($row['severidade']) ?></td>
                        <td><?= htmlspecialchars($row['categoria']) ?></td>
                        <td><?= htmlspecialchars($row['localizacao']) ?></td>
                        <td><?= $hora ?></td>
                        <td><?= htmlspecialchars($status) ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>

        <div class="map-details">
            <div class="map-section">
                <!-- <h3>Mapa em Tempo Real</h3> -->
                <iframe src="https://monitoramento.mobilesat.com.br/locator/index.html?t=4ebee7c35e2e2fbedde92f4b2611c141F0AA094FB415B295867B3BD93520050BB6566DD7" allowfullscreen></iframe>
            </div>
            <div class="details-section" id="occurrence-details">
                <h3>Detalhes da Ocorrência Selecionada</h3>
                <p>Nível: N/A</p>
                <p>Gravidade: N/A</p>
                <p>Tipo: N/A</p>
                <p>Local: N/A</p>
                <p>Status: N/A</p>
                <p>Ações: N/A</p>
            </div>
        </div>

        <div class="viagens-container">
            <h3>    Em desenvolvimento</h3>
            <!-- <table class="viagens-table">
                <tr>
                    <th>Bonde ID</th>
                    <th>Direção</th>
                    <th>Última Atualização</th>
                </tr>
                <?php foreach ($viagens as $viagem): ?>
                    <tr>
                        <td><?= htmlspecialchars($viagem['id']) ?></td>
                        <td class="<?= strtolower($viagem['direcao']) ?>"><?= htmlspecialchars($viagem['direcao']) ?></td>
                        <td><?= htmlspecialchars($viagem['ultima_atualizacao']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table> -->
        </div>
    </div>

    <script>
        function selectOccurrence(id, row) {
            // Remove highlight from all rows
            document.querySelectorAll('table tr').forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');

            // Fetch details from the current row
            const rowData = row.cells;
            const details = {
                nivel: rowData[0].textContent,
                gravidade: rowData[1].textContent,
                tipo: rowData[2].textContent,
                local: rowData[3].textContent,
                status: rowData[5].textContent,
                acoes: 'Ações em andamento' // Placeholder, replace with real data if available
            };

            const detailsDiv = document.getElementById('occurrence-details');
            detailsDiv.innerHTML = `
                <h3>Detalhes da Ocorrência Selecionada</h3>
                <p>Nível: ${details.nivel}</p>
                <p>Gravidade: ${details.gravidade}</p>
                <p>Tipo: ${details.tipo}</p>
                <p>Local: ${details.local}</p>
                <p>Status: ${details.status}</p>
                <p>Ações: ${details.acoes}</p>
            `;
        }
    </script>

    <style>
        .selected {
            background-color: #d1e7dd !important;
            font-weight: bold;
        }
    </style>

    <?php $conn->close(); ?>
</body>
</html>