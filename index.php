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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="./src/style/util.css">
	<link rel="stylesheet" type="text/css" href="./src/style/main.css">
	<link rel="stylesheet" type="text/css" href="./src/style/recuperarsenha.css">
	

<!--===============================================================================================-->
</head>
<!-- <img src="src/img/bk.png" alt="Background Image" class="background-image"> -->
<body>
<div class="limiter">
        <div class="container-login100">
            <!-- Lado esquerdo (Logo e Texto) -->
						<div class="left-side">
         <div class="logo-container">
        <!-- Primeira Imagem (Maior) -->
        <img src="./src/img/gm.png" alt="PCA RJ Logo" class="logo logo-large">

        <!-- Texto -->
      <h2 class="erp">
    <i class="fas fa-laptop-code"></i> SISTEMA ERP CENTRAL LOGISTICA
</h2>


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
                        <img src="./src/img/LL.png" alt="CENTRAL">
												</div>

                    <!-- Campo de Nome de Usuário -->
<div class="wrap-input100 validate-input" data-validate="Enter username">
    <!-- Título com ícone centralizado -->
    <div class="input-label">
        <i class="fas fa-user"></i> Usuário
    </div>
    <input class="input100" type="text" name="username" placeholder="Username" required>
    <span class="focus-input100" data-placeholder="&#xf207;"></span>
</div>

<!-- Campo de Senha -->
<div class="wrap-input100 validate-input" data-validate="Enter password">
    <!-- Título com ícone centralizado -->
    <div class="input-label">
        <i class="fas fa-lock"></i> Senha
    </div>
    <input class="input100" type="password" name="senha" id="senha" placeholder="Password" required>
    <span class="focus-input100" data-placeholder="&#xf191;"></span>

    <!-- Mostrar Senha -->
    <div class="password-container">
        <input type="checkbox" id="mostrar-senha" onclick="togglePassword()">
        <label for="mostrar-senha">Mostrar senha</label>
    </div>
</div>


                   

                    <!-- Botão de Login -->
<div class="container-login100-form-btn">
    <button class="login100-form-btn">
        <!-- Ícone dentro do botão -->
        <i class="fas fa-sign-in-alt" style="color: white; margin-right: 10px;"></i> Login
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
        <!-- Botão de Fechar -->
        <div class="modal-header">
            <span class="modal-close" onclick="fecharModal()">
                <i class="fas fa-times"></i>
            </span>
        </div>

        <!-- Título com ícone -->
        <div class="modal-title-container">
            <div class="modal-icon">
                <i class="fas fa-key"></i>
            </div>
            <h3 class="modal-title">Recuperar Senha</h3>
            <p class="modal-subtitle">Digite suas informações para recuperar o acesso</p>
        </div>
<!-- 
        <div class="logo-central-1">
            <img class="img-logo-central-1" src="./src/img/colo.png" alt="Logo">
        </div> -->

        <!-- Formulário para Verificação -->
        <form id="form-esqueci-senha" method="POST">
            <div class="modal-field">
                <label for="username-recover" class="modal-label">
                    <i class="fas fa-user"></i>
                    Nome de Usuário
                </label>
                <div class="input-wrapper">
                    <input class="modal-input" type="text" id="username-recover" name="username-recover" placeholder="Digite seu usuário" required>
                    <div class="input-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
            </div>
            
            <div class="modal-field">
                <label for="email-recover" class="modal-label">
                    <i class="fas fa-envelope"></i>
                    E-mail
                </label>
                <div class="input-wrapper">
                    <input class="modal-input" type="email" id="email-recover" name="email-recover" placeholder="Digite seu e-mail" required>
                    <div class="input-icon">
                        <i class="fas fa-at"></i>
                    </div>
                </div>
            </div>

            <button class="modal-button modal-button-primary" type="button" onclick="verificarUsuario()">
                <i class="fas fa-search"></i>
                <span>Verificar Dados</span>
                <div class="button-ripple"></div>
            </button>
        </form>

        <!-- Formulário de Nova Senha -->
        <form id="form-nova-senha" style="display: none;" method="POST">
            <div class="modal-field">
                <label for="nova-senha" class="modal-label">
                    <i class="fas fa-lock"></i>
                    Nova Senha
                </label>
                <div class="input-wrapper">
                    <input class="modal-input" type="password" id="nova-senha" name="nova-senha" placeholder="Digite sua nova senha" required>
                    <div class="input-icon">
                        <i class="fas fa-eye" onclick="togglePasswordVisibility('nova-senha')"></i>
                    </div>
                </div>
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill"></div>
                    </div>
                    <span class="strength-text">Força da senha</span>
                </div>
            </div>
            
            <div class="modal-actions">
                <button class="modal-button modal-button-success" type="button" onclick="atualizarSenha()">
                    <i class="fas fa-check-circle"></i>
                    <span>Atualizar Senha</span>
                </button>
                <button class="modal-button modal-button-secondary" type="button" onclick="fecharModal()">
                    <i class="fas fa-times-circle"></i>
                    <span>Cancelar</span>
                </button>
            </div>
        </form>

        <!-- Indicador de progresso -->
        <div class="progress-indicator">
            <div class="step active" data-step="1">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="step-line"></div>
            <div class="step" data-step="2">
                <i class="fas fa-key"></i>
            </div>
        </div>
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