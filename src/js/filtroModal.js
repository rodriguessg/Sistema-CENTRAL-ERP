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
    var filterInput = document.getElementById('filterInput');
    var filterValue = filterInput.style.display !== 'none' ? filterInput.value.toLowerCase() : "";
    var statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    var rows = document.querySelectorAll('#contractTableBody tr');
    
    rows.forEach(row => {
        var columns = row.getElementsByTagName('td');
        var match = true;

        if (filterValue && !columns[1].innerText.toLowerCase().includes(filterValue)) {
            match = false;
        }
        if (statusFilter && !columns[4].innerText.toLowerCase().includes(statusFilter)) {
            match = false;
        }
        
        row.style.display = match ? '' : 'none';
    });
}