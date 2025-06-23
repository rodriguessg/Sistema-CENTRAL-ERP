<?php
  // Include database configuration with error handling
try {
    include 'bancoo.php';
    if (!isset($pdo) || !$pdo) {
        throw new Exception("Falha na conexão com o banco de dados.");
    }
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['trip_id']) && isset($_POST['volta'])) {
    $trip_id = $_POST['trip_id'];
    $volta = $_POST['volta'];
    $data_volta = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("UPDATE viagens SET passageiros_volta = :volta, data_volta = :data_volta WHERE id = :id");
    $stmt->execute(['volta' => $volta, 'data_volta' => $data_volta, 'id' => $trip_id]);

    echo json_encode(['success' => true]);
    exit();
}
echo json_encode(['success' => false]);
exit();