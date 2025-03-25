<?php
// Inclui o arquivo com a função
require_once 'funcoes.php';

// Verifica se foi passado um ID via GET
if (isset($_GET['id'])) {
    $notificationId = $_GET['id'];

    // Chama a função
    $result = marcar_notificacao_lida($notificationId);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Erro ao marcar notificação como lida ou notificação não encontrada'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'ID da notificação não fornecido'
    ]);
}
