// Função para calcular o preço médio
function calcularPrecoMedio() {
    const custoInput = document.getElementById('custo');
    const quantidadeInput = document.getElementById('quantidade');
    const precoMedioInput = document.getElementById('preco_medio');

    // Função para converter o formato brasileiro (0.00,00) para número
    function converterParaNumero(valor) {
        if (!valor) return 0; // Retorna 0 se o valor for vazio ou inválido
        return parseFloat(valor.replace(/\./g, '').replace(',')) || 0;
    }

    // Obtém os valores de custo e quantidade no formato correto
    const custo = converterParaNumero(custoInput.value);
    const quantidade = parseFloat(quantidadeInput.value) || 0;

    // Valida os valores
    if (custo < 0 || quantidade < 0) {
        precoMedioInput.value = '0'; // Define o valor padrão
        return;
    }

    // Calcula o preço médio apenas se a quantidade for maior que zero
    const precoMedio = quantidade > 0 ? (custo / quantidade).toFixed(2) : '0';

    // Atualiza o campo de preço médio
    precoMedioInput.value = precoMedio;
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