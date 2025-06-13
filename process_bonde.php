<?php
    // Conexão com o banco de dados
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'gm_sicbd';

    $conn = new mysqli($host, $user, $password, $dbname);

    // Verificar conexão
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['bonde_id'];
    $modelo = $_POST['modelo'];
    $capacidade = $_POST['capacidade'];
    $ano_fabricacao = $_POST['ano_fabricacao'];

    $stmt = $pdo->prepare("INSERT INTO bondes (id, modelo, capacidade, ano_fabricacao) VALUES (:id, :modelo, :capacidade, :ano_fabricacao) ON DUPLICATE KEY UPDATE modelo = VALUES(modelo), capacidade = VALUES(capacidade), ano_fabricacao = VALUES(ano_fabricacao)");
    $stmt->execute(['id' => $id, 'modelo' => $modelo, 'capacidade' => $capacidade, 'ano_fabricacao' => $ano_fabricacao]);

    header("Location: homebonde.php");
    exit();
}
?>