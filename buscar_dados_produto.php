<?php
// Conectar ao banco de dados
include 'banco.php';

// Verifica se o parâmetro 'id' foi fornecido na requisição GET
if (isset($_GET['id'])) {
    $idProduto = $_GET['id'];

    // Consulta para buscar o produto com base no ID
    $query = "SELECT codigo, classificacao, natureza, localizacao, preco_medio FROM produtos WHERE id = ?";
    
    // Prepara a consulta
    $stmt = $con->prepare($query);
    
    // Vincula o parâmetro 'id' à consulta (tipo 'i' significa que é um inteiro)
    $stmt->bind_param('i', $idProduto);
    
    // Executa a consulta
    $stmt->execute();
    
    // Obtém o resultado da consulta
    $result = $stmt->get_result();
    
    // Verifica se o produto foi encontrado
    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
        
        // Retorna os dados do produto como JSON
        echo json_encode([
            'success' => true,
            'codigo' => $produto['codigo'],
            'classificacao' => $produto['classificacao'],
            'natureza' => $produto['natureza'],
            'localizacao' => $produto['localizacao'],
            'preco_medio' => $produto['preco_medio']
        ]);
    } else {
        // Caso o produto não seja encontrado
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado.']);
    }

    // Fecha a consulta
    $stmt->close();
} else {
    // Caso o ID não tenha sido fornecido
    echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido.']);
}

// Fecha a conexão com o banco de dados
$con->close();

?>
