<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit;
}

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recebe os dados do formulário
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $validade = $_POST['validade'];
    $situacao = $_POST['situacao'];

    // Atualiza os dados do processo no banco de dados
    $sql = "UPDATE gestao_contratos SET titulo = :titulo, descricao = :descricao, validade = :validade, situacao = :situacao WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':validade', $validade);
    $stmt->bindParam(':situacao', $situacao);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Retorna uma resposta de sucesso
    echo "Processo atualizado com sucesso!";
}
?>
