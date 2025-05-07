<?php
header('Content-Type: application/json');

session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['contrato_titulo']) || !$data['mes']) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos. O campo Mês é obrigatório.']);
        exit;
    }

    $sql = "INSERT INTO pagamentos (contrato_titulo, mes, empenho, tipo, nota_empenho, valor_contrato, creditos_ativos, SEI, nota_fiscal, envio_pagamento, vencimento_fatura, valor_liquidado, valor_liquidado_ag, ordem_bancaria, data_atualizacao, data_pagamento ) 
            VALUES (:contrato_titulo, :mes, :empenho, :tipo, :nota_empenho, :valor_contrato, :creditos_ativos, :SEI, :nota_fiscal, :envio_pagamento, :vencimento_fatura, :valor_liquidado, :valor_liquidado_ag, :ordem_bancaria, :data_atualizacao, :data_pagamento )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':contrato_titulo' => $data['contrato_titulo'],
        ':mes' => $data['mes'],
        ':empenho' => $data['empenho'] ?? null,
        ':tipo' => $data['tipo'] ?? null,
        ':nota_empenho' => $data['nota_empenho'] ?? null,
        ':valor_contrato' => $data['valor_contrato'] ?? 0,
        ':creditos_ativos' => $data['creditos_ativos'] ?? null,
        ':SEI' => $data['SEI'] ?? null,
        ':nota_fiscal' => $data['nota_fiscal'] ?? null,
        ':envio_pagamento' => $data['envio_pagamento'] ?? null,
        ':vencimento_fatura' => $data['vencimento_fatura'] ?? null,
        ':valor_liquidado' => $data['valor_liquidado'] ?? 0,
        ':valor_liquidado_ag' => $data['valor_liquidado_ag'] ?? 0,
        ':ordem_bancaria' => $data['ordem_bancaria'] ?? null,
        ':data_atualizacao' => $data['data_atualizacao'] ?? null,
        ':data_pagamento' => date('Y-m-d'),
        // ':valor' => $data['valor_liquidado'] ?? 0
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar pagamento: ' . $e->getMessage()]);
}
?>