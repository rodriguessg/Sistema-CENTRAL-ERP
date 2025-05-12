<?php
// Evitar qualquer saída antes do JSON
ob_start();
header('Content-Type: application/json');

// Ativar exibição de erros para depuração (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . $e->getMessage()]);
    exit;
}

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $situacao = isset($_GET['situacao']) ? trim($_GET['situacao']) : '';

    $sql = "SELECT * FROM gestao_contratos WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (titulo LIKE :search OR descricao LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (!empty($situacao)) {
        $sql .= " AND situacao = :situacao";
        $params[':situacao'] = $situacao;
    }

    $sql .= " ORDER BY validade DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Combinar valores aditivos em um array
    foreach ($contratos as &$contrato) {
        $contrato['valores_aditivos'] = array_filter([
            $contrato['valor_aditivo1'],
            $contrato['valor_aditivo2'],
            $contrato['valor_aditivo3'],
            $contrato['valor_aditivo4'],
            $contrato['valor_aditivo5']
        ], function($value) {
            return !is_null($value);
        });
    }

    ob_end_clean();
    echo json_encode(['success' => true, 'contratos' => $contratos]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar contratos: ' . $e->getMessage()]);
}
?>