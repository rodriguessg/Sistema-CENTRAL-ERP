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

function calcularParcelas(dataInicial, numParcelas) {
    const parcelas = [];
    const intervalo = 30; // A cada 30 dias (aproximadamente 1 mês)

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
