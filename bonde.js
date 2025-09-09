// Seleção de elementos do DOM para inputs do formulário e componentes da interface
const form = document.getElementById('viagem-form');
// Obtém o elemento do formulário com ID 'viagem-form', usado para enviar dados de uma viagem.
const bondeInput = document.getElementById('bonde');
// Obtém o elemento de input para selecionar o modelo do bonde.
const saidaInput = document.getElementById('saida');
// Obtém o elemento de input para selecionar o local de saída da viagem.
const retornoInput = document.getElementById('retorno');
// Obtém o elemento de input para selecionar o destino de retorno da viagem.
const maquinistasInput = document.getElementById('maquinistas');
// Obtém o elemento de input para selecionar o maquinista (condutor do bonde).
const agentesInput = document.getElementById('agentes');
// Obtém o elemento de input para selecionar o agente (responsável pela operação).
const horaInput = document.getElementById('hora');
// Obtém o elemento de input para o horário da viagem.
const pagantesInput = document.getElementById('pagantes');
// Obtém o elemento de input para o número de passageiros pagantes.
const moradoresInput = document.getElementById('moradores');
// Obtém o elemento de input para o número de passageiros moradores (com possível desconto).
const gratPcdIdosoInput = document.getElementById('grat_pcd_idoso');
// Obtém o elemento de input para o número de passageiros com gratuidade (PCD/idosos).
const gratuidadeInput = document.getElementById('gratuidade');
// Obtém o elemento de input para o total de passageiros com gratuidade (somente leitura).
const passageirosInput = document.getElementById('passageiros');
// Obtém o elemento de input para o total de passageiros (somente leitura).
const viagemInput = document.getElementById('viagem');
// Obtém o elemento de input para o número da viagem (ex.: 1 para ida, 1.5 para retorno).
const dateInput = document.getElementById('data');
// Obtém o elemento de input para a data da viagem.
const transactionsTableBody = document.getElementById('transactions-table-body');
// Obtém o corpo da tabela onde os registros das viagens são exibidos.
const addBtn = document.getElementById('add-btn');
// Obtém o botão para adicionar ou atualizar uma viagem.
const clearFormBtn = document.getElementById('clear-form-btn');
// Obtém o botão para limpar os campos do formulário.
const deleteBtn = document.getElementById('delete-btn');
// Obtém o botão para excluir uma viagem selecionada.
const alterBtn = document.getElementById('alter-btn');
// Obtém o botão para editar uma viagem selecionada ou registrar um retorno.
const clearTransactionsBtn = document.getElementById('clear-transactions-btn');
// Obtém o botão para limpar todos os registros de viagens.
const idFilterInput = document.getElementById('id-filter');
// Obtém o elemento de input para filtrar viagens por ID.
const progressBarFill = document.getElementById('progress-bar-fill');
// Obtém o elemento que exibe a barra de progresso da capacidade de passageiros.
const prevButton = document.getElementById('prev-page');
// Obtém o botão para navegar para a página anterior dos registros de viagens.
const nextButton = document.getElementById('next-page');
// Obtém o botão para navegar para a próxima página dos registros de viagens.
const pageInfo = document.getElementById('page-info');
// Obtém o elemento que exibe informações sobre a página atual e o total de páginas.
const returnButton = document.getElementById('return-btn');
// Obtém o botão para registrar o retorno de uma viagem de ida selecionada.

// Variáveis de estado
let transactions = [];
// Inicializa um array vazio para armazenar os registros de viagens obtidos do servidor.
let selectedRowId = null;
// Armazena o ID da linha de viagem selecionada na tabela, ou null se nenhuma estiver selecionada.
let formMode = 'add'; // 'add', 'edit', ou 'registerReturn'
// Controla o modo do formulário: 'add' para nova viagem, 'edit' para edição, ou 'registerReturn' para retorno.
let idOfSubidaToComplete = null; // ID da viagem de 'ida' para registrar um retorno
// Armazena o ID da viagem de ida quando se está registrando um retorno correspondente.
const MAX_PASSENGERS = 32;
// Define a capacidade máxima de passageiros por bonde (constante).
const ROWS_PER_PAGE = 4;
// Define o número de registros de viagens exibidos por página (constante).
let currentPage = 1;
// Controla o número da página atual para a paginação.

// Opções de retorno
const defaultRetornoOptions = [
    { value: '', text: 'Selecione (para retorno)' },
    { value: 'Carioca', text: 'Carioca' },
    { value: 'D.Irmãos', text: 'D.Irmãos' },
    { value: 'Paula Mattos', text: 'Paula Mattos' },
    { value: 'Silvestre', text: 'Silvestre' },
    { value: 'Oficina', text: 'Oficina' }
];
// Define as opções padrão para o dropdown de destinos de retorno, incluindo um placeholder.
const returnDestinationOptions = [
    { value: 'Carioca', text: 'Carioca' },
    { value: 'Oficina', text: 'Oficina' }
];
// Define opções limitadas para destinos de retorno ao registrar uma viagem de retorno.

// Funções utilitárias
async function loadTransactions() {
    // Define uma função assíncrona para carregar os registros de viagens do servidor.
    try {
        // Inicia um bloco try-catch para lidar com possíveis erros durante a requisição.
        const response = await fetch('./get_viagens.php');
        // Faz uma requisição assíncrona ao endpoint 'get_viagens.php' para obter os dados das viagens.
        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
        // Verifica se a resposta HTTP não está OK (ex.: 404, 500) e lança um erro com o código de status.
        transactions = await response.json();
        // Converte a resposta em JSON e armazena os dados no array transactions.
    } catch (error) {
        // Captura qualquer erro durante a requisição ou conversão do JSON.
        console.error('Erro ao carregar transações:', error);
        // Registra o erro no console para depuração.
        alert('Erro ao carregar transações: ' + error.message);
        // Exibe um alerta ao usuário com a mensagem de erro.
        transactions = [];
        // Reseta o array transactions para vazio para evitar problemas com dados inválidos.
    }
}

