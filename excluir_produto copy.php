<?php
// Configuração de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o ID foi enviado via POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // 1. Buscar o 'material_id' (produto), 'natureza' e 'tipo' (entrada/saída) da tabela 'transicao'
    $selectQuery = "SELECT material_id, tipo FROM transicao WHERE id = ?";
    $stmtSelect = $conn->prepare($selectQuery);
    if (!$stmtSelect) {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $conn->error]);
        exit;
    }

    $stmtSelect->bind_param("i", $id);
    $stmtSelect->execute();
    $stmtSelect->store_result();

    if ($stmtSelect->num_rows > 0) {
        $stmtSelect->bind_result($material_id, $tipo);
        $stmtSelect->fetch();

        // 2. Obter a 'natureza' associada ao 'material_id' (produto) na tabela 'produtos'
        $selectNaturezaQuery = "SELECT natureza FROM produtos WHERE id = ?";
        $stmtSelectNatureza = $conn->prepare($selectNaturezaQuery);
        if (!$stmtSelectNatureza) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta para natureza: ' . $conn->error]);
            exit;
        }

        $stmtSelectNatureza->bind_param("i", $material_id);
        $stmtSelectNatureza->execute();
        $stmtSelectNatureza->store_result();

        if ($stmtSelectNatureza->num_rows > 0) {
            $stmtSelectNatureza->bind_result($natureza);
            $stmtSelectNatureza->fetch();

            // 3. Atualizar a tabela 'fechamento' baseado no tipo da transação (entrada ou saída)
            if ($tipo == 'Saida') {
                // Se for saída, subtrai da coluna 'total_saida' e ajusta o 'saldo_atual'
                $updateFechamentoQuery = "UPDATE fechamento SET total_saida = total_saida - (SELECT quantidade FROM transicao WHERE id = ?), saldo_atual = saldo_atual + (SELECT quantidade FROM transicao WHERE id = ?) WHERE natureza = ?";
            } else if ($tipo == 'Entrada') {
                // Se for entrada, subtrai da coluna 'total_entrada' e ajusta o 'saldo_atual'
                $updateFechamentoQuery = "UPDATE fechamento SET total_entrada = total_entrada - (SELECT quantidade FROM transicao WHERE id = ?), saldo_atual = saldo_atual - (SELECT quantidade FROM transicao WHERE id = ?) WHERE natureza = ?";
            }

            $stmtUpdateFechamento = $conn->prepare($updateFechamentoQuery);
            if (!$stmtUpdateFechamento) {
                echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta para atualização no fechamento: ' . $conn->error]);
                exit;
            }

            $stmtUpdateFechamento->bind_param("iis", $id, $id, $natureza);
            if ($stmtUpdateFechamento->execute()) {
                // Exclusão bem-sucedida da tabela 'fechamento'
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar entrada ou saída no fechamento']);
                $stmtSelect->close();
                $conn->close();
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Produto não encontrado na tabela produtos']);
            $stmtSelect->close();
            $conn->close();
            exit;
        }

        // 4. Excluir o produto da tabela 'transicao' com base no 'id'
        $sql = "DELETE FROM transicao WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta para exclusão na transição: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param("i", $id);

        // Executar a consulta de exclusão
        if ($stmt->execute()) {
            // Se a exclusão foi bem-sucedida, retornar um sucesso
            echo json_encode(['success' => true]);
        } else {
            // Caso ocorra algum erro
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir o produto da transição: ' . $conn->error]);
        }

        // Fechar os statements
        $stmt->close();
        $stmtSelect->close();
        $stmtSelectNatureza->close();
        $stmtUpdateFechamento->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Produto não encontrado na transição']);
        $stmtSelect->close();
        $conn->close();
        exit;
    }
}

// Fechar a conexão
$conn->close();
?>
