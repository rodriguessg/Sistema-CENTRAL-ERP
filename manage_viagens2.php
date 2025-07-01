<?php
include 'banco.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'data' => [], 'total_records' => 0];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $bonde_id = $_POST['bonde_id'] ?? null;
        $origem_id = $_POST['origem'] ?? null; // Using text for simplicity, map to ID if needed
        $destino_id = $_POST['retorno'] ?? null; // Using text for simplicity
        $maquinista_id = $_POST['maquinista'] ?? null; // Using text for simplicity
        $agente_id = $_POST['agente'] ?? null; // Using text for simplicity
        $hora = $_POST['hora'] ?? null;
        $pagantes = $_POST['pagantes'] ?? 0;
        $moradores = $_POST['moradores'] ?? 0;
        $gratuidade = $_POST['gratuidade'] ?? 0; // This is the calculated total gratuidade
        $tipo_viagem = $_POST['tipo_viagem'] ?? 'subida';
        $data_viagem = $_POST['data'] ?? null;
        $viagem_numero = $_POST['viagem'] ?? 1;

        // Basic validation
        if (!$bonde_id || !$origem_id || !$destino_id || !$maquinista_id || !$agente_id || !$data_viagem) {
            $response['message'] = 'Preencha todos os campos obrigatórios.';
            echo json_encode($response);
            exit;
        }

        try {
            $stmt = $con->prepare("INSERT INTO viagens (bonde_id, origem_id, destino_id, maquinista_id, agente_id, hora, pagantes, moradores, gratuidade, tipo_viagem, data_viagem, viagem) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssiiisss", $bonde_id, $origem_id, $destino_id, $maquinista_id, $agente_id, $hora, $pagantes, $moradores, $gratuidade, $tipo_viagem, $data_viagem, $viagem_numero);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Viagem adicionada com sucesso!';
            } else {
                $response['message'] = 'Erro ao adicionar viagem: ' . $stmt->error;
            }
            $stmt->close();
        } catch (Exception $e) {
            $response['message'] = 'Erro: ' . $e->getMessage();
        }
        break;

    case 'update':
        $id = $_POST['id'] ?? null;
        $bonde_id = $_POST['bonde_id'] ?? null;
        $origem_id = $_POST['origem'] ?? null;
        $destino_id = $_POST['retorno'] ?? null;
        $maquinista_id = $_POST['maquinista'] ?? null;
        $agente_id = $_POST['agente'] ?? null;
        $hora = $_POST['hora'] ?? null;
        $pagantes = $_POST['pagantes'] ?? 0;
        $moradores = $_POST['moradores'] ?? 0;
        $gratuidade = $_POST['gratuidade'] ?? 0;
        $tipo_viagem = $_POST['tipo_viagem'] ?? 'subida'; // Keep original type
        $data_viagem = $_POST['data'] ?? null;
        $viagem_numero = $_POST['viagem'] ?? 1;

        if (!$id || !$bonde_id || !$origem_id || !$destino_id || !$maquinista_id || !$agente_id || !$data_viagem) {
            $response['message'] = 'Preencha todos os campos obrigatórios para alterar.';
            echo json_encode($response);
            exit;
        }

        try {
            $stmt = $con->prepare("UPDATE viagens SET bonde_id = ?, origem_id = ?, destino_id = ?, maquinista_id = ?, agente_id = ?, hora = ?, pagantes = ?, moradores = ?, gratuidade = ?, tipo_viagem = ?, data_viagem = ?, viagem = ? WHERE id = ?");
            $stmt->bind_param("isssssiiisssi", $bonde_id, $origem_id, $destino_id, $maquinista_id, $agente_id, $hora, $pagantes, $moradores, $gratuidade, $tipo_viagem, $data_viagem, $viagem_numero, $id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Viagem atualizada com sucesso!';
            } else {
                $response['message'] = 'Erro ao atualizar viagem: ' . $stmt->error;
            }
            $stmt->close();
        } catch (Exception $e) {
            $response['message'] = 'Erro: ' . $e->getMessage();
        }
        break;

    case 'delete':
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $response['message'] = 'ID da viagem não fornecido.';
            echo json_encode($response);
            exit;
        }

        try {
            $stmt = $con->prepare("DELETE FROM viagens WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Viagem excluída com sucesso!';
            } else {
                $response['message'] = 'Erro ao excluir viagem: ' . $stmt->error;
            }
            $stmt->close();
        } catch (Exception $e) {
            $response['message'] = 'Erro: ' . $e->getMessage();
        }
        break;

    case 'fetch':
    default:
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10; // Default items per page
        $offset = ($page - 1) * $limit;
        $filter_id = $_GET['filter_id'] ?? '';

        $sql_count = "SELECT COUNT(*) AS total FROM viagens";
        $sql_select = "SELECT v.*, b.modelo AS bonde_modelo, b.capacidade FROM viagens v JOIN bondes b ON v.bonde_id = b.id";
        $params_types = "";
        $params = [];

        if (!empty($filter_id)) {
            $sql_count .= " WHERE id LIKE ?";
            $sql_select .= " WHERE v.id LIKE ?";
            $params_types .= "s";
            $params[] = "%" . $filter_id . "%";
        }

        $sql_select .= " ORDER BY data_viagem DESC, hora DESC LIMIT ? OFFSET ?";
        $params_types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        try {
            // Get total records for pagination
            $stmt_count = $con->prepare($sql_count);
            if (!empty($filter_id)) {
                $stmt_count->bind_param($params_types[0], $params[0]); // Only bind the filter param for count
            }
            $stmt_count->execute();
            $result_count = $stmt_count->get_result();
            $total_records = $result_count->fetch_assoc()['total'];
            $stmt_count->close();

            // Fetch paginated data
            $stmt_select = $con->prepare($sql_select);
            if (!empty($params)) {
                $stmt_select->bind_param($params_types, ...$params);
            }
            $stmt_select->execute();
            $result_select = $stmt_select->get_result();

            $viagens = [];
            while ($row = $result_select->fetch_assoc()) {
                $viagens[] = $row;
            }

            $response['success'] = true;
            $response['data'] = $viagens;
            $response['total_records'] = $total_records;

            $stmt_select->close();
        } catch (Exception $e) {
            $response['message'] = 'Erro ao buscar viagens: ' . $e->getMessage();
        }
        break;
}

$con->close();
echo json_encode($response);
?>