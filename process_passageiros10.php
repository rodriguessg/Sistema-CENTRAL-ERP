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