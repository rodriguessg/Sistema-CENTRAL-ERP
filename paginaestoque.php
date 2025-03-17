<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'usuario', 'senha', 'nome_do_banco');

if ($conn->connect_error) {
    die(json_encode(['erro' => 'Erro na conexão com o banco de dados.']));
}

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$itensPorPagina = isset($_GET['itensPorPagina']) ? (int)$_GET['itensPorPagina'] : 5;
$filtro = isset($_GET['filtro']) ? $conn->real_escape_string($_GET['filtro']) : '';

// Calcular o offset para paginação
$offset = ($pagina - 1) * $itensPorPagina;

// Consulta para buscar os produtos
$query = "SELECT id, produto, classificacao, localizacao, quantidade 
          FROM produtos 
          WHERE produto LIKE '%$filtro%' 
          LIMIT $itensPorPagina OFFSET $offset";

$result = $conn->query($query);

$dados = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dados[] = $row;
    }
}

// Consulta para contar o total de produtos para paginação
$queryTotal = "SELECT COUNT(*) AS total FROM produtos WHERE produto LIKE '%$filtro%'";
$resultTotal = $conn->query($queryTotal);
$totalItens = $resultTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalItens / $itensPorPagina);

echo json_encode(['dados' => $dados, 'total_paginas' => $totalPaginas]);

$conn->close();
?>
