<?php

// Conexão com o banco de dados
$dsn = 'mysql:host=localhost;dbname=gm_sicbd';
$username_db = 'root';  // Seu usuário do banco de dados
$password_db = '';      // Sua senha do banco de dados

try {
    $pdo = new PDO($dsn, $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Função para verificar e inserir a notificação automaticamente
function verificarContratosProximosVencimento($pdo) {
    // Definindo a data limite para 30 dias antes da validade do contrato
    $dataLimite = date('Y-m-d', strtotime('+30 days'));

    // Consulta para buscar contratos próximos do vencimento (dentro de 30 dias)
    $sql = "SELECT * FROM gestao_contratos WHERE validade <= :dataLimite AND validade >= CURDATE()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':dataLimite' => $dataLimite]);

    $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Loop para inserir uma notificação para cada contrato que está prestes a vencer
    foreach ($contratos as $contrato) {
        $nomeContrato = $contrato['titulo'];
        $validade = $contrato['validade'];
        $usuario = $_SESSION['username'];  // Usuário logado
        $setor = $_SESSION['setor'];      // Setor do usuário logado
        $situacao = 'não lida';           // Situação da notificação
        $dataNotificacao = date('Y-m-d H:i:s'); // Data da notificação

        // Verificar se já existe uma notificação para o mesmo contrato e usuário
        $sqlVerificacao = "SELECT COUNT(*) FROM notificacoes WHERE username = :username AND mensagem = :mensagem";
        $stmtVerificacao = $pdo->prepare($sqlVerificacao);
        $stmtVerificacao->execute([
            ':username' => $usuario,
            ':mensagem' => "Contrato '{$nomeContrato}' com validade em {$validade} prestes a expirar."
        ]);

        // Se não houver uma notificação, insere a nova
        if ($stmtVerificacao->fetchColumn() == 0) {
            // Inserir a notificação se não existir
            $sqlNotificacao = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                               VALUES (:username, :setor, :mensagem, :situacao, :data_criacao)";
            $stmtNotificacao = $pdo->prepare($sqlNotificacao);

            try {
                $stmtNotificacao->execute([
                    ':username' => $usuario,
                    ':setor' => $setor,
                    ':mensagem' => "Contrato '{$nomeContrato}' com validade em {$validade} prestes a expirar.",
                    ':situacao' => $situacao,
                    ':data_criacao' => $dataNotificacao
                ]);

                $_SESSION['success'] = "Notificação inserida com sucesso para o contrato '{$nomeContrato}'.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Erro ao adicionar notificação: " . $e->getMessage();
            }
        } else {
            $_SESSION['info'] = "Notificação para o contrato '{$nomeContrato}' já foi registrada.";
        }
    }
}

// Chama a função para verificar contratos e inserir as notificações automaticamente
verificarContratosProximosVencimento($pdo);

?>

