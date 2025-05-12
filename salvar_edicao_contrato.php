<?php
// Evitar qualquer saída antes do JSON
ob_start();
header('Content-Type: application/json');

// Ativar exibição de erros para depuração (remover em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados: ' . $e->getMessage()]);
    exit;
}

// Função para validar dados
function validateData($data) {
    $errors = [];
    if (empty($data['titulo'])) $errors[] = 'Título é obrigatório.';
    if (empty($data['descricao'])) $errors[] = 'Descrição é obrigatória.';
    if (empty($data['validade']) || !strtotime($data['validade'])) $errors[] = 'Validade inválida.';
    if (!in_array($data['situacao'], ['Ativo', 'Inativo', 'Encerrado'])) $errors[] = 'Situação inválida.';
    
    $valores_aditivos = [];
    for ($i = 1; $i <= 5; $i++) {
        $key = "valor_aditivo$i";
        if (!empty($data[$key])) {
            $valor = floatval($data[$key]);
            if ($valor < 0) $errors[] = "Valor aditivo $i deve ser maior ou igual a zero.";
            $valores_aditivos[$key] = $valor;
        } else {
            $valores_aditivos[$key] = null; // Permitir nulo para colunas não preenchidas
        }
    }
    
    return ['errors' => $errors, 'valores_aditivos' => $valores_aditivos];
}

// Processar requisição
try {
    $data = $_POST;
    $validation = validateData($data);

    if (!empty($validation['errors'])) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => implode(' ', $validation['errors'])]);
        exit;
    }

    $id = !empty($data['id_contrato']) ? intval($data['id_contrato']) : null;
    $valores_aditivos = $validation['valores_aditivos'];

    if ($id) {
        // Atualizar contrato
        $stmt = $pdo->prepare("
            UPDATE gestao_contratos 
            SET titulo = ?, descricao = ?, validade = ?, situacao = ?, 
                valor_aditivo1 = ?, valor_aditivo2 = ?, valor_aditivo3 = ?, 
                valor_aditivo4 = ?, valor_aditivo5 = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['titulo'],
            $data['descricao'],
            $data['validade'],
            $data['situacao'],
            $valores_aditivos['valor_aditivo1'],
            $valores_aditivos['valor_aditivo2'],
            $valores_aditivos['valor_aditivo3'],
            $valores_aditivos['valor_aditivo4'],
            $valores_aditivos['valor_aditivo5'],
            $id
        ]);
    } else {
        // Inserir novo contrato
        $stmt = $pdo->prepare("
            INSERT INTO gestao_contratos (
                titulo, descricao, validade, situacao, 
                valor_aditivo1, valor_aditivo2, valor_aditivo3, 
                valor_aditivo4, valor_aditivo5
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['titulo'],
            $data['descricao'],
            $data['validade'],
            $data['situacao'],
            $valores_aditivos['valor_aditivo1'],
            $valores_aditivos['valor_aditivo2'],
            $valores_aditivos['valor_aditivo3'],
            $valores_aditivos['valor_aditivo4'],
            $valores_aditivos['valor_aditivo5']
        ]);
        $id = $pdo->lastInsertId();
    }

    // Retornar dados atualizados
    $stmt = $pdo->prepare("SELECT * FROM gestao_contratos WHERE id = ?");
    $stmt->execute([$id]);
    $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

    // Combinar valores aditivos em um array
    $contrato['valores_aditivos'] = array_filter([
        $contrato['valor_aditivo1'],
        $contrato['valor_aditivo2'],
        $contrato['valor_aditivo3'],
        $contrato['valor_aditivo4'],
        $contrato['valor_aditivo5']
    ], function($value) {
        return !is_null($value);
    });

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Contrato salvo com sucesso!',
        'data' => $contrato
    ]);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar contrato: ' . $e->getMessage()]);
}
?>