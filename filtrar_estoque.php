<?php
// // Configurações do cabeçalho para JSON
// header('Content-Type: application/json');

// // Conexão com o banco de dados
// $conn = new mysqli('localhost', 'root', '', 'gm_sicbd');

// // Verifica a conexão
// if ($conn->connect_error) {
//     echo json_encode(['error' => 'Erro na conexão com o banco de dados']);
//     exit;
// }

// // Lê os dados recebidos no body da requisição
// $input = json_decode(file_get_contents('php://input'), true);
// $filtro = $input['filtro'] ?? '';

// // Monta a consulta SQL
// $sql = "SELECT * FROM produtos";
// if (!empty($filtro)) {
//     $sql .= " WHERE produto LIKE '%" . $conn->real_escape_string($filtro) . "%'";
// }

// $result = $conn->query($sql);

// // Prepara os dados para retorno
// $rows = [];
// $total = 0;

// if ($result && $result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         $rows[] = [
//             'id' => $row['id'],
//             'produto' => $row['produto'],
//             'classificacao' => $row['classificacao'],
//             'localizacao' => $row['localizacao'],
//             'quantidade' => $row['quantidade']
//         ];
//         $total += (int)$row['quantidade']; // Soma a quantidade
//     }
// }

// // Retorna os dados em formato JSON
// echo json_encode([
//     'rows' => $rows,
//     'total' => $total
// ]);

// // Fecha a conexão com o banco de dados
// $conn->close();
?>
