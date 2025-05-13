document.addEventListener("DOMContentLoaded", function () {
    const loadingContainer = document.querySelector('.loading-container');
    const modal = document.querySelector('.modal-container');

    // Recupera o setor do usuário armazenado no localStorage (ou sessionStorage, conforme necessidade)
    const setor = localStorage.getItem('setor'); // Aqui você pode usar sessionStorage se preferir

    // Adicionando log para verificar o valor de 'setor'
    console.log("Valor do setor:", setor); // Log para ver o valor real de 'setor'

    // Exibe o container de loading
    loadingContainer.style.display = 'flex';

    // Função para verificar o setor e redirecionar
    function checkSetorAndRedirect() {
        // Verifica se o setor está definido e é válido
        if (!setor || setor.trim() === '') {
            console.error("Setor não definido ou vazio");
            loadingContainer.style.display = 'none'; // Esconde a tela de loading
            window.location.href = "mensagem.php?mensagem=setor_nao_reconhecido&pagina=index.php"; // Redireciona para página de erro
            return;
        }

        // Simula o tempo de carregamento com um timer de 3 segundos
        setTimeout(() => {
            loadingContainer.style.display = 'none'; // Esconde a tela de loading

            // Verificação do setor e redirecionamento adequado
            switch (setor.trim().toLowerCase()) {
                case 'administrador':
                    localStorage.setItem("administrador", setor);
                    console.log("Redirecionando para painel.php (Administrador)");
                    window.location.href = "painel.php";
                    break;
                case 'patrimonio':
                    localStorage.setItem("patrimonio", setor);
                    console.log("Redirecionando para painelpatrimonio.php (Patrimônio)");
                    window.location.href = "painelpatrimonio.php";
                    break;
                case 'financeiro':
                    localStorage.setItem("financeiro", setor);
                    console.log("Redirecionando para homefinanceiro.php (Financeiro)");
                    window.location.href = "homefinanceiro.php";
                    break;
                case 'estoque':
                    localStorage.setItem("estoque", setor);
                    console.log("Redirecionando para homeestoque.php (Estoque)");
                    window.location.href = "homeestoque.php";
                    break;
                case 'recursos_humanos':
                    localStorage.setItem("recursos_humanos", setor);
                    console.log("Redirecionando para RH.php (Recursos Humanos)");
                    window.location.href = "RH.php";
                    break;
                case 'contratos':
                    localStorage.setItem("contratos", setor);
                    console.log("Redirecionando para loading.php (Contratos)");
                    window.location.href = "loading.php?painelcontratos.php";
                    break;
                case 'helpdesk':
                    localStorage.setItem("tecnico", setor);
                    console.log("Redirecionando para hometech.php (Helpdesk)");
                    window.location.href = "hometech.php";
                    break;
                default:
                    console.error(`Setor desconhecido: ${setor}`);
                    window.location.href = "mensagem.php?mensagem=setor_nao_reconhecido&pagina=index.php"; // Redireciona para página de erro
                    break;
            }
        }, 3000); // Espera 3 segundos antes de redirecionar
    }

    // Chama a função para verificar o setor e redirecionar
    checkSetorAndRedirect();
});

// Função para fechar o modal (se necessário)
function closeModal() {
    document.querySelector('.modal-container').style.display = 'none';
}

// Ação para um botão no modal (se existir)
function submitForm() {
    alert('Ação enviada!');
}
