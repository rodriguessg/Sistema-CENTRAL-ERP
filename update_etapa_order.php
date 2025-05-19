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
if (!$data || !is_array($data)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Atualizar a ordem das etapas personalizadas
    $sql = "UPDATE etapas_contratos SET `order` = :order WHERE contract_id = :contract_id AND etapa = :etapa";
    $stmt = $pdo->prepare($sql);

    foreach ($data as $item) {
        $stmt->execute([
            'order' => $item['order'],
            'contract_id' => $item['contract_id'],
            'etapa' => $item['etapa']
        ]);
    }

    $pdo->commit();
    echo json_encode(['sucesso' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar ordem: ' . $e->getMessage()]);
}
?>