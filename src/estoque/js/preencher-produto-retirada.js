const nomeInput = document.getElementById('material-nome');
const codigoInput = document.getElementById('material-codigo');
const classificacaoInput = document.getElementById('material-classificacao');
const naturezaInput = document.getElementById('material-natureza');
const localizacaoInput = document.getElementById('material-localizacao');
const quantidadeInput = document.getElementById('material-quantidade');
const mensagemDiv = document.getElementById('mensagem');

// Função para buscar material e preencher os campos automaticamente
nomeInput.addEventListener('input', () => {
    const nomeMaterial = nomeInput.value.trim();

    if (nomeMaterial.length > 0) {
        fetch(`buscar_codigo.php?nome=${encodeURIComponent(nomeMaterial)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Preenche os campos com as informações do produto
                    codigoInput.value = data.codigo || "Não encontrado";
                    classificacaoInput.value = data.classificacao || "Não encontrado";
                    naturezaInput.value = data.natureza || "Não encontrado";
                    localizacaoInput.value = data.localizacao || "Não encontrado";
                    mensagemDiv.innerText = "";  // Limpa a mensagem de erro
                } else {
                    // Caso o produto não seja encontrado
                    codigoInput.value = "";
                    classificacaoInput.value = "";
                    naturezaInput.value = "";
                    localizacaoInput.value = "";
                    mensagemDiv.innerText = "Material não encontrado.";
                }
            })
            .catch(err => {
                console.error('Erro ao buscar os dados:', err);
                mensagemDiv.innerText = "Erro na busca. Tente novamente.";
            });
    } else {
        // Limpa os campos se o nome do material for apagado
        codigoInput.value = "";
        classificacaoInput.value = "";
        naturezaInput.value = "";
        localizacaoInput.value = "";
        mensagemDiv.innerText = "";
    }
});

// Função para validação do formulário
document.getElementById('retirar-form').addEventListener('submit', function(event) {
    // Verifica se todos os campos estão preenchidos
    if (!codigoInput.value || !classificacaoInput.value || !naturezaInput.value || !localizacaoInput.value) {
        event.preventDefault();  // Impede o envio do formulário
        mensagemDiv.innerText = "Preencha corretamente todos os campos antes de continuar.";
    } else if (quantidadeInput.value <= 0) {
        event.preventDefault();  // Impede o envio do formulário
        mensagemDiv.innerText = "A quantidade a ser retirada deve ser maior que 0.";
    }
});