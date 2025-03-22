// Função para calcular o preço médio
function calcularPrecoMedio() {
    const custoInput = document.getElementById('custo');
    const quantidadeInput = document.getElementById('quantidade');
    const precoMedioInput = document.getElementById('preco_medio');

    // Função para converter o valor no formato brasileiro (ex: 3.490,90)
    function converterParaNumero(valor) {
        if (!valor) return 0; // Retorna 0 se o valor for vazio ou inválido
        return parseFloat(valor.replace(/\./g, '').replace(',', '.')) || 0; // Troca vírgula por ponto e remove os pontos de milhar
    }

    // Função para formatar o número no padrão brasileiro (ex: 3.490,90)
    function formatarComoReal(valor) {
        return valor.toFixed(2) // Limita a 2 casas decimais
            .replace('.', ',') // Substitui o ponto por vírgula
            .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Adiciona ponto como separador de milhar
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
    precoMedioInput.value = formatarComoReal(precoMedio); // Exibe com o formato brasileiro
}

// Função para formatar o custo enquanto o usuário digita
document.getElementById('custo').addEventListener('input', function() {
    let valor = this.value;

    // Remove qualquer caractere que não seja número ou vírgula
    valor = valor.replace(/\D/g, '');

    // Adiciona vírgula antes dos dois últimos números para os centavos
    valor = valor.replace(/(\d)(\d{2})$/, '$1,$2');

    // Adiciona ponto como separador de milhar
    valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    // Atualiza o campo com o valor formatado
    this.value = valor;

    // Calcula o preço médio enquanto o usuário digita
    calcularPrecoMedio();
});

// Adiciona evento de input no campo de quantidade
document.getElementById('quantidade').addEventListener('input', calcularPrecoMedio);

// Função para limpar o formulário
function limparFormulario() {
    const form = document.getElementById('form-cadastrar-produto');
    form.reset(); // Reseta todos os campos do formulário
    document.getElementById('preco_medio').value = ''; // Reseta o campo de preço médio
}

// Evento no botão de limpar
document.getElementById('limpar-formulario').addEventListener('click', limparFormulario);
