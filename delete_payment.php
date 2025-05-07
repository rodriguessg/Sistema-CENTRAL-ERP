<?php
// Conectar ao banco de dados
$dsn = 'mysql:host=localhost;dbname=gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifique se o 'id' foi passado
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];

        // Deletar o pagamento com base no 'id'
        $sql = "DELETE FROM pagamentos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Retorna sucesso
        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "ID não fornecido"]);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Erro ao conectar ao banco de dados: " . $e->getMessage()]);
}
?>