function updateProgressBar() {
    // Define uma função para atualizar a barra de progresso da capacidade de passageiros.
    const pagantes = parseInt(pagantesInput.value) || 0;
    // Obtém o número de passageiros pagantes do input, convertendo para inteiro ou usando 0 como padrão.
    const moradores = parseInt(moradoresInput.value) || 0;
    // Obtém o número de passageiros moradores, convertendo para inteiro ou usando 0 como padrão.
    const gratPcdIdoso = parseInt(gratPcdIdosoInput.value) || 0;
    // Obtém o número de passageiros com gratuidade (PCD/idosos), convertendo para inteiro ou usando 0.
    const totalPassageiros = pagantes + moradores + gratPcdIdoso;
    // Calcula o total de passageiros somando as três categorias.
    const percentage = Math.min((totalPassageiros / MAX_PASSENGERS) * 100, 100);
    // Calcula a porcentagem de ocupação em relação à capacidade máxima, limitando a 100%.
    progressBarFill.style.width = `${percentage}%`;
    // Define a largura da barra de progresso para refletir a porcentagem de ocupação.
    progressBarFill.textContent = `${totalPassageiros}/${MAX_PASSENGERS} (${Math.round(percentage)}%)`;
    // Atualiza o texto da barra de progresso com o total de passageiros, capacidade máxima e porcentagem arredondada.
    progressBarFill.classList.remove('warning', 'danger');
    // Remove as classes 'warning' e 'danger' da barra de progresso para reiniciar o estado visual.
    if (totalPassageiros > MAX_PASSENGERS) {
        // Verifica se o total de passageiros excede a capacidade máxima.
        progressBarFill.classList.add('danger');
        // Adiciona a classe 'danger' para indicar visualmente a superlotação (ex.: estilo vermelho).
    } else if (totalPassageiros >= MAX_PASSENGERS * 0.8) {
        // Verifica se o total de passageiros está em ou acima de 80% da capacidade.
        progressBarFill.classList.add('warning');
        // Adiciona a classe 'warning' para indicar alta ocupação (ex.: estilo amarelo).
    }
}
function updateTotals() {
            // Define uma função para calcular e exibir os totais de viagens de ida e retorno.
    let totalSubindo = { pagantes: 0, gratuitos: 0, moradores: 0, passageiros: 0, bondes: new Set() };
    // Inicializa um objeto para rastrear totais de viagens de ida, com um Set para bondes únicos.
    let totalRetorno = { pagantes: 0, gratuitos: 0, moradores: 0, passageiros: 0, bondes: new Set() };
    // Inicializa um objeto para rastrear totais de viagens de retorno, com um Set para bondes únicos.
    
    // Verifica se transactions está definido e é um array
    if (!Array.isArray(transactions)) {
        console.error('Erro: transactions não está definido ou não é um array');
        return;
    }

    transactions.forEach(t => {
        // Itera sobre cada transação no array transactions.
        const target = t.tipo_viagem.toLowerCase().includes('ida') || t.tipo_viagem.toLowerCase().includes('pendente') 
            ? totalSubindo 
            : totalRetorno;
        // Seleciona o objeto de totais apropriado com base no tipo de viagem ('ida', 'pendente' ou 'retorno').
        
        // Converte valores para números e trata valores undefined ou inválidos
        target.pagantes += Number(t.pagantes) || 0;
        target.gratuitos += Number(t.gratuidade) || 0;
        target.moradores += Number(t.moradores) || 0;
        target.passageiros += Number(t.passageiros) || 0;
        target.bondes.add(t.bonde || 'Desconhecido');
        // Adiciona o modelo do bonde ao Set de bondes únicos, com valor padrão se undefined.
    });

    // Atualiza os elementos DOM com os totais calculados
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
    // Define uma função para preencher o dropdown de destinos de retorno com opções.
    retornoInput.innerHTML = '';
    // Limpa as opções existentes no dropdown de retorno.
    options.forEach(opt => {
        // Itera sobre o array de opções fornecido.
        const option = document.createElement('option');
        // Cria um novo elemento <option> para o dropdown.
        option.value = opt.value;
        // Define o valor do atributo value da opção.
        option.textContent = opt.text;
        // Define o texto exibido da opção.
        if (opt.value === selectedValue) option.selected = true;
        // Marca a opção como selecionada se seu valor corresponder ao selectedValue.
        retornoInput.appendChild(option);
        // Adiciona a opção ao dropdown de retorno.
    });
}

