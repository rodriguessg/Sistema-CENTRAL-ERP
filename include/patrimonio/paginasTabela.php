<?php
include 'banco.php'; // Substitua pelo seu arquivo de conexão ao banco

// Definir o número de itens por página
$itens_por_pagina = 5;

// Obter o número da página atual (padrão para 1)
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Calcular o OFFSET para a consulta
$offset = ($pagina_atual - 1) * $itens_por_pagina;

// Consulta SQL com LIMIT e OFFSET
$sql = "SELECT id, nome, descricao, valor, localizacao, situacao, cadastrado_por, categoria, codigo , tipo_operacao
        FROM patrimonio 
        LIMIT $itens_por_pagina OFFSET $offset";

$result = $con->query($sql);

// Consulta para contar o número total de registros
$sql_total = "SELECT COUNT(*) as total FROM patrimonio";
$total_result = $con->query($sql_total);
$total_registros = $total_result->fetch_assoc()['total'];

// Calcular o total de páginas
$total_paginas = ceil($total_registros / $itens_por_pagina);

// Dados em JSON para o JavaScript
$dados = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dados[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode([
    'dados' => $dados,
    'total_paginas' => $total_paginas
]);
exit;
?>
