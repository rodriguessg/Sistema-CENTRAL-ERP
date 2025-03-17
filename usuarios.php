<?php
// Inclui a conexão com o banco de dados
include 'banco.php';

// Processar o envio do formulário para cadastrar um novo usuário
if (isset($_POST['submit_user'])) {
    $username = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $email = $_POST['email'];
    $cargo = $_POST['cargo'];
    $password = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $con->prepare("INSERT INTO usuarios (username, senha, email, cargo, matricula) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $password, $email, $cargo, $matricula);

    if ($stmt->execute()) {
        echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar usuário.');</script>";
    }
    $stmt->close();
}

// Processar adição e remoção de permissões
if (isset($_POST['add_permission'])) {
    $usuario_id = $_POST['usuario_id'];
    $setor_id = $_POST['setor_id'];

    $stmt = $con->prepare("INSERT INTO permissoes (usuario_id, setor_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $usuario_id, $setor_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['remove_permission'])) {
    $usuario_id = $_POST['usuario_id'];
    $setor_id = $_POST['setor_id'];

    $stmt = $con->prepare("DELETE FROM permissoes WHERE usuario_id = ? AND setor_id = ?");
    $stmt->bind_param("ii", $usuario_id, $setor_id);
    $stmt->execute();
    $stmt->close();
}

// Buscar usuários, setores e permissões
$usuarios = $con->query("SELECT * FROM usuarios");
$setores = $con->query("SELECT * FROM setores");


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Usuários</title>
    <link rel="stylesheet" href="src/style/style.css">
    <style>
        .container { display: flex; flex-direction: column; align-items: center; }
        .permission-box { display: flex; align-items: center; gap: 10px; margin: 20px 0; }
        .box { width: 200px; height: 300px; border: 1px solid #ccc; overflow-y: auto; padding: 10px; }
        .box ul { list-style: none; padding: 0; }
        .box ul li { margin: 5px 0; }
        .buttons { display: flex; flex-direction: column; gap: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Gerenciamento de Usuários</h1>

    <!-- Formulário de cadastro -->
    <form action="cadastro_usuario.php" method="POST">
        <h3>Cadastrar Novo Usuário</h3>
        <input type="text" name="nome" placeholder="Nome" required>
        <input type="text" name="matricula" placeholder="Matrícula" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="text" name="cargo" placeholder="Cargo" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit" name="submit_user">Cadastrar</button>
    </form>

    <!-- Gerenciamento de permissões -->
    <form action="usuarios.php" method="POST" class="permission-box">
        <div class="box">
            <h4>Setores Disponíveis</h4>
            <ul>
                <?php while ($setor = $setores->fetch_assoc()) { ?>
                    <li>
                        <input type="radio" name="setor_id" value="<?php echo $setor['id']; ?>">
                        <?php echo $setor['nome_setor']; ?>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="buttons">
            <button type="submit" name="add_permission">&gt;</button>
            <button type="submit" name="remove_permission">&lt;</button>
        </div>

        <div class="box">
            <h4>Setores do Usuário</h4>
            <ul>
            <?php
// Verifica se a variável 'usuario_id' está definida
if (isset($_POST['usuario_id']) && !empty($_POST['usuario_id'])) {
    $usuario_id = $_POST['usuario_id'];

    // Preparação da consulta
    $stmt = $con->prepare("SELECT setores.nome_setor 
                           FROM permissoes 
                           JOIN setores ON permissoes.setor_id = setores.id 
                           WHERE permissoes.usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);  // Binding the parameter as integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Exibe as permissões do usuário
    while ($row = $result->fetch_assoc()) {
        echo $row['nome_setor'] . "<br>";
    }
    $stmt->close();
} else {
    echo "Por favor, selecione um usuário.";
}
?>

            </ul>
        </div>
    </form>
    <!-- Modal para alterar senha -->
    <?php
    if (isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
        $result = $con->query("SELECT * FROM patrimonio WHERE usuario_id = $usuario_id");
        $patrimonio = $result->fetch_assoc();
        
        if ($patrimonio['primeira_vez']) {
            echo "
            <div id='change-password-modal' style='display:block; position:fixed; top:0; left:0; background-color:rgba(0,0,0,0.5); width:100%; height:100%;'>
                <div style='background-color:white; padding:20px; margin:100px auto; width: 300px;'>
                    <h3>Alterar Senha</h3>
                    <form method='POST' action='validacaocadastrado.php'>
                        <input type='password' name='nova_senha' placeholder='Nova Senha' required><br><br>
                        <button type='submit' name='save_password'>Salvar</button>
                    </form>
                </div>
            </div>";
        }
    }
    ?>
    
</div>
</body>
</html>
<?php $con->close(); ?>
