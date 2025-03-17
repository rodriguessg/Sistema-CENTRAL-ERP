<?php
header('Content-Type: application/json');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'supat';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para contar a quantidade de produtos
    $query = "SELECT COUNT(*) as quantidade FROM produtos";
    $stmt = $pdo->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Retorna a quantidade como JSON
    echo json_encode(['quantidade' => $result['quantidade']]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
}
