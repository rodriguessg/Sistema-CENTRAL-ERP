<?php
include "banco.php";

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$novaSenha = password_hash($data['novaSenha'], PASSWORD_BCRYPT);

$query = "UPDATE usuario SET senha = ? WHERE username = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ss", $novaSenha, $username);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
