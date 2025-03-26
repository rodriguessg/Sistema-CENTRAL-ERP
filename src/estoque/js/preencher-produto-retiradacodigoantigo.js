
function preencherCampos() {
    // Obtém o select do produto
    var selectProduto = document.getElementById("produto");
    // Obtém a opção selecionada
    var selectedOption = selectProduto.options[selectProduto.selectedIndex];

    // Obtém a descrição do produto selecionado
    var descricaoProduto = selectedOption.getAttribute("data-descricao");

    // Preenche o campo de descrição com a descrição do produto
    document.getElementById("descricao").value = descricaoProduto;

    // Preenche os outros campos com os dados do produto selecionado
    document.getElementById("classificacao").value = selectedOption.getAttribute("data-classificacao");
    document.getElementById("natureza").value = selectedOption.getAttribute("data-natureza");
    document.getElementById("contabil").value = selectedOption.getAttribute("data-contabil");
    document.getElementById("codigo").value = selectedOption.value;  // Código do produto
}
