<?php
header('Content-Type: application/json');

// Configurações do banco de dados
$host = 'localhost';
$user = 'root'; // Substitua pelo seu usuário do banco de dados
$password = ''; // Substitua pela sua senha
$dbname = 'gm_sicbd'; // Substitua pelo nome do seu banco de dados

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die(json_encode(['erro' => 'Erro na conexão com o banco de dados: ' . $conn->connect_error]));
}

// Obtendo os dados da requisição
$data = json_decode(file_get_contents('php://input'), true);

if ($data['acao'] != 'fechar') {
    echo json_encode(['erro' => 'Ação inválida']);
    exit;
}

$dataFechamento = $data['dataFechamento']; // Ex: 2025-03

// Consulta para buscar as transações do mês atual
$sql_transacoes = "
    SELECT t.material_id, p.natureza, 
           SUM(CASE WHEN t.tipo = 'entrada' THEN t.quantidade ELSE 0 END) AS total_entrada, 
           SUM(CASE WHEN t.tipo = 'Saída' THEN t.quantidade ELSE 0 END) AS total_saida, 
           p.custo, p.quantidade AS saldo_inicial
    FROM transicao t
    JOIN produtos p ON t.material_id = p.id
    WHERE DATE_FORMAT(t.data, '%Y-%m') = ?
    GROUP BY t.material_id, p.natureza, p.custo, p.quantidade";

$stmt = $conn->prepare($sql_transacoes);
$stmt->bind_param("s", $dataFechamento); // Passa o mês atual como parâmetro
$stmt->execute();
$result = $stmt->get_result();

// Inicializa variáveis para o fechamento
$total_entradas = 0;
$total_saidas = 0;
$natureza = [];

// Processa os resultados
while ($row = $result->fetch_assoc()) {
    // Calcula o total de entradas e saídas
    $total_entrada = $row['total_entrada'] * $row['custo']; // Calcula o valor das entradas
    $total_saida = $row['total_saida'] * $row['custo']; // Calcula o valor das saídas

    // Atualiza os totais gerais
    $total_entradas += $total_entrada;
    $total_saidas += $total_saida;

    // Calcula o saldo atual baseado no saldo inicial e nas transações
    $saldo_atual = ($row['saldo_inicial'] + $row['total_entrada'] - $row['total_saida']) * $row['custo'];

    // Atualiza o saldo por natureza
    if (!isset($natureza[$row['natureza']])) {
        $natureza[$row['natureza']] = 0;
    }
    $natureza[$row['natureza']] += $saldo_atual; // Mantém o saldo final por natureza

    // Inserir na tabela de fechamento
    $insert_sql = "INSERT INTO fechamento (data_fechamento, natureza, total_entrada, total_saida, saldo_atual, custo) 
                   VALUES (?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssddds", $dataFechamento, $row['natureza'], $total_entrada, $total_saida, $saldo_atual, $row['custo']);
    $insert_stmt->execute();
}

// Atualiza o saldo de cada produto na tabela 'produtos' com o novo saldo
foreach ($natureza as $natureza_key => $valor_total) {
    $update_sql = "UPDATE produtos p 
                   JOIN transicao t ON p.id = t.material_id
                   SET p.quantidade = p.quantidade + {$valor_total}
                   WHERE p.natureza = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("s", $natureza_key);
    $update_stmt->execute();
}

// Retorna o resultado em formato JSON
echo json_encode([
    'total_entradas' => number_format($total_entradas, 2, ',', '.'),
    'total_saidas' => number_format($total_saidas, 2, ',', '.'),
    'natureza' => $natureza
]);

// Fecha a conexão com o banco de dados
$stmt->close();
$conn->close();
?>
