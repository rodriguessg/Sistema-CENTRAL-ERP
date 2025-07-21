<?php
// Desativar exibição de erros para evitar HTML em respostas JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Definir o cabeçalho JSON
header('Content-Type: application/json; charset=utf-8');

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro de conexão com o banco: " . $e->getMessage());
    exit(json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]));
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->query('SELECT id, titulo_oportunidade, setor, valor_estimado, prazo, status, descricao, project_plan, created_at FROM planejamento');
            $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($opportunities as &$opp) {
                $opp['project_plan'] = json_decode($opp['project_plan'], true) ?: [];
                $opp['title'] = $opp['titulo_oportunidade'];
                $opp['sector'] = $opp['setor'];
                $opp['value'] = $opp['valor_estimado'] !== null ? floatval($opp['valor_estimado']) : 0;
                $opp['deadline'] = $opp['prazo'];
                $opp['projectPlan'] = $opp['project_plan'];
                unset($opp['titulo_oportunidade'], $opp['setor'], $opp['valor_estimado'], $opp['prazo'], $opp['project_plan'], $opp['created_at']);
            }
            
            exit(json_encode(['success' => true, 'opportunities' => $opportunities]));
        } catch (PDOException $e) {
            error_log("Erro ao buscar oportunidades: " . $e->getMessage());
            exit(json_encode(['success' => false, 'message' => 'Erro ao buscar oportunidades: ' . $e->getMessage()]));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        error_log("Dados recebidos no POST: " . print_r($data, true));
        
        if (!$data || empty($data['title']) || empty($data['sector'])) {
            error_log("Validação falhou: title ou sector ausentes ou vazios");
            exit(json_encode(['success' => false, 'message' => 'Título e setor são obrigatórios']));
        }

        $id = isset($data['id']) && !empty($data['id']) ? $data['id'] : null;
        $titulo_oportunidade = $data['title'];
        $setor = $data['sector'];
        $valor_estimado = isset($data['value']) && is_numeric($data['value']) ? floatval($data['value']) : 0;
        error_log("Valor estimado antes de salvar: " . $valor_estimado);
        $prazo = isset($data['deadline']) && !empty($data['deadline']) ? $data['deadline'] : null;
        $status = isset($data['status']) && !empty($data['status']) ? $data['status'] : 'planejamento';
        $descricao = isset($data['description']) ? $data['description'] : '';
        $project_plan = isset($data['projectPlan']) ? json_encode($data['projectPlan']) : json_encode([]);
        $created_at = isset($data['createdAt']) && !empty($data['createdAt']) ? date('Y-m-d H:i:s', strtotime($data['createdAt'])) : date('Y-m-d H:i:s');

        try {
            if ($id) {
                $stmt = $pdo->prepare('UPDATE planejamento SET titulo_oportunidade = ?, setor = ?, valor_estimado = ?, prazo = ?, status = ?, descricao = ?, project_plan = ?, created_at = ? WHERE id = ?');
                $stmt->execute([$titulo_oportunidade, $setor, $valor_estimado, $prazo, $status, $descricao, $project_plan, $created_at, $id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO planejamento (titulo_oportunidade, setor, valor_estimado, prazo, status, descricao, project_plan, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$titulo_oportunidade, $setor, $valor_estimado, $prazo, $status, $descricao, $project_plan, $created_at]);
                $id = $pdo->lastInsertId();
            }
            exit(json_encode(['success' => true, 'id' => $id]));
        } catch (PDOException $e) {
            error_log("Erro ao salvar oportunidade: " . $e->getMessage());
            exit(json_encode(['success' => false, 'message' => 'Erro ao salvar oportunidade: ' . $e->getMessage()]));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id']) || empty($data['id'])) {
            exit(json_encode(['success' => false, 'message' => 'ID não fornecido']));
        }

        $id = $data['id'];
        $titulo_oportunidade = isset($data['title']) ? $data['title'] : '';
        $setor = isset($data['sector']) ? $data['sector'] : '';
        $valor_estimado = isset($data['value']) && is_numeric($data['value']) ? floatval($data['value']) : 0;
        $prazo = isset($data['deadline']) && !empty($data['deadline']) ? $data['deadline'] : null;
        $status = isset($data['status']) && !empty($data['status']) ? $data['status'] : 'planejamento';
        $descricao = isset($data['description']) ? $data['description'] : '';
        $project_plan = isset($data['projectPlan']) ? json_encode($data['projectPlan']) : json_encode([]);
        $created_at = isset($data['createdAt']) && !empty($data['createdAt']) ? date('Y-m-d H:i:s', strtotime($data['createdAt'])) : date('Y-m-d H:i:s');

        try {
            $stmt = $pdo->prepare('UPDATE planejamento SET titulo_oportunidade = ?, setor = ?, valor_estimado = ?, prazo = ?, status = ?, descricao = ?, project_plan = ?, created_at = ? WHERE id = ?');
            $stmt->execute([$titulo_oportunidade, $setor, $valor_estimado, $prazo, $status, $descricao, $project_plan, $created_at, $id]);
            exit(json_encode(['success' => true]));
        } catch (PDOException $e) {
            error_log("Erro ao atualizar oportunidade: " . $e->getMessage());
            exit(json_encode(['success' => false, 'message' => 'Erro ao atualizar oportunidade: ' . $e->getMessage()]));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['id']) || empty($data['id'])) {
            exit(json_encode(['success' => false, 'message' => 'ID não fornecido']));
        }

        $id = $data['id'];
        try {
            $stmt = $pdo->prepare('DELETE FROM planejamento WHERE id = ?');
            $stmt->execute([$id]);
            exit(json_encode(['success' => true]));
        } catch (PDOException $e) {
            error_log("Erro ao excluir oportunidade: " . $e->getMessage());
            exit(json_encode(['success' => false, 'message' => 'Erro ao excluir oportunidade: ' . $e->getMessage()]));
        }
        break;

    default:
        exit(json_encode(['success' => false, 'message' => 'Método não suportado']));
        break;
}
?>