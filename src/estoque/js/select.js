document.getElementById('material-nome').addEventListener('change', function() {
    const nomeMaterialId = this.value; // Pega o ID do material selecionado

    // Limpa os campos e a mensagem de erro ao selecionar o material
    document.getElementById('material-codigo').value = '';
    document.getElementById('material-classificacao').value = '';
    document.getElementById('material-natureza').value = '';
    document.getElementById('material-localizacao').value = '';
    document.getElementById('mensagem').innerText = ''; // Limpa a mensagem de erro

    // Verifica se o nome do material foi selecionado
    if (nomeMaterialId) {
        // Faz a requisição para buscar os dados do material
        fetch('buscar_dados_produto.php?id=' + nomeMaterialId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Preenche os campos automaticamente com os dados recebidos
                    document.getElementById('material-codigo').value = data.codigo || '';
                    document.getElementById('material-classificacao').value = data.classificacao || '';
                    document.getElementById('material-natureza').value = data.natureza || '';
                    document.getElementById('material-localizacao').value = data.localizacao || '';
                    // Limpa a mensagem de erro quando o produto é encontrado
                    document.getElementById('mensagem').innerText = '';
                } else {
                    // Caso o produto não seja encontrado, exibe a mensagem de erro
                    document.getElementById('mensagem').innerText = 'Material não encontrado.';
                }
            })
            .catch(err => {
                console.error('Erro ao buscar os dados:', err);
                document.getElementById('mensagem').innerText = 'Erro na busca. Tente novamente.';
            });
    } else {
        // Caso nenhum material seja selecionado, limpa os campos
        document.getElementById('mensagem').innerText = ''; // Limpa a mensagem de erro
    }
});
