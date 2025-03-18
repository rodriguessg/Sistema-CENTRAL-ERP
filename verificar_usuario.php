<?php
include "banco.php";

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$email = $data['email'];

$query = "SELECT * FROM usuario WHERE username = ? AND email = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
