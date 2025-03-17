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
    for (let i = 0; i < numParcelas; i++) {
        const novaData = new Date(dataInicial);
        novaData.setDate(novaData.getDate() + (i * intervalo));
        parcelas.push(novaData.toISOString().split('T')[0]); // Formata para YYYY-MM-DD
    }
    return parcelas;
}
