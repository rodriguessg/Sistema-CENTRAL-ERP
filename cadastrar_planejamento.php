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
    $pdo->beginTransaction(); // Iniciar transação
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

            // Sincronizar macroetapas com project_plan
            $projectPlanData = json_decode($project_plan, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Erro ao decodificar project_plan (ID $id): " . json_last_error_msg() . ". Conteúdo: " . $project_plan);
                $pdo->rollBack();
                exit(json_encode(['success' => false, 'message' => 'Erro ao processar project_plan: JSON inválido']));
            }
            error_log("Project_plan decodificado para ID $id: " . print_r($projectPlanData, true));

            $macroetapasExistentes = $pdo->prepare("SELECT nome_macroetapa, responsavel, etapa_nome, etapa_concluida, data_conclusao FROM macroetapas WHERE planejamento_id = ?");
            $macroetapasExistentes->execute([$id]);
            $existentes = $macroetapasExistentes->fetchAll(PDO::FETCH_ASSOC);
            $nomesExistentes = array_column($existentes, 'nome_macroetapa');
            error_log("Macroetapas existentes para ID $id: " . print_r($existentes, true));

            $macroetapasPlan = $projectPlanData; // Assumindo que projectPlanData é um array de macroetapas
            error_log("Macroetapas de project_plan para ID $id: " . print_r($macroetapasPlan, true));

            // Inserir ou atualizar novas macroetapas
            foreach ($macroetapasPlan as $macro) {
                $nomeMacroetapa = $macro['name'] ?? $macro['nome_macroetapa'] ?? 'Sem Nome';
                $index = array_search($nomeMacroetapa, $nomesExistentes);
                if ($index === false) {
                    try {
                        $stmtInsert = $pdo->prepare("INSERT INTO macroetapas (planejamento_id, setor, nome_macroetapa, responsavel, etapa_nome, etapa_concluida, data_conclusao) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmtInsert->execute([
                            $id,
                            $setor,
                            $nomeMacroetapa,
                            $macro['responsible'] ?? 'N/A',
                            $macro['etapas'][0]['name'] ?? 'Sem Etapa',
                            'não',
                            null
                        ]);
                        error_log("Nova etapa inserida em macroetapas para planejamento ID $id: " . $nomeMacroetapa);
                    } catch (PDOException $e) {
                        error_log("Erro ao inserir etapa em macroetapas (ID $id): " . $e->getMessage());
                    }
                } else {
                    // Atualizar se necessário (ex.: responsavel ou etapa_concluida mudou)
                    $existente = $existentes[$index];
                    $novosDados = [
                        'responsavel' => $macro['responsible'] ?? 'N/A',
                        'etapa_nome' => $macro['etapas'][0]['name'] ?? 'Sem Etapa',
                        'etapa_concluida' => $macro['etapas'][0]['completed'] ? 'sim' : 'não',
                        'data_conclusao' => $macro['etapas'][0]['completed'] ? date('Y-m-d H:i:s') : null
                    ];
                    $atualizar = false;
                    foreach ($novosDados as $campo => $valor) {
                        if ($existente[$campo] != $valor) {
                            $atualizar = true;
                            break;
                        }
                    }
                    if ($atualizar) {
                        try {
                            $stmtUpdate = $pdo->prepare("UPDATE macroetapas SET responsavel = ?, etapa_nome = ?, etapa_concluida = ?, data_conclusao = ? WHERE planejamento_id = ? AND nome_macroetapa = ?");
                            $stmtUpdate->execute([
                                $novosDados['responsavel'],
                                $novosDados['etapa_nome'],
                                $novosDados['etapa_concluida'],
                                $novosDados['data_conclusao'],
                                $id,
                                $nomeMacroetapa
                            ]);
                            error_log("Macroetapa atualizada em macroetapas para planejamento ID $id: " . $nomeMacroetapa);
                        } catch (PDOException $e) {
                            error_log("Erro ao atualizar etapa em macroetapas (ID $id): " . $e->getMessage());
                        }
                    }
                }
            }

            // Remover macroetapas que não estão mais no project_plan
            $nomesPlan = array_map(function($macro) { return $macro['name'] ?? $macro['nome_macroetapa'] ?? 'Sem Nome'; }, $macroetapasPlan);
            $remover = array_diff($nomesExistentes, $nomesPlan);
            if (!empty($remover)) {
                try {
                    $stmtDelete = $pdo->prepare("DELETE FROM macroetapas WHERE planejamento_id = ? AND nome_macroetapa = ?");
                    foreach ($remover as $nome) {
                        $stmtDelete->execute([$id, $nome]);
                        error_log("Macroetapa removida de macroetapas para planejamento ID $id: " . $nome);
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao remover etapa em macroetapas (ID $id): " . $e->getMessage());
                }
            }

            $pdo->commit(); // Confirmar transação
            exit(json_encode(['success' => true, 'id' => $id]));
        } catch (PDOException $e) {
            $pdo->rollBack(); // Reverter transação em caso de erro
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

            // Sincronizar macroetapas com project_plan
            $projectPlanData = json_decode($project_plan, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Erro ao decodificar project_plan (ID $id): " . json_last_error_msg() . ". Conteúdo: " . $project_plan);
                $pdo->rollBack();
                exit(json_encode(['success' => false, 'message' => 'Erro ao processar project_plan: JSON inválido']));
            }
            error_log("Project_plan decodificado para ID $id: " . print_r($projectPlanData, true));

            $macroetapasExistentes = $pdo->prepare("SELECT nome_macroetapa, responsavel, etapa_nome, etapa_concluida, data_conclusao FROM macroetapas WHERE planejamento_id = ?");
            $macroetapasExistentes->execute([$id]);
            $existentes = $macroetapasExistentes->fetchAll(PDO::FETCH_ASSOC);
            $nomesExistentes = array_column($existentes, 'nome_macroetapa');
            error_log("Macroetapas existentes para ID $id: " . print_r($existentes, true));

            $macroetapasPlan = $projectPlanData; // Assumindo que projectPlanData é um array de macroetapas
            error_log("Macroetapas de project_plan para ID $id: " . print_r($macroetapasPlan, true));

            // Inserir ou atualizar novas macroetapas
            foreach ($macroetapasPlan as $macro) {
                $nomeMacroetapa = $macro['name'] ?? $macro['nome_macroetapa'] ?? 'Sem Nome';
                $index = array_search($nomeMacroetapa, $nomesExistentes);
                if ($index === false) {
                    try {
                        $stmtInsert = $pdo->prepare("INSERT INTO macroetapas (planejamento_id, setor, nome_macroetapa, responsavel, etapa_nome, etapa_concluida, data_conclusao) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmtInsert->execute([
                            $id,
                            $setor,
                            $nomeMacroetapa,
                            $macro['responsible'] ?? 'N/A',
                            $macro['etapas'][0]['name'] ?? 'Sem Etapa',
                            'não',
                            null
                        ]);
                        error_log("Nova etapa inserida em macroetapas para planejamento ID $id: " . $nomeMacroetapa);
                    } catch (PDOException $e) {
                        error_log("Erro ao inserir etapa em macroetapas (ID $id): " . $e->getMessage());
                    }
                } else {
                    // Atualizar se necessário
                    $existente = $existentes[$index];
                    $novosDados = [
                        'responsavel' => $macro['responsible'] ?? 'N/A',
                        'etapa_nome' => $macro['etapas'][0]['name'] ?? 'Sem Etapa',
                        'etapa_concluida' => $macro['etapas'][0]['completed'] ? 'sim' : 'não',
                        'data_conclusao' => $macro['etapas'][0]['completed'] ? date('Y-m-d H:i:s') : null
                    ];
                    $atualizar = false;
                    foreach ($novosDados as $campo => $valor) {
                        if ($existente[$campo] != $valor) {
                            $atualizar = true;
                            break;
                        }
                    }
                    if ($atualizar) {
                        try {
                            $stmtUpdate = $pdo->prepare("UPDATE macroetapas SET responsavel = ?, etapa_nome = ?, etapa_concluida = ?, data_conclusao = ? WHERE planejamento_id = ? AND nome_macroetapa = ?");
                            $stmtUpdate->execute([
                                $novosDados['responsavel'],
                                $novosDados['etapa_nome'],
                                $novosDados['etapa_concluida'],
                                $novosDados['data_conclusao'],
                                $id,
                                $nomeMacroetapa
                            ]);
                            error_log("Macroetapa atualizada em macroetapas para planejamento ID $id: " . $nomeMacroetapa);
                        } catch (PDOException $e) {
                            error_log("Erro ao atualizar etapa em macroetapas (ID $id): " . $e->getMessage());
                        }
                    }
                }
            }

            // Remover macroetapas que não estão mais no project_plan
            $nomesPlan = array_map(function($macro) { return $macro['name'] ?? $macro['nome_macroetapa'] ?? 'Sem Nome'; }, $macroetapasPlan);
            $remover = array_diff($nomesExistentes, $nomesPlan);
            if (!empty($remover)) {
                try {
                    $stmtDelete = $pdo->prepare("DELETE FROM macroetapas WHERE planejamento_id = ? AND nome_macroetapa = ?");
                    foreach ($remover as $nome) {
                        $stmtDelete->execute([$id, $nome]);
                        error_log("Macroetapa removida de macroetapas para planejamento ID $id: " . $nome);
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao remover etapa em macroetapas (ID $id): " . $e->getMessage());
                }
            }

            $pdo->commit(); // Confirmar transação
            exit(json_encode(['success' => true]));
        } catch (PDOException $e) {
            $pdo->rollBack(); // Reverter transação em caso de erro
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
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('DELETE FROM macroetapas WHERE planejamento_id = ?');
            $stmt->execute([$id]);
            $stmt = $pdo->prepare('DELETE FROM planejamento WHERE id = ?');
            $stmt->execute([$id]);
            $pdo->commit();
            exit(json_encode(['success' => true]));
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Erro ao excluir oportunidade: " . $e->getMessage());
            exit(json_encode(['success' => false, 'message' => 'Erro ao excluir oportunidade: ' . $e->getMessage()]));
        }
        break;

    default:
        exit(json_encode(['success' => false, 'message' => 'Método não suportado']));
        break;
}

$pdo = null;
?>