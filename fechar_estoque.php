<?php
// Incluir a conexão com o banco de dados
include('banco.php');

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar as justificativas de discrepâncias
    $justificativa = isset($_POST['justificativa']) ? $_POST['justificativa'] : '';
    
    // Registrar a justificativa de fechamento no banco de dados
    $data_fechamento = date('Y-m-d H:i:s');
    
    // Inserir uma entrada de fechamento no histórico (opcional)
    $sql_fechamento = "INSERT INTO historico_fechamento (data_fechamento, justificativa) VALUES ('$data_fechamento', '$justificativa')";
    if ($con->query($sql_fechamento) === TRUE) {
        echo "Fechamento registrado com sucesso!";
    } else {
        echo "Erro ao registrar fechamento: " . $con->error;
    }

    // Processar as quantidades conferidas para cada item
    $sql = "SELECT codigo, descricao FROM materiais";
    $result = $con->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Verificar se foi recebida uma quantidade conferida para este produto
            $quantidade_conferida = isset($_POST['quantidade_' . $row['codigo']]) ? $_POST['quantidade_' . $row['codigo']] : 0;

            // Atualizar a quantidade do produto no banco de dados
            $sql_update = "UPDATE materiais SET quantidade = $quantidade_conferida WHERE codigo = " . $row['codigo'];
            if ($con->query($sql_update) === TRUE) {
                echo "Quantidade de " . $row['descricao'] . " atualizada com sucesso.<br>";
            } else {
                echo "Erro ao atualizar quantidade de " . $row['descricao'] . ": " . $con->error . "<br>";
            }
        }
    }

    // Fechar a conexão com o banco de dados
    $con->close();
    
    // Redirecionar para uma página de confirmação ou painel de administração
    header('Location: painelalmoxarifado.php'); // ou qualquer outra página de sucesso
    exit();
} else {
    // Caso o método não seja POST, redireciona de volta
    header('Location: homeestoque.php');
    exit();
}
?>
