<?php
$host = 'localhost';
$dbname = 'gm_sicbd';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $ano = $_GET['ano'] ?? date('Y');

    $sql = "SELECT produto, quantidade, codigo, data_cadastro 
            FROM produtos 
            WHERE YEAR(data_cadastro) = :ano";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ano' => $ano]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
