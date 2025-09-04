<?php
session_start();
include 'header.php';

// Definir fuso horário de São Paulo (BRT, UTC-3)
date_default_timezone_set('America/Sao_Paulo');

// Database configuration
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se existe algum acidente com status 'em andamento'
    $stmt_check = $pdo->query("SELECT COUNT(*) as total FROM acidentes WHERE status = 'em andamento'");
    $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
    if ($row['total'] > 0) {
        // Bloquear acesso à página
        echo "<div style='text-align: center; padding: 20px; background-color: #f9f9f9; border-radius: 5px; border: 1px solid #ddd; margin: 20px auto; max-width: 600px;'>";
        echo "<h2>Operação Indisponível</h2>";
        echo "<p>Não é possível realizar novas operações devido a uma ocorrência em andamento. Por favor, resolva a ocorrência pendente em <a href='reportacidentes.php'>Registrar Ocorrências</a>.</p>";
        echo "</div>";
        exit();
    }

    // Query to fetch all bondes from the 'bondes' table
    $stmt = $pdo->query("SELECT id, modelo, capacidade, ativo, ano_fabricacao, descricao FROM bondes ORDER BY modelo ASC");
    $bondes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    $bondes = [
        ['id' => 1, 'modelo' => 'BONDE 17', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2010, 'descricao' => 'Bonde padrão'],
        ['id' => 2, 'modelo' => 'BONDE 16', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2009, 'descricao' => 'Bonde clássico'],
        ['id' => 3, 'modelo' => 'BONDE 19', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2011, 'descricao' => 'Bonde renovado'],
        ['id' => 4, 'modelo' => 'BONDE 22', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2013, 'descricao' => 'Bonde moderno'],
        ['id' => 5, 'modelo' => 'BONDE 18', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2010, 'descricao' => 'Bonde intermediário'],
        ['id' => 6, 'modelo' => 'BONDE 20', 'capacidade' => 32, 'ativo' => 0, 'ano_fabricacao' => 2012, 'descricao' => 'Bonde atualizado']
    ];
}
?>