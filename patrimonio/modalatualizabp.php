<?php
// Conexão com o banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'supat';

$conn = new mysqli($host, $user, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados']));
}

// Coleta os dados do POST
$id = $_POST['id'];
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$situacao = $_POST['situacao'];
$categoria = $_POST['categoria'];

// Prepara a query de atualização
$sql = "UPDATE patrimonio 
        SET nome = ?, descricao = ?, valor = ?, situacao = ?, categoria = ? 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdssi", $nome, $descricao, $valor, $situacao, $categoria, $id);

// Executa e verifica o resultado
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar os dados: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