async function renderTransactions() {
    // Define uma função assíncrona para renderizar os registros de viagens na tabela.
    transactionsTableBody.innerHTML = '';
    // Limpa o conteúdo existente do corpo da tabela.
    const filterId = idFilterInput.value.trim();
    // Obtém o valor do filtro de ID, removendo espaços em branco.
    const filteredTransactions = filterId
        ? transactions.filter(t => t.id.toString().includes(filterId))
        : transactions;
    // Filtra as transações pelo ID se um filtro for fornecido; caso contrário, usa todas as transações.
    const start = (currentPage - 1) * ROWS_PER_PAGE;
    // Calcula o índice inicial para a página atual com base na paginação.
    const end = start + ROWS_PER_PAGE;
    // Calcula o índice final para a página atual.
    const paginatedTransactions = filteredTransactions.slice(start, end);
    // Extrai as transações da página atual usando os índices inicial e final.
    if (paginatedTransactions.length === 0) {
        // Verifica se não há transações para exibir.
        const row = transactionsTableBody.insertRow();
        // Insere uma nova linha no corpo da tabela.
        row.innerHTML = `<td colspan="13" style="text-align: center;">Nenhuma transação encontrada.</td>`;
        // Define o conteúdo da linha para indicar que nenhuma transação foi encontrada, ocupando 13 colunas.
    } else {
        // Caso existam transações para exibir, prossegue com a renderização.
        paginatedTransactions.forEach(t => {
            // Itera sobre cada transação no conjunto paginado.
            const row = transactionsTableBody.insertRow();
            // Insere uma nova linha no corpo da tabela para a transação.
            row.dataset.id = t.id;
            // Define um atributo data-id na linha com o ID da transação.
            const hasReturn = transactions.some(r => r.tipo_viagem === 'retorno' && r.subida_id === t.id);
            // Verifica se a transação de ida atual possui um retorno vinculado.
            if (t.tipo_viagem === 'ida' && !hasReturn) {
                // Se a viagem é de ida e não possui retorno, marca como pendente.
                row.classList.add('ida-pendente');
                // Adiciona a classe 'ida-pendente' para indicar visualmente uma ida sem retorno.
            } else if (t.tipo_viagem === 'retorno') {
                // Se a viagem é de retorno, aplica o estilo correspondente.
                row.classList.add('retorno-row');
                // Adiciona a classe 'retorno-row' para estilizar viagens de retorno.
            }
            row.insertCell().textContent = t.id;
            // Adiciona uma célula com o ID da transação.
            row.insertCell().textContent = t.bonde;
            // Adiciona uma célula com o modelo do bonde.
            row.insertCell().textContent = t.saida;
            // Adiciona uma célula com o local de saída.
            row.insertCell().textContent = t.tipo_viagem === 'ida' && !hasReturn ? 'Pendente' : t.retorno;
            // Adiciona uma célula com o destino de retorno, mostrando 'Pendente' para idas sem retorno.
            row.insertCell().textContent = t.maquinista;
            // Adiciona uma célula com o nome do maquinista.
            row.insertCell().textContent = t.agente;
            // Adiciona uma célula com o nome do agente.
            row.insertCell().textContent = t.hora;
            // Adiciona uma célula com o horário da viagem.
            row.insertCell().textContent = t.pagantes;
            // Adiciona uma célula com o número de passageiros pagantes.
            row.insertCell().textContent = t.gratuidade;
            // Adiciona uma célula com o número de passageiros com gratuidade.
            row.insertCell().textContent = t.moradores;
            // Adiciona uma célula com o número de passageiros moradores.
            row.insertCell().textContent = t.passageiros;
            // Adiciona uma célula com o total de passageiros.
            row.insertCell().textContent = t.tipo_viagem;
            // Adiciona uma célula com o tipo de viagem (ida ou retorno).
            row.insertCell().textContent = t.data;
            // Adiciona uma célula com a data da viagem.
            row.addEventListener('click', () => {
                // Adiciona um ouvinte de evento de clique à linha para seleção.
                document.querySelector('.table-section tr.selected')?.classList.remove('selected');
                // Remove a classe 'selected' de qualquer linha previamente selecionada.
                row.classList.add('selected');
                // Adiciona a classe 'selected' à linha clicada.
                selectedRowId = t.id;
                // Armazena o ID da transação selecionada.
                deleteBtn.disabled = false;
                // Habilita o botão de exclusão, pois uma linha foi selecionada.
                alterBtn.disabled = false;
                // Habilita o botão de alteração, pois uma linha foi selecionada.
                alterBtn.textContent = 'Alterar';
                // Define o texto do botão de alteração como 'Alterar'.
                const hasReturnForThisIda = transactions.some(r => r.tipo_viagem === 'retorno' && r.subida_id === t.id);
                // Verifica se a viagem de ida selecionada possui um retorno vinculado.
                if (returnButton && t.tipo_viagem === 'ida' && !hasReturnForThisIda) {
                    // Se existe um botão de retorno e a viagem é de ida sem retorno...
                    returnButton.style.display = 'inline-block';
                    // Exibe o botão de retorno para permitir o registro do retorno.
                } else if (returnButton) {
                    // Caso contrário, se o botão de retorno existe...
                    returnButton.style.display = 'none';
                    // Oculta o botão de retorno (ex.: para viagens de retorno ou idas com retorno).
                }
            });
        });
    }
    const totalPages = Math.ceil(filteredTransactions.length / ROWS_PER_PAGE);
    // Calcula o número total de páginas com base nas transações filtradas e linhas por página.
    pageInfo.textContent = `Página ${currentPage} de ${totalPages || 1}`;
    // Atualiza o texto de informações da página para mostrar a página atual e o total de páginas (1 se não houver páginas).
    prevButton.disabled = currentPage === 1;
    // Desabilita o botão de página anterior se estiver na primeira página.
    nextButton.disabled = currentPage === totalPages || totalPages === 0;
    // Desabilita o botão de próxima página se estiver na última página ou não houver páginas.
    updateTotals();
    // Chama a função para atualizar os totais de viagens de ida e retorno na interface.
}

