<?php
// Configuração da conexão com o banco de dados
$host = 'localhost';  // Endereço do servidor do banco de dados
$dbname = 'gm_sicbd';  // Nome do banco de dados
$username = 'root';  // Nome de usuário do banco de dados
$password = '';  // Senha do banco de dados

try {
    // Conectando ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consultando contratos encerrados
    $sql = "SELECT * FROM gestao_contratos WHERE situacao = 'encerrado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Verificando se há contratos
    $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>
