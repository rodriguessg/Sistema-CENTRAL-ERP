<?php
// add_viagem.php

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Pegue os dados do formulário
    $modelo_bonde = $_POST['modelo_bonde'] ?? '';
    $saida = $_POST['saida'] ?? '';
    $retorno = $_POST['retorno'] ?? '';
    $maquinista = $_POST['maquinista'] ?? '';
    $agente = $_POST['agente'] ?? '';
    $hora = $_POST['hora'] ?? null;
    $pagantes = $_POST['pagantes'] ?? 0;
    $moradores = $_POST['moradores'] ?? 0;
    $grat_pcd_idoso = $_POST['grat_pcd_idoso'] ?? 0;
    $gratuidade = $_POST['gratuidade'] ?? 0;
    $passageiros = $_POST['passageiros'] ?? 0;
    $viagem = $_POST['viagem'] ?? 1;
    $data = $_POST['data'] ?? null;

    $sql = "INSERT INTO viagens (
        modelo_bonde, saida, retorno, maquinista, agente, hora, pagantes, moradores, grat_pcd_idoso, gratuidade, passageiros, viagem, data
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $modelo_bonde, $saida, $retorno, $maquinista, $agente, $hora, $pagantes, $moradores, $grat_pcd_idoso, $gratuidade, $passageiros, $viagem, $data
    ]);

    // Redireciona de volta para a página principal
    header('Location: index.php?success=1');
    exit;
} catch (PDOException $e) {
    echo "Erro ao inserir: " . $e->getMessage();
}
?>
