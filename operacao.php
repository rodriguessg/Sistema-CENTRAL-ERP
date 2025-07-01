<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Viagens - Bondes Santa Teresa</title>
    <link rel="stylesheet" href="./src/bonde/style/bonde.css">
</head>
<body>

    <div class="form-container" id="controle-viagem">
        <div class="header-section">
            <div>
                <div class="section-title">CADASTRAMENTO DE TRANSAÇÕES</div>
                <div class="input-group">
                    <div class="input-item">
                        <label for="bonde">BONDE</label>
                        <select id="bonde">
                            <option value="">Selecione</option>
                            <option value="BONDE 17">BONDE 17</option>
                            <option value="BONDE 16">BONDE 16</option>
                            <option value="BONDE 19">BONDE 19</option>
                            <option value="BONDE 22">BONDE 22</option>
                            <option value="BONDE 18">BONDE 18</option>
                            <option value="BONDE 20">BONDE 20</option>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="saida">SAÍDA</label>
                        <select id="saida">
                            <option value="Carioca">Carioca</option>
                            <option value="D.Irmãos">D.Irmãos</option>
                            <option value="Paula Mattos">Paula Mattos</option>
                            <option value="Silvestre">Silvestre</option>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="retorno">RETORNO</label>
                        <select id="retorno">
                            <option value="">Selecione (para retorno)</option>
                            <option value="Carioca">Carioca</option>
                            <option value="D.Irmãos">D.Irmãos</option>
                            <option value="Paula Mattos">Paula Mattos</option>
                            <option value="Silvestre">Silvestre</option>
                            <option value="Oficina">Oficina</option>
                        </select>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-item">
                        <label for="maquinistas">MAQUINISTAS</label>
                        <select id="maquinistas">
                            <option value="">Selecione</option>
                            <option value="Sergio Lima">Sergio Lima</option>
                            <option value="Adriano">Adriano</option>
                            <option value="Helio">Helio</option>
                            <option value="M. Celestino">M. Celestino</option>
                            <option value="Leonardo">Leonardo</option>
                            <option value="Andre">Andre</option>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="agentes">AGENTES</label>
                        <select id="agentes">
                            <option value="">Selecione</option>
                            <option value="Samir">Samir</option>
                            <option value="Vinicius">Vinicius</option>
                            <option value="P. Nascimento">P. Nascimento</option>
                            <option value="Oliveira">Oliveira</option>
                            <option value="Carlos">Carlos</option>
                        </select>
                    </div>
                    <div class="input-item">
                        <label for="hora">HORA</label>
                        <input type="text" id="hora" value="00:00:00" readonly>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-item">
                        <label for="pagantes">PAGANTES</label>
                        <input type="number" id="pagantes" value="0" min="0">
                    </div>
                    <div class="input-item">
                        <label for="moradores">MORADORES</label>
                        <input type="number" id="moradores" value="0" min="0">
                    </div>
                    <div class="input-item">
                        <label for="grat_pcd_idoso">GRAT. PCD/IDOSO</label>
                        <input type="number" id="grat_pcd_idoso" value="0" min="0">
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-item">
                        <label for="gratuidade">GRATUIDADE</label>
                        <input type="number" id="gratuidade" value="0" readonly>
                    </div>
                    <div class="input-item">
                        <label for="passageiros">PASSAGEIROS</label>
                        <input type="number" id="passageiros" value="0" readonly>
                    </div>
                    <div class="input-item">
                        <label for="viagem">VIAGEM</label>
                        <input type="number" id="viagem" value="1" min="1">
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-item">
                        <label for="data">DATA</label>
                        <input type="date" id="data">
                    </div>
                    <div class="input-item progress-container">
                        <label>CAPACIDADE DO BONDE (Máx. 32 Passageiros)</label>
                        <div class="progress-bar">
                            <div class="progress-bar-fill" id="progress-bar-fill">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="counts-section">
                <div class="total-box">
                    <div class="section-title">TOTAL BONDES SUBINDO</div>
                    <div class="total-item"><span>Pagantes</span><span id="total-subindo-pagantes">0</span></div>
                    <div class="total-item"><span>Gratuitos</span><span id="total-subindo-gratuitos">0</span></div>
                    <div class="total-item"><span>Moradores</span><span id="total-subindo-moradores">0</span></div>
                    <div class="total-item"><span>Passageiros</span><span id="total-subindo-passageiros">0</span></div>
                    <div class="total-item"><span>Bondes Saída</span><span id="total-bondes-saida">0</span></div>
                </div>
                <div class="total-box">
                    <div class="section-title">TOTAL BONDES RETORNO</div>
                    <div class="total-item"><span>Pagantes</span><span id="total-retorno-pagantes">0</span></div>
                    <div class="total-item"><span>Gratuitos</span><span id="total-retorno-gratuitos">0</span></div>
                    <div class="total-item"><span>Moradores</span><span id="total-retorno-moradores">0</span></div>
                    <div class="total-item"><span>Passageiros</span><span id="total-retorno-passageiros">0</span></div>
                    <div class="total-item"><span>Bondes Retorno</span><span id="total-bondes-retorno">0</span></div>
                </div>
            </div>
        </div>

        <div class="buttons-section">
            <button id="add-btn">Adicionar</button>
            <button id="clear-form-btn">Limpar</button>
            <button id="delete-btn" disabled>Excluir</button>
            <button id="alter-btn" disabled>Alterar</button>
            <button id="clear-transactions-btn">Limpar Transações</button>
            <div class="id-input-container">
                <label for="id-filter">ID:</label>
                <input type="text" id="id-filter" placeholder="Filtrar por ID">
            </div>
        </div>

        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>ID-M</th>
                        <th>Bondes</th>
                        <th>Saída</th>
                        <th>Retorno</th>
                        <th>Maquinista</th>
                        <th>Agente</th>
                        <th>Hora</th>
                        <th>Pagantes</th>
                        <th>Gratuidade</th>
                        <th>Moradores</th>
                        <th>Passageiros</th>
                        <th>Tipo Viagem</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody id="transactions-table-body">
                    <!-- Data will be populated here by JavaScript -->
                </tbody>
            </table>
            <div class="pagination">
                <button id="prev-page" disabled>Anterior</button>
                <span id="page-info"></span>
                <button id="next-page">Próximo</button>
            </div>
        </div>
    </div>

    <script>
        // Referências aos elementos do DOM
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

        let transactions = [];
        let selectedRowId = null;
        let formMode = 'add';
        let idOfSubidaToComplete = null;
        const MAX_PASSENGERS = 32;
        const rowsPerPage = 4;
        let currentPage = 1;

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

        function generateUniqueId() {
            return Math.floor(10000 + Math.random() * 90000);
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
            if (totalPassageiros > MAX_PASSENGERS) {
                progressBarFill.classList.add('danger');
            } else if (totalPassageiros >= MAX_PASSENGERS * 0.8) {
                progressBarFill.classList.add('warning');
            }
        }

        function updateTotals() {
            let totalSubindoPagantes = 0;
            let totalSubindoGratuitos = 0;
            let totalSubindoMoradores = 0;
            let totalSubindoPassageiros = 0;
            let totalBondesSaida = new Set();

            let totalRetornoPagantes = 0;
            let totalRetornoGratuitos = 0;
            let totalRetornoMoradores = 0;
            let totalRetornoPassageiros = 0;
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
                if (opt.value === selectedValue) {
                    option.selected = true;
                }
                retornoInput.appendChild(option);
            });
        }

        function renderTransactions() {
            transactionsTableBody.innerHTML = '';
            const filterId = idFilterInput ? idFilterInput.value.trim() : '';
            const filteredTransactions = transactions.filter(transaction => {
                if (filterId === '') return true;
                return transaction.id.toString().includes(filterId);
            });

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            const paginatedTransactions = filteredTransactions.slice(start, end);

            if (paginatedTransactions.length === 0) {
                const noDataRow = transactionsTableBody.insertRow();
                noDataRow.innerHTML = `<td colspan="13" style="text-align: center;">Nenhuma transação encontrada.</td>`;
            } else {
                paginatedTransactions.forEach(transaction => {
                    const row = transactionsTableBody.insertRow();
                    row.dataset.id = transaction.id;
                    if (transaction.tipoViagem === 'subida' && transaction.retorno === '') {
                        row.classList.add('subida-pendente');
                    }

                    row.insertCell().textContent = transaction.id;
                    row.insertCell().textContent = transaction.bonde;
                    row.insertCell().textContent = transaction.saida;
                    row.insertCell().textContent = transaction.retorno || 'Pendente';
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
                        if (currentSelected) {
                            currentSelected.classList.remove('selected');
                        }
                        row.classList.add('selected');
                        selectedRowId = transaction.id;

                        deleteBtn.disabled = false;
                        const selectedTransaction = transactions.find(t => t.id === selectedRowId);
                        if (selectedTransaction.tipoViagem === 'subida' && selectedTransaction.retorno === '') {
                            alterBtn.textContent = 'Registrar Retorno';
                            alterBtn.disabled = false;
                        } else {
                            alterBtn.textContent = 'Alterar';
                            alterBtn.disabled = false;
                        }
                    });
                });
            }

            const totalPages = Math.ceil(filteredTransactions.length / rowsPerPage);
            pageInfo.textContent = `Página ${currentPage} de ${totalPages}`;
            prevButton.disabled = currentPage === 1;
            nextButton.disabled = currentPage === totalPages || totalPages === 0;

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
            const currentSelected = document.querySelector('.table-section tr.selected');
            if (currentSelected) {
                currentSelected.classList.remove('selected');
            }
            setTimeAndDate();
            calculateCounts();
            updateProgressBar();
            currentPage = 1;
            renderTransactions();
        }

        function setTimeAndDate() {
            const now = new Date();
            const year = now.getFullYear();
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const day = now.getDate().toString().padStart(2, '0');
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');

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

        pagantesInput.addEventListener('input', calculateCounts);
        moradoresInput.addEventListener('input', calculateCounts);
        gratPcdIdosoInput.addEventListener('input', calculateCounts);

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
            if (totalPassageiros > MAX_PASSENGERS) {
                alert(`Atenção: O número total de passageiros (${totalPassageiros}) excede a capacidade máxima de ${MAX_PASSENGERS} por vagão.`);
                return;
            }

            if (!bonde || !maquinista || !agente || !data) {
                alert('Por favor, preencha os campos: Bonde, Maquinista, Agente e Data.');
                return;
            }

            const gratuidadeCalculated = gratPcdIdoso;
            const passageirosCalculated = totalPassageiros;

            if (formMode === 'add') {
                if (!saida) {
                    alert('Por favor, selecione a SAÍDA da viagem.');
                    return;
                }
                if (!retorno) {
                    alert('Por favor, selecione o DESTINO da viagem de subida.');
                    return;
                }
                const ascentTransaction = {
                    id: generateUniqueId(),
                    bonde,
                    saida,
                    retorno,
                    maquinista,
                    agente,
                    hora,
                    pagantes,
                    gratPcdIdoso,
                    gratuidadeCalculated,
                    moradores,
                    passageirosCalculated,
                    viagem,
                    tipoViagem: 'subida',
                    data
                };
                transactions.push(ascentTransaction);
                alert('Viagem de SUBIDA adicionada com sucesso! O retorno está pendente.');

            } else if (formMode === 'registerReturn') {
                if (!idOfSubidaToComplete) {
                    alert('Erro: ID da viagem de subida para completar não encontrado.');
                    return;
                }
                if (!retorno) {
                    alert('Por favor, selecione o destino do retorno (Carioca ou Oficina).');
                    return;
                }

                const originalSubidaIndex = transactions.findIndex(t => t.id === idOfSubidaToComplete);
                let originalSubida = null;
                if (originalSubidaIndex !== -1) {
                    originalSubida = transactions[originalSubidaIndex];
                } else {
                    alert('Erro: Viagem de subida original não encontrada.');
                    return;
                }

                const returnTransaction = {
                    id: generateUniqueId(),
                    bonde,
                    saida: originalSubida.retorno,
                    retorno,
                    maquinista,
                    agente,
                    hora,
                    pagantes,
                    gratPcdIdoso,
                    gratuidadeCalculated,
                    moradores,
                    passageirosCalculated,
                    viagem: originalSubida.viagem + 0.5,
                    tipoViagem: 'retorno',
                    data
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
                    transactions[indexToUpdate] = {
                        id: selectedRowId,
                        bonde,
                        saida,
                        retorno,
                        maquinista,
                        agente,
                        hora,
                        pagantes,
                        gratPcdIdoso,
                        gratuidadeCalculated,
                        moradores,
                        passageirosCalculated,
                        viagem,
                        tipoViagem: transactions[indexToUpdate].tipoViagem,
                        data
                    };
                    alert('Transação alterada com sucesso!');
                } else {
                    alert('Erro: Transação não encontrada para alteração.');
                }
            }

            saveTransactions();
            currentPage = 1; // Reset to first page after adding/editing
            renderTransactions();
            clearForm();
        });

        clearFormBtn.addEventListener('click', clearForm);

        deleteBtn.addEventListener('click', () => {
            if (selectedRowId) {
                const transactionToDelete = transactions.find(t => t.id === selectedRowId);
                if (transactionToDelete && confirm(`Tem certeza que deseja excluir a transação ID ${selectedRowId} (${transactionToDelete.tipoViagem})?`)) {
                    if (transactionToDelete.tipoViagem === 'subida' && transactionToDelete.retorno !== '' && transactions.some(t => t.tipoViagem === 'retorno' && t.viagem === (transactionToDelete.viagem + 0.5) && t.bonde === transactionToDelete.bonde)) {
                        alert('Atenção: Esta viagem de subida já possui um retorno registrado. Excluir esta subida não removerá automaticamente o registro de retorno associado. Você precisará excluí-lo separadamente se desejar.');
                    }

                    transactions = transactions.filter(transaction => transaction.id !== selectedRowId);
                    saveTransactions();
                    currentPage = 1; // Reset to first page after deletion
                    renderTransactions();
                    clearForm();
                }
            } else {
                alert('Por favor, selecione uma transação na tabela para excluir.');
            }
        });

        alterBtn.addEventListener('click', () => {
            if (selectedRowId) {
                const transactionToHandle = transactions.find(transaction => transaction.id === selectedRowId);
                if (transactionToHandle) {
                    if (transactionToHandle.tipoViagem === 'subida' && transactionToHandle.retorno !== '' && !transactions.some(t => t.tipoViagem === 'retorno' && t.viagem === (transactionToHandle.viagem + 0.5) && t.bonde === transactionToHandle.bonde)) {
                        formMode = 'registerReturn';
                        idOfSubidaToComplete = transactionToHandle.id;

                        bondeInput.value = transactionToHandle.bonde;
                        maquinistasInput.value = transactionToHandle.maquinista;
                        agentesInput.value = transactionToHandle.agente;
                        dateInput.value = transactionToHandle.data;
                        horaInput.value = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                        saidaInput.value = transactionToHandle.retorno;
                        saidaInput.disabled = true;

                        populateRetornoOptions(returnDestinationOptions, 'Carioca');
                        retornoInput.disabled = false;

                        pagantesInput.value = '0';
                        moradoresInput.value = '0';
                        gratPcdIdosoInput.value = '0';
                        viagemInput.value = transactionToHandle.viagem + 0.5;

                        calculateCounts();
                        addBtn.textContent = 'Registrar Retorno';
                        alert('Formulário pronto para registrar o RETORNO para esta subida. Preencha os passageiros e clique em "Registrar Retorno".');

                    } else if (transactionToHandle.tipoViagem === 'subida' && transactionToHandle.retorno === '') {
                        formMode = 'registerReturn';
                        idOfSubidaToComplete = transactionToHandle.id;

                        bondeInput.value = transactionToHandle.bonde;
                        maquinistasInput.value = transactionToHandle.maquinista;
                        agentesInput.value = transactionToHandle.agente;
                        dateInput.value = transactionToHandle.data;
                        horaInput.value = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                        saidaInput.value = transactionToHandle.saida;
                        saidaInput.disabled = false;
                        populateRetornoOptions(defaultRetornoOptions, '');
                        retornoInput.disabled = false;

                        pagantesInput.value = transactionToHandle.pagantes;
                        moradoresInput.value = transactionToHandle.moradores;
                        gratPcdIdosoInput.value = transactionToHandle.gratPcdIdoso;
                        viagemInput.value = transactionToHandle.viagem;

                        calculateCounts();
                        addBtn.textContent = 'Registrar Retorno (Subida)';
                        alert('Formulário pronto para registrar o DESTINO da SUBIDA. Selecione o destino da subida e clique em "Registrar Retorno (Subida)".');

                    } else {
                        formMode = 'edit';
                        bondeInput.value = transactionToHandle.bonde;
                        saidaInput.value = transactionToHandle.saida;
                        saidaInput.disabled = false;
                        populateRetornoOptions(defaultRetornoOptions, transactionToHandle.retorno);
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
                        transactions = transactions.filter(transaction => transaction.id !== selectedRowId);
                        saveTransactions();
                        currentPage = 1; // Reset to first page after editing
                        renderTransactions();
                        selectedRowId = null;
                        deleteBtn.disabled = true;
                        alterBtn.disabled = true;
                        addBtn.textContent = 'Atualizar';
                        alert('Transação carregada para edição. Faça as alterações e clique em "Atualizar" para salvar.');
                    }
                }
            } else {
                alert('Por favor, selecione uma transação na tabela para alterar ou registrar um retorno.');
            }
        });

        clearTransactionsBtn.addEventListener('click', () => {
            if (confirm('Tem certeza que deseja limpar TODAS as transações? Esta ação não pode be desfeita.')) {
                transactions = [];
                saveTransactions();
                currentPage = 1; // Reset to first page after clearing
                renderTransactions();
                clearForm();
            }
        });

        idFilterInput.addEventListener('input', () => {
            currentPage = 1; // Reset to first page on filter change
            renderTransactions();
        });

        prevButton.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTransactions();
            }
        });

        nextButton.addEventListener('click', () => {
            const filterId = idFilterInput ? idFilterInput.value.trim() : '';
            const filteredTransactions = transactions.filter(transaction => {
                if (filterId === '') return true;
                return transaction.id.toString().includes(filterId);
            });
            if (currentPage < Math.ceil(filteredTransactions.length / rowsPerPage)) {
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
    </script>
</body>
</html>