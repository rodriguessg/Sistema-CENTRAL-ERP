let aditivosCount = 0;  // Controla quantos aditivos foram adicionados

// Função para abrir o modal de visualização de detalhes do contrato
function openModal(contrato) {
    try {
        // Preencher os campos do modal com os dados do contrato
        document.getElementById('modalTituloContrato').textContent = contrato.titulo || 'N/A';
        document.getElementById('modalDescricao').textContent = contrato.descricao || 'N/A';
        document.getElementById('modalValidade').textContent = contrato.validade || 'N/A';
        document.getElementById('modalSEI').textContent = contrato.SEI || 'N/A';
        document.getElementById('modalGestor').textContent = contrato.gestor || 'N/A';
        document.getElementById('modalFiscais').textContent = contrato.fiscais || 'N/A';
        document.getElementById('modalValorContrato').textContent = contrato.valor_contrato ? 
            parseFloat(contrato.valor_contrato).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : 'R$ 0,00';
        document.getElementById('modalNumParcelas').textContent = contrato.num_parcelas || 'N/A';

        // Exibir o total de aditivos no modal de detalhes
        document.getElementById('modalValorAditivo').textContent = contrato.valor_aditivo ? 
            parseFloat(contrato.valor_aditivo).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) : 'R$ 0,00';

        // Abrir o modal usando Bootstrap 5
        const modal = new bootstrap.Modal(document.getElementById('modalContrato'));
        modal.show();
    } catch (error) {
        console.error('Erro ao abrir modal de visualização:', error);
        alert('Erro ao abrir modal: ' + error.message);
    }
}

// Função para abrir o modal de edição de contrato
function openModalContrato(contractData) {
    try {
        // Preencher os campos na aba de detalhes
        document.getElementById('contractTitulo').textContent = contractData.titulo || 'N/A';
        document.getElementById('contractDescricao').textContent = contractData.descricao || 'N/A';
        document.getElementById('contractValidade').textContent = contractData.validade || 'N/A';
        document.getElementById('contractSituacao').textContent = contractData.situacao || 'N/A';

        // Preencher os campos no formulário de edição
        document.getElementById('editTitulo').value = contractData.titulo || '';
        document.getElementById('editDescricao').value = contractData.descricao || '';
        document.getElementById('editValidade').value = contractData.validade || '';
        document.getElementById('editSituacao').value = contractData.situacao || '';

        // Preencher os aditivos (se já existirem)
        if (contractData.aditivos) {
            aditivosCount = contractData.aditivos.length;
            contractData.aditivos.forEach((aditivo, index) => {
                addAditivoField(aditivo, index + 1);
            });
        }

        // **IMPORTANTE**: Não chamar `showTab('gerenciar')` aqui, para evitar que a aba seja alterada quando abrir o modal de edição.
        // Abrir o modal de edição usando Bootstrap 5
        const modal = new bootstrap.Modal(document.getElementById('editContractModal'));
        modal.show();
    } catch (error) {
        console.error('Erro ao abrir modal de edição:', error);
        alert('Erro ao abrir modal de edição: ' + error.message);
    }
}

// Função para adicionar um novo campo de aditivo
function addAditivoField(value = '', aditivoNumber = aditivosCount + 1) {
    if (aditivosCount < 5) {  // Limitar a 5 aditivos
        aditivosCount++;
        const container = document.getElementById('aditivosContainer');
        const aditivoInput = document.createElement('div');
        aditivoInput.classList.add('form-group');
        aditivoInput.innerHTML = `
            <label for="editAditivo${aditivosCount}">Valor Aditivo ${aditivosCount}</label>
            <input type="number" class="form-control form-control-sm" id="editAditivo${aditivosCount}" name="aditivo${aditivosCount}" value="${value}" step="0.01" required>
        `;
        container.appendChild(aditivoInput);
        updateModalValorAditivo();  // Atualiza o total de aditivos no modal
    } else {
        alert('Máximo de 5 aditivos alcançado');
    }
}

// Função para atualizar o valor total dos aditivos no modal de detalhes
function updateModalValorAditivo() {
    let totalAditivos = 0;
    for (let i = 1; i <= aditivosCount; i++) {
        const aditivoValue = parseFloat(document.getElementById(`editAditivo${i}`).value) || 0;
        totalAditivos += aditivoValue;
    }
    document.getElementById('modalValorAditivo').textContent = totalAditivos.toFixed(2);
}

// Função para salvar as alterações do contrato
document.getElementById('editContractForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const form = e.target;
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const updatedContractData = {
        titulo: document.getElementById('editTitulo').value,
        descricao: document.getElementById('editDescricao').value,
        validade: document.getElementById('editValidade').value,
        situacao: document.getElementById('editSituacao').value,
        aditivos: []  // Array para armazenar os valores dos aditivos
    };

    // Coletando os valores dos aditivos
    for (let i = 1; i <= aditivosCount; i++) {
        const aditivoValue = parseFloat(document.getElementById(`editAditivo${i}`).value) || 0;
        updatedContractData.aditivos.push(aditivoValue);
    }

    try {
        const response = await fetch('./salvar_edicao_contrato.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(updatedContractData)
        });

        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }

        const result = await response.json();
        if (result.success) {
            alert('Contrato atualizado com sucesso!');
            // Fechar o modal de edição
            const modal = bootstrap.Modal.getInstance(document.getElementById('editContractModal'));
            modal.hide();
        } else {
            alert('Erro ao atualizar contrato: ' + result.message);
        }
    } catch (error) {
        console.error('Erro ao salvar contrato:', error);
        alert('Erro ao salvar contrato: ' + error.message);
    }
});

// Função para redirecionar para uma aba específica
function redirectTo(tab) {
    showTab(tab); // Chama a função para exibir a aba
}

// Função para exibir a aba específica
function showTab(tabName) {
    try {
        const tabs = document.querySelectorAll('.tab');
        const contents = document.querySelectorAll('.form-container'); // Assume que as abas têm essa classe

        // Atualizar as abas
        tabs.forEach(tab => {
            tab.classList.toggle('active', tab.dataset.tab === tabName);
        });

        // Exibir o conteúdo correspondente
        contents.forEach(content => {
            content.style.display = content.id === tabName ? 'block' : 'none';
        });

        console.log(`Aba exibida: ${tabName}`);
    } catch (error) {
        console.error('Erro ao exibir aba:', error);
    }
}
