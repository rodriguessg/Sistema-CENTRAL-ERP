document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("graficoProdutos").getContext("2d");
    let chart;

    const modal = document.getElementById("modalAno");
    const closeButton = document.querySelector(".close-button");
    const tabelaProdutosAno = document.getElementById("tabelaProdutosAno");
    const anoSelecionado = document.getElementById("anoSelecionado");

    const renderChart = (labels, data, label) => {
        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                onClick: async (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const selectedYear = labels[index];

                        // Exibir modal com produtos do ano
                        anoSelecionado.textContent = selectedYear;
                        await fetchProdutosDoAno(selectedYear);
                        modal.style.display = "block";
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    };

    const fetchData = async (type) => {
        const response = await fetch(`fetch_data.php?type=${type}`);
        const result = await response.json();

        const labels = result.map(item => item.label);
        const data = result.map(item => item.count);

        renderChart(labels, data, type === 'year' ? 'Quantidade de Produtos por Ano' : 'Quantidade de Produtos por Mês');
    };

    const fetchProdutosDoAno = async (year) => {
        const response = await fetch(`fetch_produtos.php?ano=${year}`);
        const produtos = await response.json();

        // Preencher tabela do modal
        tabelaProdutosAno.innerHTML = produtos.map(produto => `
            <tr>
                <td>${produto.produto}</td>
                <td>${produto.quantidade}</td>
                <td>${produto.codigo}</td>
                <td>${produto.data_cadastro}</td>
            </tr>
        `).join('');
    };

    closeButton.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    // Evento na caixa de combinação
    document.getElementById("filterType").addEventListener("change", (event) => {
        const selectedValue = event.target.value;
        fetchData(selectedValue);
    });

    // Carregar dados inicial por mês
    fetchData('month');
});