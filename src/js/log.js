function filterLogs() {
    const dataOperacao = document.getElementById('data_operacao').value;
    const matricula = document.getElementById('matricula').value;
    const tipoOperacao = document.getElementById('tipo_operacao').value;

    // Enviar dados para o servidor via AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `filtrar_logs.php?data_operacao=${dataOperacao}&matricula=${matricula}&tipo_operacao=${tipoOperacao}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Atualiza a tabela com os resultados
            document.getElementById('logTable').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

// Adicionando eventos para filtrar conforme o usuário digita
document.getElementById('data_operacao').addEventListener('input', filterLogs);
document.getElementById('matricula').addEventListener('input', filterLogs);
document.getElementById('tipo_operacao').addEventListener('input', filterLogs);

// Função para limpar os filtros
document.getElementById('clearFilters').addEventListener('click', function() {
    // Limpar os campos
    document.getElementById('data_operacao').value = '';
    document.getElementById('matricula').value = '';
    document.getElementById('tipo_operacao').value = '';

    // Recarregar a tabela com todos os itens
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'filtrar_logs.php', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Atualiza a tabela com todos os registros
            document.getElementById('logTable').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
});