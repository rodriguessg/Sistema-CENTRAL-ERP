<?php
// Inclui a conexão
include 'banco.php';

// Verifica se a conexão foi bem-sucedida
if ($con->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $con->connect_error]));
}

// Verifica se a requisição foi enviada via GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['nome'])) {
    $nome = isset($_GET['nome']) ? $con->real_escape_string($_GET['nome']) : '';

    if (!empty($nome)) {
        // Verifica se a consulta está correta
        $query = "SELECT codigo, classificacao, natureza, localizacao FROM produtos WHERE produto LIKE ?";
        $stmt = $con->prepare($query);

        if (!$stmt) {
            die(json_encode(['success' => false, 'message' => 'Erro na preparação da consulta: ' . $con->error]));
        }

        $likeNome = "%" . $nome . "%";
        $stmt->bind_param('s', $likeNome);

        if (!$stmt->execute()) {
            die(json_encode(['success' => false, 'message' => 'Erro na execução da consulta: ' . $stmt->error]));
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $produto = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'codigo' => $produto['codigo'],
                'classificacao' => $produto['classificacao'],
                'natureza' => $produto['natureza'],
                'localizacao' => $produto['localizacao']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Material não encontrado.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Nome do material não fornecido.']);
    }

    $con->close();
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
    exit;
}
?>
