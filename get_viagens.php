<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, bonde, saida, retorno, maquinista, agente, hora, pagantes, gratuidade, moradores, passageiros, tipo_viagem, data, subida_id, created_at FROM viagens ORDER BY id DESC");
    $viagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($viagens);
} catch (PDOException $e) {
    error_log("Erro ao buscar viagens: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
}
?>