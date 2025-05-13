// Função para exibir o resumo do processo e abrir a aba Gerenciar Contratos
async function showResumoProcesso(rowData) {
    const contractData = typeof rowData === 'string' ? JSON.parse(rowData) : rowData;
    showTab('gerenciar');
    await loadContractsAndPayments(contractData);
}

// Função para carregar os dados do contrato e os pagamentos anteriores
async function loadContractsAndPayments(contractData) {
    const tbody = document.getElementById('contratosTableBody');
    tbody.innerHTML = ''; // Limpar tabela

    // Atualizar o título com o contrato_titulo
    const contractTitleHeader = document.getElementById('contractTitleHeader');
    const titulo = contractData.titulo || 'Desconhecido';
    const sei = contractData.SEI || 'N/A';

    // Link de pesquisa no SEI
    const seiLink = sei !== 'N/A' 
        ? `<a href="https://sei.rj.gov.br/sei/controlador_externo.php?acao=procedimento_trabalhar&acao_origem=procedimento_pesquisar&id_procedimento=${encodeURIComponent(sei)}" target="_blank" rel="noopener noreferrer" title="Link de acesso direto ao processo SEI">SEI: ${sei}</a>`
        : 'SEI: N/A';

    contractTitleHeader.innerHTML = `Pagamentos do contrato ${titulo} (${seiLink})`;

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

        // Exibir pagamentos anteriores como linhas não editáveis
        payments.forEach(payment => {
            const tr = document.createElement('tr');
            tr.classList.add('read-only');
            tr.innerHTML = `
                <td>${payment.mes || ''}</td>
                <td>${payment.empenho || ''}</td>
                <td>${payment.tipo || ''}</td>
                <td>${payment.nota_empenho || ''}</td>
                <td>${payment.valor_contrato || 0}</td>
                <td>${payment.creditos_ativos || ''}</td>
                <td>${payment.fonte || ''}</td>
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
                <td><button class="btn btn-danger btn-sm" onclick="deletePayment(${payment.id})">Excluir</button></td>
            `;
            tbody.appendChild(tr);
        });

        // Gerar meses para as parcelas
        const mesesExistentes = payments.map(p => p.mes); // Meses já salvos
        const mesesParcelas = [];
        for (let i = 0; i < numParcelas; i++) {
            const dataParcela = new Date(dataInicio);
            dataParcela.setMonth(dataInicio.getMonth() + i);
            const mesFormatado = `${String(dataParcela.getMonth() + 1).padStart(2, '0')}/${dataParcela.getFullYear()}`;
            if (!mesesExistentes.includes(mesFormatado)) {
                mesesParcelas.push(mesFormatado);
            }
        }

        // Adicionar linhas editáveis para cada parcela restante
        mesesParcelas.forEach((mes, index) => {
            const trEditable = document.createElement('tr');
            trEditable.classList.add('editable');
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
                <td></td> <!-- Sem botão Excluir -->
            `;
            tbody.appendChild(trEditable);
        });

        // Armazenar o título do contrato para uso no salvamento
        tbody.dataset.contractTitle = contractData.titulo;
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
        alert('Erro ao carregar dados: ' + error.message);
    }
}

// Função para excluir o pagamento
async function deletePayment(id) {
    if (!id) {
        alert('ID do pagamento não fornecido');
        return;
    }

    if (!confirm('Tem certeza que deseja excluir este pagamento?')) {
        return;
    }

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
        }
    } catch (error) {
        console.error('Erro ao excluir pagamento:', error);
        alert('Erro ao excluir pagamento: ' + error.message);
    }
}

// Função para salvar os dados na tabela pagamentos
async function savePayment() {
    const contractTitle = document.getElementById('contratosTableBody').dataset.contractTitle;
    const editableRows = document.querySelectorAll('#contratosTableBody .editable');

    for (const row of editableRows) {
        const inputs = row.querySelectorAll('input');
        const paymentData = { contrato_titulo: contractTitle };

        const columns = [
            'mes', 'empenho', 'tipo', 'nota_empenho', 'valor_contrato', 'creditos_ativos', 'fonte',
            'SEI', 'nota_fiscal', 'envio_pagamento', 'vencimento_fatura', 'valor_liquidado',
            'valor_liquidado_ag', 'ordem_bancaria', 'agencia_bancaria', 'data_atualizacao', 'data_pagamento'
        ];

        inputs.forEach(input => {
            const key = input.getAttribute('data-key');
            if (columns.includes(key)) {
                if (input.type === "number") {
                    paymentData[key] = parseFloat(input.value) || 0;
                } else if (input.type === "date") {
                    paymentData[key] = input.value || null;
                } else {
                    paymentData[key] = input.value || null;
                }
            }
        });

        // Validar que o mês foi preenchido
        if (!paymentData.mes) {
            alert('O campo Mês é obrigatório para a parcela.');
            return;
        }

        try {
            const response = await fetch('./save_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(paymentData)
            });

            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status} - Verifique se save_payment.php existe no servidor.`);
            }

            const data = await response.json();
            if (!data.success) {
                alert('Erro ao salvar pagamento: ' + data.message);
                return;
            }
        } catch (error) {
            console.error('Erro ao salvar pagamento:', error);
            alert('Erro ao salvar pagamento: ' + error.message);
            return;
        }
    }

    alert('Pagamentos salvos com sucesso!');
    // Recarregar a tabela com os pagamentos atualizados
    await loadContractsAndPayments({ titulo: contractTitle });
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

// Estilizar linhas não editáveis
document.addEventListener('DOMContentLoaded', () => {
    const style = document.createElement('style');
    style.textContent = `
        .read-only td { background-color: #f0f0f0; }
        .read-only td input { display: none; }
        .editable td input { width: 100%; }
    `;
    document.head.appendChild(style);
});