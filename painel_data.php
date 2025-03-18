<?php
// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'gm_sicbd');

// Verifica conexão
if ($conn->connect_error) {
    die(json_encode(['error' => 'Erro de conexão: ' . $conn->connect_error]));
}

// Inicializa os contadores
$totalColaboradores = 0;
$treinamentosExecutados = 0;
$treinamentosEmAndamento = 0;

// Consulta para contar colaboradores
$sql = "SELECT COUNT(*) as total FROM funcionario";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $totalColaboradores = $result->fetch_assoc()['total'];
}

// Consulta para contar treinamentos executados
$sql = "SELECT COUNT(*) as total FROM treinamentos WHERE status = 'Concluído'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $treinamentosExecutados = $result->fetch_assoc()['total'];
}

// Consulta para contar treinamentos em andamento
$sql = "SELECT COUNT(*) as total FROM treinamentos WHERE status = 'Em andamento'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $treinamentosEmAndamento = $result->fetch_assoc()['total'];
}

// Retorna os dados como JSON
echo json_encode([
    'totalColaboradores' => $totalColaboradores,
    'treinamentosExecutados' => $treinamentosExecutados,
    'treinamentosEmAndamento' => $treinamentosEmAndamento,
]);

$conn->close();
?>