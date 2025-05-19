<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao conectar ao banco']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$contractId = $data['contract_id'] ?? null;
$etapa = $data['etapa'] ?? null;

if (!$contractId || !$etapa) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

$sql = "DELETE FROM etapas_contratos WHERE contract_id = :contract_id AND etapa = :etapa";
$stmt = $pdo->prepare($sql);
$stmt->execute(['contract_id' => $contractId, 'etapa' => $etapa]);

echo json_encode(['sucesso' => true]);
?>