// Função para formatar o número de telefone no padrão brasileiro (XX) XXXXX-XXXX
function formatPhoneNumber(tel) {
  // Remove tudo que não for dígito
  tel = tel.replace(/\D/g, '');

  // Verifica se o número tem 10 ou 11 dígitos (com ou sem DDD)
  if (tel.length === 10 || tel.length === 11) {
    let ddd = tel.substring(0, 2);
    let part1 = tel.length === 10 ? tel.substring(2, 6) : tel.substring(2, 7);
    let part2 = tel.length === 10 ? tel.substring(6) : tel.substring(7);

    return `(${ddd}) ${part1}-${part2}`;
  }
  return tel; // Retorna o número original se não for válido
}

// Função para validar o formulário
function validateForm() {
  const name = document.getElementById("name").value;
  const sector = document.getElementById("sector").value;
  const sector1 = document.getElementById("sector1").value;
  const tel = document.getElementById("tel").value;
  const room = document.getElementById("room").value;

  // Remover classes de erro anteriores
  const inputs = document.querySelectorAll("#signatureForm input, #signatureForm select");
  inputs.forEach((input) => {
    input.classList.remove("input-error");
  });

  // Verificar se todos os campos estão preenchidos
  let isValid = true;
  let firstInvalidField = null;

  if (!name) {
    document.getElementById("name").classList.add("input-error");
    isValid = false;
    if (!firstInvalidField) firstInvalidField = document.getElementById("name");
  }
  if (!sector) {
    document.getElementById("sector").classList.add("input-error");
    isValid = false;
    if (!firstInvalidField) firstInvalidField = document.getElementById("sector");
  }
  if (!sector1 || sector1 === "Escolha a Área") {
    document.getElementById("sector1").classList.add("input-error");
    isValid = false;
    if (!firstInvalidField) firstInvalidField = document.getElementById("sector1");
  }
  if (!tel) {
    document.getElementById("tel").classList.add("input-error");
    isValid = false;
    if (!firstInvalidField) firstInvalidField = document.getElementById("tel");
  }
  if (!room) {
    document.getElementById("room").classList.add("input-error");
    isValid = false;
    if (!firstInvalidField) firstInvalidField = document.getElementById("room");
  }

  if (!isValid) {
    showErrorModal();
    if (firstInvalidField) {
      firstInvalidField.focus();
    }
    return;
  }

  showLoadingModal();

  setTimeout(() => {
    // Preencher os dados no modal de visualização com o telefone formatado
    document.getElementById("modalName").textContent = name.toUpperCase();
    document.getElementById("modalSector").textContent = sector;
    document.getElementById("modalSector1").textContent = sector1;
    document.getElementById("modalPhone").textContent = `Telefone: ${formatPhoneNumber(tel)}`;
    document.getElementById("modalRoom").textContent = `Sala: ${room}`;

    document.getElementById("loadingMessage").style.display = "none";
    document.getElementById("successMessage").style.display = "block";

    setTimeout(() => {
      closeLoadingModal();
      showSignatureModal();
    }, 1000);
  }, 1500);
}

// Função para criar elemento de download otimizado
function createDownloadElement(name, sector, sector1, tel, room) {
  const downloadElement = document.createElement("div");
  downloadElement.className = "signature-download";

  downloadElement.innerHTML = `
    <div class="logo-section-download">
      <img class="gvn-download" src="./RH/src/img/colo.png" alt="logo" />
      <img class="gvn1-download" src="./RH/src/img/central.png" alt="logo2" />
    </div>

    <div class="content-section-download">
      <!-- Grupo 1: Nome e setores -->
      <div class="group-top">
        <div class="name-download">${name.toUpperCase()}</div>
        <div class="sector-download">${sector}</div>
        <div class="sector-download">${sector1}</div>
      </div>

      <!-- Grupo 2: Empresa e endereço -->
      <div class="group-middle">
        <div class="company-download strong">CENTRAL RJ</div>
        <div class="info-download-line">
          <span>Av. Nossa Senhora de Copacabana, 493</span>
          <span>Sala: ${room}</span>
        </div>
        <div class="info-download">Copacabana, Rio de Janeiro - RJ CEP: 22031-000</div>
      </div>

      <!-- Grupo 3: Telefone -->
      <div class="group-bottom">
        <div class="info-download">Tel: ${formatPhoneNumber(tel)}</div>
      </div>
    </div>
  `;

  return downloadElement;
}

