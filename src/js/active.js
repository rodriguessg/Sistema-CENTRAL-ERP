  // Função para alternar entre as abas
  function showTab(tabName) {
    // Esconder todas as abas do tipo form-container e form-container2
    const tabs = document.querySelectorAll('.form-container, .form-container3');
    tabs.forEach(tab => tab.style.display = 'none');

    // Exibir a aba selecionada (form-container ou form-container2)
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = 'block';
    }

    // Atualizar o estilo das abas para mostrar qual está ativa
    const tabLinks = document.querySelectorAll('.tab');
    tabLinks.forEach(tab => tab.classList.remove('active'));
    const activeTabLink = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeTabLink) {
        activeTabLink.classList.add('active');
    }
    }


    // Mostrar a aba 'cadastrar' como padrão quando a página for carregada
    window.onload = function() {
        showTab('cadastrar');
    };

