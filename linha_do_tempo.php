<?php
header('Content-Type: application/json');

// Configuração do banco de dados
$servername = "localhost";
$user = "root";
$password = "";
$dbname = "gm_sicbd";

// Criando a conexão com o banco de dados
$conn = new mysqli($servername, $user, $password, $dbname);

// Verificando se a conexão foi bem-sucedida
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . $conn->connect_error]);
    exit;
}

$response = ["success" => false, "fechamentos" => []];

try {
    $sql = "SELECT username, saldo_atual, data_fechamento FROM fechamentos ORDER BY data_fechamento DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $response["fechamentos"][] = $row;
        }
        $response["success"] = true;
    }
} catch (Exception $e) {
    $response["message"] = "Erro ao buscar os fechamentos: " . $e->getMessage();
}

$conn->close();

echo json_encode($response);
?>