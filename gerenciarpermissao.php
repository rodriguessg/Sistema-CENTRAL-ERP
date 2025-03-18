<?php
include 'banco.php';
include 'header.php';

$search_query = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_user'])) {
        $usuario_id = $_POST['usuario_id'];
        $setor_id = $_POST['setor_id'];
        $situacao = $_POST['situacao'];
        $nova_senha = isset($_POST['senha']) && !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;

        if ($usuario_id && $setor_id && $situacao) {
            $setor_result = $con->query("SELECT nome_setor FROM setores WHERE id = $setor_id");
            if ($setor_result && $setor_result->num_rows > 0) {
                $setor = $setor_result->fetch_assoc();
                $nome_setor = $setor['nome_setor'];

                if ($nova_senha) {
                    $stmt = $con->prepare("UPDATE usuario SET setor = ?, situacao = ?, senha = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $nome_setor, $situacao, $nova_senha, $usuario_id);
                } else {
                    $stmt = $con->prepare("UPDATE usuario SET setor = ?, situacao = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $nome_setor, $situacao, $usuario_id);
                }

                if ($stmt->execute()) {
                    $_SESSION['message'] = "Usuário atualizado com sucesso!";
                } else {
                    $_SESSION['message'] = "Erro ao atualizar o usuário.";
                }
                $stmt->close();
            } else {
                $_SESSION['message'] = "Setor não encontrado.";
            }
        } else {
            $_SESSION['message'] = "Por favor, preencha todos os campos.";
        }
    }

    if (isset($_POST['delete_user'])) {
        $usuario_id = $_POST['usuario_id'];
        $senha_admin = $_POST['senha_admin'];

        // Verificar senha do administrador
        $admin_query = $con->query("SELECT senha FROM usuario WHERE username = 'master'");
        $admin = $admin_query->fetch_assoc();

        if (password_verify($senha_admin, $admin['senha'])) {
            $stmt = $con->prepare("DELETE FROM usuario WHERE id = ?");
            $stmt->bind_param("i", $usuario_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Usuário excluído com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao excluir o usuário.";
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Senha administrativa inválida.";
        }
    }
}

// Campo de pesquisa
$query = "SELECT id, username, setor, situacao FROM usuario";
if ($search_query) {
    $query .= " WHERE username LIKE '%$search_query%'";
}
$usuarios = $con->query($query);

// Buscar setores
$setores = $con->query("SELECT id, nome_setor FROM setores");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Permissões</title>
    <link rel="stylesheet" href="src/style/style.css">

<style>
        .container {  display: table; }

        .container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl {
    width: 100%;
    /* padding-right: 15px;
    /* padding-left: 15px; */
    /* margin-right: auto; */
    /* margin-left: auto; */ 
}
        /* margin-left:20%; */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        .btn { padding: 5px 10px; cursor: pointer; border: none; border-radius: 4px; }
        .btn-update { background-color: #007bff; color: white; }
        .btn-delete { background-color: #f44336; color: white; }
        .btn-update:hover { background-color: #0056b3; }
        .btn-delete:hover { background-color: #d32f2f; }
        .modal-container { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-container.active { display: flex; }
        .modal-content { background: #fff; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%; text-align: center; }
        .modal-actions { display: flex; justify-content: center; gap: 10px; }
        .container1 { display: flex; align-items: baseline; gap: 10px;}
            /* Estilo do formulário */
    #form-atualizar {
        display: flex;
        flex-direction: column;
        gap: 0px;
    }

    #form-atualizar label {
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }

    #form-atualizar input,
    #form-atualizar select {
        width: 100%;
        padding: 12px 10px;
        font-size: 14px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    /* Foco nos inputs e selects */
    #form-atualizar input:focus,
    #form-atualizar select:focus {
        border-color: #007BFF;
        box-shadow: 0 0 4px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    /* Botões do formulário */
    #form-atualizar .button-group {
        display: flex;
        margin-top: 10px;
        justify-content: center;
        margin-left: 7%;
        width: 78%;
        gap: 12px;
        text-align: center;
    }


    #form-atualizar button {
        flex: 1;
        padding: 12px 15px;
        font-size: 14px;
        font-weight: bold;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-align: center;
    }

    /* Botão Salvar */
    #form-atualizar button[type="submit"] {
        background-color: #007BFF;
    }

    #form-atualizar button[type="submit"]:hover {
        background-color: #0056b3;
    }

    /* Botão Cancelar */
    #form-atualizar button[type="button"] {
        background-color: #dc3545;
    }

    #form-atualizar button[type="button"]:hover {
        background-color: #a71d2a;
    }
    .btn1 {
        cursor: pointer;
    }
