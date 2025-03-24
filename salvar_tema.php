<?php
session_start();
include 'banco.php';

// Verificar se o dado foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tema_sistema'])) {
    // Obter o tema selecionado
    $temaSistema = $_POST['tema_sistema'];

    // Atualizar a configuração do tema no banco de dados
    $query = "UPDATE configuracoes SET tema_sistema = ? WHERE id = 1";
    $stmt = $con->prepare($query);
    $stmt->bind_param('s', $temaSistema);

    if ($stmt->execute()) {
        echo "<script>alert('Tema alterado com sucesso!'); window.location.href = 'configuracao.php';</script>";
    } else {
        echo "<script>alert('Erro ao alterar o tema.'); window.location.href = 'configuracao.php';</script>";
    }

    $stmt->close();
    $con->close();
} else {
    echo "<script>alert('Requisição inválida.'); window.location.href = 'configuracao.php';</script>";
}
?>
