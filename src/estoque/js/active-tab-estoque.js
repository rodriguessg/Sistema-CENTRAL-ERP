// Função para alternar entre as abas
function showTab(tabName) {
  carregarLinhaDoTempo(); // Atualiza a lista após o fechamento
  // Esconder todas as abas do tipo form-container
  const tabs = document.querySelectorAll(".form-container");
  tabs.forEach((tab) => (tab.style.display = "none"));

  // Exibir a aba selecionada
  const selectedTab = document.getElementById(tabName);
  if (selectedTab) {
    selectedTab.style.display = "block"; // Exibe a aba ativa
  }

  // Atualizar o estilo das abas para mostrar qual está ativa
  const tabLinks = document.querySelectorAll(".tab");
  tabLinks.forEach((tab) => tab.classList.remove("active")); // Remove 'active' de todas as abas
  const activeTabLink = document.querySelector(`[data-tab="${tabName}"]`);
  if (activeTabLink) {
    activeTabLink.classList.add("active"); // Adiciona 'active' à aba clicada
  }
}

// Mostrar a aba 'cadastrar' como padrão quando a página for carregada
window.onload = function () {
  showTab("cadastrar"); // Exibe a aba de cadastro ao carregar
};
