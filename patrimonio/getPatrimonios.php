<?php
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    // Criação da conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Definir modo de erro para exceção
} catch (PDOException $e) {
    // Em caso de erro na conexão, loga o erro e exibe uma mensagem amigável
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Consulte o administrador.");
}
// Definir o cabeçalho como JSON para a resposta
header('Content-Type: application/json');

// Função para buscar os dados da tabela 'patrimonio'
function getPatrimonios($pdo) {
    try {
        // Query SQL para selecionar as colunas específicas da tabela 'patrimonio'
        $query = 'SELECT id, nome, codigo, categoria, data_registro, foto FROM patrimonio';
        $stmt = $pdo->query($query);

        // Retornar os dados como um array associativo
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Caso haja algum erro ao executar a consulta
        echo json_encode(['error' => 'Erro ao buscar os dados: ' . $e->getMessage()]);
        exit;
    }
}

// Chama a função para pegar os dados da tabela 'patrimonio'
$patrimonios = getPatrimonios($pdo);

// Retorna os dados como JSON
echo json_encode($patrimonios);
?>
