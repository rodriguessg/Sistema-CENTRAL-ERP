// Valida o formulário para garantir que todos os campos estão preenchidos
function validateForm() {
  const name = document.getElementById("name").value.trim();
  const sector = document.getElementById("sector").value.trim();
  const email = document.getElementById("email").value.trim();
  const tel = document.getElementById("tel").value.trim();
  const room = document.getElementById("room").value.trim();

  if (!name || !sector || !email || !tel || !room) {
    showErrorModal(); // Exibe o modal de erro se algum campo estiver vazio
  } else {
    generateSignature(); // Cria a assinatura se todos os campos estiverem preenchidos
  }
}

// Exibe o modal de erro com a barra de progresso de 5 segundos
function showErrorModal() {
  const errorModal = document.getElementById("errorModal");
  const progressBarFill = document.getElementById("progressBarFill");

  // Exibe o modal de erro e inicia a barra de progresso
  errorModal.style.display = "flex";
  progressBarFill.style.width = "0"; // Reseta a barra de progresso para 0%

  // Anima a barra de progresso até 100% em 5 segundos
  setTimeout(() => {
    progressBarFill.style.width = "100%";
  }, 10);

  // Fecha o modal de erro automaticamente após 5 segundos
  setTimeout(closeErrorModal, 5000); // Agora o modal de erro fecha após 5 segundos
}

// Fecha o modal de erro e reseta a barra de progresso
function closeErrorModal() {
  document.getElementById("errorModal").style.display = "none";
  
  // Verifica cada campo e aplica/remova a classe de erro
  const fields = ["name", "sector", "email", "tel", "room"];
  fields.forEach((fieldId) => {
    const field = document.getElementById(fieldId);
    if (!field.value.trim()) {
      field.classList.add("input-error"); // Adiciona a classe de erro se o campo estiver vazio
    } else {
      field.classList.remove("input-error"); // Remove a classe se o campo foi preenchido
    }
  });

  // Reseta a barra de progresso para 0%
  const progressBarFill = document.getElementById("progressBarFill");
  progressBarFill.style.width = "0"; // Reseta a barra para 0% para reutilização
}

// Gera a assinatura e exibe no modal
function generateSignature() {
  const name = document.getElementById("name").value;
  const sector = document.getElementById("sector").value;
  const sector1 = document.getElementById("sector1").value;
  const email = document.getElementById("email").value;
  const tel = document.getElementById("tel").value;
  const room = document.getElementById("room").value;

  // Atualiza os valores no layout do modal
  document.getElementById("modalName").textContent = name;
  document.getElementById("modalSector").textContent = sector;
  document.getElementById("modalSector1").textContent = sector1;
  document.getElementById("modalEmail").textContent = "Email: " + email;
  document.getElementById("modalPhone").textContent = "Telefone: " + tel;
  document.getElementById("modalRoom").textContent = "Sala: " + room;

  // Exibe o modal de assinatura
  document.getElementById("signatureModal").style.display = "block";
}

// Fecha o modal de assinatura
function closeModal() {
  document.getElementById("signatureModal").style.display = "none";
}

// Baixa a assinatura gerada
function downloadSignature() {
  showLoadingModal(); // Mostra o modal de carregamento

  setTimeout(() => {
    closeLoadingModal(); // Fecha o modal de carregamento após 3 segundos
    showSuccessModal(); // Mostra o modal de sucesso

    const signatureElement = document.getElementById("signatureLayout");

    // Obtém as dimensões do elemento signatureLayout
    const width = signatureElement.offsetWidth;
    const height = signatureElement.offsetHeight;

    // Define a escala baseada nas dimensões desejadas (700x350)
    const desiredWidth = 900;
    const desiredHeight = 350;

    const scaleX = desiredWidth / width;
    const scaleY = desiredHeight / height;

    const scale = Math.min(scaleX, scaleY); // Escolhe o menor fator de escala para manter a proporção

    // Renderiza a assinatura com a escala calculada
    html2canvas(signatureElement, { scale: scale }).then((canvas) => {
      const link = document.createElement("a");
      link.href = canvas.toDataURL("image/png", 1.0); // Gera a imagem com 100% de qualidade
      link.download = "assinatura.png";
      link.click();

      // Fechar o modal de sucesso, limpar o formulário e recarregar a página
      setTimeout(() => {
        closeLoadingModal();
        document.getElementById("signatureForm").reset(); // Limpa o formulário
        location.reload(); // Recarrega a página
      }, 2000); // Aguarda 2 segundos antes de fechar e limpar
    });
  }, 3000);
}

// Funções auxiliares
function showLoadingModal() {
  document.getElementById("loadingModal").style.display = "flex";
  document.getElementById("loadingMessage").style.display = "block";
  document.getElementById("successMessage").style.display = "none";
}

function closeLoadingModal() {
  document.getElementById("loadingModal").style.display = "none";
}

function showSuccessModal() {
  document.getElementById("loadingModal").style.display = "flex";
  document.getElementById("loadingMessage").style.display = "none";
  document.getElementById("successMessage").style.display = "block";
}
