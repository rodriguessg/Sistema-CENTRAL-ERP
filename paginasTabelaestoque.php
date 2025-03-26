<?php
// Configuração do banco de dados
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "gm_sicbd";

// Conexão com o banco de dados
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verificar a conexão
if ($conn->connect_error) {
    die(json_encode(["erro" => "Erro ao conectar com o banco de dados: " . $conn->connect_error]));
}

// Parâmetros de paginação e filtro
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$itensPorPagina = isset($_GET['itensPorPagina']) ? intval($_GET['itensPorPagina']) : 5;
$filtro = isset($_GET['filtro']) ? $conn->real_escape_string($_GET['filtro']) : ''; // Filtro de produto
$offset = ($pagina - 1) * $itensPorPagina;

// Consulta para contar o número total de registros com filtro
// Aqui aplicamos o filtro diretamente na coluna 'produto'
$totalRegistrosQuery = "SELECT COUNT(*) AS total FROM produtos WHERE produto LIKE '%$filtro%'";
$totalRegistrosResult = $conn->query($totalRegistrosQuery);
$totalRegistros = $totalRegistrosResult->fetch_assoc()['total'];

// Calcular o número total de páginas
$totalPaginas = ceil($totalRegistros / $itensPorPagina);

// Consulta para buscar os produtos com limite, offset e filtro, incluindo a descrição da tabela 'materiais'
$sql = "
    SELECT p.*, 
           (SELECT descricao FROM materiais m WHERE m.codigo = p.produto LIMIT 1) AS descricao_material 
    FROM produtos p
    WHERE p.produto LIKE '%$filtro%' 
    LIMIT $itensPorPagina OFFSET $offset
";

$result = $conn->query($sql);

// Verificar se há resultados
$dados = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Se a descrição for nula ou vazia, atribui uma descrição padrão
        $row['descricao'] = !empty($row['descricao_material']) ? $row['descricao_material'] : 'Descrição não encontrada';
        unset($row['descricao_material']); // Remover o campo auxiliar
        $dados[] = $row;
    }
}

// Retornar os dados e o total de páginas como JSON
echo json_encode([
    "dados" => $dados,
    "total_paginas" => $totalPaginas
]);

// Fechar a conexão
$conn->close();
?>
