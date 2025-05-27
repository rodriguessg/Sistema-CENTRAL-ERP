<?php
include 'banco.php'; // Conexão com o banco de dados

// Verifica se a requisição foi enviada via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os valores do formulário
    $nome = isset($_POST['material-nome']) ? $con->real_escape_string($_POST['material-nome']) : '';
    $codigo = isset($_POST['material-codigo']) ? $con->real_escape_string($_POST['material-codigo']) : '';
    $quantidade = isset($_POST['material-quantidade']) ? (int) $_POST['material-quantidade'] : 0;
    $data = date('Y-m-d'); // Data atual

    // Verifica se todos os campos obrigatórios estão preenchidos
    if (empty($nome) || empty($codigo) || $quantidade <= 0) {
        header("Location: mensagem.php?mensagem=Campos obrigatórios não preenchidos&pagina=retirar_materialestoque.php");
        exit;
    }

    // Consulta para verificar se o produto existe no estoque
    $query = "SELECT id, produto, descricao, quantidade, custo, natureza, preco_medio FROM produtos WHERE descricao = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Produto encontrado
        $produto = $result->fetch_assoc();
        $material_id = $produto['id'];
        $nome = $produto['produto']; // Nome do produto
        $descricao = $produto['descricao'];
        $quantidadeAtual = (int) $produto['quantidade'];
        $custo = (float) $produto['custo']; // Captura o custo do produto
        $preco_medio = (float) $produto['preco_medio']; // Captura o preço médio do produto
        $natureza = $produto['natureza']; // Captura a natureza do produto

        // Verifica se o estoque tem exatamente 5 unidades, caso sim, gera uma notificação
        if ($quantidadeAtual == 5) {
            session_start(); // Inicia a sessão para pegar os dados do usuário
            $username = $_SESSION['username']; // Nome do usuário da sessão
            $setor = $_SESSION['setor']; // Setor do usuário da sessão
            $mensagem = "O produto '$nome' atingiu o limite mínimo de 5 unidades. Precisa comprar mais.";

            // Inserir a notificação no banco de dados
            $notificacaoQuery = "INSERT INTO notificacoes (username, setor, mensagem, situacao) 
                                 VALUES (?, ?, ?, 'nao lida')";
            $notificacaoStmt = $con->prepare($notificacaoQuery);
            $notificacaoStmt->bind_param('sss', $username, $setor, $mensagem);
            if ($notificacaoStmt->execute()) {
                $notificacaoStmt->close();
            } else {
                echo "Erro ao inserir notificação: " . $con->error;
                exit;
            }

            // Mensagem informando que a retirada não pode ser feita, pois atingiu o limite mínimo
            header("Location: mensagem.php?mensagem=Estoque mínimo atingido (5 unidades). Não é possível retirar&pagina=homeestoque.php");
            exit;
        }

        // Verifica se o estoque é suficiente para a quantidade a ser retirada
        if ($quantidadeAtual >= $quantidade) {
            // Atualiza o estoque ou remove o produto se a quantidade for igual à disponível
            if ($quantidadeAtual === $quantidade) {
                $deleteQuery = "DELETE FROM produtos WHERE descricao = ?";
                $deleteStmt = $con->prepare($deleteQuery);
                $deleteStmt->bind_param('s', $codigo);
                $deleteStmt->execute();
                $deleteStmt->close();
            } else {
                // Atualiza a quantidade do produto no estoque após a retirada
                $updateQuery = "UPDATE produtos SET quantidade = quantidade - ?, tipo_operacao = 'retirado' WHERE descricao = ?";
                $updateStmt = $con->prepare($updateQuery);
                $updateStmt->bind_param('is', $quantidade, $codigo);
                $updateStmt->execute();
                $updateStmt->close();
            }

            // Registrar a transação de retirada na tabela 'transicao'
            $query_transacao = "INSERT INTO transicao (material_id, quantidade, data, tipo) 
                                VALUES (?, ?, ?, 'Saida')";
            $transacaoStmt = $con->prepare($query_transacao);
            $transacaoStmt->bind_param('iis', $material_id, $quantidade, $data);
            if ($transacaoStmt->execute()) {
                $transacaoStmt->close();
            } else {
                echo "Erro ao registrar a transação: " . $con->error;
                exit;
            }

            // Verifica e atualiza a tabela de fechamento com o total de saída e saldo atual
            // Ajuste aqui para usar 'natureza' para identificar o material
            $stmt_fe = $con->prepare("SELECT saldo_atual, total_saida FROM fechamento WHERE natureza = ?");
            $stmt_fe->bind_param('s', $natureza);
            $stmt_fe->execute();
            $stmt_fe->store_result();

            if ($stmt_fe->num_rows > 0) {
                // Já existe um fechamento para essa natureza, atualiza os valores
                $stmt_fe->bind_result($saldo_atual, $total_saida_existente);
                $stmt_fe->fetch();

                // Calcula o novo total de saída (adiciona a saída do produto)
                $novo_total_saida = $total_saida_existente + ($quantidade * $preco_medio);

                // Calcula o novo saldo atual (diminui o valor da saída)
                $novo_saldo_atual = $saldo_atual - ($quantidade * $preco_medio);  // Decrease saldo atual

                // Atualiza o fechamento
                $sql_update_fe = "UPDATE fechamento SET total_saida = ?, saldo_atual = ? WHERE natureza = ?";
                $stmt_update_fe = $con->prepare($sql_update_fe);
                $stmt_update_fe->bind_param("dds", $novo_total_saida, $novo_saldo_atual, $natureza);
                if ($stmt_update_fe->execute()) {
                    echo "Fechamento atualizado com sucesso!";
                } else {
                    echo "Erro ao atualizar fechamento: " . $stmt_update_fe->error;
                }
                $stmt_update_fe->close();
            } else {
                // Não existe um fechamento para essa natureza, insere um novo registro
                $total_saida = $quantidade * $preco_medio;  // Valor da saída calculado pela quantidade * preco_medio
                $sql_insert_fe = "INSERT INTO fechamento (natureza, total_saida, saldo_atual, custo, data_fechamento) 
                                  VALUES (?, ?, ?, ?, NOW())";
                $stmt_insert_fe = $con->prepare($sql_insert_fe);
                $stmt_insert_fe->bind_param("sdds", $natureza, $total_saida, $total_saida, $preco_medio);
                if ($stmt_insert_fe->execute()) {
                    echo "Novo fechamento inserido com sucesso!";
                } else {
                    echo "Erro ao inserir fechamento: " . $stmt_insert_fe->error;
                }
                $stmt_insert_fe->close();
            }

            // Redireciona com mensagem de sucesso
            header("Location: mensagem.php?mensagem=produto_retirado&pagina=homeestoque.php");
            exit;
        } else {
            // Estoque insuficiente
            header("Location: mensagem.php?mensagem=estoque_insuficiente&pagina=homeestoque.php");
            exit;
        }
    } else {
        // Produto não encontrado
        header("Location: mensagem.php?mensagem=produto_nao_encontrado&pagina=homeestoque.php");
        exit;
    }

    $stmt->close();
} else {
    // Caso a requisição não seja POST
    header("Location: mensagem.php?mensagem=Requisição inválida.&pagina=homeestoque.php");
    exit;
}

$con->close();
?>
