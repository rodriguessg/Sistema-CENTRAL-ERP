<?php
header('Content-Type: application/json');

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'supat');
if ($conn->connect_error) {
    die(json_encode(['error' => 'Falha na conexão com o banco de dados']));
}

// Consulta total de produtos
$sqlTotal = "SELECT SUM(quantidade) AS total FROM produtos";
$resultTotal = $conn->query($sqlTotal);
$totalProdutos = $resultTotal->fetch_assoc()['total'] ?? 0;

// Consulta produto com menor quantidade (verifica se está abaixo de 5 unidades)
$sqlAcabando = "SELECT produto AS nome, quantidade FROM produtos WHERE quantidade < 5 ORDER BY quantidade ASC LIMIT 1";
$resultAcabando = $conn->query($sqlAcabando);
$produtoAcabando = $resultAcabando->fetch_assoc() ?? ['nome' => 'Nenhum', 'quantidade' => 0];

// Se não houver produto abaixo de 5 unidades, definimos produtoAcabando com valores padrões
if (!$produtoAcabando || $produtoAcabando['quantidade'] < 6) {
    $produtoAcabando = ['nome' => 'Nenhum', 'quantidade' => 0];
}

// Consulta para o gráfico
$sqlProdutos = "SELECT produto AS nome, quantidade FROM produtos";
$resultProdutos = $conn->query($sqlProdutos);
$produtos = [];
while ($row = $resultProdutos->fetch_assoc()) {
    $produtos[] = $row;
}

$conn->close();

// Retornar dados em JSON
echo json_encode([
    'totalProdutos' => $totalProdutos,
    'produtoAcabando' => $produtoAcabando,
    'produtos' => $produtos
]);
?>
