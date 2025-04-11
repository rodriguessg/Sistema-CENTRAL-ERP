function openFilterModal() {
    document.getElementById('filterModal').style.display = 'block';
}

function closeFilterModal() {
    document.getElementById('filterModal').style.display = 'none';
}

function toggleFilterField(placeholder) {
    var inputField = document.getElementById('filterInput');
    inputField.style.display = 'block';  // Garante que o campo de pesquisa seja visível
    inputField.placeholder = placeholder;
    inputField.value = ""; // Limpa o campo ao trocar o filtro
}

function clearFilters(event) {
    event.preventDefault(); // Evita a navegação padrão do link
    document.querySelectorAll("input[name='filterOption']").forEach(input => input.checked = false); // Limpa os filtros de opções
    document.getElementById('filterInput').style.display = 'block'; // Garante que o campo de pesquisa esteja visível
    document.getElementById('filterInput').value = ""; // Limpa o valor do campo de pesquisa
    searchContracts(); // Realiza a pesquisa novamente
}

function searchContracts() {
    var filterInput = document.getElementById('searchInput');
    var filterValue = filterInput.value.toLowerCase();  // Obtém o valor do campo de pesquisa
    var statusFilter = document.getElementById('statusSelect').value.toLowerCase(); // Obtém o valor do status selecionado
    var rows = document.querySelectorAll('#contractTableBody tr');  // Seleciona todas as linhas da tabela
    
    rows.forEach(row => {
        var columns = row.getElementsByTagName('td');  // Obtém as colunas de cada linha
        var match = true;  // Inicialmente, assume que a linha corresponde ao filtro

        // Filtra com base no campo de pesquisa (nome ou descrição)
        if (filterValue && !(columns[1].textContent.toLowerCase().includes(filterValue) || columns[2].textContent.toLowerCase().includes(filterValue))) {
            match = false;  // Se não corresponder, esconde a linha
        }

        // Filtra com base no status selecionado
        if (statusFilter && statusFilter !== '') {
            var status = columns[4].textContent.toLowerCase().trim();  // Pega o status de forma limpa
            console.log('Status da linha:', status, 'Filtro de status:', statusFilter);  // Diagnóstico de status
            if (status !== statusFilter) {
                match = false;  // Se o status não corresponder, esconde a linha
            }
        }
        
        // Exibe ou oculta a linha dependendo dos filtros
        row.style.display = match ? '' : 'none';
    });
}
