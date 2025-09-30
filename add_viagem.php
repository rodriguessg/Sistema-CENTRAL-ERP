<?php
session_start(); // Inicia a sessão para obter o usuário logado

header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root'; // Credencial do banco de dados
$password = ''; // Credencial do banco de dados

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica se o usuário está logado
        if (!isset($_SESSION['username'])) {
            echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
            exit;
        }

        $logged_user = $_SESSION['username']; // Obtém o username do usuário logado

        $input = json_decode(file_get_contents('php://input'), true);

        // Atribuição dos valores com verificação de null
        $bonde = $input['bonde'] ?? '';
        $saida = $input['saida'] ?? '';
        $retorno = $input['retorno'] ?? '';
        $maquinista = $input['maquinista'] ?? '';
        $agente = $input['agente'] ?? '';
        $hora = $input['hora'] ?? '';
        $pagantes = $input['pagantes'] ?? 0;
        $moradores = $input['moradores'] ?? 0;
        $grat_pcd_idoso = $input['grat_pcd_idoso'] ?? 0;
        $gratuidade = $input['gratuidade'] ?? 0;
        $passageiros = $input['passageiros'] ?? 0;
        $tipo_viagem = $input['tipo_viagem'] ?? '';
        $data = $input['data'] ?? '';
        $subida_id = $input['subida_id'] ?? null;

        // Validação dos campos obrigatórios
        if (empty($bonde) || empty($maquinista) || empty($agente) || empty($data) || empty($tipo_viagem)) {
            echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos.']);
            exit;
        }

        // Validação específica para tipo_viagem = 'ida'
        if ($tipo_viagem === 'ida' && (empty($saida) || empty($retorno))) {
            echo json_encode(['success' => false, 'message' => 'Saída e retorno são obrigatórios para viagens de ida.']);
            exit;
        }

        // Inserção no log_eventos (usando PDO e o username do usuário logado)
        $stmt_log = $pdo->prepare("INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (:matricula, :tipo_operacao, NOW())");
        $tipo_operacao = "viagem cadastrada";
        $stmt_log->execute([
            ':matricula' => $logged_user, // Usa o username do usuário logado
            ':tipo_operacao' => $tipo_operacao
        ]);

        // Inserção na tabela viagens
        $sql = "INSERT INTO viagens (bonde, saida, retorno, maquinista, agente, hora, pagantes, gratuidade, moradores, grat_pcd_idoso, passageiros, tipo_viagem, data, subida_id, created_at)
                VALUES (:bonde, :saida, :retorno, :maquinista, :agente, :hora, :pagantes, :gratuidade, :moradores, :grat_pcd_idoso, :passageiros, :tipo_viagem, :data, :subida_id, NOW())";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':bonde' => $bonde,
            ':saida' => $saida,
            ':retorno' => $retorno,
            ':maquinista' => $maquinista,
            ':agente' => $agente,
            ':hora' => $hora,
            ':pagantes' => (int)$pagantes,
            ':gratuidade' => (int)$gratuidade,
            ':moradores' => (int)$moradores,
            ':grat_pcd_idoso' => (int)$grat_pcd_idoso,
            ':passageiros' => (int)$passageiros,
            ':tipo_viagem' => $tipo_viagem,
            ':data' => $data,
            ':subida_id' => $subida_id ? (int)$subida_id : null
        ]);

        echo json_encode(['success' => true, 'message' => 'Viagem adicionada com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    }
} catch (PDOException $e) {
    error_log("Erro ao salvar viagem: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
}
?>