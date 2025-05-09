<?php
header('Content-Type: application/json');

// Simulando uma conexão com o banco de dados
try {
    // Conectar ao banco de dados
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
    exit;
}

// Processar os dados recebidos
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Simulando um processo de salvamento de dados
try {
    $query = "UPDATE gestao_contratos SET titulo = ?, descricao = ?, validade = ?, situacao = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$data['titulo'], $data['descricao'], $data['validade'], $data['situacao'], 1]);  // Exemplo de ID
    echo json_encode(['success' => true, 'message' => 'Contrato atualizado com sucesso']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar contrato: ' . $e->getMessage()]);
}
?>
