// DOM Elements
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
const returnButton = document.getElementById('return-btn'); // Pode ser null se não existir

// State Variables
let transactions = [];
let selectedRowId = null;
let formMode = 'add';
let idOfSubidaToComplete = null; // Stores the ID of the subida transaction to which a return is being associated
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
function generateUniqueId() {
    const lastId = localStorage.getItem('bondesSantaTeresaLastId');
    const baseId = 19800;
    const newId = lastId ? parseInt(lastId) + 1 : baseId;
    localStorage.setItem('bondesSantaTeresaLastId', newId);
    return newId;
}

function saveTransactions() {
    localStorage.setItem('bondesSantaTeresaTransactions', JSON.stringify(transactions));
}

function loadTransactions() {
    const storedTransactions = localStorage.getItem('bondesSantaTeresaTransactions');
    if (storedTransactions) {
        transactions = JSON.parse(storedTransactions);
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
    if (totalPassageiros > MAX_PASSENGERS) progressBarFill.classList.add('danger');
    else if (totalPassageiros >= MAX_PASSENGERS * 0.8) progressBarFill.classList.add('warning');
}

function updateTotals() {
    let totalSubindoPagantes = 0, totalSubindoGratuitos = 0, totalSubindoMoradores = 0, totalSubindoPassageiros = 0;
    let totalBondesSaida = new Set();
    let totalRetornoPagantes = 0, totalRetornoGratuitos = 0, totalRetornoMoradores = 0, totalRetornoPassageiros = 0;
    let totalBondesRetorno = new Set();

    transactions.forEach(transaction => {
        const currentGratuidade = transaction.gratPcdIdoso;
        const currentPassageiros = transaction.pagantes + transaction.moradores + currentGratuidade;

        if (transaction.tipoViagem === 'subida') {
            totalSubindoPagantes += transaction.pagantes;
            totalSubindoGratuitos += currentGratuidade;
            totalSubindoMoradores += transaction.moradores;
            totalSubindoPassageiros += currentPassageiros;
            totalBondesSaida.add(transaction.bonde);
        } else if (transaction.tipoViagem === 'retorno') {
            totalRetornoPagantes += transaction.pagantes;
            totalRetornoGratuitos += currentGratuidade;
            totalRetornoMoradores += transaction.moradores;
            totalRetornoPassageiros += currentPassageiros;
            totalBondesRetorno.add(transaction.bonde);
        }
    });

    document.getElementById('total-subindo-pagantes').textContent = totalSubindoPagantes;
    document.getElementById('total-subindo-gratuitos').textContent = totalSubindoGratuitos;
    document.getElementById('total-subindo-moradores').textContent = totalSubindoMoradores;
    document.getElementById('total-subindo-passageiros').textContent = totalSubindoPassageiros;
    document.getElementById('total-bondes-saida').textContent = totalBondesSaida.size;

    document.getElementById('total-retorno-pagantes').textContent = totalRetornoPagantes;
    document.getElementById('total-retorno-gratuitos').textContent = totalRetornoGratuitos;
    document.getElementById('total-retorno-moradores').textContent = totalRetornoMoradores;
    document.getElementById('total-retorno-passageiros').textContent = totalRetornoPassageiros;
    document.getElementById('total-bondes-retorno').textContent = totalBondesRetorno.size;
}

function populateRetornoOptions(optionsArray, selectedValue = '') {
    retornoInput.innerHTML = '';
    optionsArray.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.text;
        if (opt.value === selectedValue) option.selected = true;
        retornoInput.appendChild(option);
    });
}

