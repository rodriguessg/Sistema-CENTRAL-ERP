<?php
include 'banco.php'; // Inclua a conexão com o banco de dados

// Verifica se a requisição foi enviada via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtém os valores do formulário
    $nome = isset($_POST['material-nome']) ? $con->real_escape_string($_POST['material-nome']) : '';
    $codigo = isset($_POST['material-codigo']) ? $con->real_escape_string($_POST['material-codigo']) : '';
    $quantidade = isset($_POST['material-quantidade']) ? (int) $_POST['material-quantidade'] : 0;

    // Verifica se todos os campos obrigatórios estão preenchidos
    if (empty($nome) || empty($codigo) || $quantidade <= 0) {
        header("Location: mensagem.php?mensagem=Campos obrigatórios não preenchidos&pagina=retirar_materialestoque.php");
        exit;
    }

    // Consulta para verificar se o produto existe no estoque
    $query = "SELECT quantidade FROM produtos WHERE codigo = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Produto encontrado
        $produto = $result->fetch_assoc();
        $quantidadeAtual = (int) $produto['quantidade'];

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
                $deleteQuery = "DELETE FROM produtos WHERE codigo = ?";
                $deleteStmt = $con->prepare($deleteQuery);
                $deleteStmt->bind_param('s', $codigo);
                $deleteStmt->execute();
                $deleteStmt->close();
            } else {
                // Atualiza a quantidade do produto no estoque após a retirada
                $updateQuery = "UPDATE produtos SET quantidade = quantidade - ?, tipo_operacao = 'retirado' WHERE codigo = ?";
                $updateStmt = $con->prepare($updateQuery);
                $updateStmt->bind_param('is', $quantidade, $codigo);
                $updateStmt->execute();
                $updateStmt->close();
            }

            // Após a retirada, verifica se a quantidade do produto ficou abaixo de 5
            $queryAtualizada = "SELECT quantidade FROM produtos WHERE codigo = ?";
            $stmtAtualizada = $con->prepare($queryAtualizada);
            $stmtAtualizada->bind_param('s', $codigo);
            $stmtAtualizada->execute();
            $resultAtualizada = $stmtAtualizada->get_result();

            if ($resultAtualizada->num_rows > 0) {
                $produtoAtualizado = $resultAtualizada->fetch_assoc();
                $quantidadeRestante = (int) $produtoAtualizado['quantidade'];

                // Se a quantidade restante for inferior a 5, insere a notificação
                if ($quantidadeRestante < 5) {
                    session_start(); // Inicia a sessão para pegar os dados do usuário
                    $username = $_SESSION['username']; // Nome do usuário da sessão
                    $setor = $_SESSION['setor']; // Setor do usuário da sessão
                    $mensagem = "O produto '$nome' chegou ao limite mínimo de 5 unidades.";

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
                }
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
