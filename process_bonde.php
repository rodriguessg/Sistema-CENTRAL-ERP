<?php
// Include database configuration with error handling
try {
    include 'bancoo.php';
    if (!isset($pdo) || !$pdo) {
        throw new Exception("Falha na conexão com o banco de dados.");
    }
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
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