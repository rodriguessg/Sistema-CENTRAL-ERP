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
$sql = "SELECT  descricao, localizacao, severidade, categoria, usuario, data_registro FROM acidentes ORDER BY data_registro DESC";
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
    <link rel="stylesheet" href="src/bonde/style/acidente.css">

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
                            <th>Usuário</th>
                            <th>Data de Registro</th>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                         
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
