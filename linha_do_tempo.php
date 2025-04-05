<?php
header('Content-Type: application/json');

// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado.']);
    exit;
}

// Obtém o username da sessão
$username = $_SESSION['username'];

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

// Configura o fuso horário para São Paulo (UTC-3)
date_default_timezone_set('America/Sao_Paulo');

$response = ["success" => false, "fechamentos" => []];

try {
    // Soma total de saldo_atual de todos os fechamentos
    $sql = "SELECT SUM(saldo_atual) AS total_saldo FROM fechamentos";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // A consulta retorna uma linha com o total do saldo_atual
        $row = $result->fetch_assoc();
        $response["fechamentos"][] = [
            "username" => $username,  // Inclui o username logado
            "data_fechamento" => date('Y-m-d H:i:s'), // Adiciona a data e hora atual no fuso horário de São Paulo
            "saldo_atual" => $row["total_saldo"] // Adiciona o saldo total
        ];
        $response["success"] = true;
    }
} catch (Exception $e) {
    $response["message"] = "Erro ao buscar os fechamentos: " . $e->getMessage();
}

$conn->close();

// Retorna a resposta com o username e saldo
echo json_encode($response);
?>
