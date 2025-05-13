<?php
session_start();
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');
ini_set('display_errors', 0);

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

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID da notificação não fornecido.']);
        exit;
    }

    $notificationId = (int)$_GET['id'];

    // Verificar se a notificação existe e pertence ao usuário
    $sql = "SELECT situacao FROM notificacoes WHERE id = :id AND username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'id' => $notificationId,
        'username' => $_SESSION['username'] ?? ''
    ]);
    $notification = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notification) {
        echo json_encode(['success' => false, 'message' => 'Notificação não encontrada ou não pertence ao usuário.']);
        exit;
    }

    if ($notification['situacao'] === 'lida') {
        echo json_encode(['success' => true, 'message' => 'Notificação já está marcada como lida.']);
        exit;
    }

    // Marcar como lida
    $sql = "UPDATE notificacoes SET situacao = 'lida' WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $notificationId]);

    echo json_encode(['success' => true, 'message' => 'Notificação marcada como lida com sucesso.']);
} catch (Exception $e) {
    error_log("Erro ao marcar notificação como lida: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao marcar notificação como lida: ' . $e->getMessage()]);
}
?>