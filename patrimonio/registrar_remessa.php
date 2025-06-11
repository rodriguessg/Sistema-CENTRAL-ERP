<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

$host = 'localhost';
$dbname = 'gm_sicbd';
$username_db = 'root';
$password = '';

try {
    // Criação da conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Definir modo de erro para exceção
} catch (PDOException $e) {
    // Em caso de erro na conexão, loga o erro e exibe uma mensagem amigável
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Consulte o administrador.");
}

// Captura os dados enviados pelo formulário
$patrimonio_id = $_POST['patrimonio_id'];
$destino = $_POST['destino'];
$responsavel = $_POST['responsavel'];
$data_transferencia = date('Y-m-d'); // Data atual

// Verifica se todos os campos estão preenchidos
if (empty($patrimonio_id) || empty($destino) || empty($responsavel)) {
    die("Erro: Todos os campos devem ser preenchidos.");
}

// Verifica se o patrimonio_id existe na tabela patrimonio
$query_check = "SELECT id FROM patrimonio WHERE id = :patrimonio_id";
$stmt_check = $pdo->prepare($query_check);
$stmt_check->bindParam(':patrimonio_id', $patrimonio_id, PDO::PARAM_INT);
$stmt_check->execute();

if ($stmt_check->rowCount() == 0) {
    die("Erro: O patrimônio com ID $patrimonio_id não existe na tabela patrimonio.");
}

// Insere os dados na tabela transferencias com o valor 'Transferido' para tipo_operacao
$query = "INSERT INTO transferencias (patrimonio_id, destino, responsavel, data_transferencia, tipo_operacao) VALUES (:patrimonio_id, :destino, :responsavel, :data_transferencia, :tipo_operacao)";
$stmt = $pdo->prepare($query);

// Define o valor fixo para tipo_operacao como "Transferido"
$tipo_operacao = "Transferido";

// Bind dos parâmetros
$stmt->bindParam(':patrimonio_id', $patrimonio_id, PDO::PARAM_INT);
$stmt->bindParam(':destino', $destino, PDO::PARAM_STR);
$stmt->bindParam(':responsavel', $responsavel, PDO::PARAM_STR);
$stmt->bindParam(':data_transferencia', $data_transferencia, PDO::PARAM_STR);
$stmt->bindParam(':tipo_operacao', $tipo_operacao, PDO::PARAM_STR);

// Executa a consulta
if ($stmt->execute()) {
    // Atualiza a coluna tipo_operacao na tabela patrimonio
    $query_update = "UPDATE patrimonio SET situacao = :tipo_operacao WHERE id = :patrimonio_id";
    $stmt_update = $pdo->prepare($query_update);

    // Bind dos parâmetros
    $stmt_update->bindParam(':tipo_operacao', $tipo_operacao, PDO::PARAM_STR);
    $stmt_update->bindParam(':patrimonio_id', $patrimonio_id, PDO::PARAM_INT);

    // Executa a consulta de atualização
    if ($stmt_update->execute()) {
        // Registrar no log de eventos após a atualização na tabela patrimonio
        $tipo_operacao_log = 'Transferência de Patrimônio';
        $query_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (:matricula, :tipo_operacao, NOW())";
        $stmt_log = $pdo->prepare($query_log);

        // Bind dos parâmetros para o log
        $stmt_log->bindParam(':matricula', $username, PDO::PARAM_STR); // Registrar a matrícula do usuário
        $stmt_log->bindParam(':tipo_operacao', $tipo_operacao_log, PDO::PARAM_STR);

        // Executa o log
        if ($stmt_log->execute()) {
            // Redireciona para a página de sucesso
            header('Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=transferencia&pagina=/Sistema-CENTRAL-ERP/homepatrimonio.php');
            exit();
        } else {
            echo "Erro ao registrar no log de eventos: " . $stmt_log->errorInfo()[2];
        }
    } else {
        echo "Erro ao atualizar a coluna tipo_operacao na tabela patrimonio: " . $stmt_update->errorInfo()[2];
    }
} else {
    echo "Erro ao registrar a transferência: " . $stmt->errorInfo()[2];
}

// Fechar as conexões
$stmt_check = null;
$stmt = null;
$stmt_update = null;
$stmt_log = null;
$pdo = null;
?>
