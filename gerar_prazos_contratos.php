<?php
// gerar_prazos_contratos.php
header('Content-Type: application/json');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar prazos de contratos
    $stmt = $pdo->query("SELECT contrato_id, data_vigencia, titulo FROM gestao_contratos ORDER BY data_vigencia ASC");
    $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Gerar relatório de prazos
    $relatorio = '<h3>Prazos de Contratos e Vigências</h3>';
    $relatorio .= '<table><tr><th>Contrato</th><th>Data de Vigência</th></tr>';
    foreach ($contratos as $contrato) {
        $relatorio .= '<tr><td>' . $contrato['titulo'] . '</td><td>' . $contrato['data_vigencia'] . '</td></tr>';
    }
    $relatorio .= '</table>';

    echo $relatorio;
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
