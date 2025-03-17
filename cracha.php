
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerador de Crachá</title>
    <link rel="icon" href="./RH/crachaa/publico/imagem/favicon.ico">
	<!-- Fonte -->
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

	<!-- CSS -->
	<link rel="stylesheet" href="./RH/crachaa/publico/external/materialize/css/materialize.min.css">
    <link rel="stylesheet" href="./RH/crachaa/publico/css/style.css">
    <link rel="stylesheet" href="./RH/crachaa/publico/css/template.css">
    
    <!-- JS -->
	<!-- ajax --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!-- jQuery --><script type="text/javascript" src="controller/js/jquery-3.3.1.min.js"></script>

	<script defer src="./RH/crachaa/publico/external/materialize/js/materialize.min.js" type="text/javascript"></script>
    <script defer src="./RH/crachaa/publico/external/fontawesome-all.js" type="text/javascript"></script>
    <script defer src="./RH/crachaa/publico//external/html2canvas.min.js" type="text/javascript"></script>
	<script defer src="./RH/crachaa/publico//external/sweetalert.min.js" type="text/javascript"></script>	
    <script defer src="./RH/crachaa/controller/js/javascript.js" type="text/javascript"></script>	

	<!-- Fecha imports do Firebase -->

	<meta name="viewport" content="width=device-width, user-scalable=no">
