<?php
// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'gm_sicbd');

// Verifica conexão
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Erro de conexão: ' . $conn->connect_error]));
}

// Recebe os dados enviados pelo JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Validação simples
if (!isset($data['nome'], $data['email'], $data['cargo'], $data['dataAdmissao'])) {
    echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
    exit;
}

// Insere os dados na tabela
$stmt = $conn->prepare("INSERT INTO funcionario (nome, email, cargo, data_admissao) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $data['nome'], $data['email'], $data['cargo'], $data['dataAdmissao']);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erro ao salvar os dados']);
}

$stmt->close();
$conn->close();
?>
