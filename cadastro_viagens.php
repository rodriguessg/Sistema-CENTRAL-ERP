<?php
// Database configuration
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    // Establish database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the request is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize form data
        $bonde = (string)($_POST['bonde'] ?? '');
        $saida = (string)($_POST['saida'] ?? '');
        $retorno = (string)($_POST['retorno'] ?? '');
        $maquinistas = (string)($_POST['maquinistas'] ?? '');
        $agentes = (string)($_POST['agentes'] ?? '');
        $hora = (string)($_POST['hora'] ?? '');
        $pagantes = filter_input(INPUT_POST, 'pagantes', FILTER_VALIDATE_INT) ?? 0;
        $moradores = filter_input(INPUT_POST, 'moradores', FILTER_VALIDATE_INT) ?? 0;
        $grat_pcd_idoso = filter_input(INPUT_POST, 'grat_pcd_idoso', FILTER_VALIDATE_INT) ?? 0;
        $gratuidade = filter_input(INPUT_POST, 'gratuidade', FILTER_VALIDATE_INT) ?? 0;
        $passageiros = filter_input(INPUT_POST, 'passageiros', FILTER_VALIDATE_INT) ?? 0;
        $viagem = filter_input(INPUT_POST, 'viagem', FILTER_VALIDATE_INT) ?? 1;
        $data = (string)($_POST['data'] ?? '');

        // Validate required fields
        if (!$bonde || !$saida || !$maquinistas || !$agentes || !$hora || !$data || $pagantes === false || $moradores === false || $grat_pcd_idoso === false || $viagem === false) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Todos os campos obrigatórios devem ser preenchidos corretamente.']);
            exit;
        }

        // Validate passenger counts
        $total_passageiros = $pagantes + $moradores + $grat_pcd_idoso;
        if ($total_passageiros > 32) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'O total de passageiros excede a capacidade máxima de 32.']);
            exit;
        }

        // Determine tipo_viagem
        $tipo_viagem = $retorno ? 'Ida e Volta' : 'Ida';

        // Prepare SQL query to insert data into viagens table
        $sql = "INSERT INTO viagens (bonde, saida, retorno, maquinista, agente, hora, pagantes, gratuidade, moradores, passageiros, tipo_viagem, data)
                VALUES (:bonde, :saida, :retorno, :maquinista, :agente, :hora, :pagantes, :gratuidade, :moradores, :passageiros, :tipo_viagem, :data)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':bonde' => $bonde,
            ':saida' => $saida,
            ':retorno' => $retorno ?: null,
            ':maquinista' => $maquinistas,
            ':agente' => $agentes,
            ':hora' => $hora,
            ':pagantes' => $pagantes,
            ':gratuidade' => $grat_pcd_idoso,
            ':moradores' => $moradores,
            ':passageiros' => $total_passageiros,
            ':tipo_viagem' => $tipo_viagem,
            ':data' => $data
        ]);

        // Return success response
        echo json_encode(['success' => true, 'message' => 'Viagem cadastrada com sucesso!']);
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    }
} catch (PDOException $e) {
    // Log error and return error response
    error_log("Erro ao cadastrar viagem: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar com o banco de dados.']);
} catch (Exception $e) {
    // Handle other errors
    error_log("Erro geral: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro inesperado.']);
}
?>