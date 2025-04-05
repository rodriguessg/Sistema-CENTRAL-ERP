<?php
// Configuração de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o ID foi enviado via POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepare a consulta SQL para excluir o produto
    $sql = "DELETE FROM transicao WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Executar a consulta
    if ($stmt->execute()) {
        // Se a exclusão foi bem-sucedida, retornar um sucesso
        echo json_encode(['success' => true]);
    } else {
        // Caso ocorra algum erro
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir']);
    }

    $stmt->close();
}

$conn->close();
?>
