
// Seleção de elementos do DOM para inputs do formulário e componentes da interface
const form = document.getElementById('viagem-form');
const bondeInput = document.getElementById('bonde');
const saidaInput = document.getElementById('saida');
const retornoInput = document.getElementById('retorno');
const maquinistasInput = document.getElementById('maquinistas');
const agentesInput = document.getElementById('agentes');
const horaInput = document.getElementById('hora');
const pagantesInput = document.getElementById('pagantes');
const moradoresInput = document.getElementById('moradores');
const gratPcdIdosoInput = document.getElementById('grat_pcd_idoso');
const gratuidadeInput = document.getElementById('gratuidade');
const passageirosInput = document.getElementById('passageiros');
const viagemInput = document.getElementById('viagem');
const dateInput = document.getElementById('data');
const transactionsTableBody = document.getElementById('transactions-table-body');
const addBtn = document.getElementById('add-btn');
const clearFormBtn = document.getElementById('clear-form-btn');
const deleteBtn = document.getElementById('delete-btn');
const alterBtn = document.getElementById('alter-btn');
const clearTransactionsBtn = document.getElementById('clear-transactions-btn');
const idFilterInput = document.getElementById('id-filter');
const progressBarFill = document.getElementById('progress-bar-fill');
const prevButton = document.getElementById('prev-page');
const nextButton = document.getElementById('next-page');
const pageInfo = document.getElementById('page-info');
const returnButton = document.getElementById('return-btn');

// Variáveis de estado
let transactions = [];
let selectedRowId = null;
let formMode = 'add'; // 'add', 'edit', ou 'registerReturn'
let idOfSubidaToComplete = null;
const MAX_PASSENGERS = 32;
const ROWS_PER_PAGE = 4;
let currentPage = 1;

// Opções de retorno
const defaultRetornoOptions = [
    { value: '', text: 'Selecione (para retorno)' },
    { value: 'Carioca', text: 'Carioca' },
    { value: 'D.Irmãos', text: 'D.Irmãos' },
    { value: 'Paula Mattos', text: 'Paula Mattos' },
    { value: 'Silvestre', text: 'Silvestre' },
    { value: 'Oficina', text: 'Oficina' }
];
const returnDestinationOptions = [
    { value: 'Carioca', text: 'Carioca' },
    { value: 'Oficina', text: 'Oficina' }
];

// Funções utilitárias
async function loadTransactions() {
    try {
        const response = await fetch('./get_viagens.php');
        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
        transactions = await response.json();
    } catch (error) {
        console.error('Erro ao carregar transações:', error);
        alert('Erro ao carregar transações: ' + error.message);
        transactions = [];
    }
}

function updateProgressBar() {
    const pagantes = parseInt(pagantesInput.value) || 0;
    const moradores = parseInt(moradoresInput.value) || 0;
    const gratPcdIdoso = parseInt(gratPcdIdosoInput.value) || 0;
    const totalPassageiros = pagantes + moradores + gratPcdIdoso;
    const percentage = Math.min((totalPassageiros / MAX_PASSENGERS) * 100, 100);
    progressBarFill.style.width = `${percentage}%`;
    progressBarFill.textContent = `${totalPassageiros}/${MAX_PASSENGERS} (${Math.round(percentage)}%)`;
    progressBarFill.classList.remove('warning', 'danger');
    if (totalPassageiros > MAX_PASSENGERS) {
        progressBarFill.classList.add('danger');
    } else if (totalPassageiros >= MAX_PASSENGERS * 0.8) {
        progressBarFill.classList.add('warning');
    }
}


