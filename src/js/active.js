// Função para alternar entre as abas
function showTab(tabName) {
    const loading = document.querySelector('.loading');
    loading.style.display = 'block'; // Exibe o ícone de loading

    // Esconde todas as abas do tipo form-container
    const tabs = document.querySelectorAll('.form-container');
    tabs.forEach(tab => tab.style.display = 'none');

    // Atualizar o estilo das abas para mostrar qual está ativa
    const tabLinks = document.querySelectorAll('.tab');
    tabLinks.forEach(tab => tab.classList.remove('active')); // Remove 'active' de todas as abas
    const activeTabLink = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeTabLink) {
        activeTabLink.classList.add('active'); // Adiciona 'active' à aba clicada
    }

    // Exibir a aba selecionada após um pequeno delay para simular o carregamento
    setTimeout(() => {
        const selectedTab = document.getElementById(tabName);
        if (selectedTab) {
            selectedTab.style.display = 'block'; // Exibe a aba ativa
        }

        // Esconde o loading após o delay
        loading.style.display = 'none';
    }, 1000); // Tempo de delay para simular o carregamento (1 segundo)
}

// Mostrar a aba 'cadastrar' como padrão quando a página for carregada
window.onload = function() {
    showTab('cadastrar');
};
