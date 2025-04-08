   // Função chamada quando o botão "Editar processo" for clicado
   function editProcess(event, contractData) {
    // Impede que o evento de clique se propague
    event.stopPropagation();

    // Chama a função para abrir o modal e preencher as abas com os dados do contrato
    openEditModal(contractData);
}

// Função para abrir o modal de edição e preencher os campos com os dados do contrato
function openEditModal(contractData) {
    // Preenche a aba de Detalhes com os dados do contrato
    document.getElementById('contractTitulo').textContent = contractData.titulo;
    document.getElementById('contractDescricao').textContent = contractData.descricao;
    document.getElementById('contractValidade').textContent = contractData.validade;
    document.getElementById('contractSituacao').textContent = contractData.situacao;

    // Preenche a aba de Edição com os dados do contrato
    document.getElementById('editTitulo').value = contractData.titulo;
    document.getElementById('editDescricao').value = contractData.descricao;
    document.getElementById('editValidade').value = contractData.validade;
    document.getElementById('editSituacao').value = contractData.situacao;

    // Exibe o modal
    $('#editProcessModal').modal('show');
}

// Função para salvar as alterações
document.getElementById('editProcessForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Previne o envio normal do formulário

    // Coleta os dados do formulário
    var updatedData = {
        titulo: document.getElementById('editTitulo').value,
        descricao: document.getElementById('editDescricao').value,
        validade: document.getElementById('editValidade').value,
        situacao: document.getElementById('editSituacao').value
    };

    // Aqui você pode fazer uma requisição para salvar os dados atualizados no banco de dados
    // Exemplo com Fetch API:
    /*
    fetch('/path/to/your/api', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(updatedData),
    })
    .then(response => response.json())
    .then(data => {
        // Fechar o modal após salvar os dados
        $('#editProcessModal').modal('hide');
    })
    .catch((error) => {
        console.error('Erro:', error);
    });
    */

    // Fechar o modal após salvar os dados
    $('#editProcessModal').modal('hide');
});
function showResumoProcesso(data) {
    // Exibe a div do resumo do processo
    document.getElementById('consultar').style.display = 'none'; // Esconde a lista de contratos
    document.getElementById('resumo_processo').style.display = 'block'; // Exibe o resumo do processo

    // Preenche os detalhes do processo na div
    const processoDetalhes = document.getElementById('processoDetalhes');
    processoDetalhes.innerHTML = `
        <p><strong>ID:</strong> ${data.id}</p>
        <p><strong>Título:</strong> ${data.titulo}</p>
        <p><strong>Descrição:</strong> ${data.descricao}</p>
        <p><strong>Validade:</strong> ${data.validade}</p>
        <p><strong>Status:</strong> ${data.situacao}</p>
    `;
}
// Função para redirecionar para a aba "resumo_processo"
function redirectTo(tab) {
    // Altera a aba para "resumo_processo"
    showTab(tab);
}

// Função para exibir a aba específica
function showTab(tabName) {
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => {
        if (tab.dataset.tab === tabName) {
            tab.classList.add('active');  // Marca a aba como ativa
        } else {
            tab.classList.remove('active');
        }
    });
    // Adicione a lógica de exibição do conteúdo da aba se necessário
    console.log("Aba exibida: " + tabName);
}


function openModal(contrato) {
    document.getElementById('modalTituloContrato').innerText = contrato.titulo;
    document.getElementById('modalDescricao').innerText = contrato.descricao;
    document.getElementById('modalValidade').innerText = contrato.validade;
    document.getElementById('modalSEI').innerText = contrato.SEI;
    document.getElementById('modalGestor').innerText = contrato.gestor;
    document.getElementById('modalFiscais').innerText = contrato.fiscais;
    document.getElementById('modalValorContrato').innerText = contrato.valor_contrato;
    document.getElementById('modalNumParcelas').innerText = contrato.num_parcelas ? contrato.num_parcelas : 'N/A';

    var modal = new bootstrap.Modal(document.getElementById('modalContrato'));
    modal.show();
}

