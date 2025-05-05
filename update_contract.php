<?php
header('Content-Type: application/json');

session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SESSION['username'] !== 'contratos') {
        echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
        exit;
    }

    $sql = "UPDATE gestao_contratos SET 
            mes = :mes,
            empenho = :empenho,
            tipo = :tipo,
            nota_empenho = :nota_empenho,
            valor_nota_fiscal = :valor_nota_fiscal,
            creditos_ativos = :creditos_ativos,
            SEI = :SEI,
            nota_fiscal = :nota_fiscal,
            envio_pagamento = :envio_pagamento,
            validade = :validade,
            valor_liquidado = :valor_liquidado,
            valor_liquidado_ag = :valor_liquidado_ag,
            ordem_bancaria = :ordem_bancaria,
            data_atualizacao = :data_atualizacao
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $data['id'],
        'mes' => $data['mes'],
        'empenho' => $data['empenho'],
        'tipo' => $data['tipo'],
        'nota_empenho' => $data['nota_empenho'],
        'valor_nota_fiscal' => $data['valor_nota_fiscal'],
        'creditos_ativos' => $data['creditos_ativos'],
        'SEI' => $data['SEI'],
        'nota_fiscal' => $data['nota_fiscal'],
        'envio_pagamento' => $data['envio_pagamento'],
        ' validade' => $data[' validade'],
        'valor_liquidado' => $data['valor_liquidado'],
        'valor_liquidado_ag' => $data['valor_liquidado_ag'],
        'ordem_bancaria' => $data['ordem_bancaria'],
        'data_atualizacao' => $data['data_atualizacao']
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar contrato: ' . $e->getMessage()]);
}
?>