function updateTotals() {
    // Initialize objects to store totals for "subindo" (departure) and "retorno" (return)
    let totalSubindo = { pagantes: 0, gratuitos: 0, moradores: 0, grat_pcd_idoso: 0, passageiros: 0, bondes: new Set() };
    let totalRetorno = { pagantes: 0, gratuitos: 0, moradores: 0, grat_pcd_idoso: 0, passageiros: 0, bondes: new Set() };

    // Check if transactions is an array
    if (!Array.isArray(transactions)) {
        console.error('Erro: transactions não está definido ou não é um array');
        return;
    }

    // Aggregate totals based on transaction type
    transactions.forEach(t => {
        const target = t.tipo_viagem.toLowerCase().includes('ida') || t.tipo_viagem.toLowerCase().includes('pendente') 
            ? totalSubindo 
            : totalRetorno;
        target.pagantes += Number(t.pagantes) || 0;
        target.moradores += Number(t.moradores) || 0;
        target.grat_pcd_idoso += Number(t.grat_pcd_idoso) || 0;
        target.gratuitos = target.moradores + target.grat_pcd_idoso; // Sum of moradores and grat_pcd_idoso
        target.passageiros = target.pagantes + target.moradores + target.grat_pcd_idoso; // Sum of pagantes, moradores, and grat_pcd_idoso
        target.bondes.add(t.bonde || 'Desconhecido');
    });

    // Helper function to update DOM element safely
    const updateElement = (id, value) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        } else {
            console.warn(`Elemento com ID "${id}" não encontrado no DOM`);
        }
    };

    // Update DOM elements with totals
    updateElement('total-subindo-pagantes', totalSubindo.pagantes);
    updateElement('total-subindo-gratuitos', totalSubindo.gratuitos);
    updateElement('total-subindo-moradores', totalSubindo.moradores);
    updateElement('total-subindo-passageiros', totalSubindo.passageiros);
    updateElement('total-subindo-grat_pcd_idoso', totalSubindo.grat_pcd_idoso);
    updateElement('total-bondes-saida', totalSubindo.bondes.size);
    updateElement('total-retorno-pagantes', totalRetorno.pagantes);
    updateElement('total-retorno-gratuitos', totalRetorno.gratuitos);
        updateElement('total-retorno-grat_pcd_idoso', totalRetorno.grat_pcd_idoso);
    updateElement('total-retorno-moradores', totalRetorno.moradores);
    updateElement('total-retorno-passageiros', totalRetorno.passageiros);
    updateElement('total-bondes-retorno', totalRetorno.bondes.size);
}



function populateRetornoOptions(options, selectedValue = '') {
    retornoInput.innerHTML = '';
    options.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.text;
        if (opt.value === selectedValue) option.selected = true;
        retornoInput.appendChild(option);
    });
}

async function renderTransactions() {
    transactionsTableBody.innerHTML = '';
    const filterId = idFilterInput.value.trim();
    const filteredTransactions = filterId
        ? transactions.filter(t => t.id.toString().includes(filterId))
        : transactions;
    const start = (currentPage - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;
    const paginatedTransactions = filteredTransactions.slice(start, end);

    if (paginatedTransactions.length === 0) {
        const row = transactionsTableBody.insertRow();
        row.innerHTML = `<td colspan="13" style="text-align: center;">Nenhuma transação encontrada.</td>`;
    } else {
        paginatedTransactions.forEach(t => {
            const row = transactionsTableBody.insertRow();
            row.dataset.id = t.id;
            const hasReturn = transactions.some(r => r.tipo_viagem === 'retorno' && r.subida_id === t.id);
            if (t.tipo_viagem === 'ida' && !hasReturn) {
                row.classList.add('ida-pendente');
            } else if (t.tipo_viagem === 'retorno') {
                row.classList.add('retorno-row');
            }
            row.insertCell().textContent = t.id;
            row.insertCell().textContent = t.bonde;
            row.insertCell().textContent = t.saida;
            row.insertCell().textContent = t.tipo_viagem === 'ida' && !hasReturn ? 'Pendente' : t.retorno;
            row.insertCell().textContent = t.maquinista;
            row.insertCell().textContent = t.agente;
            row.insertCell().textContent = t.hora;
            row.insertCell().textContent = t.pagantes;
            row.insertCell().textContent = t.grat_pcd_idoso;
            row.insertCell().textContent = t.moradores;
            row.insertCell().textContent = t.passageiros;
            row.insertCell().textContent = t.tipo_viagem;
            row.insertCell().textContent = t.data;
            row.addEventListener('click', () => {
                document.querySelector('.table-section tr.selected')?.classList.remove('selected');
                row.classList.add('selected');
                selectedRowId = t.id;
                deleteBtn.disabled = false;
                alterBtn.disabled = false;
                alterBtn.textContent = 'Alterar';
                const hasReturnForThisIda = transactions.some(r => r.tipo_viagem === 'retorno' && r.subida_id === t.id);
                if (returnButton && t.tipo_viagem === 'ida' && !hasReturnForThisIda) {
                    returnButton.style.display = 'inline-block';
                } else if (returnButton) {
                    returnButton.style.display = 'none';
                }
            });
        });
    }
    const totalPages = Math.ceil(filteredTransactions.length / ROWS_PER_PAGE);
    pageInfo.textContent = `Página ${currentPage} de ${totalPages || 1}`;
    prevButton.disabled = currentPage === 1;
    nextButton.disabled = currentPage === totalPages || totalPages === 0;
    updateTotals();
}

function clearForm() {
    bondeInput.value = '';
    saidaInput.value = 'Carioca';
    saidaInput.disabled = false;
    populateRetornoOptions(defaultRetornoOptions);
    retornoInput.disabled = false;
    maquinistasInput.value = '';
    agentesInput.value = '';
    horaInput.value = '';
    pagantesInput.value = '0';
    moradoresInput.value = '0';
    gratPcdIdosoInput.value = '0';
    gratuidadeInput.value = '0';
    passageirosInput.value = '0';
    viagemInput.value = '1';
    dateInput.value = '';
    idFilterInput.value = '';
    selectedRowId = null;
    idOfSubidaToComplete = null;
    formMode = 'add';
    addBtn.textContent = 'Adicionar';
    deleteBtn.disabled = true;
    alterBtn.disabled = true;
    if (returnButton) returnButton.style.display = 'none';
    document.querySelector('.table-section tr.selected')?.classList.remove('selected');
    setTimeAndDate();
    calculateCounts();
}

function setTimeAndDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    dateInput.value = `${year}-${month}-${day}`;
    horaInput.value = `${hours}:${minutes}:${seconds}`;
}

