<?php
session_start();

// Conexão com o banco
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $dbname);
include 'header.php';

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica sessão
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

// Ativa erros PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Consulta acidentes
$sql = "SELECT descricao, localizacao, severidade, categoria, usuario, cor, data_registro FROM acidentes ORDER BY data_registro DESC";
$result = $conn->query($sql);

// Verifica se a consulta foi executada corretamente
if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}

// Verifica se há dados
$temDados = ($result instanceof mysqli_result && $result->num_rows > 0);

// Map colors to CSS classes
$colorClasses = [
    'Verde' => 'severity-green',
    'Amarelo' => 'severity-yellow',
    'Vermelho' => 'severity-red',
    'Amarelo/Vermelho' => 'severity-yellow-red'
];

// Determine the CSS class for each row's severity based on the 'cor' column
function getSeverityClass($cor, $colorClasses) {
    return isset($colorClasses[$cor]) ? $colorClasses[$cor] : '';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Acidentes</title>
    <link rel="stylesheet" href="src/estoque/style/estoque2.css">
    <link rel="stylesheet" href="src/bonde/style/acidente.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .button-container {
            margin-bottom: 20px;
            text-align: center;
        }
        .button-container button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .button-container button:hover {
            background-color: #2980b9;
        }
        .accidents-table {
            margin-bottom: 30px;
        }
        .accidents-table h2 {
            margin-bottom: 15px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tr:hover {
            background-color: #e0e0e0;
        }
        .severity-green {
            background-color: #2ecc71;
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }
        .severity-yellow {
            background-color: #f1c725;
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }
        .severity-red {
            background-color: #e74c3c;
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }
        .severity-yellow-red {
            background: linear-gradient(to right, #f1c725, #e74c3c);
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }
        .no-data {
            text-align: center;
            color: #777;
            padding: 20px;
            font-style: italic;
        }
        .attention-zones {
            margin-top: 20px;
        }
        .attention-zones h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .attention-zones ul {
            list-style: none;
            padding: 0;
        }
        .attention-zones li {
            padding: 5px 0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Acidentes</h1>
    </div>

    <div class="container">
        <div>
            <div class="button-container">
                <button onclick="window.location.href='reportacidentes.php'">Registrar Novo Acidente</button>
            </div>

            <div class="accidents-table">
                <h2>Histórico de Acidentes</h2>

                <?php if ($temDados): ?>
                    <table>
                        <tr>
                            <th>Descrição</th>
                            <th>Localização</th>
                            <th>Severidade</th>
                            <th>Categoria</th>
                          
                            <th>Data de Registro</th>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['descricao']) ?></td>
                                <td><?= htmlspecialchars($row['localizacao']) ?></td>
                                <td class="<?= getSeverityClass($row['cor'], $colorClasses) ?>">
                                    <?= htmlspecialchars($row['severidade']) ?>
                                </td>
                                <td><?= htmlspecialchars($row['categoria']) ?></td>
                             
                               
                                <td><?= htmlspecialchars($row['data_registro']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <div class="no-data">Nenhum acidente registrado.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="attention-zones">
            <h3>Zonas de Atenção</h3>
            <ul>
                <li>Almoxarifado Central - Alto risco de quedas.</li>
                <li>Setor de Produção - Equipamentos instáveis.</li>
                <li>Área de Estocagem - Acúmulo de materiais inflamáveis.</li>
            </ul>
        </div>
    </div>

    <?php $conn->close(); ?>
</body>
</html>