<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

$host = 'localhost';
$dbname = 'gm_sicbd';
$username_db = 'root';
$password = '';

try {
    // Criação da conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Definir modo de erro para exceção
} catch (PDOException $e) {
    // Em caso de erro na conexão, loga o erro e exibe uma mensagem amigável
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Consulte o administrador.");
}

// Captura os filtros
$status = isset($_GET['situacao']) ? $_GET['situacao'] : '';
$destino = isset($_GET['destino']) ? $_GET['destino'] : '';
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

// Monta a query
$query = "SELECT * FROM patrimonio WHERE 1=1";

// Aplica os filtros
if (!empty($situacao)) {
    $query .= " AND situacao = '$situacao'";
}
if (!empty($destino)) {
    $query .= " AND destino = '$destino'";
}
if (!empty($data_inicio) && !empty($data_fim)) {
    $query .= " AND data_aquisicao BETWEEN '$data_inicio' AND '$data_fim'";
}

$result = $con->query($query);

// Exibe os resultados
if ($result->num_rows > 0) {
    echo "<h3>Relatório de Patrimônios</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Código</th><th>Descrição</th><th>Status</th><th>Destino</th><th>Data de Aquisição</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['codigo']}</td>
            <td>{$row['descricao']}</td>
            <td>{$row['situacao']}</td>
            <td>{$row['destino']}</td>
            <td>{$row['data_aquisicao']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nenhum patrimônio encontrado com os critérios selecionados.</p>";
}

$con->close();
?>