function calculateCounts() {
    const pagantes = parseInt(pagantesInput.value) || 0;
    const moradores = parseInt(moradoresInput.value) || 0;
    const gratPcdIdoso = parseInt(gratPcdIdosoInput.value) || 0;
    const totalGratuidade = gratPcdIdoso + moradores; // Soma de grat_pcd_idoso e moradores
    const totalPassageiros = pagantes + moradores + gratPcdIdoso;
    gratuidadeInput.value = totalGratuidade; // Atualiza o campo gratuidade
    passageirosInput.value = totalPassageiros; // Atualiza o campo passageiros
    updateProgressBar();
}

// Função para aplicar regras de cores e organização
function applyTransactionRules() {
    const tableBody = document.getElementById('transactions-table-body');
    if (!tableBody) return;

    const rows = Array.from(tableBody.querySelectorAll('tr'));
    if (rows.length === 0) return;

    rows.forEach((row, index) => {
        const cells = row.querySelectorAll('td');
        if (cells.length === 0) return;

        const retornoCell = cells[3];
        const tipoViagemCell = cells[11];
        if (!tipoViagemCell) return;

        const tipoViagem = tipoViagemCell.textContent.trim().toLowerCase();
        const retornoText = retornoCell ? retornoCell.textContent.trim() : '';

        row.classList.remove('transaction-row', 'ida', 'retorno', 'retorno-pendente');

        if (tipoViagem === 'ida') {
            if (retornoText === '' || retornoText === 'Pendente') {
                row.classList.add('transaction-row', 'retorno-pendente');
                updateCellWithIcon(tipoViagemCell, '<span class="status-badge retorno-pendente"><i class="fas fa-clock"></i> Pendente</span>');
            } else {
                row.classList.add('transaction-row', 'ida');
                updateCellWithIcon(tipoViagemCell, '<span class="status-badge ida"><i class="fas fa-arrow-up"></i> Partida</span>');
            }
        } else if (tipoViagem === 'retorno') {
            row.classList.add('transaction-row', 'retorno');
            updateCellWithIcon(tipoViagemCell, '<span class="status-badge chegada"><i class="fas fa-arrow-down"></i> Chegada</span>');
        }

        addIconsToRowSafely(cells);
    });

    organizeTransactionPairs();
}

