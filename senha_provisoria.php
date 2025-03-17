<?php
// Após validar o login
session_start();
include 'banco.php'; 

// Obter informações do usuário logado
$username = $_POST['username'];
$password = $_POST['senha'];

// Consulta para verificar senha provisória
$stmt = $con->prepare("SELECT id, senha, senha_provisoria FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($id, $hashedPassword, $senhaProvisoria);
$stmt->fetch();
$stmt->close();

if (password_verify($password, $hashedPassword)) {
    $_SESSION['user_id'] = $id;

    // Redirecionar para o modal de alteração de senha, caso seja provisória
    if ($senhaProvisoria) {
        header("Location: alterar_senha.php");
        exit;
    } else {
        header("Location: home.php");
        exit;
    }
} else {
    echo "Usuário ou senha inválidos.";
}
?>
