  const MAX_ADITIVOS = 5;
        const SALVAR_CONTRATO_URL = './salvar_edicao_contrato.php';
        const GET_CONTRATOS_URL = './get_contratos.php';
        let aditivoCount = 0;

        // Função para formatar data
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('pt-BR');
        }

        // Função para determinar estilo da validade
        function getValidadeStyle(validade) {
            const today = new Date();
            const validadeDate = new Date(validade);
            const oneMonthLater = new Date(today);
            oneMonthLater.setMonth(today.getMonth() + 1);

            if (validadeDate < today) {
                return { class: 'expired', color: 'red', icon: 'fa-times-circle' };
            } else if (validadeDate <= oneMonthLater) {
                return { class: 'approaching', color: 'orange', icon: 'fa-exclamation-circle' };
            }
            return { class: 'valid', color: 'green', icon: 'fa-check-circle' };
        }
function getSituacaoStyle(situacao) {
    let style = {};

    if (situacao === 'Ativo') {
        style = { 
            class: 'Ativo', 
            color: 'green', 
            icon: 'fa-arrow-up',
            background: 'lightgreen', 
            borderRadius: '15px',
            padding: '5px 10px',
            display: 'inline-block' // Ajusta o fundo para o tamanho do texto
        };
    } else if (situacao === 'Renovado') {
        style = { 
            class: 'Renovado', 
            color: 'blue', 
            icon: 'fa-sync-alt',
            background: 'lightblue', 
            borderRadius: '15px',
            padding: '5px 10px',
            display: 'inline-block'
        };
    } else if (situacao === 'Inativo') {
        style = { 
            class: 'Inativo', 
            color: 'red', 
            icon: 'fa-arrow-down',
            background: 'lightcoral', 
            borderRadius: '15px',
            padding: '5px 10px',
            display: 'inline-block'
        };
    } else if (situacao === 'Encerrado') {
        style = { 
            class: 'Encerrado', 
            color: 'darkred', 
            icon: 'fa-ban',
            background: 'lightgray', 
            borderRadius: '15px',
            padding: '5px 10px',
            display: 'inline-block'
        };
    } else {
        style = { 
            class: 'Desconhecido', 
            color: 'gray', 
            icon: 'fa-question-circle',
            background: 'lightgray', 
            borderRadius: '15px',
            padding: '5px 10px',
            display: 'inline-block'
        };
    }

    // Retorna o estilo com a configuração para ajustar o fundo ao tamanho do texto
    return style;
}




        // Função para carregar contratos
        function searchContracts() {
            const searchInput = document.getElementById('searchInput').value;
            const statusSelect = document.getElementById('statusSelect').value;
            const tableBody = document.getElementById('contractTableBody');
            tableBody.innerHTML = '<tr><td colspan="6">Carregando...</td></tr>';

            fetch(`${GET_CONTRATOS_URL}?search=${encodeURIComponent(searchInput)}&situacao=${encodeURIComponent(statusSelect)}`)
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`Erro HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    //EXIBI NO JASON ASSO, QUE ABRE O SISTEMA
                    // console.log('Resposta do servidor (get_contratos):', data);
                    tableBody.innerHTML = '';
                    if (data.success && data.contratos.length > 0) {
                        data.contratos.forEach(contrato => {
                            const validadeStyle = getValidadeStyle(contrato.validade);
                            const situacaoStyle = getSituacaoStyle(contrato.situacao);
                            const row = document.createElement('tr');
                            row.innerHTML = `
<td class="truncated-text" title="${contrato.id}">${contrato.id}</td>
<td class="truncated-text" title="${contrato.titulo}">${contrato.titulo}</td>
<td class="truncated-text" title="${contrato.descricao}">${contrato.descricao}</td>
<td class="${validadeStyle.class}" style="color: ${validadeStyle.color}">
    <i class="fas ${validadeStyle.icon}"></i> ${formatDate(contrato.validade)}
</td>
<td class="${situacaoStyle.class}" 
    style="color: ${situacaoStyle.color}; 
           background-color: ${situacaoStyle.background}; 
           border-radius: ${situacaoStyle.borderRadius}; 
           padding: ${situacaoStyle.padding};
           display: inline-flex;
           justify-content: center;
           align-items: center;
           margin-top: 13px;
           font-size: 12px;
           border: solid 1px ${situacaoStyle.color};">
    <i class="fas ${situacaoStyle.icon}"></i> ${contrato.situacao}
</td>
<td>
    <button class="btn btn-info btn-sm" onclick='openModal(${JSON.stringify(contrato)}); event.stopPropagation()' title="Visualizar">
        <i class="fas fa-eye"></i>
    </button>
    <button class="btn btn-primary btn-sm" onclick='showResumoProcesso(${JSON.stringify(contrato)}); event.stopPropagation()' title="Relatório">
        <i class="fas fa-file-alt"></i>
    </button>
    <button class="btn btn-warning btn-sm" onclick='editacontrato(${JSON.stringify(contrato)})' title="Editar contrato">
        <i class="fas fa-pen"></i>
    </button>
</td>


                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="6">Nenhum contrato encontrado.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar contratos:', error);
                    tableBody.innerHTML = `<tr><td colspan="6">Erro ao carregar contratos: ${error.message}</td></tr>`;
                });
        }

        // Função para abrir o modal de visualização
        function openModal(contrato) {
            try {
                const modalElement = document.getElementById('modalContrato');
                if (!modalElement) throw new Error('Modal de visualização não encontrado.');

                document.getElementById('modalTituloContrato').textContent = contrato.titulo || 'N/A';
                document.getElementById('modalDescricao').textContent = contrato.descricao || 'N/A';
                document.getElementById('modalValidade').textContent = contrato.validade ? formatDate(contrato.validade) : 'N/A';
                document.getElementById('modalSEI').textContent = contrato.SEI || 'N/A';
                document.getElementById('modalGestor').textContent = contrato.gestor || 'N/A';
                document.getElementById('modalFiscais').textContent = contrato.fiscais || 'N/A';
                document.getElementById('modalValorContrato').textContent = contrato.valor_contrato
                    ? parseFloat(contrato.valor_contrato).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
                    : 'R$ 0,00';
                document.getElementById('modalNumParcelas').textContent = contrato.num_parcelas || 'N/A';
                document.getElementById('modalValorAditivo').textContent = contrato.valores_aditivos && contrato.valores_aditivos.length
                    ? contrato.valores_aditivos.map(v => parseFloat(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })).join(', ')
                    : 'R$ 0,00';

                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } catch (error) {
                console.error('Erro ao abrir modal de visualização:', error);
                alert('Erro ao abrir modal: ' + error.message);
            }
        }

        // Função para abrir o modal de edição
