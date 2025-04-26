<?php
session_start(); // Iniciar a sessão para acessar $_SESSION

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gm_sicbd", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se o usuário está autenticado
    if (!isset($_SESSION['username'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado.']);
        exit;
    }

    $username = $_SESSION['username'];

    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $salvar_email = isset($_POST['salvar_email']) && $_POST['salvar_email'] == '1';
    $periodicidade = isset($_POST['periodicidade']) ? $_POST['periodicidade'] : '';
    $contrato_id = isset($_POST['contrato']) ? intval($_POST['contrato']) : null;
    $relatorio_todos = isset($_POST['relatorio_todos']) ? $_POST['relatorio_todos'] : null;
    $tipo_relatorio = isset($_POST['relatorio_tipo']) ? $_POST['relatorio_tipo'] : null;
    $mes = isset($_POST['mes']) ? intval($_POST['mes']) : null;
    $ano = isset($_POST['ano']) ? intval($_POST['ano']) : null;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail inválido.']);
        exit;
    }

    if (!in_array($periodicidade, ['diario', 'semanal', 'mensal'])) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Periodicidade inválida.']);
        exit;
    }

    // Determinar o tipo de relatório
    if ($relatorio_todos) {
        // Se for um relatório do tipo "todos os contratos", usamos o valor de relatorio_todos como tipo_relatorio
        if ($relatorio_todos === 'mensal_todos') {
            $tipo_relatorio = 'mensal_todos';
        } elseif ($relatorio_todos === 'anual_todos') {
            $tipo_relatorio = 'anual_todos';
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Tipo de relatório inválido para todos os contratos.']);
            exit;
        }
    } else {
        // Se for um relatório de contrato individual, tipo_relatorio já foi definido via $_POST['relatorio_tipo']
        if (!$tipo_relatorio) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Tipo de relatório não especificado.']);
            exit;
        }
    }

    // Salvar o e-mail na tabela emails_salvos, se solicitado
    $email_salvo = false;
    if ($salvar_email) {
        $sql_check_email = "SELECT COUNT(*) FROM emails_salvos WHERE email = :email AND username = :username";
        $stmt_check_email = $pdo->prepare($sql_check_email);
        $stmt_check_email->execute(['email' => $email, 'username' => $username]);
        $email_exists = $stmt_check_email->fetchColumn();

        if (!$email_exists) {
            $sql_save_email = "INSERT INTO emails_salvos (email, username) VALUES (:email, :username)";
            $stmt_save_email = $pdo->prepare($sql_save_email);
            $stmt_save_email->execute([
                'email' => $email,
                'username' => $username
            ]);
            $email_salvo = true;
        }
    }

    // Calcular a data do próximo envio
    $proximo_envio = new DateTime();
    switch ($periodicidade) {
        case 'diario':
            $proximo_envio->modify('+1 day');
            break;
        case 'semanal':
            $proximo_envio->modify('+1 week');
            break;
        case 'mensal':
            $proximo_envio->modify('+1 month');
            break;
    }

    $sql = "INSERT INTO relatorios_agendados (tipo_relatorio, contrato_id, relatorio_todos, mes, ano, email_destinatario, periodicidade, proximo_envio)
            VALUES (:tipo_relatorio, :contrato_id, :relatorio_todos, :mes, :ano, :email, :periodicidade, :proximo_envio)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'tipo_relatorio' => $tipo_relatorio,
        'contrato_id' => $contrato_id,
        'relatorio_todos' => $relatorio_todos,
        'mes' => $mes,
        'ano' => $ano,
        'email' => $email,
        'periodicidade' => $periodicidade,
        'proximo_envio' => $proximo_envio->format('Y-m-d H:i:s')
    ]);

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Relatório agendado com sucesso!',
        'email_salvo' => $email_salvo
    ]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao agendar relatório: ' . $e->getMessage()]);
}
?>