function clearForm() {
    // Define uma função para limpar todos os campos do formulário e redefinir o estado.
    bondeInput.value = '';
    // Limpa o campo de seleção do bonde.
    saidaInput.value = 'Carioca';
    // Define o campo de saída com o valor padrão 'Carioca'.
    saidaInput.disabled = false;
    // Habilita o campo de saída para edição.
    populateRetornoOptions(defaultRetornoOptions);
    // Preenche o dropdown de retorno com as opções padrão.
    retornoInput.disabled = false;
    // Habilita o campo de retorno para edição.
    maquinistasInput.value = '';
    // Limpa o campo de seleção do maquinista.
    agentesInput.value = '';
    // Limpa o campo de seleção do agente.
    horaInput.value = '';
    // Limpa o campo de horário.
    pagantesInput.value = '0';
    // Define o número de passageiros pagantes como 0.
    moradoresInput.value = '0';
    // Define o número de passageiros moradores como 0.
    gratPcdIdosoInput.value = '0';
    // Define o número de passageiros com gratuidade (PCD/idosos) como 0.
    gratuidadeInput.value = '0';
    // Define o total de gratuidades como 0 (somente leitura).
    passageirosInput.value = '0';
    // Define o total de passageiros como 0 (somente leitura).
    viagemInput.value = '1';
    // Define o número da viagem como 1 (padrão para viagens de ida).
    dateInput.value = '';
    // Limpa o campo de data.
    idFilterInput.value = '';
    // Limpa o campo de filtro por ID.
    selectedRowId = null;
    // Reseta o ID da linha selecionada.
    idOfSubidaToComplete = null;
    // Reseta o ID da viagem de ida para registro de retorno.
    formMode = 'add';
    // Define o modo do formulário como 'add' (adicionar nova viagem).
    addBtn.textContent = 'Adicionar';
    // Define o texto do botão de adicionar como 'Adicionar'.
    deleteBtn.disabled = true;
    // Desabilita o botão de exclusão, pois nenhuma linha está selecionada.
    alterBtn.disabled = true;
    // Desabilita o botão de alteração, pois nenhuma linha está selecionada.
    if (returnButton) returnButton.style.display = 'none';
    // Se o botão de retorno existe, oculta-o.
    document.querySelector('.table-section tr.selected')?.classList.remove('selected');
    // Remove a classe 'selected' de qualquer linha selecionada na tabela.
    setTimeAndDate();
    // Chama a função para definir a data e hora atuais nos campos correspondentes.
    calculateCounts();
    // Chama a função para recalcular os totais de passageiros e atualizar a barra de progresso.
}

function setTimeAndDate() {
    // Define uma função para preencher os campos de data e hora com os valores atuais.
    const now = new Date();
    // Cria um objeto Date com a data e hora atuais.
    const year = now.getFullYear();
    // Obtém o ano atual (ex.: 2025).
    const month = String(now.getMonth() + 1).padStart(2, '0');
    // Obtém o mês atual (0-11, soma 1) e formata com dois dígitos (ex.: '09').
    const day = String(now.getDate()).padStart(2, '0');
    // Obtém o dia atual e formata com dois dígitos (ex.: '04').
    const hours = String(now.getHours()).padStart(2, '0');
    // Obtém a hora atual e formata com dois dígitos (ex.: '15').
    const minutes = String(now.getMinutes()).padStart(2, '0');
    // Obtém os minutos atuais e formata com dois dígitos (ex.: '55').
    const seconds = String(now.getSeconds()).padStart(2, '0');
    // Obtém os segundos atuais e formata com dois dígitos (ex.: '00').
    dateInput.value = `${year}-${month}-${day}`;
    // Define o campo de data no formato 'YYYY-MM-DD' (ex.: '2025-09-04').
    horaInput.value = `${hours}:${minutes}:${seconds}`;
    // Define o campo de hora no formato 'HH:MM:SS' (ex.: '15:55:00').
}

function calculateCounts() {
    // Define uma função para calcular os totais de passageiros e atualizar a barra de progresso.
    const pagantes = parseInt(pagantesInput.value) || 0;
    // Obtém o número de passageiros pagantes, convertendo para inteiro ou usando 0 como padrão.
    const moradores = parseInt(moradoresInput.value) || 0;
    // Obtém o número de passageiros moradores, convertendo para inteiro ou usando 0 como padrão.
    const gratPcdIdoso = parseInt(gratPcdIdosoInput.value) || 0;
    // Obtém o número de passageiros com gratuidade (PCD/idosos), convertendo para inteiro ou 0.
    const totalGratuidade = gratPcdIdoso;
    // Define o total de gratuidades como o valor de gratPcdIdoso (pode ser expandido no futuro).
    const totalPassageiros = pagantes + moradores + totalGratuidade;
    // Calcula o total de passageiros somando as três categorias.
    gratuidadeInput.value = totalGratuidade;
    // Atualiza o campo de gratuidade (somente leitura) com o total calculado.
    passageirosInput.value = totalPassageiros;
    // Atualiza o campo de total de passageiros (somente leitura) com o valor calculado.
    updateProgressBar();
    // Chama a função para atualizar a barra de progresso com base nos totais.
}

// Listeners de eventos
[pagantesInput, moradoresInput, gratPcdIdosoInput].forEach(input => {
    // Itera sobre os inputs de passageiros (pagantes, moradores, gratuidade).
    input.addEventListener('input', calculateCounts);
    // Adiciona um ouvinte de evento para recalcular os totais sempre que o valor do input mudar.
});

