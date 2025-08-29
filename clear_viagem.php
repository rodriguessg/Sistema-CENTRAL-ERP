<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sql = "DELETE FROM viagens";
        $pdo->exec($sql);
        echo json_encode(['success' => true, 'message' => 'Todas as viagens foram limpas!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    }
} catch (PDOException $e) {
    error_log("Erro ao limpar viagens: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
}
?>