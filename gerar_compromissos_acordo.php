<?php
// gerar_compromissos_acordo.php
header('Content-Type: application/json');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar compromissos de acordo
    $stmt = $pdo->query("SELECT compromisso, data_compromisso FROM compromissos ORDER BY data_compromisso DESC");
    $compromissos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Exibir os compromissos
    $relatorio = '<h3>Compromissos de Acordo</h3><table><tr><th>Compromisso</th><th>Data</th></tr>';
    foreach ($compromissos as $compromisso) {
        $relatorio .= '<tr><td>' . $compromisso['compromisso'] . '</td><td>' . $compromisso['data_compromisso'] . '</td></tr>';
    }
    $relatorio .= '</table>';

    echo $relatorio;
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
