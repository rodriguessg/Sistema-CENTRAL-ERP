<?php
session_start();

// Configuração para conexão com o banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Erro na conexão com o banco de dados: " . $conn->connect_error]);
    exit;
}

// Consulta para buscar os anos disponíveis
$sql = "SELECT DISTINCT YEAR(data_cadastro) AS ano FROM produtos ORDER BY ano DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $anos = [];
    while ($row = $result->fetch_assoc()) {
        $anos[] = $row['ano'];
    }
    echo json_encode($anos);
} else {
    echo json_encode([]);
}

$conn->close();
?>
