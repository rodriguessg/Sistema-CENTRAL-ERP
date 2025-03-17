<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "supat";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Recuperando os agendamentos do banco de dados
$sql = "SELECT id, nome, descricao, data_g, email FROM agendamentos";
$result = $conn->query($sql);

$agendamentos = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data = $row['data_g'];
        $agendamentos[$data][] = $row; // Organiza os agendamentos por data
    }
}

$conn->close();
?>
