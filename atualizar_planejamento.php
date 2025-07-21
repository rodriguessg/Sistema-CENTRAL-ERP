<?php
header('Content-Type: application/json');

// Configuração da conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

try {
    // Conexão com o banco de dados usando PDO para melhor segurança
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conn->exec("SET NAMES utf8");
} catch (PDOException $e) {
    error_log("Falha na conexão com o banco de dados: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Falha na conexão com o banco de dados: " . $e->getMessage()]);
    exit;
}

// Receber dados do frontend
$input = json_decode(file_get_contents('php://input'), true);
if ($input === null) {
    error_log("Erro ao decodificar JSON: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Dados JSON inválidos"]);
    exit;
}

// Depuração: Log dos dados recebidos
error_log("Dados recebidos: " . print_r($input, true));

// Verificar o tipo de requisição
if (isset($input['id']) && isset($input['status'])) {
    // Atualização do status da oportunidade
    $id = (int)$input['id'];
    $status = $conn->quote($input['status']);

    // Validar status
    $valid_statuses = ['planejamento', 'andamento', 'finalizado'];
    if (!in_array($input['status'], $valid_statuses)) {
        error_log("Status inválido: " . $input['status']);
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Status inválido. Use: planejamento, andamento ou finalizado."]);
        exit;
    }

    try {
        $sql = "UPDATE planejamento SET status = :status WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['status' => $input['status'], 'id' => $id]);

        if ($stmt->rowCount() > 0) {
            error_log("Status atualizado com sucesso para ID: $id, Status: " . $input['status']);
            echo json_encode(["success" => true]);
        } else {
            error_log("Nenhuma linha afetada ao atualizar status para ID: $id");
            echo json_encode(["success" => false, "message" => "Nenhuma oportunidade encontrada para o ID fornecido."]);
        }
    } catch (PDOException $e) {
        error_log("Erro ao atualizar status: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erro ao atualizar status: " . $e->getMessage()]);
    }
} elseif (isset($input['planejamento_id']) && isset($input['setor']) && isset($input['etapa_nome']) && isset($input['etapa_concluida'])) {
    // Atualização de etapas/macroetapas
    $planejamento_id = (int)$input['planejamento_id'];
    $setor = $conn->quote($input['setor']);
    $nome_macroetapa = $conn->quote($input['nome_macroetapa'] ?? '');
    $responsavel = $conn->quote($input['responsavel'] ?? '');
    $etapa_nome = $conn->quote($input['etapa_nome']);
    $etapa_concluida = $input['etapa_concluida'];
    $data_conclusao = isset($input['data_conclusao']) ? $conn->quote($input['data_conclusao']) : null;

    // Validar etapa_concluida
    if (!in_array($etapa_concluida, ['sim', 'nao'])) {
        error_log("Valor inválido para etapa_concluida: " . $etapa_concluida);
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Valor inválido para etapa_concluida. Use: sim ou nao."]);
        exit;
    }

    try {
        // Verificar se a etapa já existe
        $check_sql = "SELECT id FROM macroetapas WHERE planejamento_id = :planejamento_id AND etapa_nome = :etapa_nome";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->execute(['planejamento_id' => $planejamento_id, 'etapa_nome' => $input['etapa_nome']]);
        $exists = $check_stmt->fetch();

        if ($exists) {
            // Atualizar etapa existente
            $sql = "UPDATE macroetapas SET etapa_concluida = :etapa_concluida, data_conclusao = :data_conclusao WHERE planejamento_id = :planejamento_id AND etapa_nome = :etapa_nome";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'etapa_concluida' => $etapa_concluida,
                'data_conclusao' => $data_conclusao,
                'planejamento_id' => $planejamento_id,
                'etapa_nome' => $input['etapa_nome']
            ]);
        } else {
            // Inserir nova etapa
            $sql = "INSERT INTO macroetapas (planejamento_id, setor, nome_macroetapa, responsavel, etapa_nome, etapa_concluida, data_conclusao) 
                    VALUES (:planejamento_id, :setor, :nome_macroetapa, :responsavel, :etapa_nome, :etapa_concluida, :data_conclusao)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'planejamento_id' => $planejamento_id,
                'setor' => $input['setor'],
                'nome_macroetapa' => $input['nome_macroetapa'] ?? '',
                'responsavel' => $input['responsavel'] ?? '',
                'etapa_nome' => $input['etapa_nome'],
                'etapa_concluida' => $etapa_concluida,
                'data_conclusao' => $data_conclusao
            ]);
        }

        if ($stmt->rowCount() > 0) {
            error_log("Etapa atualizada/inserida com sucesso para planejamento_id: $planejamento_id, Etapa: " . $input['etapa_nome']);
            echo json_encode(["success" => true]);
        } else {
            error_log("Nenhuma linha afetada para planejamento_id: $planejamento_id, Etapa: " . $input['etapa_nome']);
            echo json_encode(["success" => false, "message" => "Nenhuma alteração realizada."]);
        }
    } catch (PDOException $e) {
        error_log("Erro ao processar etapa: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Erro ao processar etapa: " . $e->getMessage()]);
    }
} else {
    error_log("Dados incompletos para requisição: " . print_r($input, true));
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Dados incompletos para a requisição."]);
}

$conn = null; // Fecha a conexão
?>