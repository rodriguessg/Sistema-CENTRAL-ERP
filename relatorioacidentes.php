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
$sql = "SELECT data, descricao, localizacao, severidade, categoria, usuario, data_registro FROM acidentes ORDER BY data_registro DESC";
$result = $conn->query($sql);

// Verifica se a consulta foi executada corretamente
if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}

// Verifica se há dados
$temDados = ($result instanceof mysqli_result && $result->num_rows > 0);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Acidentes</title>
    <link rel="stylesheet" href="src/estoque/style/estoque2.css">
    <link rel="stylesheet" href="src/estoque/style/linhadotempo.css">
    <link rel="stylesheet" href="src/style/tabs.css">
    <style>
        /* [estilos iguais ao seu código original omitidos para brevidade] */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }
        .button-container {
            text-align: right;
            margin-bottom: 20px;
        }
        button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .accidents-table {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
        }
       
        .attention-zones {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            height: fit-content;
        }
        .attention-zones h3 {
            color: #e74c3c;
            margin-top: 0;
        }
        .attention-zones ul {
            list-style-type: none;
            padding: 0;
        }
        .attention-zones li {
            margin-bottom: 10px;
            color: #333;
        }
        .no-data {
            padding: 10px;
            color: #666;
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
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Localização</th>
                            <th>Severidade</th>
                            <th>Categoria</th>
                            <th>Usuário</th>
                            <th>Data de Registro</th>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['data']) ?></td>
                                <td><?= htmlspecialchars($row['descricao']) ?></td>
                                <td><?= htmlspecialchars($row['localizacao']) ?></td>
                                <td><?= htmlspecialchars($row['severidade']) ?></td>
                                <td><?= htmlspecialchars($row['categoria']) ?></td>
                                <td><?= htmlspecialchars($row['usuario']) ?></td>
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
