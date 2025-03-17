<?php
// Incluir a conexão com o banco de dados
include 'banco.php';

// Receber os dados via POST
$data = json_decode(file_get_contents('php://input'), true);

// Verificar se o ID da notificação foi recebido
if (isset($data['id'])) {
    $id = $data['id'];

    // Atualizar o status da notificação para 'lida'
    $updateQuery = "UPDATE notificacoes SET status = 'lida' WHERE id = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false]);
}
?>
