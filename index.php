<!-- <?php
// Inicia a sessão


// Iniciar a sessão
session_start();

// Definir o nome de usuário após o login (certifique-se de que o nome de usuário está correto no login)
$_SESSION['username'] = 'usuario_logado'; // Exemplo de atribuição após o login


// Inclui a conexão com o banco de dados
include "banco.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $setor = $_POST['setor'];
    $username = $_POST['username'];
    $senha = $_POST['senha'];

    // Consulta para verificar o usuário no banco de dados
    $query = "SELECT * FROM usuario WHERE username = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verifica a senha
        if (password_verify($senha, $usuario['senha'])) {
            // Armazena o username na sessão
            $_SESSION['username'] = $username;
            $_SESSION['setor'] = $setor;

            // Define cookies para manter a sessão (opcional, mas não recomendado para informações sensíveis)
            setcookie('username', $username, time() + 3600, '/');
            setcookie('setor', $setor, time() + 3600, '/');

            // Redireciona para a página com base no setor
            switch (strtolower($setor)) {
                case 'administrativo':
                    header("Location: painel.php");
                    break;
                case 'patrimonio':
                    header("Location: homepatrimonio.php");
                    break;
                case 'financeiro':
                    header("Location: homefinanceiro.php");
                    break;
                    case 'estoque':
                        header("Location: painelestoque.php");
                        break;
                        case 'recuros_humanos':
                            header("Location: painelrh.php");
                            break;    
                            case 'contratos':
                                header("Location: painelcontratos.php");
                                break;    
                default:
                    // Setor não reconhecido
                    header("Location: login.php?mensagem=404");
                    break;
            }
            exit();
        } else {
            // Senha inválida
            header("Location: login.php?mensagem=404");
            exit();
        }
    } else {
        // Usuário não encontrado
        header("Location: login.php?mensagem=404");
        exit();
    }
}
?> -->
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login V3</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="./src/style/util.css">
	<link rel="stylesheet" type="text/css" href="./src/style/main.css">
	<link rel="stylesheet" type="text/css" href="./src/style/recuperarsenha.css">
	

<!--===============================================================================================-->
</head>
<img src="src/img/bk.png" alt="Background Image" class="background-image">
<body>
<div class="limiter">
        <div class="container-login100">
            <!-- Lado esquerdo (Logo e Texto) -->
						<div class="left-side">
         <div class="logo-container">
        <!-- Primeira Imagem (Maior) -->
        <img src="./src/img/colo.png" alt="PCA RJ Logo" class="logo logo-large">

        <!-- Texto -->
        <h2 class="erp">SISTEMA ERP CENTRAL LOGISTICA</h2>

				<div class="icon-container">
    <!-- Bondinho -->
    <div class="icon">
        <img src="src/img/bondinho.png" alt="Bondinho" class="icon-img">
    </div>
    
    <!-- Trem (Ícone do Meio) -->
    <div class="icon">
        <img src="src/img/trem.png" alt="Trem" class="icon-img middle-icon">
    </div>

    <!-- Teleférico -->
    <div class="icon">
        <img src="src/img/teleferico.png" alt="Teleférico" class="icon-img">
    </div>
</div>


        <!-- Segunda Imagem (Menor) -->
        <img src="./src/img/log.png" alt="PCA RJ Logo" class="logo logo-small">

				
    </div>
</div>


            <!-- Lado direito (Formulário de Login) -->
            <div class="wrap-login100">
                <form class="login100-form validate-form" action="login.php" method="POST">
                        <div class="logo-central">
                        <img src="./src/img/GM.png" alt="GM">
												</div>

                    <!-- Seletor de área de acesso -->
                    <div class="wrap-input100 validate-input">
                        <select class="menu-custom" name="setor" id="setor" required>
                            <option value="" disabled selected>Área de Acesso</option>
                            <option value="administrador">Administrador</option>
                            <option value="patrimonio">Patrimônio</option>
                            <option value="financeiro">Financeiro</option>
                            <option value="estoque">Estoque</option>
                            <option value="recursos_humanos">Recursos Humanos</option>
                            <option value="contratos">Contratos</option>
                        </select>
                    </div>

                    <!-- Campo de Nome de Usuário -->
                    <div class="wrap-input100 validate-input" data-validate="Enter username">
                        <input class="input100" type="text" name="username" placeholder="Username" required>
                        <span class="focus-input100" data-placeholder="&#xf207;"></span>
                    </div>

                    <!-- Campo de Senha -->
                    <div class="wrap-input100 validate-input" data-validate="Enter password">
                        <input class="input100" type="password" name="senha" id="senha" placeholder="Password" required>
                        <span class="focus-input100" data-placeholder="&#xf191;"></span>
                    </div>

                    <!-- Mostrar Senha -->
                    <div class="password-container">
                        <input type="checkbox" id="mostrar-senha" onclick="togglePassword()">
                        <label for="mostrar-senha">Mostrar senha</label>
                    </div>

                    <!-- Botão de Login -->
                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn">
                            Login
                        </button>
                    </div>

                    <!-- Link para "Esqueci minha senha" -->
                    <div class="text-cente">
                        <a class="txt1" href="javascript:void(0);" onclick="abrirModal()">
                            Esqueceu sua Senha ?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal para "Esqueci minha senha" -->
<div id="modal-esqueci-senha" class="modal-container">
    <div class="modal-content">
    
        <!-- Título -->
        <h3 class="modal-title">Recuperar Senha</h3>

		

		    <!-- Botão de Fechar -->
			<div class="modal-header">
				<span class="modal-close" onclick="fecharModal()">&times;</span>
			</div>

			<div class="logo-central-1">
				<img class="img-logo-central-1" src="./src/img/Logo.png" alt="">
			</div>

        <!-- Formulário para Verificação -->
        <form id="form-esqueci-senha" method="POST">
            <div class="modal-field">
                <label for="username-recover">Nome de Usuário:</label>
                <input class="modal-input" type="text" id="username-recover" name="username-recover" placeholder="Digite seu usuário" required>
            </div>
            <div class="modal-field">
                <label for="email-recover">E-mail:</label>
                <input class="modal-input" type="email" id="email-recover" name="email-recover" placeholder="Digite seu e-mail" required>
            </div>

	
            <button class="modal-button" type="button" onclick="verificarUsuario()">Verificar</button>
        </form>
        <!-- Formulário de Nova Senha -->
        <form id="form-nova-senha" style="display: none;" method="POST">
            <div class="modal-field">
                <label for="nova-senha">Nova Senha:</label>
                <input class="modal-input" type="password" id="nova-senha" name="nova-senha" placeholder="Digite sua nova senha" required>
            </div>
            <div class="modal-actions">
                <button class="modal-button" type="button" onclick="atualizarSenha()">Atualizar Senha</button>
                <button class="modal-button modal-button-secondary" type="button" onclick="fecharModal()">Fechar</button>
            </div>
        </form>
    </div>
    </div>

	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<script src="vendor/animsition/js/animsition.min.js"></script>
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
	<script src="vendor/countdowntime/countdowntime.js"></script>
	<script src="./src/js/main.js"></script>
	<script src="./src/js/recsenhamodal.js"></script>
</body>
</html>