// Contador global para índices únicos
let editableRowCounter = 0;

// Função para exibir o resumo do processo e abrir a aba Gerenciar Contratos
async function showResumoProcesso(rowData) {
    const contractData = typeof rowData === 'string' ? JSON.parse(rowData) : rowData;
    console.log('Abrindo resumo para contrato:', contractData);
    showTab('gerenciar');
    await loadContractsAndPayments(contractData);
}

// Função para carregar os dados do contrato e os pagamentos anteriores
async function loadContractsAndPayments(contractData) {
    const tbody = document.getElementById('contratosTableBody');
    tbody.innerHTML = ''; // Limpar tabela
    editableRowCounter = 0; // Resetar contador

    // Atualizar o título com o contrato_titulo
    const contractTitleHeader = document.getElementById('contractTitleHeader');
    const titulo = contractData.titulo || 'Desconhecido';
    const sei = contractData.SEI || 'N/A';
    const agencia_bancaria = contractData.agencia_bancaria || 'N/A';

    // Link de pesquisa no SEI
    const seiLink = sei !== 'N/A' 
        ? `<a href="https://sei.rj.gov.br/sei/controlador_externo.php?acao=procedimento_trabalhar&acao_origem=procedimento_pesquisar&id_procedimento=${encodeURIComponent(sei)}" target="_blank" rel="noopener noreferrer" title="Link de acesso direto ao processo SEI">SEI: ${sei}</a>`
        : 'SEI: N/A';

    contractTitleHeader.innerHTML = `Pagamentos do contrato ${titulo} (${seiLink}) Conta Bancária ${agencia_bancaria}`;

    try {
        // Buscar num_parcelas e data_inicio do contrato
        const contractResponse = await fetch(`./get_contract_details.php?titulo=${encodeURIComponent(contractData.titulo)}`);
        if (!contractResponse.ok) throw new Error('Erro ao carregar detalhes do contrato');
        const contractDetails = await contractResponse.json();
        const numParcelas = contractDetails.num_parcelas || 1;
        const dataInicio = contractDetails.data_inicio ? new Date(contractDetails.data_inicio) : new Date();
        const valorContrato = contractDetails.valor_contrato || contractData.valor_contrato || 0;

        // Carregar pagamentos anteriores
        const paymentResponse = await fetch(`./get_payment.php?contrato_titulo=${encodeURIComponent(contractData.titulo)}`);
        if (!paymentResponse.ok) throw new Error('Erro ao carregar pagamentos');
        const payments = await paymentResponse.json();
        console.log('Pagamentos carregados:', payments);

        // Exibir pagamentos anteriores como linhas não editáveis
        payments.forEach(payment => {
            const isSubRow = !!payment.fonte_adicional;
            const tr = document.createElement('tr');
            tr.classList.add('read-only');
            if (isSubRow) tr.classList.add('sub-row');
            tr.dataset.paymentId = payment.id; // Armazenar ID para edição
            tr.dataset.paymentData = JSON.stringify(payment); // Armazenar dados para edição
            tr.innerHTML = `
                <td>${payment.mes || ''}</td>
                <td>${payment.empenho || ''}</td>
                <td>${payment.tipo || ''}</td>
                <td>${payment.nota_empenho || ''}</td>
                <td>${payment.valor_contrato || 0}</td>
                <td>${payment.creditos_ativos || ''}</td>
                <td>${isSubRow ? '↳ ' : ''}${payment.fonte || ''}${isSubRow ? ` (Fonte ${payment.fonte_adicional})` : ''}</td>
                <td>${payment.SEI || ''}</td>
                <td>${payment.nota_fiscal || ''}</td>
                <td>${payment.envio_pagamento || ''}</td>
                <td>${payment.vencimento_fatura || ''}</td>
                <td>${payment.valor_liquidado || 0}</td>
                <td>${payment.valor_liquidado_ag || 0}</td>
                <td>${payment.ordem_bancaria || ''}</td>
                <td>${payment.agencia_bancaria || ''}</td>
                <td>${payment.data_atualizacao || ''}</td>
                <td>${payment.data_pagamento || ''}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editPayment(${payment.id}, this)" title="Editar">
                        <i class="bi bi-pencil"></i> Editar
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deletePayment(${payment.id}, this)" title="Excluir">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                    ${!isSubRow ? `
                        <button class="btn btn-success btn-sm" onclick="addSource(${payment.id}, this)" title="Adicionar Fonte">
                            <i class="bi bi-plus-circle"></i> Adicionar Fonte
                        </button>
                    ` : ''}
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Gerar meses para as parcelas
        const mesesPagos = payments
            .filter(p => !p.fonte_adicional) // Considerar apenas pagamentos principais
            .map(p => p.mes); // Meses já salvos como pagamentos principais
        const mesesParcelas = [];
        for (let i = 0; i < numParcelas; i++) {
            const dataParcela = new Date(dataInicio);
            dataParcela.setMonth(dataInicio.getMonth() + i);
            const mesFormatado = `${String(dataParcela.getMonth() + 1).padStart(2, '0')}/${dataParcela.getFullYear()}`;
            if (!mesesPagos.includes(mesFormatado)) {
                mesesParcelas.push(mesFormatado);
            }
        }

        // Adicionar linhas editáveis para cada parcela restante
        mesesParcelas.forEach((mes) => {
            const trEditable = document.createElement('tr');
            trEditable.classList.add('editable');
            trEditable.dataset.rowIndex = editableRowCounter; // Armazenar índice único
            const valorParcela = valorContrato / numParcelas; // Dividir igualmente
            trEditable.innerHTML = `
                <td><input type="text" value="${mes}" class="form-control form-control-sm" data-key="mes" readonly></td>
                <td><input type="text" value="${contractData.empenho || ''}" class="form-control form-control-sm" data-key="empenho"></td>
                <td><input type="text" value="${contractData.tipo || ''}" class="form-control form-control-sm" data-key="tipo"></td>
                <td><input type="text" value="${contractData.nota_empenho || ''}" class="form-control form-control-sm" data-key="nota_empenho"></td>
                <td><input type="number" step="0.01" value="${valorParcela.toFixed(2)}" class="form-control form-control-sm" data-key="valor_contrato" readonly></td>
                <td><input type="text" value="${contractData.creditos_ativos || ''}" class="form-control form-control-sm" data-key="creditos_ativos"></td>
                <td><input type="text" value="${contractData.fonte || ''}" class="form-control form-control-sm" data-key="fonte"></td>
                <td><input type="text" value="${contractData.SEI || ''}" class="form-control form-control-sm" data-key="SEI"></td>
                <td><input type="text" value="${contractData.nota_fiscal || ''}" class="form-control form-control-sm" data-key="nota_fiscal"></td>
                <td><input type="text" value="${contractData.envio_pagamento || ''}" class="form-control form-control-sm" data-key="envio_pagamento"></td>
                <td><input type="date" value="${contractData.validade || ''}" class="form-control form-control-sm" data-key="vencimento_fatura"></td>
                <td><input type="number" step="0.01" value="${contractData.valor_liquidado || 0}" class="form-control form-control-sm" data-key="valor_liquidado"></td>
                <td><input type="number" step="0.01" value="${contractData.valor_liquidado_ag || 0}" class="form-control form-control-sm" data-key="valor_liquidado_ag"></td>
                <td><input type="text" value="${contractData.ordem_bancaria || ''}" class="form-control form-control-sm" data-key="ordem_bancaria"></td>
                <td><input type="text" value="${contractData.agencia_bancaria || ''}" class="form-control form-control-sm" data-key="agencia_bancaria"></td>
                <td><input type="date" value="${contractData.data_atualizacao || ''}" class="form-control form-control-sm" data-key="data_atualizacao"></td>
                <td><input type="date" value="${new Date().toISOString().split('T')[0]}" class="form-control form-control-sm" data-key="data_pagamento"></td>
                <td><button class="btn btn-primary btn-sm" onclick="saveSinglePayment(${editableRowCounter}, this)">
                    <i class="bi bi-save"></i> Salvar
                </button></td>
            `;
            tbody.appendChild(trEditable);
            editableRowCounter++;
        });

        // Armazenar o título do contrato para uso no salvamento
        tbody.dataset.contractTitle = contractData.titulo;
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
        alert('Erro ao carregar dados: ' + error.message);
    }
}

// Função para sanitizar strings para JSON
function sanitizeForJson(str) {
    if (typeof str !== 'string') return str;
    // Remover caracteres de controle, quebras de linha e emojis
    return str
        .replace(/[\x00-\x1F\x7F-\x9F]/g, '') // Caracteres de controle
        .replace(/[\u{1F000}-\u{1FFFF}]/gu, '') // Emojis
        .replace(/\n/g, ' ')
        .trim();
}

// Função para validar JSON
function isValidJson(data) {
    try {
        JSON.stringify(data);
        return true;
    } catch (e) {
        console.error('Erro ao validar JSON:', e.message);
        return false;
    }
}

// Função para salvar uma única linha de pagamento (nova ou edição)
async function saveSinglePayment(rowIndex, button) {
    const contractTitle = document.getElementById('contratosTableBody').dataset.contractTitle;
    const editableRows = document.querySelectorAll('#contratosTableBody .editable');
    const row = Array.from(editableRows).find(r => r.dataset.rowIndex == rowIndex);
    if (!row) {
        alert('Linha não encontrada.');
        return;
    }

    // Desativar botão para evitar múltiplos cliques
    button.disabled = true;
    button.textContent = 'Salvando...';

    const inputs = row.querySelectorAll('input');
    const paymentData = { contrato_titulo: sanitizeForJson(contractTitle) };

    const columns = [
        'mes', 'empenho', 'tipo', 'nota_empenho', 'valor_contrato', 'creditos_ativos', 'fonte',
        'SEI', 'nota_fiscal', 'envio_pagamento', 'vencimento_fatura', 'valor_liquidado',
        'valor_liquidado_ag', 'ordem_bancaria', 'agencia_bancaria', 'data_atualizacao', 'data_pagamento',
        'fonte_adicional'
    ];

    inputs.forEach(input => {
        const key = input.getAttribute('data-key');
        if (columns.includes(key)) {
            if (input.type === "number") {
                paymentData[key] = parseFloat(input.value) || 0;
            } else if (input.type === "date") {
                paymentData[key] = input.value || null;
            } else {
                paymentData[key] = sanitizeForJson(input.value) || null;
            }
        }
    });

    // Validações de campos obrigatórios
    if (!paymentData.mes) {
        alert('O campo Mês é obrigatório.');
        button.disabled = false;
        button.textContent = 'Salvar';
        return;
    }
    if (!paymentData.empenho) {
        alert('O campo Empenho é obrigatório.');
        button.disabled = false;
        button.textContent = 'Salvar';
        return;
    }

    // Incluir ID do pagamento apenas para edições
    const paymentId = row.dataset.paymentId;
    if (paymentId && !row.classList.contains('sub-row')) {
        paymentData.id = parseInt(paymentId);
        console.log('Salvando edição para paymentId:', paymentId, 'Dados:', paymentData);
    } else {
        console.log('Salvando novo pagamento:', paymentData);
    }

    // Validar JSON antes de enviar
    if (!isValidJson(paymentData)) {
        alert('Erro: Dados inválidos para envio.');
        console.error('Dados inválidos:', paymentData);
        button.disabled = false;
        button.textContent = 'Salvar';
        return;
    }

    // Logar JSON antes de enviar
    const jsonPayload = JSON.stringify(paymentData);
    console.log('JSON enviado para save_payment.php:', jsonPayload);

    try {
        const response = await fetch('./save_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: jsonPayload
        });

        // Verificar Content-Type da resposta
        const contentType = response.headers.get('Content-Type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Resposta não é JSON:', text);
            throw new Error(`Resposta do servidor não é JSON. Content-Type: ${contentType}. Resposta: ${text.substring(0, 200)}`);
        }

        if (!response.ok) {
            const text = await response.text();
            console.error('Erro HTTP:', response.status, text);
            throw new Error(`Erro HTTP: ${response.status}. Resposta: ${text.substring(0, 200)}`);
        }

        const data = await response.json();
        if (data.success) {
            alert('Pagamento salvo com sucesso!');
            // Recarregar a tabela com os pagamentos atualizados
            await loadContractsAndPayments({ titulo: contractTitle });
        } else {
            console.error('Erro retornado pelo servidor:', data.message);
            alert('Erro ao salvar pagamento: ' + data.message);
            button.disabled = false;
            button.textContent = 'Salvar';
        }
    } catch (error) {
        console.error('Erro ao salvar pagamento:', error);
        alert('Erro ao salvar pagamento: ' + error.message);
        button.disabled = false;
        button.textContent = 'Salvar';
    }
}

// Função para editar um pagamento existente
function editPayment(paymentId, button) {
    console.log('Iniciando edição para paymentId:', paymentId);
    const row = document.querySelector(`tr[data-payment-id="${paymentId}"]`);
    if (!row) {
        console.error('Linha não encontrada para paymentId:', paymentId);
        alert('Linha não encontrada para edição.');
        return;
    }

    // Obter dados do pagamento armazenados na linha
    let payment;
    try {
        payment = JSON.parse(row.dataset.paymentData || '{}');
    } catch (e) {
        console.error('Erro ao parsear paymentData:', e);
        alert('Erro ao carregar dados do pagamento.');
        return;
    }
    console.log('Editando pagamento:', payment);

    // Alterar a classe da linha para editável
    row.classList.remove('read-only');
    row.classList.add('editable');
    if (payment.fonte_adicional) row.classList.add('sub-row');
    row.dataset.rowIndex = editableRowCounter; // Atribuir índice único

    // Substituir células por inputs
    const cells = row.querySelectorAll('td');
    cells[0].innerHTML = `<input type="text" value="${sanitizeForJson(payment.mes || '')}" class="form-control form-control-sm" data-key="mes" readonly>`;
    cells[1].innerHTML = `<input type="text" value="${sanitizeForJson(payment.empenho || '')}" class="form-control form-control-sm" data-key="empenho">`;
    cells[2].innerHTML = `<input type="text" value="${sanitizeForJson(payment.tipo || '')}" class="form-control form-control-sm" data-key="tipo">`;
    cells[3].innerHTML = `<input type="text" value="${sanitizeForJson(payment.nota_empenho || '')}" class="form-control form-control-sm" data-key="nota_empenho">`;
    cells[4].innerHTML = `<input type="number" step="0.01" value="${payment.valor_contrato || 0}" class="form-control form-control-sm" data-key="valor_contrato" readonly>`;
    cells[5].innerHTML = `<input type="text" value="${sanitizeForJson(payment.creditos_ativos || '')}" class="form-control form-control-sm" data-key="creditos_ativos">`;
    cells[6].innerHTML = `<input type="text" value="${sanitizeForJson(payment.fonte || '')}" class="form-control form-control-sm" data-key="fonte">`;
    cells[7].innerHTML = `<input type="text" value="${sanitizeForJson(payment.SEI || '')}" class="form-control form-control-sm" data-key="SEI">`;
    cells[8].innerHTML = `<input type="text" value="${sanitizeForJson(payment.nota_fiscal || '')}" class="form-control form-control-sm" data-key="nota_fiscal">`;
    cells[9].innerHTML = `<input type="text" value="${sanitizeForJson(payment.envio_pagamento || '')}" class="form-control form-control-sm" data-key="envio_pagamento">`;
    cells[10].innerHTML = `<input type="date" value="${payment.vencimento_fatura || ''}" class="form-control form-control-sm" data-key="vencimento_fatura">`;
    cells[11].innerHTML = `<input type="number" step="0.01" value="${payment.valor_liquidado || 0}" class="form-control form-control-sm" data-key="valor_liquidado">`;
    cells[12].innerHTML = `<input type="number" step="0.01" value="${payment.valor_liquidado_ag || 0}" class="form-control form-control-sm" data-key="valor_liquidado_ag">`;
    cells[13].innerHTML = `<input type="text" value="${sanitizeForJson(payment.ordem_bancaria || '')}" class="form-control form-control-sm" data-key="ordem_bancaria">`;
    cells[14].innerHTML = `<input type="text" value="${sanitizeForJson(payment.agencia_bancaria || '')}" class="form-control form-control-sm" data-key="agencia_bancaria">`;
    cells[15].innerHTML = `<input type="date" value="${payment.data_atualizacao || ''}" class="form-control form-control-sm" data-key="data_atualizacao">`;
    cells[16].innerHTML = `<input type="date" value="${payment.data_pagamento || ''}" class="form-control form-control-sm" data-key="data_pagamento">`;
    cells[17].innerHTML = `
        ${payment.fonte_adicional ? `<input type="hidden" value="${sanitizeForJson(payment.fonte_adicional)}" data-key="fonte_adicional">` : ''}
        <button class="btn btn-primary btn-sm" onclick="saveSinglePayment(${editableRowCounter}, this)">
            <i class="bi bi-save"></i> Salvar
        </button>
    `;

    editableRowCounter++;
    console.log('Linha tornada editável para paymentId:', paymentId);
}

// Função para adicionar uma nova fonte vinculada ao pagamento
function addSource(paymentId, button) {
    console.log('Iniciando addSource para paymentId:', paymentId);
    const row = document.querySelector(`tr[data-payment-id="${paymentId}"]`);
    if (!row) {
        console.error('Linha não encontrada para paymentId:', paymentId);
        alert('Linha não encontrada para adicionar fonte.');
        return;
    }

    // Obter dados do pagamento armazenados na linha
    let payment;
    try {
        payment = JSON.parse(row.dataset.paymentData || '{}');
    } catch (e) {
        console.error('Erro ao parsear paymentData:', e);
        alert('Erro ao carregar dados do pagamento.');
        return;
    }
    console.log('Adicionando fonte para pagamento:', payment);

    // Contar fontes existentes para o mesmo mês
    const existingSources = Array.from(document.querySelectorAll(`tr[data-payment-id]`))
        .filter(tr => {
            try {
                const data = JSON.parse(tr.dataset.paymentData || '{}');
                return data.mes === payment.mes && data.fonte_adicional;
            } catch {
                return false;
            }
        })
        .length;
    const fonteNumber = existingSources + 1;

    // Criar nova linha editável como sublinha
    const trEditable = document.createElement('tr');
    trEditable.classList.add('editable', 'sub-row');
    trEditable.dataset.rowIndex = editableRowCounter; // Índice único
    // Não definir dataset.paymentId para indicar novo registro
    trEditable.innerHTML = `
        <td><input type="text" value="${sanitizeForJson(payment.mes || '')}" class="form-control form-control-sm" data-key="mes" readonly></td>
        <td><input type="text" value="${sanitizeForJson(payment.empenho || '')}" class="form-control form-control-sm" data-key="empenho"></td>
        <td><input type="text" value="${sanitizeForJson(payment.tipo || '')}" class="form-control form-control-sm" data-key="tipo"></td>
        <td><input type="text" value="${sanitizeForJson(payment.nota_empenho || '')}" class="form-control form-control-sm" data-key="nota_empenho"></td>
        <td><input type="number" step="0.01" value="${payment.valor_contrato || 0}" class="form-control form-control-sm" data-key="valor_contrato" readonly></td>
        <td><input type="text" value="${sanitizeForJson(payment.creditos_ativos || '')}" class="form-control form-control-sm" data-key="creditos_ativos"></td>
        <td><input type="text" value="${sanitizeForJson('Fonte ' + fonteNumber)}" class="form-control form-control-sm" data-key="fonte"></td>
        <td><input type="text" value="${sanitizeForJson(payment.SEI || '')}" class="form-control form-control-sm" data-key="SEI"></td>
        <td><input type="text" value="${sanitizeForJson(payment.nota_fiscal || '')}" class="form-control form-control-sm" data-key="nota_fiscal"></td>
        <td><input type="text" value="${sanitizeForJson(payment.envio_pagamento || '')}" class="form-control form-control-sm" data-key="envio_pagamento"></td>
        <td><input type="date" value="${payment.vencimento_fatura || ''}" class="form-control form-control-sm" data-key="vencimento_fatura"></td>
        <td><input type="number" step="0.01" value="${payment.valor_liquidado || 0}" class="form-control form-control-sm" data-key="valor_liquidado"></td>
        <td><input type="number" step="0.01" value="${payment.valor_liquidado_ag || 0}" class="form-control form-control-sm" data-key="valor_liquidado_ag"></td>
        <td><input type="text" value="${sanitizeForJson(payment.ordem_bancaria || '')}" class="form-control form-control-sm" data-key="ordem_bancaria"></td>
        <td><input type="text" value="${sanitizeForJson(payment.agencia_bancaria || '')}" class="form-control form-control-sm" data-key="agencia_bancaria"></td>
        <td><input type="date" value="${payment.data_atualizacao || ''}" class="form-control form-control-sm" data-key="data_atualizacao"></td>
        <td><input type="date" value="${payment.data_pagamento || ''}" class="form-control form-control-sm" data-key="data_pagamento"></td>
        <td>
            <input type="hidden" value="${sanitizeForJson('Fonte ' + fonteNumber)}" data-key="fonte_adicional">
            <button class="btn btn-primary btn-sm" onclick="saveSinglePayment(${editableRowCounter}, this)">
                <i class="bi bi-save"></i> Salvar
            </button>
        </td>
    `;

    // Inserir a nova linha logo após a linha selecionada
    row.insertAdjacentElement('afterend', trEditable);
    editableRowCounter++;
    console.log('Nova sublinha de fonte adicionada para paymentId:', paymentId, 'Fonte:', fonteNumber);
}

// Função para excluir o pagamento
async function deletePayment(id, button) {
    console.log('Iniciando exclusão para paymentId:', id);
    if (!id) {
        alert('ID do pagamento não fornecido');
        return;
    }

    if (!confirm('Tem certeza que deseja excluir este pagamento?')) {
        return;
    }

    // Desativar botão para evitar múltiplos cliques
    button.disabled = true;
    button.textContent = 'Excluindo...';

    try {
        const response = await fetch(`./delete_payment.php?id=${encodeURIComponent(id)}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        const data = await response.json();

        if (data.success) {
            alert('Pagamento excluído com sucesso!');
            // Recarregar os pagamentos após exclusão
            const contractTitle = document.getElementById('contratosTableBody').dataset.contractTitle;
            await loadContractsAndPayments({ titulo: contractTitle });
        } else {
            alert('Erro ao excluir pagamento: ' + (data.message || 'Erro desconhecido'));
            button.disabled = false;
            button.textContent = 'Excluir';
        }
    } catch (error) {
        console.error('Erro ao excluir pagamento:', error);
        alert('Erro ao excluir pagamento: ' + error.message);
        button.disabled = false;
        button.textContent = 'Excluir';
    }
}

// Função para salvar todos os pagamentos (desativada)
async function savePayment() {
    alert('Use os botões "Salvar" em cada linha para salvar individualmente.');
}

// Certificar que a função showTab está definida
function showTab(tabId) {
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.form-container');
    tabs.forEach(tab => tab.classList.remove('active'));
    contents.forEach(content => content.style.display = 'none');
    document.querySelector(`.tab[data-tab="${tabId}"]`).classList.add('active');
    document.getElementById(tabId).style.display = 'block';
}

// Estilizar linhas não editáveis, editáveis, sublinhas e botões
document.addEventListener('DOMContentLoaded', () => {
    const style = document.createElement('style');
    style.textContent = `
       
    `;
    document.head.appendChild(style);
});