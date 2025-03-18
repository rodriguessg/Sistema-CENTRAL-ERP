<?php
include 'banco.php'; // Inclua a conexão com o banco de dados

header('Content-Type: application/json');

// Verifica se o parâmetro 'nome' foi passado na URL
if (isset($_GET['nome'])) {
    // Protege contra SQL Injection
    $nome = $con->real_escape_string($_GET['nome']);

    // Consulta para buscar as informações do material
    $query = "SELECT codigo, classificacao, natureza, localizacao 
              FROM produtos 
              WHERE produto LIKE ?";
    $stmt = $con->prepare($query);

    if ($stmt) {
        $searchTerm = "%" . $nome . "%"; // Permite busca parcial
        $stmt->bind_param('s', $searchTerm); // Vincula o parâmetro
        $stmt->execute();
        $result = $stmt->get_result(); // Obtém o resultado da consulta

        // Verifica se encontrou o produto
        if ($result->num_rows > 0) {
            $produto = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'codigo' => $produto['codigo'],
                'classificacao' => $produto['classificacao'],
                'natureza' => $produto['natureza'],
                'localizacao' => $produto['localizacao']
            ]);
        } else {
            // Produto não encontrado
            echo json_encode([
                'success' => false,
                'codigo' => null,
                'classificacao' => null,
                'natureza' => null,
                'localizacao' => null
            ]);
        }
        $stmt->close();
    } else {
        // Caso ocorra um erro na preparação da query
        echo json_encode([
            'success' => false,
            'error' => 'Erro na preparação da consulta.'
        ]);
    }
} else {
    // Caso o parâmetro 'nome' não seja passado
    echo json_encode([
        'success' => false,
        'error' => 'Parâmetro "nome" não foi fornecido.'
    ]);
}

// Fecha a conexão com o banco de dados
$con->close();
?>