// Função para baixar a assinatura como imagem
function downloadSignature() {
  showLoadingModal();

  // Pegar os dados do formulário
  const name = document.getElementById("name").value;
  const sector = document.getElementById("sector").value;
  const sector1 = document.getElementById("sector1").value;
  const tel = document.getElementById("tel").value;
  const room = document.getElementById("room").value;

  // Criar elemento específico para download
  const downloadElement = createDownloadElement(name, sector, sector1, tel, room);

  // Adicionar ao body (fora da tela)
  document.body.appendChild(downloadElement);

  // Configurações otimizadas para download
  const options = {
    scale: 2,
    useCORS: true,
    backgroundColor: "#ffffff",
    logging: false,
    allowTaint: true,
    letterRendering: true,
    width: 500,
    height: 140,
  };

  html2canvas(downloadElement, options)
    .then((canvas) => {
      // Remover elemento temporário
      document.body.removeChild(downloadElement);

      // Converter canvas para URL de dados
      const imgData = canvas.toDataURL("image/png");

      // Criar link para download
      const link = document.createElement("a");
      link.download = "assinatura-email.png";
      link.href = imgData;

      // Simular clique para iniciar download
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      // Mostrar mensagem de sucesso
      document.getElementById("loadingMessage").style.display = "none";
      document.getElementById("successMessage").style.display = "block";

      setTimeout(() => {
        closeLoadingModal();
      }, 1500);
    })
    .catch((error) => {
      // Remover elemento temporário em caso de erro
      if (document.body.contains(downloadElement)) {
        document.body.removeChild(downloadElement);
      }
      console.error("Erro ao gerar a assinatura:", error);
      closeLoadingModal();
      alert("Ocorreu um erro ao gerar a assinatura. Por favor, tente novamente.");
    });
}

// Função para mostrar o modal de erro
function showErrorModal() {
  const modal = document.getElementById("errorModal");
  modal.classList.add("show");
  modal.style.display = "flex";

  const progressBar = document.getElementById("progressBarFill");
  progressBar.style.width = "0";

  setTimeout(() => {
    progressBar.style.width = "100%";
  }, 10);

  setTimeout(() => {
    closeErrorModal();
  }, 5000);
}

// Função para fechar o modal de erro
function closeErrorModal() {
  const modal = document.getElementById("errorModal");
  modal.classList.remove("show");
  setTimeout(() => {
    modal.style.display = "none";
  }, 300);
}

// Função para mostrar o modal de carregamento
function showLoadingModal() {
  const modal = document.getElementById("loadingModal");
  document.getElementById("loadingMessage").style.display = "block";
  document.getElementById("successMessage").style.display = "none";
  modal.classList.add("show");
  modal.style.display = "flex";
}

// Função para fechar o modal de carregamento
function closeLoadingModal() {
  const modal = document.getElementById("loadingModal");
  modal.classList.remove("show");
  setTimeout(() => {
    modal.style.display = "none";
  }, 300);
}

// Função para mostrar o modal de assinatura
function showSignatureModal() {
  const modal = document.getElementById("signatureModal");
  modal.style.display = "flex";
  modal.classList.add("show");
}

// Adicionar eventos de tecla para fechar modais com ESC
document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeModal();
    closeErrorModal();
    closeLoadingModal();
  }
});

// Fechar modais ao clicar fora do conteúdo
document.addEventListener("click", (event) => {
  const signatureModal = document.getElementById("signatureModal");
  const errorModal = document.getElementById("errorModal");
  const loadingModal = document.getElementById("loadingModal");

  if (event.target === signatureModal) {
    closeModal();
  }

  if (event.target === errorModal) {
    closeErrorModal();
  }

  if (event.target === loadingModal && document.getElementById("successMessage").style.display === "block") {
    closeLoadingModal();
  }
});

// Impedir que o formulário seja enviado ao pressionar Enter
document.getElementById("signatureForm").addEventListener("keypress", (event) => {
  if (event.key === "Enter") {
    event.preventDefault();
    validateForm();
  }
});