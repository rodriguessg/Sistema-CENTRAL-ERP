<?php
// Conexão com o banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifica se o parâmetro id foi enviado via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para obter os dados do produto com o ID
    $query = "SELECT descricao, classificacao, natureza, localizacao, preco_medio FROM produtos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $id);  // 'i' para tipo integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o produto foi encontrado
    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
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
    $con->close();
} else {
    echo json_encode(['success' => false]);
}
?>
