<?php
header('Content-Type: application/json');

try {
    // Database connection
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if event_id is provided
    if (!isset($_POST['event_id']) || !is_numeric($_POST['event_id'])) {
        echo json_encode(['error' => 'ID do evento não fornecido ou inválido.']);
        exit;
    }

    $eventId = (int)$_POST['event_id'];

    // Fetch event details
    $sql = "SELECT id, titulo, descricao, data, hora, categoria FROM eventos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo json_encode(['error' => 'Evento não encontrado.']);
        exit;
    }

    // Return event data as JSON
    echo json_encode($event);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro no banco de dados: ' . htmlspecialchars($e->getMessage())]);
}
?>