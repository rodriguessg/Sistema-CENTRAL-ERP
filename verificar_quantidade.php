<?php
include 'banco.php'; // Inclua a conexão com o banco de dados

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['codigo'], $input['quantidade'])) {
    $codigo = $con->real_escape_string($input['codigo']);
    $quantidade = (int) $input['quantidade'];

    // Consulta para obter a quantidade disponível
    $query = "SELECT quantidade FROM produtos WHERE codigo = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
        $quantidadeDisponivel = (int) $produto['quantidade'];

        if ($quantidade <= $quantidadeDisponivel) {
            // Atualiza a quantidade no banco de dados
            $novaQuantidade = $quantidadeDisponivel - $quantidade;
            $updateQuery = "UPDATE produtos SET quantidade = ? WHERE codigo = ?";
            $updateStmt = $con->prepare($updateQuery);
            $updateStmt->bind_param('is', $novaQuantidade, $codigo);
            $updateStmt->execute();
            $updateStmt->close();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'quantidadeDisponivel' => $quantidadeDisponivel]);
        }
    } else {
        echo json_encode(['success' => false, 'quantidadeDisponivel' => 0]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'quantidadeDisponivel' => 0]);
}

$con->close();
?>
