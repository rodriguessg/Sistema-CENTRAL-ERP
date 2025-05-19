
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
    echo json_encode([]);
    exit;
}

$contractId = isset($_GET['contract_id']) ? (int)$_GET['contract_id'] : null;
if (!$contractId) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT 
        id, contrato_titulo, data_pagamento, valor_contrato, mes, empenho, nota_empenho, 
        valor_liquidado, ordem_bancaria, data_atualizacao, envio_pagamento, vencimento_fatura, 
        agencia_bancaria, tipo, SEI, nota_fiscal, creditos_ativos, valor_liquidado_ag, fonte
    FROM pagamentos 
    WHERE contrato_titulo = (SELECT titulo FROM gestao_contratos WHERE id = :contract_id LIMIT 1)
    ORDER BY id ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['contract_id' => $contractId]);
$pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($pagamentos);
?>