function updateCellWithIcon(cell, newContent) {
    if (cell.innerHTML !== newContent) {
        cell.innerHTML = newContent;
        const icons = cell.querySelectorAll('i');
        icons.forEach(icon => {
            icon.style.pointerEvents = 'none';
        });
    }
}

function addIconsToRowSafely(cells) {
    const iconMappings = [
        { index: 1, icon: 'fas fa-train', color: 'var(--accent-color)' },
        { index: 2, icon: 'fas fa-map-marker-alt', color: 'var(--success-color)' },
        { index: 3, icon: 'fas fa-map-marker-alt', color: 'var(--danger-color)' },
        { index: 4, icon: 'fas fa-user-tie', color: 'var(--info-color)' },
        { index: 5, icon: 'fas fa-user', color: 'var(--secondary-color)' },
        { index: 6, icon: 'fas fa-clock', color: 'var(--warning-color)' }
    ];

    iconMappings.forEach(mapping => {
        const cell = cells[mapping.index];
        if (cell && !cell.querySelector('i')) {
            const originalText = cell.textContent.trim();
            if (originalText && originalText !== '') {
                const icon = document.createElement('i');
                icon.className = mapping.icon;
                icon.style.color = mapping.color;
                icon.style.marginRight = '0.5rem';
                icon.style.pointerEvents = 'none';
                cell.textContent = originalText;
                cell.insertBefore(icon, cell.firstChild);
            }
        }
    });
}

function organizeTransactionPairs() {
    const tableBody = document.getElementById('transactions-table-body');
    const rows = Array.from(tableBody.querySelectorAll('tr'));
    const transactionMap = new Map();
    const organizedRows = [];
    const processedIds = new Set();

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length === 0) return;

        const id = cells[0].textContent.trim();
        const tipoViagem = cells[11].textContent.toLowerCase();
        const bonde = cells[1].textContent.replace(/<[^>]*>/g, '').trim();
        const maquinista = cells[4].textContent.replace(/<[^>]*>/g, '').trim();
        const data = cells[12].textContent.trim();
        const key = `${bonde}-${maquinista}-${data}`;

        if (!transactionMap.has(key)) {
            transactionMap.set(key, { ida: null, retorno: null });
        }

        const entry = transactionMap.get(key);
        if (tipoViagem.includes('ida') || tipoViagem.includes('pendente')) {
            entry.ida = row;
        } else if (tipoViagem.includes('chegada')) {
            entry.retorno = row;
        }
    });

    transactionMap.forEach((entry, key) => {
        if (entry.ida && !processedIds.has(entry.ida.querySelector('td').textContent.trim())) {
            organizedRows.push(entry.ida);
            processedIds.add(entry.ida.querySelector('td').textContent.trim());
            if (entry.retorno && !processedIds.has(entry.retorno.querySelector('td').textContent.trim())) {
                organizedRows.push(entry.retorno);
                processedIds.add(entry.retorno.querySelector('td').textContent.trim());
            }
        }
    });

    transactionMap.forEach((entry, key) => {
        if (entry.retorno && !processedIds.has(entry.retorno.querySelector('td').textContent.trim())) {
            organizedRows.push(entry.retorno);
            processedIds.add(entry.retorno.querySelector('td').textContent.trim());
        }
    });

    const fragment = document.createDocumentFragment();
    organizedRows.forEach(row => fragment.appendChild(row));
    tableBody.innerHTML = '';
    tableBody.appendChild(fragment);
}

function updateBondeStatus(checkbox) {
    const bondeId = checkbox.getAttribute('data-id');
    const modelo = checkbox.getAttribute('data-modelo');
    const ativo = checkbox.checked ? 1 : 0;
    const url = new URL('/Sistema-CENTRAL-ERP/update_bonde_status.php', window.location.origin);

    fetch(url.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: bondeId, ativo: ativo })
    })
    .then(response => {
        if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            updateSelectOptions();
            const modalCheckbox = document.getElementById('modal_bonde_' + bondeId);
            if (modalCheckbox) {
                modalCheckbox.checked = checkbox.checked;
            }
        } else {
            alert('Erro ao atualizar status do bonde: ' + data.message);
            checkbox.checked = !checkbox.checked;
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro na conexão com o servidor.');
        checkbox.checked = !checkbox.checked;
    });
}

