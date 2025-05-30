<?php
header('Content-Type: application/json');

$response = ['sucesso' => false, 'mensagem' => '', 'dados' => []];

try {
    // Conexão com o banco de dados (substitua pelos seus dados)
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tipo_relatorio = $_POST['relatorio_tipo'] ?? '';
    $dados = [];

    if ($tipo_relatorio === 'mensal_todos') {
        $mes = $_POST['mes'] ?? '';
        if (!$mes) {
            throw new Exception('Mês não especificado.');
        }

       // Consulta para relatórios mensais de todos os contratos usando contrato_titulo
$sql = "SELECT c.titulo, c.num_parcelas, p.data_pagamento, p.valor_liquidado
FROM gestao_contratos c
LEFT JOIN pagamentos p ON c.titulo = p.contrato_titulo
WHERE MONTH(p.data_pagamento) = :mes";
$stmt = $pdo->prepare($sql);
$stmt->execute(['mes' => $mes]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiza os dados
$contratos = [];
foreach ($result as $row) {
$titulo = $row['titulo'];
if (!isset($contratos[$titulo])) {
$contratos[$titulo] = [
    'titulo' => $titulo,
    'num_parcelas' => $row['num_parcelas'],
    'pagamentos' => []
];
}
if ($row['data_pagamento']) {
// Adiciona o pagamento com o valor liquidado
$contratos[$titulo]['pagamentos'][] = [
    'data_pagamento' => $row['data_pagamento'],
    'valor_liquidado' => $row['valor_liquidado']  // Agora o valor liquidado da tabela 'pagamentos' é incluído
];
}


// Agora o array $contratos contém os dados organizados com o valor_liquidado da tabela pagamentos.

        }
        $dados = array_values($contratos);

    }elseif ($tipo_relatorio === 'anual_todos') {
    $ano = $_POST['ano'] ?? '';
    if (!$ano) {
        throw new Exception('Ano não especificado.');
    }

    // Consulta para relatórios anuais de todos os contratos, somando o valor do contrato e os aditivos da tabela gestao_contratos
    $sql = "SELECT c.titulo, c.num_parcelas, YEAR(c.data_cadastro) as ano, 
                   SUM(c.valor_contrato + COALESCE(c.valor_aditivo1, 0) + COALESCE(c.valor_aditivo2, 0) + 
                   COALESCE(c.valor_aditivo3, 0) + COALESCE(c.valor_aditivo4, 0) + COALESCE(c.valor_aditivo5, 0)) as total_pago
            FROM gestao_contratos c
            WHERE YEAR(c.data_cadastro) = :ano
            GROUP BY c.titulo, YEAR(c.data_cadastro)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ano' => $ano]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organiza os dados
    $contratos = [];
    foreach ($result as $row) {
        $titulo = $row['titulo'];
        if (!isset($contratos[$titulo])) {
            $contratos[$titulo] = [
                'titulo' => $titulo,
                'num_parcelas' => $row['num_parcelas'],
                'anos' => []
            ];
        }
        $contratos[$titulo]['anos'][] = [
            'ano' => $row['ano'],
            'total_pago' => $row['total_pago'],
        ];
    }
    $dados = array_values($contratos);
}

$response['sucesso'] = true;
$response['dados'] = $dados;

} catch (Exception $e) {
    $response['mensagem'] = $e->getMessage();
}


echo json_encode($response);
?>