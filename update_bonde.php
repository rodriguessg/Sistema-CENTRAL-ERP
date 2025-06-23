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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['modelo']) && isset($_POST['capacidade'])) {
    $id = $_POST['id'];
    $modelo = $_POST['modelo'];
    $capacidade = $_POST['capacidade'];

    $stmt = $pdo->prepare("UPDATE bondes SET modelo = :modelo, capacidade = :capacidade WHERE id = :id");
    $stmt->execute(['id' => $id, 'modelo' => $modelo, 'capacidade' => $capacidade]);

    echo json_encode(['success' => true]);
    exit();
}
echo json_encode(['success' => false]);
exit();
?>