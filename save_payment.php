<?php
// Forçar Content-Type como JSON
header('Content-Type: application/json; charset=utf-8');

// Limpar buffer de saída para evitar HTML indesejado
ob_clean();

// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

// Criar pasta de logs, se não existir
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    if (!mkdir($logDir, 0777, true)) {
        // Não interromper execução, mas logar erro internamente
        error_log(date('Y-m-d H:i:s') . " - Falha ao criar pasta de logs: $logDir\n", 3, __DIR__ . '/error.log');
    }
}
$logFile = $logDir . '/save_payment.log';

// Verificar permissões de escrita
if (!is_writable($logDir)) {
    error_log(date('Y-m-d H:i:s') . " - Sem permissão para escrever em: $logDir\n", 3, __DIR__ . '/error.log');
}

try {
    // Conexão com o banco
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Receber dados JSON
    $json = file_get_contents('php://input');
    
    // Logar dados recebidos
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Dados recebidos: " . $json . "\n", FILE_APPEND);

    // Decodificar JSON
    $data = json_decode($json, true);

    // Verificar se JSON é válido
    if (json_last_error() !== JSON_ERROR_NONE) {
        $jsonError = json_last_error_msg();
        $jsonDebug = "JSON bruto: " . substr($json, 0, 500); // Limitar tamanho para log
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Erro JSON: $jsonError - $jsonDebug\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => "JSON inválido: $jsonError"]);
        exit;
    }

    // Verificar campos obrigatórios
    if (!isset($data['contrato_titulo']) || !isset($data['mes']) || !isset($data['empenho'])) {
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Campos obrigatórios ausentes\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Campos obrigatórios ausentes']);
        exit;
    }

    // Preparar dados
    $columns = [
        'contrato_titulo', 'mes', 'empenho', 'tipo', 'nota_empenho', 'valor_contrato', 'creditos_ativos',
        'fonte', 'SEI', 'nota_fiscal', 'envio_pagamento', 'vencimento_fatura', 'valor_liquidado',
        'valor_liquidado_ag', 'ordem_bancaria', 'data_atualizacao', 'data_pagamento'
 
    ];
    $values = [];
    foreach ($columns as $column) {
        $values[$column] = isset($data[$column]) ? $data[$column] : null;
    }

    if (isset($data['id']) && $data['id']) {
        // Atualizar registro existente
        $sql = "UPDATE pagamentos SET 
                contrato_titulo = :contrato_titulo,
                mes = :mes,
                empenho = :empenho,
                tipo = :tipo,
                nota_empenho = :nota_empenho,
                valor_contrato = :valor_contrato,
                creditos_ativos = :creditos_ativos,
                fonte = :fonte,
                SEI = :SEI,
                nota_fiscal = :nota_fiscal,
                envio_pagamento = :envio_pagamento,
                vencimento_fatura = :vencimento_fatura,
                valor_liquidado = :valor_liquidado,
                valor_liquidado_ag = :valor_liquidado_ag,
                ordem_bancaria = :ordem_bancaria,
                data_atualizacao = :data_atualizacao,
                data_pagamento = :data_pagamento
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $values['id'] = $data['id'];
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Executando UPDATE para id: " . $data['id'] . "\n", FILE_APPEND);
    } else {
        // Inserir novo registro
        $sql = "INSERT INTO pagamentos (
                contrato_titulo, mes, empenho, tipo, nota_empenho, valor_contrato, creditos_ativos,
                fonte, SEI, nota_fiscal, envio_pagamento, vencimento_fatura, valor_liquidado,
                valor_liquidado_ag, ordem_bancaria,  data_atualizacao, data_pagamento
            ) VALUES (
                :contrato_titulo, :mes, :empenho, :tipo, :nota_empenho, :valor_contrato, :creditos_ativos,
                :fonte, :SEI, :nota_fiscal, :envio_pagamento, :vencimento_fatura, :valor_liquidado,
                :valor_liquidado_ag, :ordem_bancaria, :data_atualizacao, :data_pagamento
            )";
        $stmt = $conn->prepare($sql);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Executando INSERT\n", FILE_APPEND);
    }

    // Executar a query
    $stmt->execute($values);

    echo json_encode(['success' => true, 'message' => 'Pagamento salvo com sucesso']);
} catch (Exception $e) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Erro: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar pagamento: ' . $e->getMessage()]);
}
$conn = null;
?>