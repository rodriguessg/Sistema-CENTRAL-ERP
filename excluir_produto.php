<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // 1. Buscar material_id, tipo e quantidade da transição
    $selectQuery = "SELECT material_id, tipo, quantidade FROM transicao WHERE id = ?";
    $stmtSelect = $conn->prepare($selectQuery);
    if (!$stmtSelect) {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta: ' . $conn->error]);
        exit;
    }

    $stmtSelect->bind_param("i", $id);
    $stmtSelect->execute();
    $stmtSelect->store_result();

    if ($stmtSelect->num_rows > 0) {
        $stmtSelect->bind_result($material_id, $tipo, $quantidade_transacao);
        $stmtSelect->fetch();

        // 2. Obter natureza e preco_medio do produto
        $selectProduto = "SELECT natureza, preco_medio FROM produtos WHERE id = ?";
        $stmtProduto = $conn->prepare($selectProduto);
        if (!$stmtProduto) {
            echo json_encode(['success' => false, 'message' => 'Erro ao preparar consulta produto: ' . $conn->error]);
            exit;
        }

        $stmtProduto->bind_param("i", $material_id);
        $stmtProduto->execute();
        $stmtProduto->store_result();

        if ($stmtProduto->num_rows > 0) {
            $stmtProduto->bind_result($natureza, $preco_medio);
            $stmtProduto->fetch();

            // 3. Calcular valor total = quantidade * preco_medio
            $valor_total = $quantidade_transacao * $preco_medio;

            // 4. Atualizar fechamento com base no tipo
            if ($tipo == 'Saida') {
                $updateFechamento = "UPDATE fechamento SET total_saida = total_saida - ?, saldo_atual = saldo_atual + ? WHERE natureza = ?";
            } else if ($tipo == 'Entrada') {
                $updateFechamento = "UPDATE fechamento SET total_entrada = total_entrada - ?, saldo_atual = saldo_atual - ? WHERE natureza = ?";
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de transação inválido']);
                exit;
            }

            $stmtUpdate = $conn->prepare($updateFechamento);
            if (!$stmtUpdate) {
                echo json_encode(['success' => false, 'message' => 'Erro ao preparar update no fechamento: ' . $conn->error]);
                exit;
            }

            $stmtUpdate->bind_param("dds", $valor_total, $valor_total, $natureza);
            if (!$stmtUpdate->execute()) {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar fechamento']);
                exit;
            }

            // 5. Excluir transição
            $deleteTransicao = "DELETE FROM transicao WHERE id = ?";
            $stmtDelete = $conn->prepare($deleteTransicao);
            if (!$stmtDelete) {
                echo json_encode(['success' => false, 'message' => 'Erro ao preparar exclusão da transição']);
                exit;
            }

            $stmtDelete->bind_param("i", $id);
            if ($stmtDelete->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao excluir transição']);
            }

            $stmtDelete->close();
            $stmtUpdate->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Produto não encontrado']);
        }

        $stmtProduto->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Transação não encontrada']);
    }

    $stmtSelect->close();
}

$conn->close();
?>
