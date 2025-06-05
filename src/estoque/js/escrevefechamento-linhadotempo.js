// Função para realizar o fechamento e carregar a linha do tempo
function realizarFechamento() {
    fetch('./realizar_fechamento.php', { method: 'POST' })
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
    fetch('./linha_do_tempo.php')  // Requisição para o backend
        .then(response => response.json())
        .then(data => {
            const linhaDoTempo = document.getElementById("linhaDoTempo");

            // Limpa a linha do tempo antes de adicionar novos fechamentos
            linhaDoTempo.innerHTML = '';

            // Exibe o saldo total, uma única vez
            if (data.success) {
                const fechamento = data.fechamentos[0]; // Obtém o único fechamento retornado

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
            } else {
                linhaDoTempo.innerHTML = '<p style="text-align: center; color: red;">Erro ao carregar os fechamentos.</p>';
            }
        })
        .catch(error => {
            console.error("Erro ao carregar a linha do tempo:", error);
            document.getElementById("linhaDoTempo").innerHTML = '<p style="text-align: center; color: red;">Erro ao carregar os fechamentos.</p>';
        });
}

// Carregar a linha do tempo ao carregar a página
window.onload = function() {
    carregarLinhaDoTempo();  // Chama a função para carregar os dados na linha do tempo
};
