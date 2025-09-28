<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

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
        $viagem = $input['viagem'] ?? 1;
        $data = $input['data'] ?? '';
        $tipo_viagem = $input['tipo_viagem'] ?? '';
        $subida_id = $input['subida_id'] ?? null;

        if (empty($bonde) || empty($maquinista) || empty($agente) || empty($data) || empty($tipo_viagem)) {
            echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos.']);
            exit;
        }

        if ($tipo_viagem === 'ida' && (empty($saida) || empty($retorno))) {
            echo json_encode(['success' => false, 'message' => 'Saída e destino são obrigatórios para viagens de ida.']);
            exit;
        }

        $sql = "INSERT INTO viagens (bonde, saida, retorno, maquinista, agente, hora, pagantes, gratuidade, moradores, grat_pcd_idoso, passageiros, tipo_viagem, data, subida_id, created_at)
                VALUES (:bonde, :saida, :retorno, :maquinista, :agente, :hora, :pagantes, :gratuidade, :moradores, :grat_pcd_idoso, :passageiros, :tipo_viagem, :data, :subida_id, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':bonde', $bonde);
        $stmt->bindParam(':saida', $saida);
        $stmt->bindParam(':retorno', $retorno);
        $stmt->bindParam(':maquinista', $maquinista);
        $stmt->bindParam(':agente', $agente);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':pagantes', $pagantes, PDO::PARAM_INT);
        $stmt->bindParam(':gratuidade', $gratuidade, PDO::PARAM_INT);
        $stmt->bindParam(':moradores', $moradores, PDO::PARAM_INT);
        $stmt->bindParam(':grat_pcd_idoso', $grat_pcd_idoso, PDO::PARAM_INT);
        $stmt->bindParam(':passageiros', $passageiros, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_viagem', $tipo_viagem);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':subida_id', $subida_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Viagem adicionada com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao adicionar a viagem.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    }
} catch (PDOException $e) {
    error_log("Erro ao salvar viagem: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
}
?>