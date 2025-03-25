<?php
// Incluir o arquivo com a função 'marcar_notificacao_lida'
include('./funcoes.php');

// Verificar se o ID da notificação foi passado como parâmetro
if (isset($_GET['id'])) {
    $notificationId = $_GET['id'];

    // Chama a função para marcar a notificação como lida
    $result = marcar_notificacao_lida($notificationId);

    // Retorna uma resposta JSON dependendo do resultado
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao marcar notificação como lida ou notificação não encontrada']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID da notificação não fornecido']);
}
?>
