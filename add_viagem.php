<?php
header('Content-Type: application/json');

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro na conexão: ' . $e->getMessage()]);
    exit;
}

// Processar dados do formulário
$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bonde_id = isset($_POST['bonde_id']) ? (int)$_POST['bonde_id'] : null;
    $subindo_id = isset($_POST['subindo_id']) ? (int)$_POST['subindo_id'] : null;
    $retorno_id = isset($_POST['retorno_id']) ? (int)$_POST['retorno_id'] : null;
    $maquinista_id = isset($_POST['maquinista_id']) ? (int)$_POST['maquinista_id'] : null;
    $agente_id = isset($_POST['agente_id']) ? (int)$_POST['agente_id'] : null;
    $pagantes = isset($_POST['pagantes']) ? (int)$_POST['pagantes'] : 0;
    $moradores = isset($_POST['moradores']) ? (int)$_POST['moradores'] : 0;
    $gratuidade = isset($_POST['gratuidade']) ? (int)$_POST['gratuidade'] : 0;
    $viagem = isset($_POST['viagem']) ? (int)$_POST['viagem'] : 1;
    $data = isset($_POST['data']) ? date('Y-m-d', strtotime(str_replace('/', '-', $_POST['data']))) : date('Y-m-d');
    $hora = date('Y-m-d H:i:s');

    // Validar capacidade máxima
    $total_passageiros = $pagantes + $moradores + $gratuidade;
    if ($total_passageiros > 32) {
        $response['message'] = "Capacidade máxima de 32 passageiros excedida!";
    } elseif ($bonde_id && $subindo_id && $maquinista_id && $agente_id) {
        try {
            $sql = "INSERT INTO viagens (bonde_id, subindo_id, retorno_id, maquinista_id, agente_id, hora, pagantes, moradores, gratuidade, passageiros, viagem, data)
                    VALUES (:bonde_id, :subindo_id, :retorno_id, :maquinista_id, :agente_id, :hora, :pagantes, :moradores, :gratuidade, :passageiros, :viagem, :data)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':bonde_id' => $bonde_id,
                ':subindo_id' => $subindo_id,
                ':retorno_id' => $retorno_id,
                ':maquinista_id' => $maquinista_id,
                ':agente_id' => $agente_id,
                ':hora' => $hora,
                ':pagantes' => $pagantes,
                ':moradores' => $moradores,
                ':gratuidade' => $gratuidade,
                ':passageiros' => $total_passageiros,
                ':viagem' => $viagem,
                ':data' => $data
            ]);
            $response['success'] = true;
            $response['message'] = "Viagem adicionada com sucesso!";
            $response['id'] = $conn->lastInsertId(); // Retorna o ID da última inserção
        } catch(PDOException $e) {
            $response['message'] = "Erro ao adicionar viagem: " . $e->getMessage();
        }
    } else {
        $response['message'] = "Preencha todos os campos obrigatórios!";
    }
} else {
    $response['message'] = "Método de requisição inválido!";
}

echo json_encode($response);
?>