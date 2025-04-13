// Função para carregar os títulos dos contratos baseados na seleção do relatório
document.getElementById('tipo_relatorio').addEventListener('change', function() {
    if (this.value === 'pagamentos') {
        // Exibir select de contratos
        document.getElementById('contratos-container').style.display = 'block';
        
        // Carregar os títulos dos contratos
        carregarContratos();
        
        // Exibir a periodicidade (completo, mensal, anual)
        document.getElementById('periodicidade-container').style.display = 'block';
    } else {
        // Ocultar select de contratos e parcelamentos
        document.getElementById('contratos-container').style.display = 'none';
        document.getElementById('parcelamentos-container').style.display = 'none';
        document.getElementById('resultadoRelatorio').innerHTML = '';  // Limpar o relatório
        document.getElementById('periodicidade-container').style.display = 'none';
    }
});

// Função para carregar os contratos do banco de dados via AJAX
function carregarContratos() {
    var tipoRelatorio = document.getElementById('tipo_relatorio').value;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'carregar_contratos.php?tipo_relatorio=' + tipoRelatorio, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var contratos = JSON.parse(xhr.responseText);
            var selectContratos = document.getElementById('contrato_titulo');
            selectContratos.innerHTML = '<option value="">Selecione um contrato</option>'; // Limpar opções
            contratos.forEach(function(contrato) {
                var option = document.createElement('option');
                option.value = contrato.id;
                option.textContent = contrato.titulo;
                selectContratos.appendChild(option);
            });
        }
    };
    xhr.send();
}

// Função para carregar os parcelamentos do contrato selecionado
document.getElementById('contrato_titulo').addEventListener('change', function() {
    var contratoId = this.value;
    if (contratoId) {
        // Carregar parcelamentos
        carregarParcelamentos(contratoId);
    } else {
        document.getElementById('parcelamentos-container').style.display = 'none';
        document.getElementById('resultadoRelatorio').innerHTML = '';  // Limpar o relatório
    }
});

// Função para carregar os parcelamentos com base no contrato selecionado
function carregarParcelamentos(contratoId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'carregar_parcelamentos.php?contrato_id=' + contratoId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var parcelamentos = JSON.parse(xhr.responseText);
            if (parcelamentos.length > 0) {
                var tableHtml = '<table border="1" cellpadding="5" cellspacing="0">';
                tableHtml += '<thead><tr><th>Parcela</th><th>Valor</th><th>Data Vencimento</th></tr></thead>';
                tableHtml += '<tbody>';
                parcelamentos.forEach(function(parcelamento) {
                    tableHtml += '<tr>';
                    tableHtml += '<td>' + parcelamento.parcela + '</td>';
                    tableHtml += '<td>' + parcelamento.valor + '</td>';
                    tableHtml += '<td>' + parcelamento.data_vencimento + '</td>';
                    tableHtml += '</tr>';
                });
                tableHtml += '</tbody></table>';
                document.getElementById('parcelamentos-container').innerHTML = tableHtml;
                document.getElementById('parcelamentos-container').style.display = 'block';
            } else {
                document.getElementById('parcelamentos-container').innerHTML = '<p>Nenhum parcelamento encontrado para o contrato selecionado.</p>';
                document.getElementById('parcelamentos-container').style.display = 'block';
            }
        }
    };
    xhr.send();
}

// Função para gerar o relatório (completo, mensal ou anual)
function gerarRelatorio() {
    var contratoId = document.getElementById('contrato_titulo').value;
    var periodicidade = document.getElementById('periodicidade').value;

    if (contratoId && periodicidade) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'gerar_relatorio.php?contrato_id=' + contratoId + '&periodicidade=' + periodicidade, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var resultado = xhr.responseText;
                document.getElementById('resultadoRelatorio').innerHTML = resultado;
            } else {
                document.getElementById('resultadoRelatorio').innerHTML = '<p>Erro ao gerar o relatório.</p>';
            }
        };
        xhr.send();
    } else {
        document.getElementById('resultadoRelatorio').innerHTML = '<p>Selecione um contrato e a periodicidade antes de gerar o relatório.</p>';
    }
}