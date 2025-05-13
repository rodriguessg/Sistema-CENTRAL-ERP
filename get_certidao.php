<?php
session_start();
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

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
    $sql = "SELECT c.id, c.documento, c.data_vencimento, c.nome, c.fornecedor, c.responsavel, 
                   c.arquivo, c.contrato_id, gc.titulo AS contrato_titulo
            FROM certidoes c
            LEFT JOIN gestao_contratos gc ON c.contrato_id = gc.id
            ORDER BY c.data_vencimento ASC";
    $stmt = $pdo->query($sql);
    $certidoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Certidões retornadas: " . json_encode($certidoes));
    echo json_encode(['success' => true, 'certidoes' => $certidoes]);
} catch (Exception $e) {
    error_log("Erro ao buscar certidões: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar certidões: ' . $e->getMessage()]);
}
?>