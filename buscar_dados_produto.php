<?php
include 'banco.php';  // Inclua seu arquivo de conexão com o banco

if (isset($_GET['id'])) {
    $idProduto = $_GET['id'];

    // Consulta para buscar os dados do produto
    $query = "SELECT descricao, classificacao, natureza, localizacao, preco_medio FROM produtos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $idProduto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $produto = $resultado->fetch_assoc();
        echo json_encode([
            'success' => true,
            'descricao' => $produto['descricao'],
            'classificacao' => $produto['classificacao'],
            'natureza' => $produto['natureza'],
            'localizacao' => $produto['localizacao'],
            'preco_medio' => $produto['preco_medio']
        ]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}
?>
