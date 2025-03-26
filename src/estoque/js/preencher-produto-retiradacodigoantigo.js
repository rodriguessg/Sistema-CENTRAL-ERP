// const nomeSelect = document.getElementById('material-nome');
// const codigoInput = document.getElementById('material-codigo');
// const classificacaoInput = document.getElementById('material-classificacao');
// const naturezaInput = document.getElementById('material-natureza');
// const localizacaoInput = document.getElementById('material-localizacao');
// const mensagemDiv = document.getElementById('mensagem');

// // Função para buscar material e preencher os campos automaticamente
// nomeSelect.addEventListener('change', () => {
//     const nomeMaterial = nomeSelect.value.trim(); // Pega o nome do material selecionado

//     if (nomeMaterial) {
//         fetch(`buscar_dados_produto.php?nome=${encodeURIComponent(nomeMaterial)}`) // Envia o parâmetro nome
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success) {
//                     // Preenche os campos com as informações do produto
//                     codigoInput.value = data.codigo || "";
//                     classificacaoInput.value = data.classificacao || "";
//                     naturezaInput.value = data.natureza || "";
//                     localizacaoInput.value = data.localizacao || "";
//                     mensagemDiv.innerText = "";  // Limpa a mensagem de erro
//                 } else {
//                     // Caso o produto não seja encontrado
//                     codigoInput.value = "";
//                     classificacaoInput.value = "";
//                     naturezaInput.value = "";
//                     localizacaoInput.value = "";
//                     mensagemDiv.innerText = "Material não encontrado.";
//                 }
//             })
//             .catch(err => {
//                 console.error('Erro ao buscar os dados:', err);
//                 mensagemDiv.innerText = "Erro na busca. Tente novamente.";
//             });
//     } else {
//         // Limpa os campos se o material não for selecionado
//         codigoInput.value = "";
//         classificacaoInput.value = "";
//         naturezaInput.value = "";
//         localizacaoInput.value = "";
//         mensagemDiv.innerText = "";
//     }
// });
function preencherCampos() {
    // Obtém o select do produto
    var selectProduto = document.getElementById("produto");
    // Obtém a opção selecionada
    var selectedOption = selectProduto.options[selectProduto.selectedIndex];

    // Obtém o código do produto e outras informações usando os atributos 'data-*'
    var codigoProduto = selectedOption.value;
    var descricaoProduto = selectedOption.text;
    var naturezaProduto = selectedOption.getAttribute("data-natureza");
    var classificacaoProduto = selectedOption.getAttribute("data-classificacao");
    var contabilProduto = selectedOption.getAttribute("data-contabil");

    // Preenche o campo de código
    document.getElementById("codigo").value = codigoProduto;
    
    // Preenche os campos adicionais
    document.getElementById("natureza").value = naturezaProduto;
    document.getElementById("classificacao").value = classificacaoProduto;
    document.getElementById("contabil").value = contabilProduto;
}