<?php
// Inicie a sessão
session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
    echo json_encode(['error' => 'Usuário não logado']);
    exit();
}

// Inclua a conexão com o banco de dados
require_once 'bancoo.php';

try {
    // Recupere as informações do usuário da sessão
    $username = $_SESSION['username'];

    // Consultar os dados do usuário (email, setor, tempo_registro, matricula)
    $query = $pdo->prepare("SELECT email, setor, tempo_registro, matricula FROM usuario WHERE username = :username");
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();
    $userData = $query->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        // Definir a matrícula do usuário
        $matricula = $userData['matricula'];

        // Consultar a contagem das operações "LOGOU NO SISTEMA" e "Cadastro de Patrimônio" na tabela log_eventos
        $queryMovimentacoes = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN tipo_operacao = 'LOGOU NO SISTEMA' THEN 1 ELSE 0 END) AS logou_no_sistema,
                SUM(CASE WHEN tipo_operacao = 'Cadastro de Patrimônio' THEN 1 ELSE 0 END) AS cadastro_patrimonio
            FROM log_eventos
            WHERE matricula = :matricula
        ");
        $queryMovimentacoes->bindParam(':matricula', $matricula, PDO::PARAM_STR);
        $queryMovimentacoes->execute();
        $movimentacoesData = $queryMovimentacoes->fetch(PDO::FETCH_ASSOC);

        // Calcular o total de movimentações
        $totalMovimentacoes = $movimentacoesData['logou_no_sistema'] + $movimentacoesData['cadastro_patrimonio'];

        // Retorne as informações como JSON
        echo json_encode([
            'matricula' => $matricula,
            'email' => $userData['email'],
            'setor' => $userData['setor'],
            'tempo_registro' => $userData['tempo_registro'],
            'tipo_operacao' => $totalMovimentacoes // Contagem total das movimentações
        ]);
    } else {
        echo json_encode(['error' => 'Usuário não encontrado']);
    }
} catch (PDOException $e) {
    // Caso haja erro ao buscar as informações
    echo json_encode(['error' => 'Erro ao acessar as informações: ' . $e->getMessage()]);
}
?>
