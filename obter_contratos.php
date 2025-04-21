<?php
header('Content-Type: application/json');
// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'gm_sicbd');
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(['erro' => 'Falha na conexão com o banco']);
  exit;
}

$result = $conn->query('SELECT id, titulo FROM gestao_contratos');
$contratos = [];
while ($row = $result->fetch_assoc()) {
  $contratos[] = $row;
}
echo json_encode($contratos);
$conn->close();
?>