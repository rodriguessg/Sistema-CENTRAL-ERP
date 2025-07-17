<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database configuration
$host = "localhost";
$dbname = "gm_sicbd";
$username = "root";
$password = "";

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the request is a POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get JSON data from the request body
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate required fields
        if (isset($data['username']) && isset($data['setor']) && isset($data['mensagem'])) {
            $username = $data['username'];
            $setor = $data['setor'];
            $mensagem = $data['mensagem'];
            $situacao = isset($data['situacao']) ? $data['situacao'] : 'pendente';
            $data_criacao = isset($data['data_criacao']) ? $data['data_criacao'] : date('Y-m-d H:i:s');

            // Prepare and execute the SQL insert statement (fixed syntax)
            $stmt = $pdo->prepare("INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) VALUES (:username, :setor, :mensagem, :situacao, :data_criacao)");
            $stmt->execute([
                ':username' => $username,
                ':setor' => $setor,
                ':mensagem' => $mensagem,
                ':situacao' => $situacao,
                ':data_criacao' => $data_criacao
            ]);

            // Return success response
            http_response_code(201); // Created
            echo json_encode(["message" => "Notificação salva com sucesso", "id" => $pdo->lastInsertId()]);
        } else {
            // Return error if required fields are missing
            http_response_code(400); // Bad Request
            echo json_encode(["error" => "Dados incompletos. username, setor e mensagem são obrigatórios."]);
        }
    } else {
        // Return error if method is not POST
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Método não permitido. Use POST."]);
    }
} catch (PDOException $e) {
    // Log error for debugging (in production, use a log file)
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Erro ao conectar ao banco de dados: " . $e->getMessage()]);
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Erro interno no servidor. Tente novamente mais tarde."]);
}

// Close the connection
$pdo = null;
?>