form.addEventListener('submit', async (e) => {
    // Adiciona um ouvinte de evento para o envio do formulário.
    e.preventDefault();
    // Impede o comportamento padrão do formulário (recarregar a página).
    const data = {
        // Cria um objeto com os dados do formulário para enviar ao servidor.
        bonde: bondeInput.value,
        // Obtém o valor do campo de bonde.
        saida: saidaInput.value,
        // Obtém o valor do campo de saída.
        retorno: retornoInput.value,
        // Obtém o valor do campo de retorno.
        maquinista: maquinistasInput.value,
        // Obtém o valor do campo de maquinista.
        agente: agentesInput.value,
        // Obtém o valor do campo de agente.
        hora: horaInput.value,
        // Obtém o valor do campo de horário.
        pagantes: parseInt(pagantesInput.value) || 0,
        // Obtém o número de passageiros pagantes, convertendo para inteiro ou 0.
        grat_pcd_idoso: parseInt(gratPcdIdosoInput.value) || 0,
        // Obtém o número de passageiros com gratuidade, convertendo para inteiro ou 0.
        gratuidade: parseInt(gratuidadeInput.value) || 0,
        // Obtém o total de gratuidades, convertendo para inteiro ou 0.
        moradores: parseInt(moradoresInput.value) || 0,
        // Obtém o número de passageiros moradores, convertendo para inteiro ou 0.
        passageiros: parseInt(passageirosInput.value) || 0,
        // Obtém o total de passageiros, convertendo para inteiro ou 0.
        viagem: parseFloat(viagemInput.value) || 1,
        // Obtém o número da viagem, convertendo para float ou usando 1 como padrão.
        data: dateInput.value,
        // Obtém o valor do campo de data.
        tipo_viagem: '',
        // Inicializa o tipo de viagem como vazio (será definido posteriormente).
        subida_id: null
        // Inicializa o ID da viagem de ida como null (usado para retornos).
    };
    const totalPassageiros = data.pagantes + data.moradores + data.grat_pcd_idoso;
    // Calcula o total de passageiros para validação.
    if ((formMode === 'add' || (formMode === 'edit' && transactions.find(t => t.id === selectedRowId)?.tipo_viagem === 'ida')) && totalPassageiros > MAX_PASSENGERS) {
        // Verifica se o modo é 'add' ou se está editando uma viagem de ida e o total de passageiros excede a capacidade máxima.
        alert(`Atenção: O número total de passageiros (${totalPassageiros}) excede a capacidade máxima de ${MAX_PASSENGERS}.`);
        // Exibe um alerta se a capacidade for excedida.
        return;
        // Interrompe a execução para evitar o envio do formulário.
    }
    if (!data.bonde || !data.maquinista || !data.agente || !data.data) {
        // Verifica se os campos obrigatórios (bonde, maquinista, agente, data) estão preenchidos.
        alert('Por favor, preencha os campos obrigatórios: Bonde, Maquinista, Agente e Data.');
        // Exibe um alerta se algum campo obrigatório estiver vazio.
        return;
        // Interrompe a execução.
    }
    if (formMode === 'add') {
        // Se o modo do formulário é 'add' (adicionar nova viagem)...
        if (!data.saida || !data.retorno) {
            // Verifica se os campos de saída e retorno estão preenchidos.
            alert('Por favor, selecione a Saída e o Destino da viagem de ida.');
            // Exibe um alerta se saída ou retorno estiverem vazios.
            return;
            // Interrompe a execução.
        }
        data.tipo_viagem = 'ida';
        // Define o tipo de viagem como 'ida' para novas viagens.
    } else if (formMode === 'registerReturn') {
        // Se o modo do formulário é 'registerReturn' (registrar retorno)...
        if (!idOfSubidaToComplete || !data.retorno) {
            // Verifica se há um ID de ida válido e um destino de retorno selecionado.
            alert('Erro: Selecione uma viagem de ida pendente e um destino de retorno.');
            // Exibe um alerta se faltar o ID da ida ou o destino de retorno.
            return;
            // Interrompe a execução.
        }
        data.tipo_viagem = 'retorno';
        // Define o tipo de viagem como 'retorno'.
        data.subida_id = idOfSubidaToComplete;
        // Define o ID da viagem de ida associada ao retorno.
    } else if (formMode === 'edit') {
        // Se o modo do formulário é 'edit' (editar viagem existente)...
        if (!selectedRowId) {
            // Verifica se há uma transação selecionada para edição.
            alert('Erro: Nenhuma transação selecionada para alterar.');
            // Exibe um alerta se nenhuma linha estiver selecionada.
            return;
            // Interrompe a execução.
        }
        data.id = selectedRowId;
        // Define o ID da transação a ser editada.
        data.tipo_viagem = transactions.find(t => t.id === selectedRowId).tipo_viagem;
        // Mantém o tipo de viagem original da transação.
        data.subida_id = transactions.find(t => t.id === selectedRowId).subida_id || null;
        // Mantém o ID da viagem de ida associada, se houver.
    }
    const url = formMode === 'edit' ? './update_viagem.php' : './add_viagem.php';
    // Define a URL do endpoint com base no modo: 'update_viagem.php' para edição, 'add_viagem.php' para adição ou retorno.
    try {
        // Inicia um bloco try-catch para lidar com erros na requisição ao servidor.
        const response = await fetch(url, {
            // Faz uma requisição ao servidor com a URL definida.
            method: 'POST',
            // Usa o método POST para enviar os dados.
            headers: { 'Content-Type': 'application/json' },
            // Define o cabeçalho para indicar que os dados são JSON.
            body: JSON.stringify(data)
            // Converte o objeto de dados para JSON e o envia no corpo da requisição.
        });
        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
        // Verifica se a resposta HTTP não está OK e lança um erro com o código de status.
        const result = await response.json();
        // Converte a resposta do servidor em JSON.
        if (result.success) {
            // Verifica se a operação foi bem-sucedida com base no campo 'success' da resposta.
            alert(formMode === 'edit' ? 'Transação alterada com sucesso!' :
                  formMode === 'registerReturn' ? 'Retorno registrado com sucesso!' :
                  'Viagem de ida adicionada com sucesso!');
            // Exibe uma mensagem de sucesso específica para o modo do formulário.
            await loadTransactions();
            // Recarrega os dados das viagens do servidor.
            currentPage = 1;
            // Reseta para a primeira página da tabela.
            await renderTransactions();
            // Re-renderiza a tabela com os dados atualizados.
            clearForm();
            // Limpa o formulário após a operação.
        } else {
            // Caso a operação não tenha sido bem-sucedida...
            alert('Erro: ' + result.message);
            // Exibe um alerta com a mensagem de erro retornada pelo servidor.
        }
    } catch (error) {
        // Captura qualquer erro durante a requisição ou conversão do JSON.
        console.error('Erro ao salvar transação:', error);
        // Registra o erro no console para depuração.
        alert('Erro na conexão com o servidor: ' + error.message);
        // Exibe um alerta ao usuário com a mensagem de erro.
    }
});

