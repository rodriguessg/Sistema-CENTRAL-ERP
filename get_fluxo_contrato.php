<?php
header('Content-Type: application/json');

session_start();
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    echo json_encode([]);
    exit;
}

$contractId = isset($_GET['contract_id']) && !empty($_GET['contract_id']) && is_numeric($_GET['contract_id']) ? (int)$_GET['contract_id'] : null;

$fluxos = [];
$sql = $contractId ? "SELECT id, titulo, validade, date_service, publicacao, data_cadastro FROM gestao_contratos WHERE id = :id" : "SELECT id, titulo, validade, date_service, publicacao, data_cadastro FROM gestao_contratos";
$stmt = $pdo->prepare($sql);
$stmt->execute($contractId ? ['id' => $contractId] : []);
$contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($contratos as $contrato) {
    $contractId = $contrato['id'];
    $titulo = $contrato['titulo'];

    $status = 'Em Elaboração';
    if ($contrato['validade'] <= date('Y-m-d')) {
        $status = 'Encerrado';
    } elseif ($contrato['date_service'] <= date('Y-m-d') && $contrato['publicacao'] <= date('Y-m-d')) {
        $status = 'Em Andamento';
    }

    $sqlPagamentos = "
        SELECT 
            id, contrato_titulo, data_pagamento, valor_contrato, mes, empenho, nota_empenho, 
            valor_liquidado, ordem_bancaria, data_atualizacao, envio_pagamento, vencimento_fatura, 
            agencia_bancaria, tipo, SEI, nota_fiscal, creditos_ativos, valor_liquidado_ag, fonte
        FROM pagamentos 
        WHERE contrato_titulo = :titulo 
        ORDER BY id ASC
    ";
    $stmtPagamentos = $pdo->prepare($sqlPagamentos);
    $stmtPagamentos->execute(['titulo' => $titulo]);
    $pagamentos = $stmtPagamentos->fetchAll(PDO::FETCH_ASSOC);

    $temPagamento = !empty($pagamentos);
    $dataPagamento = $temPagamento ? $pagamentos[0]['data_pagamento'] : date('Y-m-d', strtotime($contrato['date_service'] . ' +5 days'));
    $valorContrato = $temPagamento ? $pagamentos[0]['valor_contrato'] : null;
    $empenho = $temPagamento ? $pagamentos[0]['empenho'] : null;

    $dataAtualizacao = isset($contrato['data_atualizacao']) && !empty($contrato['data_atualizacao']) 
        ? new DateTime($contrato['data_atualizacao']) 
        : new DateTime();

    $fluxo = [
        [
            "contract_id" => $contractId,
            "titulo" => $titulo,
            "etapa" => "Criação do Contrato",
            "descricao" => "Contrato criado. Situação: $status",
            "data" => $dataAtualizacao->modify('-15 days')->format('Y-m-d'),
            "hora" => "09:00",
            "status" => "Completo",
            "icone" => "fas fa-file-signature"
        ],
        [
            "contract_id" => $contractId,
            "titulo" => $titulo,
            "etapa" => "Execução do Contrato",
            "descricao" => "A execução do contrato começou. Situação: $status",
            "data" => $contrato['date_service'],
            "hora" => "08:00",
            "status" => ($status === 'Em Andamento' || $status === 'Encerrado') ? "Completo" : "Previsto",
            "icone" => "fas fa-play"
        ]
    ];

    if ($temPagamento) {
        $fluxo[] = [
            "contract_id" => $contractId,
            "titulo" => $titulo,
            "etapa" => "Pagamentos",
            "descricao" => "Pagamentos estão sendo feitos. Situação: $status",
            "data" => $dataPagamento,
            "hora" => "09:00",
            "status" => "Em Processo de Pagamento",
            "icone" => "fas fa-money-bill-wave",
            "valor_contrato" => $valorContrato,
            "mes" => $pagamentos[0]['mes']
        ];
    }

    if ($status === 'Encerrado') {
        $fluxo[] = [
            "contract_id" => $contractId,
            "titulo" => $titulo,
            "etapa" => "Finalização do Contrato",
            "descricao" => "Contrato finalizado. Situação: $status",
            "data" => $contrato['validade'],
            "hora" => "17:00",
            "status" => "Completo",
            "icone" => "fas fa-flag-checkered"
        ];
    }

    if ($status === 'Encerrado') {
        $fluxo[] = [
            "contract_id" => $contractId,
            "titulo" => $titulo,
            "etapa" => "Prestação de Contas",
            "descricao" => "Prestação de contas realizada. Situação: $status",
            "data" => date('Y-m-d', strtotime($contrato['validade'] . ' +5 days')),
            "hora" => "14:00",
            "status" => "Completo",
            "icone" => "fas fa-clipboard-check"
        ];
    }

    $sqlEtapas = "SELECT etapa, descricao, data, hora, status FROM etapas_contratos WHERE contract_id = :contract_id ORDER BY `order` ASC";
    $stmtEtapas = $pdo->prepare($sqlEtapas);
    $stmtEtapas->execute(['contract_id' => $contractId]);
    $etapasPersonalizadas = $stmtEtapas->fetchAll(PDO::FETCH_ASSOC);

    foreach ($etapasPersonalizadas as $etapa) {
        $fluxo[] = [
            "contract_id" => $contractId,
            "titulo" => $titulo,
            "etapa" => $etapa['etapa'],
            "descricao" => $etapa['descricao'],
            "data" => $etapa['data'],
            "hora" => $etapa['hora'],
            "status" => $etapa['status'],
            "icone" => "fas fa-star"
        ];
    }

    $fluxos[] = $fluxo;
}

echo json_encode($fluxos);
?>