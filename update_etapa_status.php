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
$status = $data['status'] ?? null;

if (!$contractId || !$etapa || !$status) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

$sql = "INSERT INTO etapas_contratos (contract_id, etapa, status, descricao, data, hora) 
        VALUES (:contract_id, :etapa, :status, 'Atualização de status', :data, :hora) 
        ON DUPLICATE KEY UPDATE status = :status";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'contract_id' => $contractId,
    'etapa' => $etapa,
    'status' => $status,
    'data' => date('Y-m-d'),
    'hora' => date('H:i')
]);

echo json_encode(['sucesso' => true]);
?>