function updateBondeStatusFromModal(checkbox) {
    const bondeId = checkbox.getAttribute('data-id');
    const sidebarCheckbox = document.getElementById('bonde_' + bondeId);
    if (sidebarCheckbox) {
        sidebarCheckbox.checked = checkbox.checked;
        updateBondeStatus(sidebarCheckbox);
    }
}

function updateSelectOptions() {
    const select = document.getElementById('bonde');
    const originalValue = select.value;

    fetch('/Sistema-CENTRAL-ERP/get_active_bondes.php', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => {
        if (!response.ok) throw new Error('Erro ao carregar bondes: ' + response.status);
        return response.json();
    })
    .then(data => {
        select.innerHTML = '<option value="">Selecione</option>';
        let hasActiveBondes = false;
        data.forEach(bonde => {
            if (bonde.ativo == 1) {
                const option = document.createElement('option');
                option.value = bonde.modelo;
                option.textContent = bonde.modelo;
                select.appendChild(option);
                hasActiveBondes = true;
            }
        });
        if (!hasActiveBondes) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Nenhum bonde ativo';
            option.disabled = true;
            select.appendChild(option);
        }
        if (originalValue && data.some(bonde => bonde.modelo === originalValue && bonde.ativo == 1)) {
            select.value = originalValue;
        }
    })
    .catch(error => {
        console.error('Erro ao atualizar opções do select:', error);
        alert('Erro ao carregar a lista de bondes.');
    });
}

function updateStaffSelects() {
    const maquinistaSelect = document.getElementById('maquinistas');
    const agenteSelect = document.getElementById('agentes');

    fetch('/Sistema-CENTRAL-ERP/get_staff.php', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => {
        if (!response.ok) throw new Error('Erro ao carregar funcionários: ' + response.status);
        return response.json();
    })
    .then(data => {
        maquinistaSelect.innerHTML = '<option value="">Selecione</option>';
        data.maquinistas.forEach(maquinista => {
            const option = document.createElement('option');
            option.value = maquinista.nome;
            option.textContent = maquinista.nome;
            maquinistaSelect.appendChild(option);
        });
        agenteSelect.innerHTML = '<option value="">Selecione</option>';
        data.agentes.forEach(agente => {
            const option = document.createElement('option');
            option.value = agente.nome;
            option.textContent = agente.nome;
            agenteSelect.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Erro ao atualizar listas de funcionários:', error);
        alert('Erro ao carregar a lista de funcionários.');
    });
}

// Listeners de eventos
[pagantesInput, moradoresInput, gratPcdIdosoInput].forEach(input => {
    input.addEventListener('input', calculateCounts);
});

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = {
        bonde: bondeInput.value,
        saida: saidaInput.value,
        retorno: retornoInput.value,
        maquinista: maquinistasInput.value,
        agente: agentesInput.value,
        hora: horaInput.value,
        pagantes: parseInt(pagantesInput.value) || 0,
        grat_pcd_idoso: parseInt(gratPcdIdosoInput.value) || 0,
        gratuidade: parseInt(gratuidadeInput.value) || 0,
        moradores: parseInt(moradoresInput.value) || 0,
        passageiros: parseInt(passageirosInput.value) || 0,
        viagem: parseFloat(viagemInput.value) || 1,
        data: dateInput.value,
        tipo_viagem: '',
        subida_id: null
    };
    const totalPassageiros = data.pagantes + data.moradores + data.grat_pcd_idoso;
    if ((formMode === 'add' || (formMode === 'edit' && transactions.find(t => t.id === selectedRowId)?.tipo_viagem === 'ida')) && totalPassageiros > MAX_PASSENGERS) {
        alert(`Atenção: O número total de passageiros (${totalPassageiros}) excede a capacidade máxima de ${MAX_PASSENGERS}.`);
        return;
    }
    if (!data.bonde || !data.maquinista || !data.agente || !data.data || !data.hora) {
        alert('Por favor, preencha os campos obrigatórios: Bonde, Maquinista, Agente, Data e Hora.');
        return;
    }
    if (formMode === 'add') {
        if (!data.saida || !data.retorno) {
            alert('Por favor, selecione a Saída e o Destino da viagem de ida.');
            return;
        }
        data.tipo_viagem = 'ida';
    } else if (formMode === 'registerReturn') {
        if (!idOfSubidaToComplete || !data.retorno) {
            alert('Erro: Selecione uma viagem de ida pendente e um destino de retorno.');
            return;
        }
        data.tipo_viagem = 'retorno';
        data.subida_id = idOfSubidaToComplete;
    } else if (formMode === 'edit') {
        if (!selectedRowId) {
            alert('Erro: Nenhuma transação selecionada para alterar.');
            return;
        }
        data.id = selectedRowId;
        data.tipo_viagem = transactions.find(t => t.id === selectedRowId).tipo_viagem;
        data.subida_id = transactions.find(t => t.id === selectedRowId).subida_id || null;
    }
    const url = formMode === 'edit' ? './update_viagem.php' : './add_viagem.php';
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
        const result = await response.json();
        if (result.success) {
            alert(formMode === 'edit' ? 'Transação alterada com sucesso!' :
                  formMode === 'registerReturn' ? 'Retorno registrado com sucesso!' :
                  'Viagem de ida adicionada com sucesso!');
            await loadTransactions();
            currentPage = 1;
            await renderTransactions();
            applyTransactionRules();
            clearForm();
        } else {
            alert('Erro: ' + result.message);
        }
    } catch (error) {
        console.error('Erro ao salvar transação:', error);
        alert('Erro na conexão com o servidor: ' + error.message);
    }
});

