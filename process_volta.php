<?php
    // Conexão com o banco de dados
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'gm_sicbd';

    $conn = new mysqli($host, $user, $password, $dbname);

    // Verificar conexão
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
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