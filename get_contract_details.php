<?php
session_start();
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');
ini_set('display_errors', 0);

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

    if (!isset($_GET['titulo']) || empty($_GET['titulo'])) {
        echo json_encode(['success' => false, 'message' => 'Título do contrato não fornecido.']);
        exit;
    }

    $titulo = $_GET['titulo'];
    $sql = "SELECT num_parcelas, data_cadastro, valor_contrato FROM gestao_contratos WHERE titulo = :titulo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['titulo' => $titulo]);
    $contract = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contract) {
        echo json_encode(['success' => false, 'message' => 'Contrato não encontrado.']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'num_parcelas' => (int)$contract['num_parcelas'],
        'data_cadastro' => $contract['data_cadastro'] ?? null,
        'valor_contrato' => (float)$contract['valor_contrato'] ?? 0
    ]);
} catch (Exception $e) {
    error_log("Erro ao buscar detalhes do contrato: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar detalhes do contrato: ' . $e->getMessage()]);
}
?>