clearFormBtn.addEventListener('click', clearForm);

deleteBtn.addEventListener('click', async () => {
    if (!selectedRowId) {
        alert('Selecione uma transação para excluir.');
        return;
    }
    const transaction = transactions.find(t => t.id === selectedRowId);
    if (!transaction || !confirm(`Tem certeza que deseja excluir a transação ID ${selectedRowId} (${transaction.tipo_viagem})?`)) {
        return;
    }
    try {
        if (transaction.tipo_viagem === 'ida') {
            const linkedRetornos = transactions.filter(t => t.tipo_viagem === 'retorno' && t.subida_id === selectedRowId);
            for (const retorno of linkedRetornos) {
                await fetch('./delete_viagem.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: retorno.id })
                });
            }
        }
        const response = await fetch('./delete_viagem.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: selectedRowId })
        });
        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
        const result = await response.json();
        if (result.success) {
            alert('Transação excluída com sucesso!');
            await loadTransactions();
            currentPage = 1;
            await renderTransactions();
            applyTransactionRules();
            clearForm();
        } else {
            alert('Erro: ' + result.message);
        }
    } catch (error) {
        console.error('Erro ao excluir transação:', error);
        alert('Erro na conexão com o servidor: ' + error.message);
    }
});

alterBtn.addEventListener('click', () => {
    if (!selectedRowId) {
        alert('Selecione uma transação para alterar ou registrar retorno.');
        return;
    }
    const transaction = transactions.find(t => t.id === selectedRowId);
    if (!transaction) return;
    const hasReturn = transactions.some(t => t.tipo_viagem === 'retorno' && t.subida_id === transaction.id);
    if (transaction.tipo_viagem === 'ida' && !hasReturn) {
        formMode = 'registerReturn';
        idOfSubidaToComplete = transaction.id;
        bondeInput.value = transaction.bonde;
        maquinistasInput.value = transaction.maquinista;
        agentesInput.value = transaction.agente;
        dateInput.value = transaction.data;
        setTimeAndDate();
        saidaInput.value = transaction.retorno;
        saidaInput.disabled = true;
        populateRetornoOptions(returnDestinationOptions, 'Carioca');
        retornoInput.disabled = false;
        pagantesInput.value = '0';
        moradoresInput.value = '0';
        gratPcdIdosoInput.value = '0';
        viagemInput.value = parseFloat(transaction.viagem) + 0.5;
        calculateCounts();
        addBtn.textContent = 'Registrar Retorno';
        if (returnButton) returnButton.style.display = 'none';
        alert('Preencha os dados para registrar o retorno.');
    } else {
        formMode = 'edit';
        bondeInput.value = transaction.bonde;
        saidaInput.value = transaction.saida;
        saidaInput.disabled = false;
        populateRetornoOptions(defaultRetornoOptions, transaction.retorno || '');
        retornoInput.disabled = false;
        maquinistasInput.value = transaction.maquinista;
        agentesInput.value = transaction.agente;
        horaInput.value = transaction.hora;
        pagantesInput.value = transaction.pagantes;
        moradoresInput.value = transaction.moradores || '0';
        gratPcdIdosoInput.value = transaction.grat_pcd_idoso || '0';
        viagemInput.value = transaction.viagem;
        dateInput.value = transaction.data;
        calculateCounts();
        addBtn.textContent = 'Atualizar';
        if (returnButton) returnButton.style.display = 'none';
        alert('Transação carregada para edição. Modifique e clique em "Atualizar".');
    }
});