clearFormBtn.addEventListener('click', clearForm);
// Adiciona um ouvinte de evento ao botão de limpar formulário, chamando a função clearForm.

deleteBtn.addEventListener('click', async () => {
    // Adiciona um ouvinte de evento ao botão de exclusão.
    if (!selectedRowId) {
        // Verifica se uma linha foi selecionada.
        alert('Selecione uma transação para excluir.');
        // Exibe um alerta se nenhuma linha estiver selecionada.
        return;
        // Interrompe a execução.
    }
    const transaction = transactions.find(t => t.id === selectedRowId);
    // Encontra a transação correspondente ao ID selecionado.
    if (!transaction || !confirm(`Tem certeza que deseja excluir a transação ID ${selectedRowId} (${transaction.tipo_viagem})?`)) {
        // Verifica se a transação existe e pede confirmação ao usuário para excluir.
        return;
        // Interrompe a execução se não houver transação ou o usuário cancelar.
    }
    try {
        // Inicia um bloco try-catch para lidar com erros na requisição de exclusão.
        if (transaction.tipo_viagem === 'ida') {
            // Se a transação é uma viagem de ida...
            const linkedRetornos = transactions.filter(t => t.tipo_viagem === 'retorno' && t.subida_id === selectedRowId);
            // Encontra todas as viagens de retorno vinculadas à ida selecionada.
            for (const retorno of linkedRetornos) {
                // Itera sobre cada retorno vinculado.
                await fetch('./delete_viagem.php', {
                    // Faz uma requisição para excluir o retorno.
                    method: 'POST',
                    // Usa o método POST.
                    headers: { 'Content-Type': 'application/json' },
                    // Define o cabeçalho para JSON.
                    body: JSON.stringify({ id: retorno.id })
                    // Envia o ID do retorno no corpo da requisição.
                });
            }
        }
        const response = await fetch('./delete_viagem.php', {
            // Faz uma requisição para excluir a transação selecionada.
            method: 'POST',
            // Usa o método POST.
            headers: { 'Content-Type': 'application/json' },
            // Define o cabeçalho para JSON.
            body: JSON.stringify({ id: selectedRowId })
            // Envia o ID da transação no corpo da requisição.
        });
        if (!response.ok) throw new Error(`Erro HTTP: ${response.status}`);
        // Verifica se a resposta HTTP não está OK e lança um erro.
        const result = await response.json();
        // Converte a resposta do servidor em JSON.
        if (result.success) {
            // Verifica se a exclusão foi bem-sucedida.
            alert('Transação excluída com sucesso!');
            // Exibe uma mensagem de sucesso.
            await loadTransactions();
            // Recarrega os dados das viagens do servidor.
            currentPage = 1;
            // Reseta para a primeira página da tabela.
            await renderTransactions();
            // Re-renderiza a tabela com os dados atualizados.
            clearForm();
            // Limpa o formulário após a exclusão.
        } else {
            // Caso a exclusão não tenha sido bem-sucedida...
            alert('Erro: ' + result.message);
            // Exibe um alerta com a mensagem de erro do servidor.
        }
    } catch (error) {
        // Captura qualquer erro durante a requisição ou conversão do JSON.
        console.error('Erro ao excluir transação:', error);
        // Registra o erro no console para depuração.
        alert('Erro na conexão com o servidor: ' + error.message);
        // Exibe um alerta ao usuário com a mensagem de erro.
    }
});

