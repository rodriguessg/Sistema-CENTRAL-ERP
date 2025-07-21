<?php
header('Content-Type: application/json');

// Configuração da conexão com o banco de dados (ajuste conforme necessário)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Falha na conexão com o banco de dados: " . $conn->connect_error]);
    exit;
}

// Receber o ID de diferentes fontes
$id = null;
$data = json_decode(file_get_contents('php://input'), true); // Para requisições POST
if (isset($data['id'])) {
    $id = (int)$data['id'];
} elseif (isset($_GET['id'])) { // Para requisições GET (testes manuais)
    $id = (int)$_GET['id'];
}

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID da oportunidade não fornecido."]);
    exit;
}

// Query para excluir a oportunidade
$sql = "DELETE FROM planejamento WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao excluir a oportunidade: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>