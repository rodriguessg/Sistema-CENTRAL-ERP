// Função para alternar entre as abas
function showTab(tabName) {
  const loading = document.querySelector('.loading');
  loading.style.display = 'block'; // Exibe o ícone de loading

  // Esconde todos os conteúdos das abas
  const tabs = document.querySelectorAll(".form-container");
  tabs.forEach((tab) => {
      tab.style.display = "none";  // Remove a visibilidade de todas as abas
  });

  // Remove a classe 'active' de todas as abas
  const tabLinks = document.querySelectorAll(".tab");
  tabLinks.forEach((tab) => {
      tab.classList.remove("active"); // Remove 'active' de todas as abas
  });

  // Atualiza a aba ativa
  const activeTabLink = document.querySelector(`[data-tab="${tabName}"]`);
  const activeTabContent = document.getElementById(tabName);

  // Exibe a aba clicada com um pequeno delay para o carregamento
  setTimeout(() => {
      if (activeTabLink) {
          activeTabLink.classList.add("active"); // Adiciona 'active' à aba clicada
      }
      if (activeTabContent) {
          activeTabContent.style.display = "block"; // Exibe a aba ativa
      }

      // Esconde o loading após o delay
      loading.style.display = 'none';
  }, 1000); // Tempo de delay para simular carregamento suave
}

// Mostrar a aba 'cadastrar' como padrão ao carregar a página
window.addEventListener('load', function () {
  // Garantir que todas as abas estejam escondidas no início
  const tabs = document.querySelectorAll(".form-container");
  tabs.forEach((tab) => tab.style.display = "none");

  // Exibir a aba 'cadastrar' após o carregamento
  showTab("cadastrar"); // Exibe a aba de cadastro como padrão
});
