<?php
header('Content-Type: application/json');

session_start();

// Configurar logging de erros
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Conectar ao banco de dados
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

// Verificar se há um contract_id na URL
$contractId = isset($_GET['contract_id']) && !empty($_GET['contract_id']) && is_numeric($_GET['contract_id']) ? (int)$_GET['contract_id'] : null;

$fluxos = [];
$sql = $contractId ? "SELECT id, titulo, validade, date_service, publicacao FROM gestao_contratos WHERE id = :id" : "SELECT id, titulo, validade, date_service, publicacao FROM gestao_contratos";
$stmt = $pdo->prepare($sql);
$stmt->execute($contractId ? ['id' => $contractId] : []);
$contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($contratos as $contrato) {
    $contractId = $contrato['id'];
    $titulo = $contrato['titulo'];

    // Determinar a situação do contrato
    $status = 'Em Elaboração';
    if ($contrato['validade'] <= date('Y-m-d')) {
        $status = 'Encerrado';
    } elseif ($contrato['date_service'] <= date('Y-m-d') && $contrato['publicacao'] <= date('Y-m-d')) {
        $status = 'Em Andamento';
    }

    // Verificar se há pagamentos associados
    $sqlPagamentos = "SELECT data_pagamento FROM pagamentos WHERE contrato_titulo = :titulo LIMIT 1";
    $stmtPagamentos = $pdo->prepare($sqlPagamentos);
    $stmtPagamentos->execute(['titulo' => $titulo]);
    $pagamento = $stmtPagamentos->fetch(PDO::FETCH_ASSOC);
    $temPagamento = $pagamento !== false;
    $dataPagamento = $temPagamento ? $pagamento['data_pagamento'] : date('Y-m-d', strtotime($contrato['date_service'] . ' +5 days'));

    // Construir o fluxo
    $fluxo = [
        [
            "contract_id" => $contractId,
            "titulo" => $titulo,
            "etapa" => "Criação do Contrato",
            "descricao" => "Contrato criado. Situação: $status",
            "data" => date('Y-m-d', strtotime($contrato['date_service'] . ' -15 days')),
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

    // Adicionar etapa "Pagamentos" se houver pagamento
    if ($temPagamento) {
        $fluxo[] = [
            "contract_id" => $contractId,
            "titulo" => $titulo,
            "etapa" => "Pagamentos",
            "descricao" => "Pagamentos estão sendo feitos. Situação: $status",
            "data" => $dataPagamento,
            "hora" => "09:00",
            "status" => ($status === 'Encerrado') ? "Completo" : "Em Andamento",
            "icone" => "fas fa-money-bill-wave"
        ];
    }

    // Adicionar etapa "Finalização do Contrato" se estiver encerrado ou próximo
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

    // Adicionar etapa "Prestação de Contas" se o contrato estiver encerrado
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

    $fluxos[] = $fluxo;
}

echo json_encode($fluxos);
?>