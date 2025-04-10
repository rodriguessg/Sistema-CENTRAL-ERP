<?php
// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se a requisição foi enviada via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os valores do formulário
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    // Verifica se o ID foi enviado via POST
    if ($id > 0) {
        try {
            // 1. Buscar o 'material_id' (produto), 'tipo' (entrada/saída) e 'quantidade' da tabela 'transicao'
            $selectQuery = "SELECT material_id, tipo, quantidade FROM transicao WHERE id = ?";
            $stmtSelect = $conn->prepare($selectQuery);
            if (!$stmtSelect) {
                throw new Exception('Erro ao preparar a consulta: ' . $conn->error);
            }

            $stmtSelect->bind_param("i", $id);
            $stmtSelect->execute();
            $stmtSelect->store_result();

            if ($stmtSelect->num_rows > 0) {
                $stmtSelect->bind_result($material_id, $tipo, $quantidade);
                $stmtSelect->fetch();

                // 2. Obter a 'natureza' associada ao 'material_id' (produto) na tabela 'produtos'
                $selectNaturezaQuery = "SELECT natureza FROM produtos WHERE id = ?";
                $stmtSelectNatureza = $conn->prepare($selectNaturezaQuery);
                if (!$stmtSelectNatureza) {
                    throw new Exception('Erro ao preparar a consulta para natureza: ' . $conn->error);
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
                        $updateFechamentoQuery = "UPDATE fechamento SET total_saida = total_saida - ?, saldo_atual = saldo_atual - ? WHERE natureza = ?";
                    } else if ($tipo == 'Entrada') {
                        // Se for entrada, subtrai da coluna 'total_entrada' e ajusta o 'saldo_atual'
                        $updateFechamentoQuery = "UPDATE fechamento SET total_entrada = total_entrada - ?, saldo_atual = saldo_atual + ? WHERE natureza = ?";
                    }

                    $stmtUpdateFechamento = $conn->prepare($updateFechamentoQuery);
                    if (!$stmtUpdateFechamento) {
                        throw new Exception('Erro ao preparar a consulta para atualização no fechamento: ' . $conn->error);
                    }

                    // Atualiza a tabela fechamento
                    $stmtUpdateFechamento->bind_param("dss", $quantidade, $quantidade, $natureza);
                    if (!$stmtUpdateFechamento->execute()) {
                        throw new Exception('Erro ao atualizar a tabela fechamento');
                    }
                } else {
                    throw new Exception('Produto não encontrado na tabela produtos');
                }

                // 4. Excluir o produto da tabela 'transicao' com base no 'id'
                $deleteQuery = "DELETE FROM transicao WHERE id = ?";
                $stmtDelete = $conn->prepare($deleteQuery);
                if (!$stmtDelete) {
                    throw new Exception('Erro ao preparar a consulta para exclusão na transição: ' . $conn->error);
                }

                $stmtDelete->bind_param("i", $id);

                // Executar a consulta de exclusão
                if ($stmtDelete->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Produto excluído e fechamento atualizado com sucesso!']);
                } else {
                    throw new Exception('Erro ao excluir o produto da transição: ' . $conn->error);
                }

                // Fechar os statements
                $stmtDelete->close();
                $stmtSelect->close();
                $stmtSelectNatureza->close();
                $stmtUpdateFechamento->close();
            } else {
                throw new Exception('Produto não encontrado na transição');
            }
        } catch (Exception $e) {
            // Captura qualquer erro e envia de volta ao cliente
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID inválido']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}

// Fechar a conexão
$conn->close();
?>
