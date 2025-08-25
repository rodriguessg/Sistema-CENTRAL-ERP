<?php
header('Content-Type: application/json');

try {
    $host = 'localhost';
    $dbname = 'gm_sicbd';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!isset($data['modelo']) || !isset($data['capacidade']) || !isset($data['ano_fabricacao']) || !isset($data['descricao'])) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
            exit();
        }

        $modelo = $data['modelo'];
        $capacidade = $data['capacidade'];
        $ano_fabricacao = $data['ano_fabricacao'];
        $descricao = $data['descricao'];

        $stmt = $pdo->prepare("INSERT INTO bondes (modelo, capacidade, ano_fabricacao, descricao, ativo) VALUES (:modelo, :capacidade, :ano_fabricacao, :descricao, 0)");
        $stmt->execute([
            ':modelo' => $modelo,
            ':capacidade' => $capacidade,
            ':ano_fabricacao' => $ano_fabricacao,
            ':descricao' => $descricao
        ]);

        echo json_encode(['success' => true, 'message' => 'Bonde adicionado com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao adicionar bonde: ' . $e->getMessage()]);
}
exit();
?>