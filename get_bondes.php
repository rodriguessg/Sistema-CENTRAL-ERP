<?php
include 'banco.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'data' => []];

try {
    $stmt = $con->prepare("SELECT id, modelo, capacidade FROM bondes ORDER BY modelo ASC");
    $stmt->execute();
    $result = $stmt->get_result();

    $bondes = [];
    while ($row = $result->fetch_assoc()) {
        $bondes[] = $row;
    }

    $response['success'] = true;
    $response['data'] = $bondes;

} catch (Exception $e) {
    $response['message'] = 'Erro ao buscar bondes: ' . $e->getMessage();
}

$con->close();
echo json_encode($response);
?>