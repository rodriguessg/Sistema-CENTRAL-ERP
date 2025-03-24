<?php
// Incluir a conexão com o banco de dados
include 'banco.php';

// Pegar os parâmetros de filtro
$dataOperacao = isset($_GET['data_operacao']) ? $_GET['data_operacao'] : '';
$matricula = isset($_GET['matricula']) ? $_GET['matricula'] : '';
$tipoOperacao = isset($_GET['tipo_operacao']) ? $_GET['tipo_operacao'] : '';

// Montar a consulta SQL com base nos filtros
$query = "SELECT * FROM log_eventos WHERE 1=1";

if ($dataOperacao) {
    $query .= " AND DATE(data_operacao) = '$dataOperacao'";
}

if ($matricula) {
    $query .= " AND matricula LIKE '%$matricula%'";
}

if ($tipoOperacao) {
    $query .= " AND tipo_operacao LIKE '%$tipoOperacao%'";
}

$query .= " ORDER BY data_operacao DESC";

// Executar a consulta
$resultado = $con->query($query);

// Verificar se a consulta retornou resultados
if ($resultado === false) {
    die("Erro ao recuperar os logs: " . $con->error);
}

// Exibir os resultados filtrados
while ($row = $resultado->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['matricula']) . "</td>";
    echo "<td>" . htmlspecialchars($row['tipo_operacao']) . "</td>";
    echo "<td>" . htmlspecialchars($row['data_operacao']) . "</td>";
    // echo "<td>" . htmlspecialchars($row['detalhes']) . "</td>";
    echo "</tr>";
}

$con->close();
?>