</style>
</head>
<body>
    <h2>Gerenciar Permissões</h2>
<div class="container">
        <form class="container1" method="GET" action="gerenciarpermissao.php">
            <input type="text" name="search" placeholder="Pesquisar por nome..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="btn1" type="submit">Pesquisar</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome de Usuário</th>
                    <th>Setor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($usuarios && $usuarios->num_rows > 0): ?>
                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['setor']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['situacao']); ?></td>
                            <td>
                                <button class="btn btn-update" onclick="openModal(
                                    <?php echo $usuario['id']; ?>,
                                    '<?php echo htmlspecialchars($usuario['username']); ?>',
                                    '<?php echo htmlspecialchars($usuario['setor']); ?>',
                                    '<?php echo htmlspecialchars($usuario['situacao']); ?>'
                                )">
                                    Atualizar
                                </button>
                                <button class="btn btn-delete" onclick="openDeleteModal(<?php echo $usuario['id']; ?>)">Excluir</button>

                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhum usuário encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
</div>

<div id="userModal" class="modal-container">
        <div class="modal-content">
            <form id="form-atualizar" method="POST" action="gerenciarpermissao.php">
                <input type="hidden" id="modalUserId" name="usuario_id">
                <h2>Atualizar Usuário</h2>
                <p>Nome de Usuário: <span id="modalUsername"></span></p>
                <label for="modalSetor">Setor:</label>
                <select id="modalSetor" name="setor_id" required>
                    <option value="" disabled>Selecione um setor</option>
                    <?php while ($setor = $setores->fetch_assoc()): ?>
                        <option value="<?php echo $setor['id']; ?>"><?php echo htmlspecialchars($setor['nome_setor']); ?></option>
                    <?php endwhile; ?>
                </select>
                <label for="modalSituacao">Status:</label>
                <select id="modalSituacao" name="situacao" required>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>

                </select>
                <label for="modalSenha">Alterar Senha:</label>
                <input type="password" id="modalSenha" name="senha" placeholder="Nova senha">
                <div class="modal-actions">
                    <button type="submit" name="update_user" class="btn">Salvar</button>
                    <button type="button" class="btn btn-delete" onclick="closeModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal de Exclusão -->
    <div id="deleteModal" class="modal-container">
    <div class="modal-content">
        <h2>Excluir Usuário</h2>
        <p>Por favor, insira a senha administrativa para confirmar a exclusão.</p>
        <form method="POST" action="gerenciarpermissao.php">
            <input type="hidden" id="deleteUserId" name="usuario_id">
            <label for="deleteSenhaAdmin">Senha Administrativa:</label>
            <input type="password" id="deleteSenhaAdmin" name="senha_admin" placeholder="Digite a senha" required>
            <div class="modal-actions">
                <button type="submit" name="delete_user" class="btn btn-delete">Excluir</button>
                <button type="button" class="btn" onclick="closeDeleteModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>


<script>
        function openModal(id, username, setor, situacao) {
            document.getElementById("modalUserId").value = id;
            document.getElementById("modalUsername").innerText = username;
            document.getElementById("modalSetor").value = setor;
            document.getElementById("modalSituacao").value = situacao;
            document.getElementById("userModal").classList.add("active");
        }

        function closeModal() {
            document.getElementById("userModal").classList.remove("active");
        }

        function deleteUser(id) {
            const senhaAdmin = prompt("Digite a senha administrativa para excluir o usuário:");
            if (senhaAdmin) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'gerenciarpermissao.php';

                const userIdInput = document.createElement('input');
                userIdInput.type = 'hidden';
                userIdInput.name = 'usuario_id';
                userIdInput.value = id;

                const senhaAdminInput = document.createElement('input');
                senhaAdminInput.type = 'hidden';
                senhaAdminInput.name = 'senha_admin';
                senhaAdminInput.value = senhaAdmin;

                const deleteUserInput = document.createElement('input');
                deleteUserInput.type = 'hidden';
                deleteUserInput.name = 'delete_user';
                deleteUserInput.value = '1';

                form.appendChild(userIdInput);
                form.appendChild(senhaAdminInput);
                form.appendChild(deleteUserInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        function openDeleteModal(userId) {
    document.getElementById("deleteUserId").value = userId;
    document.getElementById("deleteModal").classList.add("active");
}

function closeDeleteModal() {
    document.getElementById("deleteModal").classList.remove("active");
}

    </script>
</body>
</html>
<?php include 'footer.php'; ?>