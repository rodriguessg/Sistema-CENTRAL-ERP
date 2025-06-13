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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['viagem_id'];
    $bonde_id = $_POST['bonde_id_pass'];
    $origem = $_POST['origin'];
    $destino = $_POST['destination'];
    $motorneiro = $_POST['motorman'];
    $auxiliar = $_POST['auxiliary'];
    $validador = $_POST['validator'];
    $passageiros_ida = $_POST['passageiros_ida'];
    $data_ida = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO viagens (id, bonde_id, origem, destino, motorneiro, auxiliar, validador, passageiros_ida, data_ida) VALUES (:id, :bonde_id, :origem, :destino, :motorneiro, :auxiliar, :validador, :passageiros_ida, :data_ida)");
    $stmt->execute([
        'id' => $id,
        'bonde_id' => $bonde_id,
        'origem' => $origem,
        'destino' => $destino,
        'motorneiro' => $motorneiro,
        'auxiliar' => $auxiliar,
        'validador' => $validador,
        'passageiros_ida' => $passageiros_ida,
        'data_ida' => $data_ida
    ]);

    header("Location: homebonde.php?tab=controle");
    exit();
}