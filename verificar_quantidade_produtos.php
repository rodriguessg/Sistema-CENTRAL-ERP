<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$user = 'root';
$password = '';

try {
    // Conectando ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultas para contar a quantidade total de produtos, com 5 unidades e abaixo de 5
    $queryTotal = "SELECT COUNT(*) as quantidade FROM produtos";
    $stmtTotal = $pdo->query($queryTotal);
    $resultTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC);

    $query5Unidades = "SELECT COUNT(*) as quantidade5 FROM produtos WHERE quantidade = 5";
    $stmt5Unidades = $pdo->query($query5Unidades);
    $result5Unidades = $stmt5Unidades->fetch(PDO::FETCH_ASSOC);

    $queryAbaixo5 = "SELECT id, produto, quantidade FROM produtos WHERE quantidade < 5"; 
    $stmtAbaixo5 = $pdo->query($queryAbaixo5);

    // Verificar se a sessão está ativa e obter o nome e setor do usuário
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Inicia a sessão se não estiver ativa
    }

    $username = $_SESSION['username']; // Nome do usuário da sessão
    $setor = $_SESSION['setor']; // Setor do usuário da sessão

    // Verificar data da última notificação
    $queryUltimaNotificacao = "SELECT MAX(data_criacao) as ultima_data FROM notificacoes WHERE username = ? AND setor = ?";
    $stmtUltimaNotificacao = $pdo->prepare($queryUltimaNotificacao);
    $stmtUltimaNotificacao->bindParam(1, $username);
    $stmtUltimaNotificacao->bindParam(2, $setor);
    $stmtUltimaNotificacao->execute();
    $ultimaNotificacao = $stmtUltimaNotificacao->fetch(PDO::FETCH_ASSOC);

    // Verifica se já foi enviado um e-mail hoje
    if (empty($ultimaNotificacao['ultima_data']) || date('Y-m-d') != date('Y-m-d', strtotime($ultimaNotificacao['ultima_data']))) {
        // Se não foi enviado hoje, proceda para enviar a notificação

        if ($stmtAbaixo5->rowCount() > 0) {
            // Para cada produto abaixo de 5 unidades, envia uma notificação e um e-mail
            while ($produto = $stmtAbaixo5->fetch(PDO::FETCH_ASSOC)) {
                $mensagem = "O produto '{$produto['produto']}' está com {$produto['quantidade']} unidades. Estoque abaixo de 5 unidades.";

                // Inserir a notificação no banco de dados
                $notificacaoQuery = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                                     VALUES (?, ?, ?, 'nao lida', NOW())";
                $notificacaoStmt = $pdo->prepare($notificacaoQuery);
                $notificacaoStmt->bindParam(1, $username);
                $notificacaoStmt->bindParam(2, $setor);
                $notificacaoStmt->bindParam(3, $mensagem);
                $notificacaoStmt->execute();

                // Enviar e-mail para os destinatários
                $emailsDestinatarios = [
                    'grodrigues@central.rj.gov.br',
                    'alexandrerocha@central.rj.gov.br',
                    'impressora@central.rj.gov.br',
                    'maikalves@central.rj.gov.br'
                ];

                $assunto = 'Alerta de Estoque Abaixo de 5 Unidades';
                $corpoEmail = "Alerta: O produto '{$produto['produto']}' está com {$produto['quantidade']} unidades, abaixo do limite de 5.";

                // Chamar a função enviarEmail para enviar os e-mails
                include 'enviar_email.php'; // Incluindo o arquivo para enviar e-mails
                foreach ($emailsDestinatarios as $emailDestinatario) {
                    enviarEmail($assunto, $corpoEmail, $emailDestinatario);
                }
            }
        }
    }

    // Retorna a quantidade total de produtos, produtos com 5 unidades e produtos com menos de 5 unidades
    echo json_encode([
        'quantidade_total' => $resultTotal['quantidade'],
        'quantidade_5_unidades' => $result5Unidades['quantidade5'],
        'quantidade_abaixo_5' => $stmtAbaixo5->rowCount() // Conta o número de produtos abaixo de 5
    ]);
    
} catch (PDOException $e) {
    // Em caso de erro na conexão com o banco de dados
    echo json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
}
?>
