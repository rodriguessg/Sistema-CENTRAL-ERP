<?php
session_start(); // Inicia a sessão para obter o usuário logado

header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root'; // Credencial do banco de dados
$password = ''; // Credencial do banco de dados

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica se o usuário está logado
        if (!isset($_SESSION['username'])) {
            echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
            exit;
        }

        $logged_user = $_SESSION['username']; // Obtém o username do usuário logado
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        // Valida o ID da viagem
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID da viagem não fornecido.']);
            exit;
        }

        // Inicia uma transação para garantir consistência
        $pdo->beginTransaction();

        // Exclusão da viagem
        $sql = "DELETE FROM viagens WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Inserção no log_eventos (usando PDO e o username do usuário logado)
            $stmt_log = $pdo->prepare("INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (:matricula, :tipo_operacao, NOW())");
            $tipo_operacao = "excluiu a viagem";
            $stmt_log->execute([
                ':matricula' => $logged_user,
                ':tipo_operacao' => $tipo_operacao
            ]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Viagem excluída com sucesso!']);
        } else {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir a viagem.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    }
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao excluir viagem: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
}
?>