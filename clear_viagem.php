<?php
// clear_viagem.php
session_start();

// Definir fuso horário de São Paulo (BRT, UTC-3)
date_default_timezone_set('America/Sao_Paulo');

// Forçar Content-Type como JSON
header('Content-Type: application/json');

// Simulação de usuário logado
$_SESSION['username'] = isset($_SESSION['username']) ? $_SESSION['username'] : 'usuario_logado';

// Conexão com o banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . htmlspecialchars($e->getMessage())]);
    exit();
}

// Criar a tabela historico_viagens (se não existir)
try {
    $sqlCreateTable = "
        CREATE TABLE IF NOT EXISTS historico_viagens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bonde VARCHAR(255) NOT NULL,
            saida VARCHAR(255) NOT NULL,
            retorno VARCHAR(255) NOT NULL,
            maquinista VARCHAR(255) NOT NULL,
            agente VARCHAR(255) NOT NULL,
            hora TIME NOT NULL,
            pagantes INT NOT NULL DEFAULT 0,
            gratuidade INT NOT NULL DEFAULT 0,
            moradores INT NOT NULL DEFAULT 0,
            passageiros INT NOT NULL DEFAULT 0,
            tipo_viagem VARCHAR(255) NOT NULL,
            data DATE NOT NULL,
            created_at DATETIME NOT NULL,
            subida_id INT DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sqlCreateTable);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao criar a tabela historico_viagens: ' . htmlspecialchars($e->getMessage())]);
    exit();
}

// Manipular limpeza da tabela viagens
try {
    // Iniciar transação para garantir consistência
    $pdo->beginTransaction();

    // Passo 1: Copiar dados da tabela viagens para historico_viagens
    $sqlCopy = "
        INSERT INTO historico_viagens (
            bonde, saida, retorno, maquinista, agente, hora, pagantes, gratuidade, moradores, passageiros, 
            tipo_viagem, data, created_at, subida_id
        )
        SELECT 
            bonde, saida, retorno, maquinista, agente, hora, pagantes, gratuidade, moradores, passageiros, 
            tipo_viagem, data, created_at, subida_id
        FROM viagens";
    $pdo->exec($sqlCopy);

    // Passo 2: Limpar a tabela viagens
    $sqlDelete = "DELETE FROM viagens";
    $pdo->exec($sqlDelete);

    // Confirmar transação
    $pdo->commit();

    // Enviar e-mail de notificação para grodrigues@central.rj.gov.br
    $to = 'grodrigues@central.rj.gov.br';
    $subject = 'Limpeza da Tabela Viagens Realizada';
    $message = "
        <h2>Notificação: Limpeza da Tabela Viagens</h2>
        <p>A tabela 'viagens' foi limpa com sucesso.</p>
        <p>Os dados foram copiados para a tabela 'historico_viagens' antes da limpeza.</p>
        <p><strong>Usuário:</strong> " . htmlspecialchars($_SESSION['username']) . "</p>
        <p><strong>Data e Hora:</strong> " . date('d/m/Y H:i:s') . "</p>
    ";
    $headers = "MIME-Version: 1.0" . "\r\n" .
               "Content-type: text/html; charset=UTF-8" . "\r\n" .
               "From: impressora@central.rj.gov.br" . "\r\n" .
               "Reply-To: impressora@central.rj.gov.br" . "\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Tabela limpa com sucesso e e-mail enviado!']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Tabela limpa com sucesso, mas erro ao enviar e-mail.']);
    }
} catch (PDOException $e) {
    // Reverter transação em caso de erro
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao copiar dados ou limpar a tabela: ' . htmlspecialchars($e->getMessage())]);
}