<?php
// Conexão com o banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados'])); 
}

// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    die(json_encode(['success' => false, 'message' => 'Erro: Usuário não autenticado ou sessão expirada!']));
}

$username = $_SESSION['username']; // Obtém a matrícula do usuário logado

// Coleta os dados do POST
$id = $_POST['id'];
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$situacao = $_POST['situacao'];
$categoria = $_POST['categoria'];

// Prepara a query de atualização para a tabela 'patrimonio'
$sql = "UPDATE patrimonio 
        SET nome = ?, descricao = ?, valor = ?, situacao = ?, categoria = ? 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdssi", $nome, $descricao, $valor, $situacao, $categoria, $id);

// Executa a atualização
if ($stmt->execute()) {
    // Após a atualização, registra o log de eventos
    $tipo_operacao_log = 'Patrimônio ' . $nome . ' atualizado'; // Inclui o nome do patrimônio no log

    // Registrar no log de eventos
    $query_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())";
    $stmt_log = $conn->prepare($query_log);
    $stmt_log->bind_param("ss", $username, $tipo_operacao_log); // Registro da matrícula do usuário e tipo de operação

    // Executa o log
    if ($stmt_log->execute()) {
        echo json_encode(['success' => true, 'message' => 'Patrimônio atualizado e log registrado com sucesso.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao registrar o log: ' . $stmt_log->error]);
    }

    $stmt_log->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar os dados: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
