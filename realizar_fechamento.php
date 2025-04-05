<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não está logado.']);
    exit;
}

// Obtém o username da sessão
$username = $_SESSION['username'];

// Configuração do banco de dados
$servername = "localhost";
$user = "root";
$password = "";
$dbname = "gm_sicbd";

// Criando a conexão com o banco de dados
$conn = new mysqli($servername, $user, $password, $dbname);

// Verificando se a conexão foi bem-sucedida
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . $conn->connect_error]);
    exit;
}

// Configura o fuso horário para São Paulo
date_default_timezone_set('America/Sao_Paulo');

// Verificando se já houve fechamento neste mês
$currentMonth = date('Y-m'); // Obtém o ano e o mês atual no formato YYYY-MM
$sqlCheck = "SELECT * FROM fechamentos WHERE DATE_FORMAT(data_fechamento, '%Y-%m') = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("s", $currentMonth);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Se já houver um fechamento para este mês, exibe a mensagem
    echo json_encode(['success' => false, 'message' => 'O fechamento do mês ' . date('F Y') . ' já foi realizado. Contate o administrador.']);
    exit;
}

// 1. Transferir todos os dados da tabela fechamento para fechamentos
// Inserindo todas as linhas de uma vez com a consulta INSERT INTO ... SELECT
$sqlInsert = "
    INSERT INTO fechamentos (username, natureza, saldo_anterior, total_entrada, total_saida, saldo_atual, data_fechamento)
    SELECT ?, natureza, saldo_anterior, total_entrada, total_saida, saldo_atual, NOW()
    FROM fechamento
";
$stmtInsert = $conn->prepare($sqlInsert);
$stmtInsert->bind_param("s", $username);

if ($stmtInsert->execute()) {
    // Sucesso na inserção, agora atualiza a tabela fechamento
    $updateSql = "UPDATE fechamento SET saldo_anterior = saldo_atual, total_entrada = 0, total_saida = 0, saldo_atual = 0";
    if ($conn->query($updateSql) === TRUE) {
        
        // 2. Limpar a tabela transicao
        $clearTransicaoSql = "DELETE FROM transicao";
        if ($conn->query($clearTransicaoSql) === TRUE) {
            echo json_encode(['success' => true, 'message' => 'Fechamento realizado com sucesso e tabela transicao limpa!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao limpar a tabela transicao: ' . $conn->error]);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar tabela fechamento: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao realizar o fechamento: ' . $stmtInsert->error]);
}

// Fechando a conexão com o banco de dados
$conn->close();
?>
