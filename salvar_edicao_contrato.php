<?php
// Cabeçalhos para garantir que o retorno seja JSON
header('Content-Type: application/json');

// Conectar ao banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    // Conectar ao banco de dados com PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()]);
    exit;
}

// Receber os dados enviados via JSON
$data = json_decode(file_get_contents('php://input'), true);

// Verificar se os dados foram recebidos corretamente
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Extrair os dados do contrato
$titulo = isset($data['titulo']) ? $data['titulo'] : null;
$descricao = isset($data['descricao']) ? $data['descricao'] : null;
$validade = isset($data['validade']) ? $data['validade'] : null;
$situacao = isset($data['situacao']) ? $data['situacao'] : null;
$aditivos = isset($data['aditivos']) ? $data['aditivos'] : [];

if (!$titulo || !$descricao || !$validade || !$situacao) {
    echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não foram preenchidos']);
    exit;
}

// Atualizar os dados do contrato na tabela 'gestao_contratos'
$query = "UPDATE gestao_contratos SET titulo = ?, descricao = ?, validade = ?, situacao = ? WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$titulo, $descricao, $validade, $situacao, $contractId]);

// Atualizar os aditivos no banco de dados
$aditivosCount = count($aditivos);

// Preencher os valores dos aditivos nas colunas apropriadas
$aditivosQuery = "UPDATE gestao_contratos SET ";
$aditivosQueryParts = [];
$aditivosParams = [];

for ($i = 0; $i < 5; $i++) {
    if ($i < $aditivosCount) {
        $aditivosQueryParts[] = "valor_aditivo" . ($i + 1) . " = ?";
        $aditivosParams[] = $aditivos[$i];
    } else {
        $aditivosQueryParts[] = "valor_aditivo" . ($i + 1) . " = NULL";  // Se não houver aditivo, coloca NULL
    }
}

$aditivosQuery .= implode(", ", $aditivosQueryParts) . " WHERE id = ?";
$aditivosParams[] = $contractId;  // ID do contrato para atualizar a linha específica

// Executar a consulta para atualizar os aditivos
$stmt = $pdo->prepare($aditivosQuery);
$stmt->execute($aditivosParams);

// Retornar sucesso
echo json_encode(['success' => true]);
?>
