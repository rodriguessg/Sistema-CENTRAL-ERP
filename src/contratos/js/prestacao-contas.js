 // Função para chamar o PHP via AJAX e exibir os contratos encerrados
 function exibirContratos() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'exibir_contratos.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('table-container-contratos').innerHTML = xhr.responseText;
            document.getElementById('table-container-contratos').style.display = 'block';
        }
    };
    xhr.send();
}

// Função para exibir os campos de Prestação de Contas ao selecionar um contrato
function iniciarPrestacao(idContrato) {
    // Esconde a tabela de contratos e exibe os campos de prestação de contas
    document.getElementById('table-container-contratos').style.display = 'none';
    document.getElementById('prestacao-container').style.display = 'block';

    // Carregar os dados do contrato selecionado
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'detalhes_contrato.php?id=' + idContrato, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var dadosContrato = JSON.parse(xhr.responseText);
            document.getElementById('nome-contrato').value = dadosContrato.contrato_titulo;
            document.getElementById('valor-inicial').value = dadosContrato.valor_inicial;
            document.getElementById('valor-total').value = dadosContrato.valor_total_pago;
            document.getElementById('situacao').value = dadosContrato.situacao;
        }
    };
    xhr.send();
}

// Função para salvar os dados da prestação de contas
function salvarPrestacao() {
    var formData = new FormData(document.getElementById('prestacao-form'));
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'salvar_prestacao.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            alert("Prestação de contas concluída com sucesso!");
            document.getElementById('prestacao-form').reset();
            document.getElementById('prestacao-container').style.display = 'none';
            document.getElementById('table-container-contratos').style.display = 'block';
            exibirContratos(); // Recarrega a tabela de contratos, se necessário
        }
    };
    xhr.send(formData);
}