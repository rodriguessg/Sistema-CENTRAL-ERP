<?php
session_start();
include 'banco.php';

if (isset($_POST['save_password']) && isset($_POST['nova_senha'])) {
    $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);
    $usuario_id = $_SESSION['usuario_id'];

    // Atualiza a senha e marca como não sendo mais a primeira vez
    $stmt = $con->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmt->bind_param("si", $nova_senha, $usuario_id);

    if ($stmt->execute()) {
        // Atualizar a coluna 'primeira_vez' na tabela 'patrimonio'
        $stmt2 = $con->prepare("UPDATE patrimonio SET primeira_vez = FALSE WHERE usuario_id = ?");
        $stmt2->bind_param("i", $usuario_id);
        $stmt2->execute();
        
        echo "<script>alert('Senha alterada com sucesso!'); window.location.href = 'usuarios.php';</script>";
    } else {
        echo "<script>alert('Erro ao alterar senha.'); window.location.href = 'usuarios.php';</script>";
    }
    $stmt->close();
    $stmt2->close();
}
?>
