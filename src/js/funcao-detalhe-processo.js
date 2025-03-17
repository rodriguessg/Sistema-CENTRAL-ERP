   // Função chamada ao clicar na linha da tabela
   function showResumoProcesso(data) {
    // Exibe a div do resumo do processo
    document.getElementById('consultar').style.display = 'none'; // Esconde a lista de contratos
    document.getElementById('resumo_processo').style.display = 'block'; // Exibe o resumo do processo

    // Preenche os detalhes do processo na div
    const processoDetalhes = document.getElementById('processoDetalhes');
    processoDetalhes.innerHTML = `
        <p><strong>ID:</strong> ${data.id}</p>
        <p><strong>Título:</strong> ${data.titulo}</p>
        <p><strong>Descrição:</strong> ${data.descricao}</p>
        <p><strong>Validade:</strong> ${data.validade}</p>
        <p><strong>Status:</strong> ${data.situacao}</p>
    `;
}