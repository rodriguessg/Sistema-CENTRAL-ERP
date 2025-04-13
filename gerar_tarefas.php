<?php
// gerar_tarefas.php
header('Content-Type: application/json');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar tarefas
    $stmt = $pdo->query("SELECT tarefa, data_agendamento FROM agendamentos ORDER BY data_agendamento DESC");
    $tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Exibir as tarefas
    $relatorio = '<h3>Tarefas Agendadas</h3><table><tr><th>Tarefa</th><th>Data Agendamento</th></tr>';
    foreach ($tarefas as $tarefa) {
        $relatorio .= '<tr><td>' . $tarefa['tarefa'] . '</td><td>' . $tarefa['data_agendamento'] . '</td></tr>';
    }
    $relatorio .= '</table>';

    echo $relatorio;
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
