<?php
header('Content-Type: application/json');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $contrato_titulo = $_GET['contrato_titulo'] ?? '';

    $sql = "SELECT * FROM pagamentos WHERE contrato_titulo = :contrato_titulo ORDER BY data_pagamento DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':contrato_titulo' => $contrato_titulo]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($payments);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao carregar pagamentos: ' . $e->getMessage()]);
}
?>