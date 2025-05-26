// Função para adicionar informações complementares
function toggleComplementares() {
    const complementaresDiv = document.getElementById("complementares");
    complementaresDiv.style.display = complementaresDiv.style.display === "none" ? "block" : "none";
}

function toggleParcelas() {
    const parcelamento = document.getElementById('parcelamento').checked;
    const parcelasContainer = document.getElementById('parcelas-container');
    parcelasContainer.style.display = parcelamento ? 'block' : 'none';
}

function atualizarParcelas() {
    const numParcelas = document.getElementById('num-parcelas').value;
    const dataPagamento = document.getElementById('data-pagamento').value;

    if (numParcelas && dataPagamento) {
        const parcelas = calcularParcelas(new Date(dataPagamento), numParcelas);
        // Aqui podemos enviar para o servidor ou atualizar algum campo com as datas das parcelas
        console.log(parcelas); // Exemplo de como as datas seriam calculadas
    }
}
// Função que verifica se o Gestor e Fiscais foram preenchidos
function verificarGestorEFiscal() {
    const gestor = document.getElementById('gestor').value;
    const fiscais = document.getElementById('fiscais').value;
    const gestorPortariaContainer = document.getElementById('gestor-portaria-container');
    const fiscalPortariaContainer = document.getElementById('fiscal-portaria-container');

    // Se o Gestor for preenchido, mostrar o campo de Nº Portaria Gestor
    if (gestor) {
        gestorPortariaContainer.style.display = 'block';
    } else {
        gestorPortariaContainer.style.display = 'none';
    }

    // Se os Fiscais forem preenchidos, mostrar o campo de Nº Portaria Fiscais
    if (fiscais) {
        fiscalPortariaContainer.style.display = 'block';
    } else {
        fiscalPortariaContainer.style.display = 'none';
    }
}
function calcularParcelas(dataInicial, numParcelas) {
    const parcelas = [];
    const intervalo = 182; // A cada 30 dias (aproximadamente 1 mês)

    // Converter a string dataInicial para um objeto Date, caso seja uma string.
    let data = new Date(dataInicial);

    for (let i = 0; i < numParcelas; i++) {
        const novaData = new Date(data); // Copia a data inicial
        novaData.setDate(data.getDate() + (i * intervalo)); // Adiciona 30 dias por parcela

        // Formata a data para o formato YYYY-MM-DD (ISO)
        const dataFormatada = novaData.toISOString().split('T')[0]; 
        parcelas.push(dataFormatada);
    }

    return parcelas;
}
function toggleOutros() {
    var outrosChecked = document.getElementById("outros").checked;
    var outrosContainer = document.getElementById("outros-container");
    outrosContainer.style.display = outrosChecked ? "block" : "none";
}