function renderTransactions() {
    transactionsTableBody.innerHTML = '';
    const filterId = idFilterInput.value.trim() || '';
    const filteredTransactions = transactions.filter(t => !filterId || t.id.toString().includes(filterId));

    const start = (currentPage - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;
    const paginatedTransactions = filteredTransactions.slice(start, end);

    if (paginatedTransactions.length === 0) {
        const noDataRow = transactionsTableBody.insertRow();
        noDataRow.innerHTML = `<td colspan="13" style="text-align: center;">Nenhuma transação encontrada.</td>`;
    } else {
        paginatedTransactions.forEach(transaction => {
            const row = transactionsTableBody.insertRow();
            row.dataset.id = transaction.id;

            // Determine if a subida is pending return. A subida is pending if it doesn't have a 'retornoId' linked to it.
            // Also, only show it as 'pending' if no return trip with the exact bonde and corresponding viagem number (viagem + 0.5) has been recorded yet.
            const hasReturnRecorded = transactions.some(t =>
                t.tipoViagem === 'retorno' &&
                t.bonde === transaction.bonde &&
                t.viagem === (transaction.viagem + 0.5) &&
                t.subidaId === transaction.id // Ensure it's explicitly linked to this subida
            );

            if (transaction.tipoViagem === 'subida' && !hasReturnRecorded) {
                row.classList.add('subida-pendente');
            } else if (transaction.tipoViagem === 'retorno') {
                row.classList.add('retorno-row');
            }

            row.insertCell().textContent = transaction.id;
            row.insertCell().textContent = transaction.bonde;
            row.insertCell().textContent = transaction.saida;
            // Display 'Pendente' only if it's a subida with no associated return trip
            row.insertCell().textContent = (transaction.tipoViagem === 'subida' && !hasReturnRecorded) ? 'Pendente' : transaction.retorno;
            row.insertCell().textContent = transaction.maquinista;
            row.insertCell().textContent = transaction.agente;
            row.insertCell().textContent = transaction.hora;
            row.insertCell().textContent = transaction.pagantes;
            row.insertCell().textContent = transaction.gratuidadeCalculated;
            row.insertCell().textContent = transaction.moradores;
            row.insertCell().textContent = transaction.passageirosCalculated;
            row.insertCell().textContent = transaction.tipoViagem;
            row.insertCell().textContent = transaction.data;

            row.addEventListener('click', () => {
                const currentSelected = document.querySelector('.table-section tr.selected');
                if (currentSelected) currentSelected.classList.remove('selected');
                row.classList.add('selected');
                selectedRowId = transaction.id;

                deleteBtn.disabled = false;
                const selectedTransaction = transactions.find(t => t.id === selectedRowId);

                // Check if a return has been recorded for this specific subida
                const returnExistsForThisSubida = transactions.some(t =>
                    t.tipoViagem === 'retorno' &&
                    t.bonde === selectedTransaction.bonde &&
                    t.viagem === (selectedTransaction.viagem + 0.5) &&
                    t.subidaId === selectedTransaction.id
                );

                if (returnButton && selectedTransaction.tipoViagem === 'subida' && !returnExistsForThisSubida) {
                    returnButton.style.display = 'inline-block'; // Show the "Registrar Retorno" button
                } else if (returnButton) {
                    returnButton.style.display = 'none'; // Hide the button if not a pending subida or if return already exists
                }
                alterBtn.textContent = 'Alterar';
                alterBtn.disabled = false;
            });
        });
    }

    const totalPages = Math.ceil(filteredTransactions.length / ROWS_PER_PAGE);
    pageInfo.textContent = `Página ${currentPage} de ${totalPages}`;
    prevButton.disabled = currentPage === 1;
    nextButton.disabled = currentPage === totalPages || !totalPages;
    updateTotals();
}

function clearForm() {
    bondeInput.value = '';
    saidaInput.value = 'Carioca';
    saidaInput.disabled = false;
    retornoInput.value = '';
    retornoInput.disabled = false;
    populateRetornoOptions(defaultRetornoOptions);
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
    if (returnButton) returnButton.style.display = 'none'; // Ensure the button is hidden when clearing
    const currentSelected = document.querySelector('.table-section tr.selected');
    if (currentSelected) currentSelected.classList.remove('selected');
    setTimeAndDate();
    calculateCounts();
    updateProgressBar();
    currentPage = 1;
    renderTransactions();
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
[pagantesInput, moradoresInput, gratPcdIdosoInput].forEach(input => input.addEventListener('input', calculateCounts));

addBtn.addEventListener('click', () => {
    const bonde = bondeInput.value;
    const saida = saidaInput.value;
    const retorno = retornoInput.value;
    const maquinista = maquinistasInput.value;
    const agente = agentesInput.value;
    const hora = horaInput.value;
    const pagantes = parseInt(pagantesInput.value) || 0;
    const moradores = parseInt(moradoresInput.value) || 0;
    const gratPcdIdoso = parseInt(gratPcdIdosoInput.value) || 0;
    const viagem = parseFloat(viagemInput.value) || 1;
    const data = dateInput.value;

    const totalPassageiros = pagantes + moradores + gratPcdIdoso;
    if (formMode === 'add' && totalPassageiros > MAX_PASSENGERS) {
        alert(`Atenção: O número total de passageiros (${totalPassageiros}) excede a capacidade máxima de ${MAX_PASSENGERS} por vagão para viagens de subida.`);
        return;
    }
    if (formMode === 'edit') {
        const transactionToUpdate = transactions.find(t => t.id === selectedRowId);
        if (transactionToUpdate && transactionToUpdate.tipoViagem === 'subida' && totalPassageiros > MAX_PASSENGERS) {
            alert(`Atenção: O número total de passageiros (${totalPassageiros}) excede a capacidade máxima de ${MAX_PASSENGERS} por vagão para esta viagem de subida.`);
            return;
        }
    }

    if (!bonde || !maquinista || !agente || !data) {
        alert('Por favor, preencha os campos: Bonde, Maquinista, Agente e Data.');
        return;
    }

    const gratuidadeCalculated = gratPcdIdoso;
    const passageirosCalculated = totalPassageiros;

    if (formMode === 'add') {
        if (!saida || !retorno) {
            alert('Por favor, selecione a SAÍDA e o DESTINO da viagem de subida.');
            return;
        }
        const ascentTransaction = {
            id: generateUniqueId(),
            bonde, saida, retorno, maquinista, agente, hora,
            pagantes, gratPcdIdoso, gratuidadeCalculated, moradores,
            passageirosCalculated, viagem, tipoViagem: 'subida', data
        };
        transactions.push(ascentTransaction);
        alert('Viagem de SUBIDA adicionada com sucesso! Um retorno pode ser registrado posteriormente.');

    } else if (formMode === 'registerReturn') {
        if (!idOfSubidaToComplete || !retorno) {
            alert('Erro: ID da viagem de subida ou destino do retorno não encontrado.');
            return;
        }
        const originalSubida = transactions.find(t => t.id === idOfSubidaToComplete);
        if (!originalSubida) {
            alert('Erro: Viagem de subida original não encontrada.');
            return;
        }

        const returnTransaction = {
            id: generateUniqueId(),
            bonde, saida: originalSubida.retorno, retorno, maquinista, agente, hora,
            pagantes, gratPcdIdoso, gratuidadeCalculated, moradores,
            passageirosCalculated, viagem: originalSubida.viagem + 0.5, tipoViagem: 'retorno', data,
            subidaId: originalSubida.id // Link the return to the specific subida
        };
        transactions.push(returnTransaction);
        alert('Retorno registrado com sucesso!');
    } else if (formMode === 'edit') {
        if (!selectedRowId) {
            alert('Erro: Nenhuma transação selecionada para alterar.');
            return;
        }
        const indexToUpdate = transactions.findIndex(t => t.id === selectedRowId);
        if (indexToUpdate !== -1) {
            try {
                // Preserve the original 'subidaId' if it's a return trip
                const originalSubidaId = transactions[indexToUpdate].subidaId || undefined;

                transactions[indexToUpdate] = {
                    id: selectedRowId,
                    bonde, saida, retorno, maquinista, agente, hora,
                    pagantes, gratPcdIdoso, gratuidadeCalculated, moradores,
                    passageirosCalculated, viagem, tipoViagem: transactions[indexToUpdate].tipoViagem, data,
                    subidaId: originalSubidaId
                };
                saveTransactions();
                alert('Transação alterada com sucesso!');
            } catch (error) {
                alert('Erro ao atualizar a transação: ' + error.message);
                return;
            }
        } else {
            alert('Erro: Transação não encontrada para alteração.');
            return;
        }
    }

    saveTransactions();
    currentPage = 1;
    renderTransactions();
    clearForm();
});

clearFormBtn.addEventListener('click', clearForm);

deleteBtn.addEventListener('click', () => {
    if (selectedRowId) {
        const transactionToDelete = transactions.find(t => t.id === selectedRowId);
        if (transactionToDelete && confirm(`Tem certeza que deseja excluir a transação ID ${selectedRowId} (${transactionToDelete.tipoViagem})?`)) {
            // If deleting a subida, also delete any linked return trips
            if (transactionToDelete.tipoViagem === 'subida') {
                transactions = transactions.filter(t => t.id !== selectedRowId && !(t.tipoViagem === 'retorno' && t.subidaId === selectedRowId));
                alert('Viagem de subida e seus retornos associados foram excluídos.');
            } else if (transactionToDelete.tipoViagem === 'retorno') {
                transactions = transactions.filter(t => t.id !== selectedRowId);
                alert('Viagem de retorno excluída.');
            }
            saveTransactions();
            currentPage = 1;
            renderTransactions();
            clearForm();
        }
    } else {
        alert('Selecione uma transação para excluir.');
    }
});

alterBtn.addEventListener('click', () => {
    if (selectedRowId) {
        const transactionToHandle = transactions.find(t => t.id === selectedRowId);
        if (transactionToHandle) {
            // Check if it's a subida and if it already has a return trip linked
            const hasReturnRecorded = transactions.some(t =>
                t.tipoViagem === 'retorno' &&
                t.bonde === transactionToHandle.bonde &&
                t.viagem === (transactionToHandle.viagem + 0.5) &&
                t.subidaId === transactionToHandle.id
            );

            if (transactionToHandle.tipoViagem === 'subida' && !hasReturnRecorded) {
                formMode = 'registerReturn';
                idOfSubidaToComplete = transactionToHandle.id;

                bondeInput.value = transactionToHandle.bonde;
                maquinistasInput.value = transactionToHandle.maquinista;
                agentesInput.value = transactionToHandle.agente;
                dateInput.value = transactionToHandle.data;
                horaInput.value = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                saidaInput.value = transactionToHandle.retorno; // The departure for return is the arrival of ascent
                saidaInput.disabled = true;
                populateRetornoOptions(returnDestinationOptions, 'Carioca'); // Pre-select Carioca for return
                retornoInput.disabled = false;

                pagantesInput.value = '0'; // Reset passenger counts for a new return trip
                moradoresInput.value = '0';
                gratPcdIdosoInput.value = '0';
                viagemInput.value = transactionToHandle.viagem + 0.5;

                calculateCounts();
                addBtn.textContent = 'Registrar Retorno';
                alert('Formulário pronto para registrar o RETORNO. Preencha os dados e confirme.');
                if (returnButton) returnButton.style.display = 'none'; // Hide the dedicated return button
            } else {
                formMode = 'edit';
                bondeInput.value = transactionToHandle.bonde;
                saidaInput.value = transactionToHandle.saida;
                saidaInput.disabled = false;
                populateRetornoOptions(defaultRetornoOptions, transactionToHandle.retorno || '');
                retornoInput.disabled = false;
                maquinistasInput.value = transactionToHandle.maquinista;
                agentesInput.value = transactionToHandle.agente;
                horaInput.value = transactionToHandle.hora;
                pagantesInput.value = transactionToHandle.pagantes;
                moradoresInput.value = transactionToHandle.moradores;
                gratPcdIdosoInput.value = transactionToHandle.gratPcdIdoso;
                viagemInput.value = transactionToHandle.viagem;
                dateInput.value = transactionToHandle.data;

                calculateCounts();
                addBtn.textContent = 'Atualizar';
                alert('Transação carregada para edição. Modifique e clique em "Atualizar".');
                if (returnButton) returnButton.style.display = 'none'; // Hide the dedicated return button
            }
        }
    } else {
        alert('Selecione uma transação para alterar ou registrar retorno.');
    }
});

// Add the listener only if returnButton exists
if (returnButton) {
    returnButton.addEventListener('click', () => {
        if (selectedRowId) {
            const transactionToHandle = transactions.find(t => t.id === selectedRowId);
            if (transactionToHandle && transactionToHandle.tipoViagem === 'subida') {
                // Check if a return has already been explicitly linked to this subida
                const returnExistsForThisSubida = transactions.some(t =>
                    t.tipoViagem === 'retorno' &&
                    t.bonde === transactionToHandle.bonde &&
                    t.viagem === (transactionToHandle.viagem + 0.5) &&
                    t.subidaId === transactionToHandle.id
                );

                if (!returnExistsForThisSubida) {
                    formMode = 'registerReturn';
                    idOfSubidaToComplete = transactionToHandle.id;

                    bondeInput.value = transactionToHandle.bonde;
                    maquinistasInput.value = transactionToHandle.maquinista;
                    agentesInput.value = transactionToHandle.agente;
                    dateInput.value = transactionToHandle.data;
                    horaInput.value = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                    saidaInput.value = transactionToHandle.retorno; // The departure for return is the arrival of ascent
                    saidaInput.disabled = true;
                    populateRetornoOptions(returnDestinationOptions, 'Carioca'); // Pre-select Carioca for return
                    retornoInput.disabled = false;

                    pagantesInput.value = '0'; // Reset passenger counts for a new return trip
                    moradoresInput.value = '0';
                    gratPcdIdosoInput.value = '0';
                    viagemInput.value = transactionToHandle.viagem + 0.5;

                    calculateCounts();
                    addBtn.textContent = 'Registrar Retorno';
                    alert('Formulário pronto para registrar o RETORNO. Preencha os dados e confirme.');
                    returnButton.style.display = 'none'; // Hide after clicking
                } else {
                    alert('Esta viagem de subida já possui um retorno registrado.');
                }
            }
        }
    });
}

clearTransactionsBtn.addEventListener('click', () => {
    if (confirm('Tem certeza que deseja limpar TODAS as transações? Esta ação não pode ser desfeita.')) {
        transactions = [];
        saveTransactions();
        currentPage = 1;
        renderTransactions();
        clearForm();
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
    const filterId = idFilterInput.value.trim() || '';
    const filteredTransactions = transactions.filter(t => !filterId || t.id.toString().includes(filterId));
    if (currentPage < Math.ceil(filteredTransactions.length / ROWS_PER_PAGE)) {
        currentPage++;
        renderTransactions();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    setTimeAndDate();
    loadTransactions();
    clearForm();
    renderTransactions();
    calculateCounts();
});