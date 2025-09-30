<?php
header('Content-Type: application/json');
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';
     $logged_user = $_SESSION['username']; // Obtém o username do usuário logado
try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['nome']) || !isset($input['tipo'])) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
        exit;
    }

    $nome = trim($input['nome']);
    $tipo = trim($input['tipo']);

    // Validate input
    if (empty($nome) || !in_array($tipo, ['maquinista', 'agente'])) {
        echo json_encode(['success' => false, 'message' => 'Nome ou tipo inválido.']);
        exit;
    }

    // Determine the table based on tipo
    $table = ($tipo === 'maquinista') ? 'maquinistas' : 'agentes';

    // Check if the name already exists
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE nome = ?");
    $stmt_check->execute([$nome]);
    if ($stmt_check->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Funcionário já existe.']);
        exit;
    }

    // Insert the new staff member
    $stmt = $pdo->prepare("INSERT INTO $table (nome) VALUES (?)");
    $stmt->execute([$nome]);

    echo json_encode(['success' => true, 'message' => 'Funcionário adicionado com sucesso!']);

} catch (PDOException $e) {
    error_log("Erro ao adicionar funcionário: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Erro geral: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro inesperado: ' . $e->getMessage()]);
}


        // Inserção no log_eventos (usando PDO e o username do usuário logado)
        $stmt_log = $pdo->prepare("INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (:matricula, :tipo_operacao, NOW())");
        $tipo_operacao = "Adicionou maquinista ou agente";
        $stmt_log->execute([
            ':matricula' => $logged_user, // Usa o username do usuário logado
            ':tipo_operacao' => $tipo_operacao
        ]);
?>