if (returnButton) {
    returnButton.addEventListener('click', () => {
        if (!selectedRowId) {
            alert('Selecione uma viagem de ida pendente para registrar o retorno.');
            return;
        }
        const transaction = transactions.find(t => t.id === selectedRowId);
        if (!transaction || transaction.tipo_viagem !== 'ida') {
            alert('Selecione uma viagem de ida para registrar o retorno.');
            return;
        }
        if (transactions.some(t => t.tipo_viagem === 'retorno' && t.subida_id === transaction.id)) {
            alert('Esta viagem de ida já possui um retorno registrado.');
            return;
        }
        formMode = 'registerReturn';
        idOfSubidaToComplete = transaction.id;
        bondeInput.value = transaction.bonde;
        maquinistasInput.value = transaction.maquinista;
        agentesInput.value = transaction.agente;
        dateInput.value = transaction.data;
        setTimeAndDate();
        saidaInput.value = transaction.retorno;
        saidaInput.disabled = true;
        populateRetornoOptions(returnDestinationOptions, 'Carioca');
        retornoInput.disabled = false;
        pagantesInput.value = '0';
        moradoresInput.value = '0';
        gratPcdIdosoInput.value = '0';
        viagemInput.value = parseFloat(transaction.viagem) + 0.5;
        calculateCounts();
        addBtn.textContent = 'Registrar Retorno';
        returnButton.style.display = 'none';
        alert('Preencha os dados para registrar o retorno.');
    });
}

