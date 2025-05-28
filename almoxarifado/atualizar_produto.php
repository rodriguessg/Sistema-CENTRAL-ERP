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

// Verificar se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter os dados do formulário
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $quantidade = isset($_POST['quantidade']) ? (int) $_POST['quantidade'] : 0;

    // Validar se os campos necessários foram preenchidos
    if ($id <= 0 || $quantidade < 0) {
        echo 'Erro: ID ou quantidade inválidos.';
        exit;
    }

    // Atualizar a quantidade no banco de dados
    $query = "UPDATE produtos SET quantidade = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ii', $quantidade, $id);

    // Executar a consulta e verificar se foi bem-sucedida
    if ($stmt->execute()) {
        echo 'Produto atualizado com sucesso!';
    } else {
        echo 'Erro ao atualizar produto: ' . $stmt->error;
    }

    // Fechar a consulta e a conexão
    $stmt->close();
    $con->close();
} else {
    echo 'Método inválido.';
}
?>
