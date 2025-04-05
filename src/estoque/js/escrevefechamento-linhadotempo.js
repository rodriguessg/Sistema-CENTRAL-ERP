// Função para realizar o fechamento e carregar a linha do tempo
function realizarFechamento() {
    fetch('realizar_fechamento.php', { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Fechamento realizado com sucesso!");
                carregarLinhaDoTempo(); // Atualiza a lista após o fechamento
            } else {
                alert(`Erro ao realizar fechamento: ${data.message}`);
            }
        })
        .catch(error => {
            console.error("Erro na requisição:", error);
            alert("Ocorreu um erro ao realizar o fechamento.");
        });
}

// Função para carregar a linha do tempo de fechamentos
function carregarLinhaDoTempo() {
    fetch('linha_do_tempo.php')
        .then(response => response.json())
        .then(data => {
            const linhaDoTempo = document.getElementById("linhaDoTempo");
            linhaDoTempo.innerHTML = '';  // Limpa a linha do tempo antes de adicionar novos fechamentos

            if (data.success && Array.isArray(data.fechamentos) && data.fechamentos.length > 0) {
                data.fechamentos.forEach(fechamento => {
                    adicionarFechamentoNaLinhaDoTempo(fechamento);
                });
            } else {
                linhaDoTempo.innerHTML = '<p style="text-align: center;">Nenhum fechamento encontrado.</p>';
            }
        })
        .catch(error => {
            console.error("Erro ao carregar a linha do tempo:", error);
            document.getElementById("linhaDoTempo").innerHTML = '<p style="text-align: center; color: red;">Erro ao carregar os fechamentos.</p>';
        });
}

// Função para adicionar um fechamento à linha do tempo
function adicionarFechamentoNaLinhaDoTempo(fechamento) {
    const linhaDoTempo = document.getElementById("linhaDoTempo");
    const div = document.createElement("div");
    div.classList.add("linha-tempo-item");

    div.innerHTML = `
        <div class="linha-tempo-content">
            <strong>Data:</strong> ${fechamento.data_fechamento}<br>
            <strong>Usuário:</strong> ${fechamento.username}<br>
            <strong>Saldo Atual:</strong> R$ ${parseFloat(fechamento.saldo_atual).toFixed(2)}
        </div>
        <button class="dots-button" onclick="abrirModal(fechamento)">&#8230;</button>
    `;
    linhaDoTempo.appendChild(div);
}

// Função para abrir o modal com os dados do fechamento
function abrirModal(fechamento) {
    console.log("Abrindo modal com fechamento:", fechamento); // Verifique os dados no console

    const tabelaBody = document.querySelector("#tabelaFechamentos tbody");

    tabelaBody.innerHTML = `
        <tr>
            <td>${fechamento.data_fechamento}</td>
            <td>${fechamento.username}</td>
            <td>R$ ${parseFloat(fechamento.saldo_atual).toFixed(2)}</td>
        </tr>
    `;

    // Exibe o modal e o fundo escuro
    document.getElementById("modalFechamento").style.display = "flex"; // Usamos flex para centralizar
}

// Função para fechar o modal
function fecharModal() {
    document.getElementById("modalFechamento").style.display = "none";
}

// Função para gerar o PDF com os dados da tabela
function gerarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const tabela = document.getElementById("tabelaFechamentos");
    const rows = tabela.rows;
    let tableContent = [];

    // Extrai os dados da tabela para o PDF
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const rowData = [];
        for (let j = 0; j < row.cells.length; j++) {
            rowData.push(row.cells[j].innerText);
        }
        tableContent.push(rowData);
    }

    doc.autoTable({
        head: [['Data', 'Usuário', 'Saldo Atual']],
        body: tableContent
    });

    doc.save('fechamentos.pdf');
}
