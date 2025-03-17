<?php
include 'banco.php';

// Verifica se os campos obrigatórios foram enviados
if (!isset($_POST['senha'], $_POST['idusuario'])) {
    die("Dados inválidos.");
}

$novaSenha = $_POST['senha'];
$idUsuario = $_POST['idusuario'];

// Hasheia a nova senha para maior segurança
$hashedSenha = password_hash($novaSenha, PASSWORD_DEFAULT);

// Prepara a consulta SQL para evitar SQL Injection
$query = "UPDATE usuario SET senha = ?, primeira_vez = '0' WHERE idusuario = ?";
$stmt = $con->prepare($query);

// Verifica se a consulta foi preparada com sucesso
if (!$stmt) {
    die("Erro ao preparar a consulta: " . $con->error);
}

// Liga os parâmetros e executa a consulta
$stmt->bind_param("si", $hashedSenha, $idUsuario);

if ($stmt->execute()) {
    // Redireciona com base no tipo de usuário
    if (isset($_COOKIE['usuario'])) {
        header("Location: homeusuario.php?msagen=left");
    } else if (isset($_COOKIE['tecnico'])) {
        header("Location: hometech.php?msg=altera");
    }
} else {
    // Em caso de erro, exibe uma mensagem
    die("Erro ao atualizar a senha: " . $stmt->error);
}

// Fecha a consulta
$stmt->close();
?>
