<?php
session_start();
include 'banco.php';

// Verificar se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter os valores dos painéis
    $painelEstoque = isset($_POST['painelalmoxarifado']) ? 1 : 0;
    $painelFinanceiro = isset($_POST['painelfinanceiro']) ? 1 : 0;
    $painelRh = isset($_POST['painelrh']) ? 1 : 0;

    // Atualizar as configurações dos painéis no banco de dados
    $query = "UPDATE painel_config SET 
               painelalmoxarifado = ?, 
                painelfinanceiro = ?, 
                painelrh = ? 
              WHERE id = 1";

    $stmt = $con->prepare($query);
    $stmt->bind_param('iii', $painelEstoque, $painelFinanceiro, $painelRh);

    if ($stmt->execute()) {
        echo "<script>alert('Visibilidade dos painéis alterada com sucesso!'); window.location.href = 'configuracao.php';</script>";
    } else {
        echo "<script>alert('Erro ao alterar a visibilidade dos painéis.'); window.location.href = 'configuracao.php';</script>";
    }

    $stmt->close();
    $con->close();
} else {
    echo "<script>alert('Requisição inválida.'); window.location.href = 'configuracao.php';</script>";
}
?>
