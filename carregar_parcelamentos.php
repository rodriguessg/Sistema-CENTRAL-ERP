<?php
// Conectar ao banco de dados
$host = 'localhost';  // Altere para o host do seu banco de dados
$dbname = 'gm_sicbd'; // Altere para o nome do seu banco de dados
$username = 'root'; // Altere para o seu usuário do banco de dados
$password = '';   // Altere para a sua senha do banco de dados

try {
    // Cria uma nova instância PDO para se conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configura o modo de erro do PDO para exceções
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar se o ID do contrato foi passado na URL
    $contratoId = $_GET['contrato_id'] ?? null;

    if ($contratoId) {
        // Consulta para buscar os parcelamentos relacionados ao contrato
        $stmt = $pdo->prepare("
            SELECT parcela, valor, data_vencimento
            FROM parcelamentos
            WHERE contrato_id = :contrato_id
        ");
        
        // Vincula o parâmetro do contrato_id
        $stmt->bindParam(':contrato_id', $contratoId, PDO::PARAM_INT);
        
        // Executa a consulta
        $stmt->execute();

        // Recupera todos os resultados
        $parcelamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Verifica se há parcelamentos
        if ($parcelamentos) {
            // Retorna os parcelamentos como um JSON
            echo json_encode($parcelamentos);
        } else {
            // Caso não haja parcelamentos, retorna um array vazio
            echo json_encode([]);
        }
    } else {
        // Caso o ID do contrato não seja passado, retorna erro
        echo json_encode(['error' => 'ID do contrato não fornecido']);
    }

} catch (PDOException $e) {
    // Caso ocorra algum erro na conexão ou consulta, exibe a mensagem de erro
    echo json_encode(['error' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()]);
}
?>
