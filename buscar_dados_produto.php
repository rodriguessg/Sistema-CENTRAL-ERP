<?php
// Incluir a conexão com o banco de dados
include 'banco.php';

// Verifica se o parâmetro 'id' foi fornecido na requisição GET
if (isset($_GET['id'])) {
    $idProduto = $_GET['id'];

    // Consulta para buscar o produto com base no ID
    $query = "SELECT codigo, classificacao, natureza, localizacao FROM produtos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $idProduto);  // Passa o ID como parâmetro inteiro
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o produto foi encontrado
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
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado.']);
    }

    // Fecha a consulta
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID do produto não fornecido.']);
}

$con->close();
?>
