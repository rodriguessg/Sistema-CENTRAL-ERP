<?php
header('Content-Type: application/json');
setlocale(LC_MONETARY, 'pt_BR.UTF-8');
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $data = json_decode(file_get_contents('php://input'), true);
    $setor = isset($data['setor']) ? $data['setor'] : '';
    $termo = isset($data['termo']) ? '%' . $data['termo'] . '%' : '%';

    // Fetch planejamentos, mapping columns to frontend-expected names
    $sql = "
        SELECT id, 
               titulo_oportunidade AS title, 
               setor AS sector, 
               COALESCE(valor_estimado, '0') AS value, 
               prazo AS deadline, 
               status, 
               descricao AS description, 
               created_at AS createdAt
        FROM planejamento 
        WHERE setor = :setor AND titulo_oportunidade LIKE :termo
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['setor' => $setor, 'termo' => $termo]);
    $opportunities = $stmt->fetchAll();

    // Fetch macroetapas and etapas for each planejamento
    foreach ($opportunities as &$opp) {
        $opp['projectPlan'] = [];

        // Fetch macroetapas
        $sqlMacro = "
            SELECT id, 
                   nome_macroetapa AS name, 
                   responsavel AS responsible 
            FROM macroetapas 
            WHERE planejamento_id = :id
        ";
        $stmtMacro = $pdo->prepare($sqlMacro);
        $stmtMacro->execute(['id' => $opp['id']]);
        $macroetapas = $stmtMacro->fetchAll();

        // Fetch etapas for each macroetapa
        foreach ($macroetapas as &$macro) {
            $sqlEtapas = "
                SELECT nome AS name, 
                       concluida AS completed 
                FROM etapas 
                WHERE macroetapa_id = :macro_id
            ";
            $stmtEtapas = $pdo->prepare($sqlEtapas);
            $stmtEtapas->execute(['macro_id' => $macro['id']]);
            $macro['etapas'] = $stmtEtapas->fetchAll();
            $macro['expanded'] = true;
            unset($macro['id']); // Remove internal ID from response
        }

        $opp['projectPlan'] = $macroetapas;
        // Ensure value is formatted as a string (e.g., "1000,00")
        $opp['value'] = number_format((float)$opp['value'], 2, ',', '.');
    }

    echo json_encode(['success' => true, 'opportunities' => $opportunities]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao buscar oportunidades: ' . $e->getMessage()]);
}
?>