<?php
header('Content-Type: application/json');

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$user = 'root';
$password = '';

try {
    // Conexão com o banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Captura do parâmetro "type" (default: 'year')
    $type = isset($_GET['type']) ? strtolower($_GET['type']) : 'year';

    // Definição da consulta com base no tipo
    if ($type === 'year') {
        $sql = "
            SELECT 
                YEAR(data_cadastro) AS label, 
                COUNT(*) AS count 
            FROM produtos 
            GROUP BY YEAR(data_cadastro) 
            ORDER BY YEAR(data_cadastro)";
    } elseif ($type === 'month') {
        $sql = "
            SELECT 
                CONCAT(MONTHNAME(data_cadastro), ' ', YEAR(data_cadastro)) AS label, 
                COUNT(*) AS count 
            FROM produtos 
            GROUP BY YEAR(data_cadastro), MONTH(data_cadastro) 
            ORDER BY YEAR(data_cadastro), MONTH(data_cadastro)";
    } else {
        // Tipo inválido
        echo json_encode(['error' => 'Tipo de filtro inválido. Use "year" ou "month".']);
        exit;
    }

    // Execução da consulta
    $stmt = $pdo->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retorno dos dados em formato JSON
    echo json_encode($data);

} catch (PDOException $e) {
    // Tratamento de erro
    echo json_encode(['error' => 'Erro ao conectar ou executar consulta: ' . $e->getMessage()]);
    exit;
}
