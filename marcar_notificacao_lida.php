<?php
// Habilitar exibição de erros para facilitar a depuração (opcional)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Defina o tipo de conteúdo como JSON
header('Content-Type: application/json');

// Conectar ao banco de dados (substitua com suas credenciais)
$host = 'localhost';  // Alterar conforme necessário
$dbname = 'gm_sicbd';  // Alterar conforme necessário
$username = 'root';  // Alterar conforme necessário
$password = '';  // Alterar conforme necessário

try {
    // Conectar ao banco de dados usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Caso ocorra um erro na conexão, exibe um erro e encerra
    echo json_encode(['success' => false, 'error' => 'Erro ao conectar ao banco de dados']);
    exit;
}

// Obtendo o corpo da requisição
$data = json_decode(file_get_contents('php://input'), true);

// Verificando se o JSON foi decodificado corretamente
if ($data === null) {
    echo json_encode(['success' => false, 'error' => 'Erro ao decodificar JSON']);
    exit;
}

// Verifique se o status foi enviado
if (!isset($data['status']) || empty($data['status'])) {
    echo json_encode(['success' => false, 'error' => 'Status não fornecido']);
    exit;
}

$status = $data['status'];

// Lógica para marcar a notificação com o status fornecido (exemplo simples usando SQL)
try {
    // Atualizar a notificação no banco de dados (ajuste conforme seu esquema de banco)
    $sql = "UPDATE notificacoes SET lida = 1 WHERE status = :status";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);

    // Executar a atualização
    $stmt->execute();

    // Verificar se a atualização foi bem-sucedida
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Notificação com o status fornecido não encontrada ou já marcada como lida']);
    }
} catch (PDOException $e) {
    // Caso ocorra um erro no banco de dados, exibe o erro
    echo json_encode(['success' => false, 'error' => 'Erro ao atualizar notificação: ' . $e->getMessage()]);
}
?>