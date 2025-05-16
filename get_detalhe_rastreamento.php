<?php
header('Content-Type: application/json');

session_start();

// Configurar logging de erros
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Conectar ao banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    echo json_encode([]);
    exit;
}

// Verificar se há um contract_id na URL
$contractId = isset($_GET['contract_id']) && is_numeric($_GET['contract_id']) ? (int)$_GET['contract_id'] : null;

$detalhes = [];
if ($contractId) {
    $sql = "SELECT id AS contract_id, titulo AS etapa, validade AS data, '10:00' AS hora, 
            CASE 
                WHEN validade <= CURDATE() THEN 'Encerrado'
                WHEN date_service <= CURDATE() AND publicacao <= CURDATE() THEN 'Em Andamento'
                ELSE 'Em Elaboração'
            END AS status
            FROM gestao_contratos 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $contractId]);
    $detalhes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($detalhes);
?>