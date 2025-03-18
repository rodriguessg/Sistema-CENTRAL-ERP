<?php
// Incluir o arquivo de conexão com o banco de dados
include 'bancoo.php';
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
