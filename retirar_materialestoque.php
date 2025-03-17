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
        header("Location: mensagem.php?mensagem=Campos obrigatórios não preenchidos&pagina=retirar");
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

        // Verifica se a quantidade do produto é inferior a 5 unidades
        if ($quantidadeAtual < 5) {
            // Mensagem de saldo insuficiente
            header("Location: mensagem.php?mensagem=Saldo insuficiente para retirada&pagina=homeestoque.php");
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
                $updateQuery = "UPDATE produtos SET quantidade = quantidade - ?, tipo_operacao = 'retirado' WHERE codigo = ?";
                $updateStmt = $con->prepare($updateQuery);
                $updateStmt->bind_param('is', $quantidade, $codigo);
                $updateStmt->execute();
                $updateStmt->close();
            }

            // Atualiza o tipo de operação para "retirado"
            $tipoOperacaoQuery = "UPDATE produtos SET tipo_operacao = 'retirado' WHERE codigo = ?";
            $tipoOperacaoStmt = $con->prepare($tipoOperacaoQuery);
            $tipoOperacaoStmt->bind_param('s', $codigo);
            $tipoOperacaoStmt->execute();
            $tipoOperacaoStmt->close();

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
