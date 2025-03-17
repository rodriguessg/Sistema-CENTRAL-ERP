<?php
header('Content-Type: application/json');

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'supat');

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    echo json_encode(['error' => $conn->connect_error]);
    exit;
}

// Obtém o parâmetro de pesquisa
$produto = isset($_GET['produto']) ? $conn->real_escape_string($_GET['produto']) : '';

// Consulta SQL com filtro
$sql = "SELECT id, produto, classificacao, localizacao, quantidade FROM produtos";
if (!empty($produto)) {
    $sql .= " WHERE produto LIKE '%$produto%'";
}

$result = $conn->query($sql);

$produtos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
}

echo json_encode($produtos);

$conn->close();
?>
