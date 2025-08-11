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
    $pdo->beginTransaction();
} catch (PDOException $e) {
    error_log("Erro de conexão com o banco: " . $e->getMessage());
    exit(json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]));
}

// Mapeamento de setores do frontend para números de setor
$sector_to_number_map = [
    'operacionalizacao_bonde' => 1,
    'bondes' => 2,
    'ferrovia' => 3,
    'teleferico' => 4,
    'ti' => 5,
    'capacitacao' => 6,
    'patrimonio' => 7,
    'pca' => 8,
    'gestao_pessoas' => 9,
    'solucoes_tecnologicas' => 10
];

// Função para gerar o próximo pe_code com base no setor (sempre 3 níveis: X.Y.Z)
function generatePeCode($pdo, $setor, $sector_to_number_map, $parent_pe_code = null) {
    if (!array_key_exists($setor, $sector_to_number_map)) {
        throw new Exception("Setor inválido: $setor");
    }

    $sector_number = $sector_to_number_map[$setor];
    $prefix = "PE $sector_number";

    if ($parent_pe_code) {
        // Se parent_pe_code for fornecido, usa-lo como base para subnível
        $stmt = $pdo->prepare("SELECT pe_code FROM planejamento WHERE pe_code LIKE ? ORDER BY pe_code DESC LIMIT 1");
        $stmt->execute(["$parent_pe_code.%"]);
        $last_code = $stmt->fetchColumn();

        if ($last_code) {
            $parts = explode('.', $last_code);
            $last_number = (int) end($parts);
            $new_code = $parent_pe_code . '.' . ($last_number + 1);
        } else {
            $new_code = $parent_pe_code . '.1';
        }
    } else {
        // Sem parent: gerar código de 3 níveis sequencial no setor
        $stmt = $pdo->prepare("SELECT pe_code FROM planejamento WHERE setor = ? ORDER BY pe_code DESC LIMIT 1");
        $stmt->execute([$setor]);
        $last_code = $stmt->fetchColumn();

        if ($last_code) {
            // Parse the last code (e.g., PE 1.1.9 -> [1,1,9])
            $parts = explode(' ', $last_code);
            $code_parts = explode('.', $parts[1]);
            $x = (int) $code_parts[0];
            $y = (int) $code_parts[1];
            $z = (int) $code_parts[2];

            // Increment Z
            $z += 1;
            if ($z > 9) {
                $z = 1;
                $y += 1;
                if ($y > 9) {
                    $y = 1;
                    // Since X is fixed, we can allow Y to go beyond 9, but according to user, after 1.9.9 -> 1.10.1
                    // So no carry to X, just Y=10, Z=1
                }
            }

            $new_code = "PE $x.$y.$z";
        } else {
            // Primeiro no setor: PE X.1.1
            $new_code = "PE $sector_number.1.1";
        }
    }

    return $new_code;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->query('SELECT id, titulo_oportunidade, setor, valor_estimado, prazo, status, descricao, project_plan, created_at, pe_code FROM planejamento');
            $opportunities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($opportunities as &$opp) {
                $decoded_plan = [];
                if (isset($opp['project_plan'])) {
                    $decoded_temp = json_decode($opp['project_plan'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $decoded_plan = $decoded_temp;
                    } else {
                        error_log("JSON decode error for ID {$opp['id']}: " . json_last_error_msg() . ". Data: " . $opp['project_plan']);
                    }
                }
                $opp['project_plan'] = $decoded_plan;
                $opp['title'] = $opp['titulo_oportunidade'];
                $opp['sector'] = $opp['setor'];
                $opp['value'] = 0;
                if (isset($opp['valor_estimado']) && $opp['valor_estimado'] !== null) {
                    $opp['value'] = floatval($opp['valor_estimado']);
                }
                $opp['deadline'] = $opp['prazo'];
                $opp['projectPlan'] = $opp['project_plan'];
                $opp['peCode'] = $opp['pe_code'];
                unset($opp['titulo_oportunidade'], $opp['setor'], $opp['valor_estimado'], $opp['prazo'], $opp['project_plan'], $opp['created_at'], $opp['pe_code']);
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

        $id = null;
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
        }
        $titulo_oportunidade = $data['title'];
        $setor = $data['sector'];
        $valor_estimado = 0;
        if (isset($data['value']) && is_numeric($data['value'])) {
            $valor_estimado = floatval($data['value']);
        }
        $prazo = null;
        if (isset($data['deadline']) && !empty($data['deadline'])) {
            $prazo = $data['deadline'];
        }
        $status = 'planejamento';
        if (isset($data['status']) && !empty($data['status'])) {
            $status = $data['status'];
        }
        $descricao = '';
        if (isset($data['description'])) {
            $descricao = $data['description'];
        }
        $project_plan = json_encode([]);
        if (isset($data['projectPlan'])) {
            $project_plan = json_encode($data['projectPlan']);
        }
        $created_at = date('Y-m-d H:i:s');
        if (isset($data['createdAt']) && !empty($data['createdAt'])) {
            $created_at = date('Y-m-d H:i:s', strtotime($data['createdAt']));
        }
        $parent_pe_code = null;
        if (isset($data['parentPeCode']) && !empty($data['parentPeCode'])) {
            $parent_pe_code = $data['parentPeCode'];
        }

        try {
            $pe_code = generatePeCode($pdo, $setor, $sector_to_number_map, $parent_pe_code);

            if ($id) {
                $stmt = $pdo->prepare('UPDATE planejamento SET titulo_oportunidade = ?, setor = ?, valor_estimado = ?, prazo = ?, status = ?, descricao = ?, project_plan = ?, created_at = ?, pe_code = ? WHERE id = ?');
                $stmt->execute([$titulo_oportunidade, $setor, $valor_estimado, $prazo, $status, $descricao, $project_plan, $created_at, $pe_code, $id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO planejamento (titulo_oportunidade, setor, valor_estimado, prazo, status, descricao, project_plan, created_at, pe_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$titulo_oportunidade, $setor, $valor_estimado, $prazo, $status, $descricao, $project_plan, $created_at, $pe_code]);
                $id = $pdo->lastInsertId();
            }

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
            $nomesExistentes = [];
            if (!empty($existentes)) {
                $nomesExistentes = array_column($existentes, 'nome_macroetapa');
            }
            error_log("Macroetapas existentes para ID $id: " . print_r($existentes, true));

            $macroetapasPlan = $projectPlanData;
            error_log("Macroetapas de project_plan para ID $id: " . print_r($macroetapasPlan, true));

            foreach ($macroetapasPlan as $macro) {
                $nomeMacroetapa = 'Sem Nome';
                if (isset($macro['name'])) {
                    $nomeMacroetapa = $macro['name'];
                } elseif (isset($macro['nome_macroetapa'])) {
                    $nomeMacroetapa = $macro['nome_macroetapa'];
                }
                $index = array_search($nomeMacroetapa, $nomesExistentes);
                if ($index === false) {
                    try {
                        $stmtInsert = $pdo->prepare("INSERT INTO macroetapas (planejamento_id, setor, nome_macroetapa, responsavel, etapa_nome, etapa_concluida, data_conclusao) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $responsavel = 'N/A';
                        if (isset($macro['responsible'])) {
                            $responsavel = $macro['responsible'];
                        }
                        $etapa_nome = 'Sem Etapa';
                        if (isset($macro['etapas']) && isset($macro['etapas'][0]) && isset($macro['etapas'][0]['name'])) {
                            $etapa_nome = $macro['etapas'][0]['name'];
                        }
                        $stmtInsert->execute([
                            $id,
                            $setor,
                            $nomeMacroetapa,
                            $responsavel,
                            $etapa_nome,
                            'não',
                            null
                        ]);
                        error_log("Nova etapa inserida em macroetapas para planejamento ID $id: " . $nomeMacroetapa);
                    } catch (PDOException $e) {
                        error_log("Erro ao inserir etapa em macroetapas (ID $id): " . $e->getMessage());
                    }
                } else {
                    $existente = $existentes[$index];
                    $novosDados = [
                        'responsavel' => 'N/A',
                        'etapa_nome' => 'Sem Etapa',
                        'etapa_concluida' => 'não',
                        'data_conclusao' => null
                    ];
                    if (isset($macro['responsible'])) {
                        $novosDados['responsavel'] = $macro['responsible'];
                    }
                    if (isset($macro['etapas']) && isset($macro['etapas'][0]) && isset($macro['etapas'][0]['name'])) {
                        $novosDados['etapa_nome'] = $macro['etapas'][0]['name'];
                    }
                    if (isset($macro['etapas']) && isset($macro['etapas'][0]) && isset($macro['etapas'][0]['completed']) && $macro['etapas'][0]['completed']) {
                        $novosDados['etapa_concluida'] = 'sim';
                        $novosDados['data_conclusao'] = date('Y-m-d H:i:s');
                    }
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

            $nomesPlan = [];
            foreach ($macroetapasPlan as $macro) {
                $nome = 'Sem Nome';
                if (isset($macro['name'])) {
                    $nome = $macro['name'];
                } elseif (isset($macro['nome_macroetapa'])) {
                    $nome = $macro['nome_macroetapa'];
                }
                $nomesPlan[] = $nome;
            }
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

            $pdo->commit();
            exit(json_encode(['success' => true, 'id' => $id, 'peCode' => $pe_code]));
        } catch (Exception $e) {
            $pdo->rollBack();
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