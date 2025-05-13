<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter parâmetros da requisição
    $contrato_titulo = $_GET['contrato_titulo'] ?? '';
    $ano = $_GET['ano'] ?? '';
    $mes = $_GET['mes'] ?? '';

    // Construir a query base
    $sql = "SELECT id, contrato_titulo, data_pagamento, valor_contrato, mes, empenho, nota_empenho, 
            valor_liquidado, ordem_bancaria, data_atualizacao, envio_pagamento, vencimento_fatura, 
            agencia_bancaria, tipo, SEI, nota_fiscal, creditos_ativos, valor_liquidado_ag 
            FROM pagamentos 
            WHERE contrato_titulo = :contrato_titulo";

    // Adicionar filtros de ano e mês, se fornecidos
    $params = [':contrato_titulo' => $contrato_titulo];
    if ($ano) {
        $sql .= " AND YEAR(STR_TO_DATE(mes, '%m/%Y')) = :ano";
        $params[':ano'] = $ano;
    }
    if ($mes) {
        $sql .= " AND MONTH(STR_TO_DATE(mes, '%m/%Y')) = :mes";
        $params[':mes'] = $mes;
    }

    // Ordenar por data de pagamento descendente
    $sql .= " ORDER BY data_pagamento DESC";

    // Preparar e executar a query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar os dados como JSON
    echo json_encode($payments);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao carregar pagamentos: ' . $e->getMessage()]);
}
?>