<?php
// Configuração do banco de dados
$host = 'localhost';
$db = 'gm_sicbd';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Processa o formulário somente se o botão "Cadastrar" for clicado
if (isset($_POST['cadastrar'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $matricula = trim($_POST['matricula']);
    $cargo = trim($_POST['cargo']);
    $setor = trim($_POST['setor']);
    $senha = password_hash(trim($_POST['senha']), PASSWORD_BCRYPT);

    $file = 'default.png';  // Caminho do arquivo para salvar no banco

    // Verificar e processar o upload da foto
    if (!empty($_FILES['foto']['name'])) {
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $novo_nome = 'perfil-user-' . uniqid() . '.' . $extensao; // Nome padrão do arquivo
        $diretorio = __DIR__ . '\\uploads\\'; // Caminho absoluto para a pasta de uploads no Windows

        // Criar o diretório de upload caso não exista
        if (!is_dir($diretorio)) {
            if (!mkdir($diretorio, 0777, true)) {
                die("Erro ao criar o diretório de uploads.");
            }
        }

        // Validar extensão do arquivo
        if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) {
            $caminho_arquivo = $diretorio . $novo_nome; // Caminho completo para salvar o arquivo
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho_arquivo)) {
                $file = $novo_nome; // Nome do arquivo salvo no banco
            } else {
                die("Erro ao fazer upload do arquivo.");
            }
        } else {
            die("Tipo de arquivo inválido. Permitido: JPG, JPEG, PNG, GIF.");
        }
    }

    // Validar campos obrigatórios
    if (!empty($username) && !empty($email) && !empty($matricula) && !empty($cargo) && !empty($setor) && !empty($senha)) {
        try {
            // Verificar duplicatas
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email OR matricula = :matricula");
            $stmt->execute([':email' => $email, ':matricula' => $matricula]);
            $userExists = $stmt->fetchColumn();

            if ($userExists) {
                header('Location: mensagem.php?mensagem=erro_adicionar_usuario&pagina=cadastro_usuario.php');
                exit();
            } else {
                // Inserir no banco de dados
                $stmt = $pdo->prepare("INSERT INTO usuario (username, email, matricula, cargo, setor, senha, foto) 
                                       VALUES (:username, :email, :matricula, :cargo, :setor, :senha, :foto)");
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':matricula' => $matricula,
                    ':cargo' => $cargo,
                    ':setor' => $setor,
                    ':senha' => $senha,
                    ':foto' => $file // Nome da imagem salva
                ]);

                // Inserir no log_eventos
                $sql_log = "INSERT INTO log_eventos (matricula, tipo_operacao, data_operacao) VALUES (?, ?, NOW())";
                $stmt_log = $pdo->prepare($sql_log);

                if ($stmt_log) {
                    $tipo_operacao = "foi cadastrado no sistema";
                    $stmt_log->bindParam(1, $matricula, PDO::PARAM_STR);
                    $stmt_log->bindParam(2, $tipo_operacao, PDO::PARAM_STR);

                    if ($stmt_log->execute()) {
                        // Redirecionar para a página de sucesso
                   
                          header("Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=sucesso2&pagina=/Sistema-CENTRAL-ERP/cadastro_usuario.php");
                        exit();
                    } else {
                        echo "Erro ao registrar ação no log: " . $stmt_log->errorInfo()[2];
                    }

                    $stmt_log->closeCursor();
                } else {
                    echo "Erro na preparação da consulta do log: " . $pdo->errorInfo()[2];
                }
            }
        } catch (PDOException $e) {
            die("Erro ao cadastrar o usuário: " . $e->getMessage());
        }
    } else {
        die("Por favor, preencha todos os campos.");
    }
}

include 'header.php';
?>




<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="src/style/style.css">
    <link rel="stylesheet" href="./src/style/form-user.css">
       <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
    <style>
        .container40 {
        /* display: flex; */
        flex-wrap: wrap;
        gap: 78px;
        align-items: center;
        justify-content: center;
        padding: 20px 24px 10px 24px;
        margin-top: 15px;
       /* width: 50%;*/
        background-color: #ffffff;
        border: 2px solid #ccc;
        box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        /* margin-left: 24%; */
        position: relative;
        }

        .user-form-container {
            flex: 1;
            margin-right: 20px;
            
        }

        .photo-upload-container {
            flex: 0.5;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed #ccc;
            padding: 20px;
            border-radius: 10px;
          
        }

        .photo-upload-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .photo-upload-container input[type="file"] {
            display: none;
        }

        .photo-upload-container label {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .photo-upload-container label:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body class="caderno">
    <div class="container40">
        <div class="user-form-container2">
            <h3>Cadastrar Novo Usuário</h3>
            <form action="cadastro_usuario.php" method="POST" enctype="multipart/form-data">
            <div class="photo-upload-container">
            <img src="default.png" alt="Foto do Usuário" id="preview">
            <input type="file" name="foto" id="foto" accept="image/*">
            <label for="foto"><i class="fas fa-user"></i> Adicionar Foto</label>
        </div>
                <div class="user-form-group">
                    <label for="username">Nome</label>
                    <input type="text" id="username" name="username" placeholder="Nome" required>
                </div>
                <div class="user-form-group">
                    <label for="matricula">Matrícula</label>
                    <input type="text" id="matricula" name="matricula" placeholder="Matrícula" required>
                </div>
                <div class="user-form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="E-mail" required>
                </div>
                <div class="user-form-group">
                    <label for="cargo">Cargo</label>
                    <input type="text" id="cargo" name="cargo" placeholder="Cargo" required>
                </div>
                <div class="user-form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Senha" required>
                </div>
                <div class="user-form-group">
                    <label for="setor">Setor</label>
                    <select name="setor" id="setor">
                        <option value="">Selecione o setor</option>
                        <option value="administrativo">Administrador</option>
                        <option value="Estoque">Estoque</option>
                        <option value="Patrimonio">Patrimonio</option>
                        <option value="Financeiro">Financeiro</option>
                       <option value="recursos_humanos">Recursos Humanos</option>
                       <option value="contratos">Contratos</option>
                       <option value="ccoOperacao">bonde</option>
                    </select>
                </div>
                <button type="submit" name="cadastrar">Cadastrar</button>
            </form>
        </div>

        
    </div>
    

    <script>
        document.getElementById('foto').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>