clearTransactionsBtn.addEventListener('click', async () => {
    if (!confirm('Tem certeza que deseja limpar TODAS as transações? Esta ação não pode ser desfeita.')) return;
    try {
        const response = await fetch('./clear_viagem.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        });
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Erro HTTP: ${response.status} - ${errorText.substring(0, 100)}...`);
        }
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            await loadTransactions();
            currentPage = 1;
            await renderTransactions();
            applyTransactionRules();
            clearForm();
        } else {
            alert('Erro: ' + result.message);
        }
    } catch (error) {
        console.error('Erro ao limpar transações:', error);
        alert('Erro na conexão com o servidor: ' + error.message);
    }
});

idFilterInput.addEventListener('input', () => {
    currentPage = 1;
    renderTransactions();
    applyTransactionRules();
});

prevButton.addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        renderTransactions();
        applyTransactionRules();
    }
});



nextButton.addEventListener('click', () => {
    const filteredTransactions = idFilterInput.value.trim()
        ? transactions.filter(t => t.id.toString().includes(idFilterInput.value.trim()))
        : transactions;
    if (currentPage < Math.ceil(filteredTransactions.length / ROWS_PER_PAGE)) {
        currentPage++;
        renderTransactions();
        applyTransactionRules();
    }
});

function checkForReturn() {
    const tableBody = document.getElementById('transactions-table-body');
    const returnBtn = document.getElementById('return-btn');
    if (tableBody && returnBtn) {
        const idasPendentes = Array.from(tableBody.querySelectorAll('tr')).some(row => {
            const tipoViagemCell = row.cells[11].textContent.toLowerCase();
            return tipoViagemCell.includes('pendente');
        });
        returnBtn.style.display = idasPendentes ? 'inline-flex' : 'none';
    }
}

// Configuração do MutationObserver para aplicar regras de cores
let updateTimeout;
const tableObserver = new MutationObserver((mutations) => {
    let shouldUpdate = false;
    mutations.forEach((mutation) => {
        if (mutation.type === 'childList' && 
            (mutation.addedNodes.length > 0 || mutation.removedNodes.length > 0)) {
            shouldUpdate = true;
        }
    });
    if (shouldUpdate) {
        clearTimeout(updateTimeout);
        updateTimeout = setTimeout(() => {
            applyTransactionRules();
        }, 300);
    }
});

if (transactionsTableBody) {
    tableObserver.observe(transactionsTableBody, {
        childList: true,
        subtree: true
    });
}

setInterval(() => {
    const tableBody = document.getElementById('transactions-table-body');
    if (tableBody && tableBody.children.length > 0) {
        const hasUntreatedRows = Array.from(tableBody.querySelectorAll('tr')).some(row => 
            !row.classList.contains('transaction-row')
        );
        if (hasUntreatedRows) {
            applyTransactionRules();
        }
    }
}, 5000);

document.addEventListener('DOMContentLoaded', async () => {
    setTimeAndDate();
    await loadTransactions();
    clearForm();
    await renderTransactions();
    applyTransactionRules();
    calculateCounts();
    setInterval(checkForReturn, 5000);
    checkForReturn();
});

// Atualização de hora em tempo real
function atualizarHora() {
    const agora = new Date();
    const opcoes = {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'America/Sao_Paulo',
        hour12: false
    };
    const horaFormatada = agora.toLocaleTimeString('pt-BR', opcoes);
    horaInput.value = horaFormatada;
}

setInterval(atualizarHora, 1000);
atualizarHora();

// Manipulação de modais
const addBondeModal = document.getElementById('add-bonde-modal');
const addBondeBtn = document.getElementById('add-bonde-btn');
const manageBondesModal = document.getElementById('manage-bondes-modal');
const manageBondesBtn = document.getElementById('manage-bondes-btn');
const addStaffModal = document.getElementById('add-staff-modal');
const addStaffBtn = document.getElementById('add-staff-btn');

addBondeBtn.addEventListener('click', () => {
    addBondeModal.style.display = 'flex';
    addBondeModal.classList.add('active');
});

function closeAddBondeModal() {
    addBondeModal.classList.remove('active');
    setTimeout(() => {
        addBondeModal.style.display = 'none';
    }, 300);
    document.getElementById('add-bonde-form').reset();
}

manageBondesBtn.addEventListener('click', () => {
    manageBondesModal.style.display = 'flex';
    manageBondesModal.classList.add('active');
});

function closeManageBondesModal() {
    manageBondesModal.classList.remove('active');
    setTimeout(() => {
        manageBondesModal.style.display = 'none';
    }, 300);
}

addStaffBtn.addEventListener('click', () => {
    addStaffModal.style.display = 'flex';
    addStaffModal.classList.add('active');
});

function closeAddStaffModal() {
    addStaffModal.classList.remove('active');
    setTimeout(() => {
        addStaffModal.style.display = 'none';
    }, 300);
    document.getElementById('add-staff-form').reset();
}

window.addEventListener('click', (event) => {
    if (event.target === addBondeModal) closeAddBondeModal();
    if (event.target === manageBondesModal) closeManageBondesModal();
    if (event.target === addStaffModal) closeAddStaffModal();
});

document.getElementById('add-bonde-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    const data = {
        modelo: formData.get('modelo'),
        capacidade: formData.get('capacidade'),
        ano_fabricacao: formData.get('ano_fabricacao'),
        descricao: formData.get('descricao'),
        ativo: formData.get('ativo') ? 1 : 0
    };

    fetch('/Sistema-CENTRAL-ERP/add_bonde.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Bonde adicionado com sucesso!');
            closeAddBondeModal();
            location.reload();
        } else {
            alert('Erro ao adicionar bonde: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro na conexão com o servidor.');
    });
});

document.getElementById('add-staff-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    const data = {
        nome: formData.get('nome'),
        tipo: formData.get('tipo')
    };

    fetch('/Sistema-CENTRAL-ERP/add_staff.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) throw new Error('Erro na resposta: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Funcionário adicionado com sucesso!');
            closeAddStaffModal();
            updateStaffSelects();
        } else {
            alert('Erro ao adicionar funcionário: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro na conexão com o servidor.');
    });
});
