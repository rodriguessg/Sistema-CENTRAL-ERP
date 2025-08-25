<?php
// Define o cabeçalho para resposta em JSON
header('Content-Type: application/json');

// Inclui a configuração do banco de dados
try {
    include 'bancoo.php'; // Certifique-se de que este arquivo contém a conexão PDO
    if (!isset($pdo) || !$pdo) {
        throw new Exception("Falha na conexão com o banco de dados.");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit();
}

// Verifica se a requisição é POST e processa os dados JSON
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data['id']) || !isset($data['ativo'])) {
        echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes']);
        exit();
    }

    $id = $data['id'];
    $ativo = $data['ativo'];

    try {
        // Prepara e executa a atualização do campo 'ativo' na tabela 'bondes'
        $stmt = $pdo->prepare("UPDATE bondes SET ativo = :ativo WHERE id = :id");
        $stmt->execute([':ativo' => $ativo, ':id' => $id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o banco: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

exit();
?>