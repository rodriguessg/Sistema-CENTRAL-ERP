<?php
// gerar_relatorio_financeiro.php
header('Content-Type: application/json');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $contratoId = $_GET['contrato_id'];
    $mes = $_GET['mes'];

    // Consultar relatório financeiro com base no mês e contrato
    $stmt = $pdo->prepare("SELECT p.parcela, p.valor, p.data_vencimento 
                           FROM pagamentos p 
                           WHERE p.contrato_id = ? AND MONTH(p.data_vencimento) = ?");
    $stmt->execute([$contratoId, $mes]);
    $pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Gerar o relatório financeiro
    $relatorio = '<h3>Relatório Financeiro - Mês de ' . date("F", mktime(0, 0, 0, $mes, 10)) . '</h3>';
    $relatorio .= '<table><tr><th>Parcela</th><th>Valor</th><th>Data Vencimento</th></tr>';
    foreach ($pagamentos as $pagamentos) {
        $relatorio .= '<tr><td>' . $pagamentos['parcela'] . '</td><td>' . $pagamentos['valor'] . '</td><td>' . $pagamentos['data_vencimento'] . '</td></tr>';
    }
    $relatorio .= '</table>';

    echo $relatorio;
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
