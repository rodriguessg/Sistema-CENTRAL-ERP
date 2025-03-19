<?php
// Conexão com o banco de dados
include 'banco.php';

// Verifique se a conexão foi bem-sucedida
if ($con->connect_error) {
    die("Falha na conexão com o banco de dados: " . $con->connect_error);
}

// Verifica se a requisição foi enviada via GET para buscar material
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['nome'])) {
    $nome = isset($_GET['nome']) ? $con->real_escape_string($_GET['nome']) : '';
    
    if (!empty($nome)) {
        // Consulta para buscar o produto pelo nome
        $query = "SELECT codigo, classificacao, natureza, localizacao FROM produtos WHERE nome LIKE ?";
        $stmt = $con->prepare($query);
        $likeNome = "%" . $nome . "%"; // Usando LIKE para buscar parcialmente
        $stmt->bind_param('s', $likeNome);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Produto encontrado, retorna os dados em JSON
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
            echo json_encode(['success' => false]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false]);
    }
    
    $con->close();
    exit;
}


?>
