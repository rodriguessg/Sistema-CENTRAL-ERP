<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the JSON data from the request
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($data['modelo']) || !isset($data['capacidade']) || !isset($data['ano_fabricacao']) || !isset($data['descricao'])) {
        echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos']);
        exit;
    }
     $logged_user = $_SESSION['username']; // Obtém o username do usuário logado
    // Check for duplicate modelo
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM bondes WHERE modelo = :modelo");
    $stmt->execute([':modelo' => $data['modelo']]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Modelo já existe no banco de dados']);
        exit;
    }

    // Prepare and execute the insert query
    $stmt = $pdo->prepare("INSERT INTO bondes (modelo, capacidade, ano_fabricacao, descricao, ativo) VALUES (:modelo, :capacidade, :ano_fabricacao, :descricao, :ativo)");
    $stmt->execute([
        ':modelo' => $data['modelo'],
        ':capacidade' => $data['capacidade'],
        ':ano_fabricacao' => $data['ano_fabricacao'],
        ':descricao' => $data['descricao'],
        ':ativo' => isset($data['ativo']) ? (int)$data['ativo'] : 0
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Erro ao adicionar bonde: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao adicionar bonde: ' . $e->getMessage()]);
}


        // Inserção no log_eventos (usando PDO e o username do usuário logado)
        $stmt_log = $pdo->prepare("INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (:matricula, :tipo_operacao, NOW())");
        $tipo_operacao = "adicionou um novo bonde";
        $stmt_log->execute([
            ':matricula' => $logged_user, // Usa o username do usuário logado
            ':tipo_operacao' => $tipo_operacao
        ]);
?>