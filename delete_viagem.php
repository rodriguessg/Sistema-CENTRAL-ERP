<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID da viagem não fornecido.']);
            exit;
        }

        $sql = "DELETE FROM viagens WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Viagem excluída com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir a viagem.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    }
} catch (PDOException $e) {
    error_log("Erro ao excluir viagem: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
}
?>