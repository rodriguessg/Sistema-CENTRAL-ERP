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
$etapa = $data['title'] ?? null;
$descricao = $data['description'] ?? '';
$dataEtapa = $data['date'] ?? date('Y-m-d');
$hora = $data['time'] ?? date('H:i');
$status = $data['status'] ?? 'Em Andamento';

if (!$contractId || !$etapa) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

$sql = "INSERT INTO etapas_contratos (contract_id, etapa, descricao, data, hora, status) 
        VALUES (:contract_id, :etapa, :descricao, :data, :hora, :status)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'contract_id' => $contractId,
    'etapa' => $etapa,
    'descricao' => $descricao,
    'data' => $dataEtapa,
    'hora' => $hora,
    'status' => $status
]);

echo json_encode(['sucesso' => true]);
?>