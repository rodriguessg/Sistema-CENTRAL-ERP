// Função para calcular o preço médio
function calcularPrecoMedio() {
    const custoInput = document.getElementById('custo');
    const quantidadeInput = document.getElementById('quantidade');
    const precoMedioInput = document.getElementById('preco_medio');

    // Função para converter o formato brasileiro (0.00,00) para número
    function converterParaNumero(valor) {
        if (!valor) return 0; // Retorna 0 se o valor for vazio ou inválido
        return parseFloat(valor.replace(/\./g, '').replace(',', '.')) || 0;  // Corrigido para trocar a vírgula por ponto
    }

    // Obtém os valores de custo e quantidade no formato correto
    const custo = converterParaNumero(custoInput.value);
    const quantidade = parseFloat(quantidadeInput.value) || 0;

    // Valida os valores
    if (custo < 0 || quantidade <= 0) {
        precoMedioInput.value = '0,00'; // Define o valor padrão para preço médio
        return;
    }

    // Calcula o preço médio sem arredondamento (truncando a 2 casas decimais)
    const precoMedio = Math.floor((custo / quantidade) * 100) / 100; // Trunca a 2 casas decimais

    // Atualiza o campo de preço médio
    precoMedioInput.value = precoMedio.toFixed(2).replace('.', ','); // Exibe com 2 casas decimais
}

// Adiciona eventos aos campos de custo e quantidade
document.getElementById('custo').addEventListener('input', calcularPrecoMedio);
document.getElementById('quantidade').addEventListener('input', calcularPrecoMedio);


// Função para limpar o formulário
function limparFormulario() {
    const form = document.getElementById('form-cadastrar-produto');
    form.reset(); // Reseta todos os campos do formulário
    document.getElementById('preco_medio').value = ''; // Reseta o campo de preço médio
}

// Evento no botão de limpar
document.getElementById('limpar-formulario').addEventListener('click', limparFormulario);
