<?php
// Inclua a conexão com o banco de dados
include 'banco.php';

// Recuperar o ID do item (passado por GET ou POST)
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    // Preparar a consulta SQL
    $sql = "SELECT * FROM patrimonio WHERE id = ?";
    
    if ($stmt = $con->prepare($sql)) {
        // Associar parâmetros
        $stmt->bind_param("i", $id); // 'i' indica que o parâmetro é um inteiro
        
        // Executar a consulta
        $stmt->execute();
        
        // Obter o resultado
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Exibir os dados ou passá-los para o modal
            echo json_encode($row); // Para retornar os dados como JSON, por exemplo
        } else {
            echo "Nenhum resultado encontrado.";
        }
        
        $stmt->close();
    } else {
        echo "Erro ao preparar a consulta: " . $conn->error;
    }
}

$con->close();
?>