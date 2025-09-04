

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

// State Variables
let transactions = [];
let selectedRowId = null;
let formMode = 'add'; // 'add', 'edit', or 'registerReturn'
let idOfSubidaToComplete = null; // ID of the 'ida' trip for registering a return
const MAX_PASSENGERS = 32;
const ROWS_PER_PAGE = 4;
let currentPage = 1;

// Return Options
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

// Utility Functions
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
    let totalSubindo = { pagantes: 0, gratuitos: 0, moradores: 0, passageiros: 0, bondes: new Set() };
    let totalRetorno = { pagantes: 0, gratuitos: 0, moradores: 0, passageiros: 0, bondes: new Set() };

    transactions.forEach(t => {
        const target = t.tipo_viagem === 'ida' ? totalSubindo : totalRetorno;
        target.pagantes += t.pagantes;
        target.gratuitos += t.gratuidade;
        target.moradores += t.moradores;
        target.passageiros += t.passageiros;
        target.bondes.add(t.bonde);
    });

    document.getElementById('total-subindo-pagantes').textContent = totalSubindo.pagantes;
    document.getElementById('total-subindo-gratuitos').textContent = totalSubindo.gratuitos;
    document.getElementById('total-subindo-moradores').textContent = totalSubindo.moradores;
    document.getElementById('total-subindo-passageiros').textContent = totalSubindo.passageiros;
    document.getElementById('total-bondes-saida').textContent = totalSubindo.bondes.size;

    document.getElementById('total-retorno-pagantes').textContent = totalRetorno.pagantes;
    document.getElementById('total-retorno-gratuitos').textContent = totalRetorno.gratuitos;
    document.getElementById('total-retorno-moradores').textContent = totalRetorno.moradores;
    document.getElementById('total-retorno-passageiros').textContent = totalRetorno.passageiros;
    document.getElementById('total-bondes-retorno').textContent = totalRetorno.bondes.size;
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
            row.insertCell().textContent = t.gratuidade;
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
    const totalGratuidade = gratPcdIdoso;
    const totalPassageiros = pagantes + moradores + totalGratuidade;

    gratuidadeInput.value = totalGratuidade;
    passageirosInput.value = totalPassageiros;
    updateProgressBar();
}

// Event Listeners
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

    if (!data.bonde || !data.maquinista || !data.agente || !data.data) {
        alert('Por favor, preencha os campos obrigatórios: Bonde, Maquinista, Agente e Data.');
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

    const hasReturn = transactions.some(t => t.tipo_viagem === 'chegada' && t.subida_id === transaction.id);

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
        moradoresInput.value = transaction.gratuidade;
        gratPcdIdosoInput.value = transaction.gratuidade;
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
        if (!transaction || transaction.tipo_viagem !== 'Subida') {
            alert('Selecione uma viagem de ida para registrar o retorno.');
            return;
        }

        if (transactions.some(t => t.tipo_viagem === 'retorno' && t.subida_id === transaction.id)) {
            alert('Esta viagem de partida já possui um retorno registrado.');
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
});

prevButton.addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        renderTransactions();
    }
});

nextButton.addEventListener('click', () => {
    const filteredTransactions = idFilterInput.value.trim()
        ? transactions.filter(t => t.id.toString().includes(idFilterInput.value.trim()))
        : transactions;
    if (currentPage < Math.ceil(filteredTransactions.length / ROWS_PER_PAGE)) {
        currentPage++;
        renderTransactions();
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    setTimeAndDate();
    await loadTransactions();
    clearForm();
    await renderTransactions();
    calculateCounts();
});