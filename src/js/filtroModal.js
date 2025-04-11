function openFilterModal() {
    document.getElementById('filterModal').style.display = 'block';
}

function closeFilterModal() {
    document.getElementById('filterModal').style.display = 'none';
}

function toggleFilterField(placeholder) {
    var inputField = document.getElementById('filterInput');
    inputField.style.display = 'block';
    inputField.placeholder = placeholder;
    inputField.value = ""; // Limpa o campo ao trocar o filtro
}

function clearFilters(event) {
    event.preventDefault(); // Evita a navegação padrão do link
    document.querySelectorAll("input[name='filterOption']").forEach(input => input.checked = false);
    document.getElementById('filterInput').style.display = 'block';
    document.getElementById('filterInput').value = "";
    searchContracts();
}
function searchContracts() {
    var filterInput = document.getElementById('searchInput');
    var filterValue = filterInput.value.toLowerCase();  // Obtém o valor do campo de pesquisa
    var statusFilter = document.getElementById('statusSelect').value.toLowerCase(); // Obtém o valor do select

    var rows = document.querySelectorAll('#contractTableBody tr');  // Supondo que a tabela tenha o id contractTableBody
    
    rows.forEach(row => {
        var columns = row.getElementsByTagName('td'); // Obtém as colunas de cada linha
        var match = true;

        // Filtra com base no campo de pesquisa (nome ou descrição do contrato)
        if (filterValue && !columns[1].innerText.toLowerCase().includes(filterValue)) { 
            match = false;
        }

        // Filtra com base no status selecionado
        if (statusFilter && !columns[4].innerText.toLowerCase().includes(statusFilter)) {
            match = false;
        }
        
        // Exibe ou oculta a linha dependendo do filtro
        row.style.display = match ? '' : 'none';
    });
}