alterBtn.addEventListener('click', () => {
    // Adiciona um ouvinte de evento ao botão de alteração.
    if (!selectedRowId) {
        // Verifica se uma linha foi selecionada.
        alert('Selecione uma transação para alterar ou registrar retorno.');
        // Exibe um alerta se nenhuma linha estiver selecionada.
        return;
        // Interrompe a execução.
    }
    const transaction = transactions.find(t => t.id === selectedRowId);
    // Encontra a transação correspondente ao ID selecionado.
    if (!transaction) return;
    // Interrompe a execução se a transação não for encontrada.
    const hasReturn = transactions.some(t => t.tipo_viagem === 'chegada' && t.subida_id === transaction.id);
    // Verifica se a viagem de ida selecionada possui um retorno registrado (observação: usa 'chegada' em vez de 'retorno', possível erro no código).
    if (transaction.tipo_viagem === 'ida' && !hasReturn) {
        // Se a viagem é de ida e não possui retorno...
        formMode = 'registerReturn';
        // Define o modo do formulário como 'registerReturn' para registrar o retorno.
        idOfSubidaToComplete = transaction.id;
        // Armazena o ID da viagem de ida para vincular ao retorno.
        bondeInput.value = transaction.bonde;
        // Preenche o campo de bonde com o valor da transação.
        maquinistasInput.value = transaction.maquinista;
        // Preenche o campo de maquinista com o valor da transação.
        agentesInput.value = transaction.agente;
        // Preenche o campo de agente com o valor da transação.
        dateInput.value = transaction.data;
        // Preenche o campo de data com o valor da transação.
        setTimeAndDate();
        // Define a hora atual no campo de horário.
        saidaInput.value = transaction.retorno;
        // Define o campo de saída como o destino de retorno da ida (ex.: ponto de partida do retorno).
        saidaInput.disabled = true;
        // Desabilita o campo de saída, pois ele é fixo para retornos.
        populateRetornoOptions(returnDestinationOptions, 'Carioca');
        // Preenche o dropdown de retorno com opções limitadas, selecionando 'Carioca' por padrão.
        retornoInput.disabled = false;
        // Habilita o campo de retorno para edição.
        pagantesInput.value = '0';
        // Define o número de passageiros pagantes como 0.
        moradoresInput.value = '0';
        // Define o número de passageiros moradores como 0.
        gratPcdIdosoInput.value = '0';
        // Define o número de passageiros com gratuidade como 0.
        viagemInput.value = parseFloat(transaction.viagem) + 0.5;
        // Incrementa o número da viagem em 0.5 (ex.: 1 para ida, 1.5 para retorno).
        calculateCounts();
        // Recalcula os totais de passageiros e atualiza a barra de progresso.
        addBtn.textContent = 'Registrar Retorno';
        // Altera o texto do botão de adicionar para 'Registrar Retorno'.
        if (returnButton) returnButton.style.display = 'none';
        // Oculta o botão de retorno, se ele existe.
        alert('Preencha os dados para registrar o retorno.');
        // Exibe um alerta para orientar o usuário a preencher os dados do retorno.
    } else {
        // Se a viagem é de retorno ou uma ida com retorno registrado...
        formMode = 'edit';
        // Define o modo do formulário como 'edit' para edição da transação.
        bondeInput.value = transaction.bonde;
        // Preenche o campo de bonde com o valor da transação.
        saidaInput.value = transaction.saida;
        // Preenche o campo de saída com o valor da transação.
        saidaInput.disabled = false;
        // Habilita o campo de saída para edição.
        populateRetornoOptions(defaultRetornoOptions, transaction.retorno || '');
        // Preenche o dropdown de retorno com as opções padrão, selecionando o destino da transação, se houver.
        retornoInput.disabled = false;
        // Habilita o campo de retorno para edição.
        maquinistasInput.value = transaction.maquinista;
        // Preenche o campo de maquinista com o valor da transação.
        agentesInput.value = transaction.agente;
        // Preenche o campo de agente com o valor da transação.
        horaInput.value = transaction.hora;
        // Preenche o campo de horário com o valor da transação.
        pagantesInput.value = transaction.pagantes;
        // Preenche o campo de passageiros pagantes com o valor da transação.
        moradoresInput.value = transaction.gratuidade;
        // Preenche o campo de moradores com o valor de gratuidade (parece um erro, deveria ser t.moradores).
        gratPcdIdosoInput.value = transaction.gratuidade;
        // Preenche o campo de gratuidade (PCD/idosos) com o valor de gratuidade (parece um erro, deveria ser t.grat_pcd_idoso).
        viagemInput.value = transaction.viagem;
        // Preenche o campo de número da viagem com o valor da transação.
        dateInput.value = transaction.data;
        // Preenche o campo de data com o valor da transação.
        calculateCounts();
        // Recalcula os totais de passageiros e atualiza a barra de progresso.
        addBtn.textContent = 'Atualizar';
        // Altera o texto do botão de adicionar para 'Atualizar'.
        if (returnButton) returnButton.style.display = 'none';
        // Oculta o botão de retorno, se ele existe.
        alert('Transação carregada para edição. Modifique e clique em "Atualizar".');
        // Exibe um alerta para orientar o usuário a editar os dados e atualizar.
    }
});

