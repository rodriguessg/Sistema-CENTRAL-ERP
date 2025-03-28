document.getElementById('material-nome').addEventListener('change', function() {
    const nomeMaterialId = this.value; // Pega o ID do material selecionado

    // Limpa os campos e a mensagem de erro ao selecionar o material
    // document.getElementById('material-codigos').value = '';
    document.getElementById('material-classificacao').value = '';
    document.getElementById('material-natureza').value = '';
    document.getElementById('material-localizacao').value = '';
    document.getElementById('material-preco-medio').value = ''; // Limpa o campo de preço médio
    document.getElementById('mensagem').innerText = ''; // Limpa a mensagem de erro

    // Verifica se o nome do material foi selecionado
    if (nomeMaterialId) {
       fetch('buscar_dados_produto.php?id=' + nomeMaterialId)
    .then(response => response.json())
    .then(data => {
        console.log(data);  // Verifique o conteúdo da resposta JSON

        if (data.success) {
            setTimeout(() => {
                //  document.getElementById('material-codigos').value = data.codigo || '';
                document.getElementById('material-classificacao').value = data.classificacao || '';
                document.getElementById('material-natureza').value = data.natureza || '';
                document.getElementById('material-localizacao').value = data.localizacao || '';
                document.getElementById('material-preco-medio').value = data.preco_medio || ''; // Preenche o preço médio
            }, 300); // Delay de 100ms
            document.getElementById('mensagem').innerText = ''; // Limpa a mensagem de erro
        } else {
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
