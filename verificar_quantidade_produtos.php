<?php
// header('Content-Type: application/json');

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'supat';
$user = 'root';
$password = '';

try {
    // Conectando ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para contar a quantidade total de produtos
    $queryTotal = "SELECT COUNT(*) as quantidade FROM produtos";
    $stmtTotal = $pdo->query($queryTotal);
    $resultTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC);

    // Consulta para contar os produtos com 5 unidades no estoque
    $query5Unidades = "SELECT COUNT(*) as quantidade5 FROM produtos WHERE quantidade = 5";
    $stmt5Unidades = $pdo->query($query5Unidades);
    $result5Unidades = $stmt5Unidades->fetch(PDO::FETCH_ASSOC);

    // Consulta para verificar produtos com menos de 5 unidades
    $queryAbaixo5 = "SELECT id, produto, quantidade FROM produtos WHERE quantidade < 5"; // Corrija a coluna 'produto_nome' se necessário
    $stmtAbaixo5 = $pdo->query($queryAbaixo5);
    
    // Verifica se há produtos com quantidade abaixo de 5
    if ($stmtAbaixo5->rowCount() > 0) {
        // Para cada produto abaixo de 5 unidades, envia uma notificação
        while ($produto = $stmtAbaixo5->fetch(PDO::FETCH_ASSOC)) {
            // Verifica se a sessão está ativa, caso contrário, inicia a sessão
            if (session_status() == PHP_SESSION_NONE) {
                session_start(); // Inicia a sessão se não estiver ativa
            }

            $username = $_SESSION['username']; // Nome do usuário da sessão
            $setor = $_SESSION['setor']; // Setor do usuário da sessão
            $mensagem = "O produto '{$produto['produto']}' está com {$produto['quantidade']} unidades. Estoque abaixo de 5 unidades.";

            // Inserir a notificação no banco de dados
            $notificacaoQuery = "INSERT INTO notificacoes (username, setor, mensagem, status) 
                                 VALUES (?, ?, ?, 'nao lida')";
            $notificacaoStmt = $pdo->prepare($notificacaoQuery);
            $notificacaoStmt->bindParam(1, $username);
            $notificacaoStmt->bindParam(2, $setor);
            $notificacaoStmt->bindParam(3, $mensagem);
            $notificacaoStmt->execute();
        }
    }

    // Retorna a quantidade total de produtos, produtos com 5 unidades e produtos com menos de 5 unidades
    // echo json_encode([
    //     'quantidade_total' => $resultTotal['quantidade'],
    //     'quantidade_5_unidades' => $result5Unidades['quantidade5'],
    //     'quantidade_abaixo_5' => $stmtAbaixo5->rowCount() // Conta o número de produtos abaixo de 5
    // ]);
} catch (PDOException $e) {
    // Em caso de erro na conexão com o banco de dados
    echo json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
}
?>
