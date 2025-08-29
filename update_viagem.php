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

        $id = $input['id'] ?? null;
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

        if (!$id || empty($bonde) || empty($maquinista) || empty($agente) || empty($data) || empty($tipo_viagem)) {
            echo json_encode(['success' => false, 'message' => 'Campos obrigatórios não preenchidos.']);
            exit;
        }

        if ($tipo_viagem === 'ida' && (empty($saida) || empty($retorno))) {
            echo json_encode(['success' => false, 'message' => 'Saída e destino são obrigatórios para viagens de ida.']);
            exit;
        }

        $sql = "UPDATE viagens SET bonde = :bonde, saida = :saida, retorno = :retorno, maquinista = :maquinista, agente = :agente, 
                hora = :hora, pagantes = :pagantes, gratuidade = :gratuidade, moradores = :moradores, passageiros = :passageiros, 
                tipo_viagem = :tipo_viagem, data = :data, subida_id = :subida_id WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':bonde', $bonde);
        $stmt->bindParam(':saida', $saida);
        $stmt->bindParam(':retorno', $retorno);
        $stmt->bindParam(':maquinista', $maquinista);
        $stmt->bindParam(':agente', $agente);
        $stmt->bindParam(':hora', $hora);
        $stmt->bindParam(':pagantes', $pagantes, PDO::PARAM_INT);
        $stmt->bindParam(':gratuidade', $gratuidade, PDO::PARAM_INT);
        $stmt->bindParam(':moradores', $moradores, PDO::PARAM_INT);
        $stmt->bindParam(':passageiros', $passageiros, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_viagem', $tipo_viagem);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':subida_id', $subida_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Viagem atualizada com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar a viagem.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
    }
} catch (PDOException $e) {
    error_log("Erro ao atualizar viagem: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
}
?>