function editacontrato(dados) {
    console.log('Dados recebidos para edição:', dados); // Verifica os dados que estão sendo passados

    try {
        const modalElement = document.getElementById('modalEditContrato');
        if (!modalElement) throw new Error('Modal de edição não encontrado.');

        // Preenche os campos do formulário com os dados do contrato
        document.getElementById('id_contrato').value = dados.id || '';
        document.getElementById('titulo').value = dados.titulo || '';
        document.getElementById('validade').value = dados.validade || '';
        document.getElementById('situacao').value = dados.situacao || 'Ativo';
        document.getElementById('descricao').value = dados.descricao || '';

        // Limpa os campos de aditivo antes de adicionar novos
        const container = document.getElementById('aditivos-container');
        container.innerHTML = '';
        aditivoCount = 0;

        // Verifica se há valores de aditivos e os adiciona ao formulário
        if (dados.valores_aditivos && Array.isArray(dados.valores_aditivos)) {
            dados.valores_aditivos.forEach(valor => addAditivo(valor));
        } else {
            addAditivo();  // Se não houver valores de aditivo, adiciona um campo vazio
        }

        // Exibe o modal de edição
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } catch (error) {
        console.error('Erro ao preencher modal de edição:', error);
        alert('Erro ao editar contrato: ' + error.message);
    }
}

        // Função para adicionar campos de valor aditivo
        function addAditivo(valor = '') {
             if (aditivoCount >= MAX_ADITIVOS) {
        alert(`Limite de ${MAX_ADITIVOS} aditivos atingido.`);
        return;
    }

    aditivoCount++;
    const container = document.getElementById('aditivos-container');
    const input = document.createElement('input');
    input.type = 'number';
    input.step = '0.01';
    input.min = '0';
    input.name = `valor_aditivo${aditivoCount}`;
    input.className = 'form-control mb-1';
    input.placeholder = `Valor Aditivo ${aditivoCount}`;
    input.value = valor;
    container.appendChild(input);

    // ✅ Altera a situação para "Renovado"
    const situacaoSelect = document.getElementById('situacao');
    if (situacaoSelect) {
        situacaoSelect.value = 'Renovado';
    }
}

        // Função para abrir o modal de filtro (placeholder)
        function openFilterModal() {
            const modal = new bootstrap.Modal(document.getElementById('filterModal'));
            modal.show();
        }

        // Função placeholder para relatório
        function showResumoProcesso(contrato) {
            alert('Funcionalidade de relatório a ser implementada para o contrato: ' + contrato.titulo);
        }

        // Envio do formulário
        document.getElementById('formEditContrato').addEventListener('submit', function (e) {
            e.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Salvando...';

            const formData = new FormData(this);
            fetch(SALVAR_CONTRATO_URL, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`Erro HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(result => {
                    console.log('Resposta do servidor (salvar_contrato):', result);
                    if (result.success) {
                        alert(result.message || 'Contrato salvo com sucesso!');
                        searchContracts(); // Recarregar tabela
                        bootstrap.Modal.getInstance(document.getElementById('modalEditContrato')).hide();
                    } else {
                        throw new Error(result.message || 'Erro desconhecido.');
                    }
                })
                .catch(error => {
                    console.error('Erro ao salvar contrato:', error);
                    alert(`Erro ao salvar contrato: ${error.message}`);
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Salvar';
                      location.reload()
                });
        });

        // Carregar contratos ao iniciar
        searchContracts();
      