<?php
include_once('./funcoes.php');

// Recebe o parâmetro username, se informado
$username = isset($_GET['username']) ? $_GET['username'] : null;
$unreadCount = getUnreadNotificationsCount($username);

echo json_encode(['unreadCount' => $unreadCount]);
?>
