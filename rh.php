<?php
include './banco.php';
include './header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gerenciador de Assinatura de E-mail</title>
  <link rel="stylesheet" href="./RH/src/style/assinatura.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body class="caderno">
  <!-- <header class="header">
    <img src="./src/img/ok.png" alt="Emblema CENTRAL" class="logo">
  </header> -->
 <div class="form-container3" id="cadastrar">
  
    <main>
      <h2>Gerador de Assinatura de E-mail</h2>

      <form id="signatureForm">
        <img src="./RH/src/img/central.png" alt="log" class="log1">
        <label for="name">Nome:</label>
        <input type="text" id="name" required />

        <label for="sector">Cargo:</label>
        <input type="text" id="sector" required />


        <label for="sector1">Setor:</label>
        <select id="sector1" required>
          <option value="" disabled selected>Escolha a Área</option>
          <option value="ASSCON">ASSCON</option>
          <option value="ASSGER">ASSGER</option>
          <option value="ASSJUR">ASSJUR</option>
          <option value="ASSPRE">ASSPRE</option>
          <option value="ASSPRIN">ASSPRIN</option>
          <option value="ASSTAD">ASSTAD</option>
          <option value="AUD">AUD</option>
          <option value="CEHAB">CHEGAB</option>
          <option value="COMAUD">COMAUD</option>
          <option value="COMEL">COMEL</option>
          <option value="CONFIS">CONFIS</option>
          <option value="CONDAM">CONDA</option>
          <option value="DIRAF">DIRAF</option>
          <option value="DIRPLA">DIRPLA</option>
          <option value="DIRPLA">DIRPLA</option>
          <option value="DIREO">DIREO</option>
          <option value="DIREXE">DIREXE</option>
          <option value="GERADM">GERADM</option>
          <option value="GERCOM">GERCOM</option>
          <option value="GERFIN">GERFIN</option>
          <option value="GERGEP">GERGEP</option>
          <option value="GERLIC">GERLIC</option>
          <option value="GERMAP">GERMAP</option>
          <option value="GERMAR">GERMAR</option>
          <option value="GERMST">GERMST</option>
          <option value="GERORT">GERORT</option>
          <option value="GERPLA">GERPLA</option>
          <option value="GERTIN">GERTIN</option>
          <option value="GERFIP">GERFIP</option>
          <option value="GEROPT">GEROPT</option>
          <option value="GERVIP">GERVIP</option>
          <option value="GERSIS">GERSIS</option>
          <option value="GERTIN">GERTIN</option>
          <option value="GERVIP">GERVIP</option>
          <option value="GERSIS">GERSIS</option>
          <option value="GERCOT">GERCOT</option>
          <option value="SUPDAM">SUPDAM</option>
          <option value="SUPFIC">SUPFIC</option>
          <option value="SUPGEP">SUPGEP</option>
          <option value="SUPMRS">SUPMRS</option>
          <option value="SUPVIP">SUPVIP</option>
          <option value="SUPTRA">SUPTRA</option>
          <option value="SUPAT">SUPAT</option>
          <option value="SUPTIN">SUPTIN</option>
          <option value="SUPLAN">SUPLAN</option>
          <option value="SUPVIP">SUPVIP</option>
          <option value="SUPDAM">SUPDAM</option>
          <option value="SUPTRA">SUPTRA</option>
           <option value="PRESIDÊNCIA">PRESI</option>
        </select>


         <label for="email">E-mail:</label>
        <input type="email" id="email" required />

        <label for="tel">Telefone:</label>
        <input type="number" id="tel" required />

        <label for="room">Número da Sala:</label>
        <input type="text" id="room" required />

        <button type="button" onclick="validateForm()">Criar</button>
      </form>


      <!-- Modal de Erro -->
      <div id="errorModal" class="modal2">
        <div class="modal-content2">
          <span class="close2" onclick="closeErrorModal()">&times;</span>
          <img src="https://img.icons8.com/ios-glyphs/50/fa314a/error.png" alt="Ícone de Erro">
          <h3>Preencha todos os campos!</h3>
          <p>Todos os campos são obrigatórios. Por favor, verifique e tente novamente.</p>
          <div class="progress-bar">
            <div id="progressBarFill" class="progress-bar-fill"></div>
          </div>
        </div>
      </div>

   

      <!-- Modal de Carregamento e Sucesso -->
      <div id="loadingModal" class="modal3">
        <div class="modal-content3">
          <!-- Ícone de Carregamento -->
          <div id="loadingMessage">
            <svg class="loading-icon" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10" opacity="0.3"></circle>
              <path d="M22 12a10 10 0 0 0-10-10"></path>
            </svg>
            <p>Gerando a assinatura...</p>
          </div>

          <!-- Mensagem de Sucesso -->
          <div id="successMessage" style="display: none;">
            <img src="https://img.icons8.com/ios-glyphs/50/4CAF50/checkmark.png" alt="Sucesso" class="success-icon">
            <p>Assinatura gerada com sucesso!</p>
          </div>
        </div>
      </div>
      

    </main>
  
 </div>
<!-- Modal de Assinatura -->
<div id="signatureModal" class="modal">
  <div class="modal-content">
    <!-- Removido o botão de fechar (X) -->

    <div id="signatureLayout">
      <div class="logo-section">
        <img class="gvn1" src="./RH/src/img/colo.png" alt="logo" />
        <img class="gvn" src="./RH/src/img/cent.png" alt="logo" />
      </div>

      <div class="content-section">
        <h2 id="modalName">Nome Sobrenome</h2>
        <h3 id="modalSector">Cargo</h3>
        <h3 id="modalSector1">Setor</h3>
        <!-- <p id="modalEmail">Email: exemplo@dominio.com</p> -->    
        <!-- <p class="strong" style="color: #1d70a3;">Companhia Estadual de Engenharia de Transportes e Logística</p> -->
        <p class="strong">CENTRAL RJ</p>

        <div class="address-line">
          <p>Av. Nossa Senhora de Copacabana,</p>
          <p id="modalRoom">Sala: </p>
        </div>

        <p>Copacabana Rio de Janeiro-RJ CEP: 22031-000</p>
        <p id="modalPhone">Telefone: (XX) XXXXX-XXXX</p>  
      </div>
    </div>

    <!-- Botões no final -->
    <div class="modal-buttons">
      <button onclick="downloadSignature()">Baixar</button>
      <button onclick="closeModal()">Fechar</button>

    </div>
  </div>
</div>

 
  <script src="./RH/script.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  
<?php
include 'footer.php'
?>
 
</body>

</html>

