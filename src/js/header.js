// Abrir o menu lateral
function openNav() {
    document.getElementById("mySidebar").style.left = "0";
    document.querySelector('.header').style.paddingLeft = '270px';
}

// Fechar o menu lateral
function closeNav() {
    document.getElementById("mySidebar").style.left = "-250px";
    document.querySelector('.header').style.paddingLeft = '0';
}

// Adiciona o efeito de hover e destaque no item do menu
const menuItems = document.querySelectorAll('.menu-item');
menuItems.forEach(item => {
    item.addEventListener('click', () => {
        menuItems.forEach(i => i.classList.remove('menu-item-active'));
        item.classList.add('menu-item-active');
    });
});


// Função para carregar os dados no modal de perfil
$(document).ready(function() {
    $('#perfilModal').on('show.bs.modal', function (e) {
        // Aqui você pode fazer uma requisição AJAX para pegar os dados do usuário, se necessário
        var usuario = {
            nome: "João da Silva",
            email: "joao@example.com",
            setor: "TI",
            tempoRegistro: "2 anos",
            movimentacoes: "120"
        };

        // Preencher o modal com as informações do usuário
        $('#modal-email').text(usuario.email);
        $('#modal-setor').text(usuario.setor);
        $('#modal-tempo-registro').text(usuario.tempoRegistro);
        $('#modal-movimentacoes').text(usuario.movimentacoes);
    });

    // Exemplo para abrir o modal de perfil ao clicar no nome de usuário
    $('.sidebar-header h4').on('click', function() {
        $('#perfilModal').modal('show');
    });
});


