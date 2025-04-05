<?php
// Configuração do banco de dados
$host = 'localhost';  // Seu host
$dbname = 'gm_sicbd';  // Seu banco de dados
$username = 'root';  // Seu usuário do banco de dados
$password = '';  // Sua senha do banco de dados

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro ao conectar com o banco de dados: ' . $e->getMessage();
    exit;
}

// Consulta para buscar os meses cadastrados na tabela produtos
$sql = "SELECT DISTINCT MONTH(data_cadastro) AS mes FROM produtos WHERE data_cadastro IS NOT NULL ORDER BY mes";
$stmt = $pdo->query($sql);

// Array para armazenar os meses
$meses = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Conversão do mês numérico para nome do mês
    $mes = date("F", mktime(0, 0, 0, $row['mes'], 10));
    $meses[] = $mes; // Adiciona o nome do mês ao array
}

// Retorna os meses em formato JSON
echo json_encode($meses);
?>
