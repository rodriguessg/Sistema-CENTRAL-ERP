<?php
ob_start(); // Iniciar buffer de saída
session_start(); // Iniciar sessão para obter o usuário logado

// Definir fuso horário de São Paulo (BRT, UTC-3)
date_default_timezone_set('America/Sao_Paulo');

// Forçar Content-Type como JSON
header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['username'])) {
    ob_end_clean();
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$logged_user = $_SESSION['username']; // Obtém o username do usuário logado

// Conexão com o banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        // Atribuição dos valores com verificação de null
        $id = $input['id'] ?? null;
        $bonde = $input['bonde'] ?? '';
        $saida = $input['saida'] ?? '';
        $retorno = $input['retorno'] ?? '';
        $maquinista = $input['maquinista'] ?? '';
        $agente = $input['agente'] ?? '';
        $hora = $input['hora'] ?? '';
        $pagantes = $input['pagantes'] ?? 0;
        $moradores = $input['moradores'] ?? 0;
        $grat_pcd_idoso = $input['grat_pcd_idoso'] ?? 0;
        $gratuidade = $input['gratuidade'] ?? 0;
        $passageiros = $input['passageiros'] ?? 0;
        $tipo_viagem = $input['tipo_viagem'] ?? '';
        $data = $input['data'] ?? '';
        $subida_id = $input['subida_id'] ?? null;

        // Validação dos campos obrigatórios
        if (!$id || empty($bonde) || empty($maquinista) || empty($agente) || empty($data) || empty($tipo_viagem)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos.']);
            exit;
        }

        // Validação específica para tipo_viagem = 'ida'
        if ($tipo_viagem === 'ida' && (empty($saida) || empty($retorno))) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Saída e retorno são obrigatórios para viagens de ida.']);
            exit;
        }

        // Iniciar transação para garantir consistência
        $pdo->beginTransaction();

        // Atualização da viagem
        $sql = "UPDATE viagens SET 
                    bonde = :bonde, 
                    saida = :saida, 
                    retorno = :retorno, 
                    maquinista = :maquinista, 
                    agente = :agente, 
                    hora = :hora, 
                    pagantes = :pagantes, 
                    gratuidade = :gratuidade, 
                    grat_pcd_idoso = :grat_pcd_idoso, 
                    moradores = :moradores, 
                    passageiros = :passageiros, 
                    tipo_viagem = :tipo_viagem, 
                    data = :data, 
                    subida_id = :subida_id 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => (int)$id,
            ':bonde' => $bonde,
            ':saida' => $saida,
            ':retorno' => $retorno,
            ':maquinista' => $maquinista,
            ':agente' => $agente,
            ':hora' => $hora,
            ':pagantes' => (int)$pagantes,
            ':gratuidade' => (int)$gratuidade,
            ':grat_pcd_idoso' => (int)$grat_pcd_idoso,
            ':moradores' => (int)$moradores,
            ':passageiros' => (int)$passageiros,
            ':tipo_viagem' => $tipo_viagem,
            ':data' => $data,
            ':subida_id' => $subida_id ? (int)$subida_id : null
        ]);

        // Verificar se a atualização afetou alguma linha
        if ($stmt->rowCount() > 0) {
            // Inserção no log_eventos
            $stmt_log = $pdo->prepare("INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (:matricula, :tipo_operacao, NOW())");
            $tipo_operacao = "viagem atualizada";
            $stmt_log->execute([
                ':matricula' => $logged_user,
                ':tipo_operacao' => $tipo_operacao
            ]);

            // Confirmar transação
            $pdo->commit();
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Viagem atualizada com sucesso!']);
        } else {
            $pdo->rollBack();
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Nenhuma viagem encontrada com o ID fornecido.']);
        }
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    }
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao atualizar viagem: " . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados: ' . htmlspecialchars($e->getMessage())]);
    exit;
}
?>