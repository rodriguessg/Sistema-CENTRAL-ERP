<?php
// Inclua a conexão com o banco de dados
include 'bancoo.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se o valor da pesquisa foi enviado
    if (isset($_POST['searchValue']) && !empty(trim($_POST['searchValue']))) {
        $searchValue = trim($_POST['searchValue']);

        // Corrige o nome da coluna para "nome" (baseado na estrutura da tabela anteriormente criada)
        $sql = "SELECT * FROM gestao_contratos WHERE titulo LIKE :search OR descricao LIKE :search ORDER BY validade DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':search', '%' . $searchValue . '%', PDO::PARAM_STR);

        try {
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Retorna os resultados como JSON
            echo json_encode($results);
        } catch (PDOException $e) {
            // Retorna erro em caso de falha na consulta
            echo json_encode(['error' => 'Erro na consulta: ' . $e->getMessage()]);
        }
    } else {
        // Retorna uma mensagem caso o valor da pesquisa esteja vazio
        echo json_encode(['error' => 'Por favor, forneça um valor para pesquisa.']);
    }
} else {
    // Responde com erro caso o método não seja POST
    echo json_encode(['error' => 'Método inválido. Use POST para enviar dados.']);
}
?>