if (returnButton) {
    // Verifica se o botão de retorno existe.
    returnButton.addEventListener('click', () => {
        // Adiciona um ouvinte de evento ao botão de retorno.
        if (!selectedRowId) {
            // Verifica se uma linha foi selecionada.
            alert('Selecione uma viagem de ida pendente para registrar o retorno.');
            // Exibe um alerta se nenhuma linha estiver selecionada.
            return;
            // Interrompe a execução.
        }
        const transaction = transactions.find(t => t.id === selectedRowId);
        // Encontra a transação correspondente ao ID selecionado.
        if (!transaction || transaction.tipo_viagem !== 'ida') {
            // Verifica se a transação existe e se é uma viagem de ida (observação: 'Subida' parece um erro, deveria ser 'ida').
            alert('Selecione uma viagem de ida para registrar o retorno.');
            // Exibe um alerta se a transação não for uma viagem de ida.
            return;
            // Interrompe a execução.
        }
        if (transactions.some(t => t.tipo_viagem === 'retorno' && t.subida_id === transaction.id)) {
            // Verifica se a viagem de ida já possui um retorno registrado.
            alert('Esta viagem de partida já possui um retorno registrado.');
            // Exibe um alerta se a viagem já tiver um retorno.
            return;
            // Interrompe a execução.
        }
        formMode = 'registerReturn';
        // Define o modo do formulário como 'registerReturn'.
        idOfSubidaToComplete = transaction.id;
        // Armazena o ID da viagem de ida para vincular ao retorno.
        bondeInput.value = transaction.bonde;
        // Preenche o campo de bonde com o valor da transação.
        maquinistasInput.value = transaction.maquinista;
        // Preenche o campo de maquinista com o valor da transação.
        agentesInput.value = transaction.agente;
        // Preenche o campo de agente com o valor da transação.
        dateInput.value = transaction.data;
        // Preenche o campo de data com o valor da transação.
        setTimeAndDate();
        // Define a hora atual no campo de horário.
        saidaInput.value = transaction.retorno;
        // Define o campo de saída como o destino de retorno da ida (ex.: ponto de partida do retorno).
        saidaInput.disabled = true;
        // Desabilita o campo de saída, pois é fixo para retornos.
        populateRetornoOptions(returnDestinationOptions, 'Carioca');
        // Preenche o dropdown de retorno com opções limitadas, selecionando 'Carioca' por padrão.
        retornoInput.disabled = false;
        // Habilita o campo de retorno para edição.
        pagantesInput.value = '0';
        // Define o número de passageiros pagantes como 0.
        moradoresInput.value = '0';
        // Define o número de passageiros moradores como 0.
        gratPcdIdosoInput.value = '0';
        // Define o número de passageiros com gratuidade como 0.
        viagemInput.value = parseFloat(transaction.viagem) + 0.5;
        // Incrementa o número da viagem em 0.5 (ex.: 1 para ida, 1.5 para retorno).
        calculateCounts();
        // Recalcula os totais de passageiros e atualiza a barra de progresso.
        addBtn.textContent = 'Registrar Retorno';
        // Altera o texto do botão de adicionar para 'Registrar Retorno'.
        returnButton.style.display = 'none';
        // Oculta o botão de retorno.
        alert('Preencha os dados para registrar o retorno.');
        // Exibe um alerta para orientar o usuário a preencher os dados do retorno.
    });
}

clearTransactionsBtn.addEventListener('click', async () => {
    // Adiciona um ouvinte de evento ao botão de limpar todas as transações.
    if (!confirm('Tem certeza que deseja limpar TODAS as transações? Esta ação não pode ser desfeita.')) return;
    // Pede confirmação ao usuário para limpar todas as transações, interrompendo se cancelado.
    try {
        // Inicia um bloco try-catch para lidar com erros na requisição.
        const response = await fetch('./clear_viagem.php', {
            // Faz uma requisição ao endpoint 'clear_viagem.php' para limpar todas as transações.
            method: 'POST',
            // Usa o método POST.
            headers: { 'Content-Type': 'application/json' }
            // Define o cabeçalho para JSON.
        });
        if (!response.ok) {
            // Verifica se a resposta HTTP não está OK.
            const errorText = await response.text();
            // Obtém o texto do erro da resposta.
            throw new Error(`Erro HTTP: ${response.status} - ${errorText.substring(0, 100)}...`);
            // Lança um erro com o código de status e uma parte do texto de erro.
        }
        const result = await response.json();
        // Converte a resposta do servidor em JSON.
        if (result.success) {
            // Verifica se a operação foi bem-sucedida.
            alert(result.message);
            // Exibe a mensagem de sucesso retornada pelo servidor.
            await loadTransactions();
            // Recarrega os dados das viagens do servidor.
            currentPage = 1;
            // Reseta para a primeira página da tabela.
            await renderTransactions();
            // Re-renderiza a tabela com os dados atualizados.
            clearForm();
            // Limpa o formulário após a operação.
        } else {
            // Caso a operação não tenha sido bem-sucedida...
            alert('Erro: ' + result.message);
            // Exibe um alerta com a mensagem de erro do servidor.
        }
    } catch (error) {
        // Captura qualquer erro durante a requisição ou conversão do JSON.
        console.error('Erro ao limpar transações:', error);
        // Registra o erro no console para depuração.
        alert('Erro na conexão com o servidor: ' + error.message);
        // Exibe um alerta ao usuário com a mensagem de erro.
    }
});

idFilterInput.addEventListener('input', () => {
    // Adiciona um ouvinte de evento ao campo de filtro por ID.
    currentPage = 1;
    // Reseta para a primeira página ao aplicar um novo filtro.
    renderTransactions();
    // Re-renderiza a tabela com as transações filtradas.
});

prevButton.addEventListener('click', () => {
    // Adiciona um ouvinte de evento ao botão de página anterior.
    if (currentPage > 1) {
        // Verifica se não está na primeira página.
        currentPage--;
        // Decrementa o número da página atual.
        renderTransactions();
        // Re-renderiza a tabela com os dados da página anterior.
    }
});

nextButton.addEventListener('click', () => {
    // Adiciona um ouvinte de evento ao botão de próxima página.
    const filteredTransactions = idFilterInput.value.trim()
        ? transactions.filter(t => t.id.toString().includes(idFilterInput.value.trim()))
        : transactions;
    // Obtém as transações filtradas pelo ID, ou todas as transações se não houver filtro.
    if (currentPage < Math.ceil(filteredTransactions.length / ROWS_PER_PAGE)) {
        // Verifica se não está na última página.
        currentPage++;
        // Incrementa o número da página atual.
        renderTransactions();
        // Re-renderiza a tabela com os dados da próxima página.
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    // Adiciona um ouvinte de evento para quando o DOM estiver completamente carregado.
    setTimeAndDate();
    // Define a data e hora atuais nos campos correspondentes.
    await loadTransactions();
    // Carrega os dados das viagens do servidor.
    clearForm();
    // Limpa o formulário para o estado inicial.
    await renderTransactions();
    // Renderiza a tabela com os dados iniciais.
    calculateCounts();
    // Calcula os totais de passageiros e atualiza a barra de progresso.
});