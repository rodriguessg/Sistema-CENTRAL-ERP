<?php
session_start();
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados.']);
    exit;
}

try {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID da certidão não fornecido.']);
        exit;
    }

    $sql = "DELETE FROM certidoes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $input['id']]);

    echo json_encode(['success' => true, 'message' => 'Certidão excluída com sucesso!']);
} catch (Exception $e) {
    error_log("Erro ao excluir certidão: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao excluir certidão.']);
}
?>