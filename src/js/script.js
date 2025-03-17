// Função para alternar entre as abas
function openTab(evt, tabName) {
    var i, tabContent, tabLinks;

    // Oculta todos os conteúdos de aba
    tabContent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
        tabContent[i].classList.remove("active");
    }

    // Remove a classe "active" de todos os botões
    tabLinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
    }

    // Mostra o conteúdo da aba atual e adiciona a classe "active" ao botão atual
    document.getElementById(tabName).style.display = "block";
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}
//modal