</head>
<body>

    <nav>
        <div class="nav-wrapper cr-backgroun-primary">
            <a href="index.html" class="brand-logo center">Gerador de Crachá</a>
            <ul class="right hide-on-med-and-down">
           
                <li><a class="waves-effect waves-light btn" id="cr-limpar">Limpar</a></li>
                <li><a class="waves-effect waves-light btn" id="cr-gerar">Gerar</a></li>
            </ul>
        </div>
    </nav>

    <div class="container center-align">
        <div class="row">
            <div class="col s6">
                <form>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="name" type="text" required oninput="validateInput()">
                            <label for="name">Nome Completo</label>
                            <script>
                                function validateInput() {
                                    var input = document.getElementById('name').value;
                                    var lettersWithSpace = /^[A-Za-z\s]+$/; // Permitindo letras e espaços
                                    if (!input.match(lettersWithSpace)) {
                                        alert('Por favor, insira apenas letras e espaços.');
                                        document.getElementById('name').value = input.replace(/[^A-Za-z\s]/g, ''); // Substituindo caracteres não permitidos
                                    }
                                }
                            </script>
                            
                        </div>
                    </div>
                    <!--
                    <div class="row">
                      <div class="input-field col s12">
                            <input id="name" type="text">
                            <label for="name">Nome</label>
                        </div>
                    </div>
                    -->  
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="name3" type="text" maxlength="14">
                            <label for="name3">Matricula</label>
                    
                            <script>
                                window.onload = function() {
                                    var input = document.getElementById('name3');
                                    input.addEventListener('input', function(evt) {
                                        // Permite apenas números
                                        evt.target.value = evt.target.value.replace(/\D/g, '');
                                    });
                                };
                            </script>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="file-field input-field">
                         <!--   <div class="btn">
                                <span>Arquivo</span>
                                <input type="file" id="logo">
                            </div> 
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" placeholder="Logo Empresa" accept=".jpg, .jpeg, .png" multiple> 
                            </div>
                            -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>Arquivo</span>
                                <input type="file" id="photo">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path" type="text" placeholder="Foto Funcionário">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s3">
                            <label>
                                <input name="profissao" type="radio" value="0" class="cr-area-input"/>
                                <span>Funcionário</span>
                            </label>                   
                        </div>
                        <div class="col s6">
                            <label>
                                <input name="profissao" type="radio" value="1" class="cr-area-input"/>
                                <span>Prestador de Serviço</span>
                            </label>        
                        </div>
                    </div>
                    <div class="row cr-invisible" id="cr-frame-area">
                        <div class="input-field col s12" id="cr-area">
                            <select >
                                <option value="" disabled selected>Escolha a Área</option>
                                <option value="COMEL">COMEL</option>
                                <option value="CONFIS">CONFIS</option>
                                <option value="CONDAM">CONDA</option>
                                <option value="COMAUD">COMAUD</option>
                                <option value="AUD">AUD</option>
                                <option value="DIREXE">DIREXE</option>
                                <option value="PRESIDENCIA">PRESIDÊNCIA</option>
                                <option value="DIRPLA">DIRPLA</option>
                                <option value="DIRAF">DIRAF</option>
                                <option value="DIREO">DIREO</option>
                                <option value="CEHAB">CHEGAB</option>
                                <option value="ASSGER">ASSGER</option>
                                <option value="ASSCON">ASSCON</option>
                                <option value="OUVI">OUVI</option>
                                <option value="ASSTAD">ASSTAD</option>
                                <option value="ASSJUR">ASSJUR</option>
                                <option value="ASSPRIN">ASSPRIN</option>
                                <option value="GERCOM">GERCOM</option>
                                <option value="DIRPLA">DIRPLA</option>
                                <option value="SUPLAN">SUPLAN</option>
                                <option value="GERPLA">GERPLA</option>
                                <option value="GERORT">GERORT</option>
                                <option value="SUPTIN">SUPTIN</option>
                                <option value="GERTIN">GERTIN</option>
                                <option value="SUPDAM">SUPDAM</option>
                                <option value="GERADM">GERADM</option>
                                <option value="GERLIC">GERLIC</option>
                                <option value="SUPGEP">SUPGEP</option>
                                <option value="GERGEP">GERGEP</option>
                                <option value="GERMST">GERMST</option>
                                <option value="SUPFIC">SUPFIC</option>
                                <option value="GERFIN">GERFIN</option>
                                <option value="GERCOT">GERCOT</option>
                                <option value="SUPAT">SUPAT</option>
                                <option value="GERADP">GERADP</option>
                                <option value="GERFIP">GERFIP</option>
                                <option value="SUPMRS">SUPMRS</option>
                                <option value="GERSIS">GERSIS</option>
                                <option value="GERMAR">GERMAR</option>
                                <option value="SUPVIP">SUPVIP</option>
                                <option value="GERVIP">GERVIP</option>
                                <option value="GERMAP">GERMAP</option>
                                <option value="SUPTRA">SUPTRA</option>
                                <option value="GERMAT">GERMAT</option>
                                <option value="GEROPT">GEROPT</option>
                                <option value="ASSPRE">ASSPRE</option>

                                
                            </select>
                        </div>
                    </div>

                    <div class="row1">
                        <div class="input-field col s12">
                            <input id="email" type="number" class="validate">
                            <label for="email">Matrícula</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <input id="data" type="date" class="validate" maxlength="10">
                            <label for="data">Emissão</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12">
                            <input id="cargo" type="text" class="validate" oninput="validateInput(this)">
                            <label for="cargo">CARGO/FUNÇÃO</label>
                        </div>
                    </div>
                
                    <script>
                        function validateInput(input) {
                            input.value = input.value.replace(/[^a-zA-Z\s]/g, ''); // Remove qualquer coisa que não seja letra ou espaço
                        }
                    </script>

                    <div class="row center-align"> <!-- style="padding: 0px 0px 50px; margin-top: -30px" -->
                        <div class="input-field col s4">
                            <input id="primary_color" type="color" style="display: none">
                            <label for="primary_color"><a class="waves-effect waves-light btn cr-btn-color cr-color-edit-1">Cor principal</a></label>
                        </div>
                        <div class="input-field col s5">
                            <input id="secondary_color" type="color" style="display: none">
                            <label for="secondary_color"><a class="waves-effect waves-light btn cr-btn-color cr-color-edit-2">Cor secundária</a></label>
                        </div>
                        <div class="input-field col s3">
                            <input id="text_color" type="color" style="display: none">
                            <label for="text_color"><a class="waves-effect waves-light btn cr-btn-color cr-color-edit-3">Detalhe</a></label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col s6">
                <div id="cr-object-card" ></div>
            </div>
        </div>
        <div class="card1" id="cr-cracha">
            <div class="col s12 m6" style="width:317px; ">
                <div class="card white darken-1 cr-card" id="cr-cracha">
                  <!-- O estilo 'height' foi ajustado para aproximadamente 4 cm (150px) -->
                  <div class="cr-frame-primary-top cr-background-primary2"></div>
                  <div class="cr-frame-secondary-top cr-background-secondary2"></div>
                  <div class="card-content text-darken-2 center-align" style="padding: 0px; position: relative; margin-top:27px;">
                    <img src="./RH/crachaa/publico/lgo rio janeiro.png" alt="" id="cr-image-logo" class="cr-card-image-logo">
                    <img src="./RH/crachaa/publico/imagem/perfil-padrao.png" alt="" id="cr-image-photo" class="square cr-card-image">
                    <h1 class="cr-name-person" style="position: relative;">Nome do Funcionário</h1>
                    <h5 class="cr-name-profession" style="position: relative; Font-size:bolder;">Área de Atuação</h5>
                    <p class="cr-email-company" style="position: relative; text-align:center; font-weight:bolder">CENTRAL-RJ</p>
                   
                    </div>
                
                  <div class="cr-frame-primary-bottom center-align cr-relative white-text cr-background-primary">
                    <img src="" alt="" id="cr-image-logo" class="cr-card-image-logo2"> <!-- Ajustado o padding para 5px -->
                  </div>
                  <div class="cr-frame-secondary-bottom cr-background-secondary"></div>
                </div>
              </div> 
        
         <div class="col s12 m6" style="width:343px;">
            <div class="card white darken-1 cr-card" id="cr-cracha">
                <div class="cr-frame-primary-top cr-background-primary"></div>
                <div class="cr-frame-secondary-top cr-background-secondary"></div>
                <div class="card-content text-darken-2 center-align" style="padding: 3px; "> 
                  <div class="sdt">
                    <p class="central">Companhia Estadual de Engenharia de
                        </p>
                        <p class="central">Transportes e Logística - CENTRAL</p>
                  <img src="./RH/crachaa/publico/imagem/logo-removebg-preview.png" alt="" id="cr-image-logo" class="cr-card-image-logo">
                 </div>
                 <div class="qrcode">
                <div class="text1">
                  <p class="azul" style="font-size:10px;">Nome</p>
                 <p class="cr-name-person2">Nome funcionário</p>
                 <p class="azul" style="font-size:10px;" >Matricula</p>
                 <p class="cr-id">00000000</p>
                 <p class="azul" style="font-size:10px">Cargo/Função</p>
                 <p class="cr-cargo">--</p>
                 <p class="azul" style="font-size:10px;">Emissão</p>
                 <p class="cr-data-company">00/00/0000</p>
                </div>
                <div>
                    <img class="img-code" src="./RH/crachaa/publico/imagem/image.png" alt="img">
                    </div>
                </div>
                <div class="rdzul">
                    <p class="azul1">PORTE OBRIGATÓRIO NO INTERIOR DAS INSTALAÇÕES</p>
                    <p class="red1">NÃO É VÁLIDO COMO DOCUMENTO
                        DE IDENTIDADE</p>
                </div>
            </div>
            </div>
          </div> 
        </div>    
    </div>

    </div>

    <footer class="page-footer cr-backgroun-primary">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <h5 class="white-texto">Maik Alves e Gabriel Souza</h5>
                    <p class="grey-text text-lighten-4">Aplicações Web</p>
                </div>
            </div>
        </div>


        <div class="footer-copyright">
            <div class="container">
            © 2024 Copyright CENTRAL
            </div>
        </div>
    </footer>
</body>
</html>