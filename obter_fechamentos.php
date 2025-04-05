<?php
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

// Consulta para obter os fechamentos
$sql = "SELECT data_fechamento, username, saldo_atual FROM fechamentos ORDER BY data_fechamento DESC";
$result = $conn->query($sql);

$fechamentos = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fechamentos[] = $row;
    }
}

// Envia os dados dos fechamentos como JSON
echo json_encode($fechamentos);

// Fechando a conexão com o banco de dados
$conn->close();
?>
