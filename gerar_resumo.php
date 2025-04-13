<?php
// gerar_resumo_processos.php
header('Content-Type: application/json');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar andamentos de processos
    $stmt = $pdo->query("SELECT id, titulo, andamento, data_atualizacao FROM processos ORDER BY data_atualizacao DESC");
    $processos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Exibir o relatório
    $relatorio = '<h3>Resumo de Andamentos de Processos e Pagamentos</h3><table><tr><th>Processo</th><th>Andamento</th><th>Data Atualização</th></tr>';
    foreach ($processos as $processo) {
        $relatorio .= '<tr><td>' . $processo['titulo'] . '</td><td>' . $processo['andamento'] . '</td><td>' . $processo['data_atualizacao'] . '</td></tr>';
    }
    $relatorio .= '</table>';

    echo $relatorio;
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
