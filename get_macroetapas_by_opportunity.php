<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "gm_sicbd");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Conexão falhou: ' . $conn->connect_error]);
    exit;
}

$planejamento_id = isset($_GET['planejamento_id']) ? (int)$_GET['planejamento_id'] : 0;
$setor = isset($_GET['setor']) ? $conn->real_escape_string($_GET['setor']) : '';

if (!$planejamento_id || !$setor) {
    echo json_encode(['success' => false, 'message' => 'Parâmetros planejamento_id e setor são obrigatórios']);
    exit;
}

$sql = "SELECT nome_macroetapa, responsavel, etapa_nome, etapa_concluida, data_conclusao 
        FROM macroetapas 
        WHERE planejamento_id = ? AND setor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $planejamento_id, $setor);
$stmt->execute();
$result = $stmt->get_result();

$macroetapas = [];
$current_macro = null;

while ($row = $result->fetch_assoc()) {
    if (!$current_macro || $current_macro['nome_macroetapa'] !== $row['nome_macroetapa']) {
        if ($current_macro) {
            $macroetapas[] = $current_macro;
        }
        $current_macro = [
            'nome_macroetapa' => $row['nome_macroetapa'],
            'responsavel' => $row['responsavel'],
            'etapas' => []
        ];
    }
    $current_macro['etapas'][] = [
        'etapa_nome' => $row['etapa_nome'],
        'etapa_concluida' => $row['etapa_concluida'],
        'data_conclusao' => $row['data_conclusao']
    ];
}

if ($current_macro) {
    $macroetapas[] = $current_macro;
}

echo json_encode(['success' => true, 'macroetapas' => $macroetapas]);
$stmt->close();
$conn->close();
?>