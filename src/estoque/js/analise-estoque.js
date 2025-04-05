// Exemplo de como manipular os dados retornados via PHP para gerar o gráfico
fetch('analise_estoque.php')
.then(response => response.json())
.then(data => {
    if (data.success) {
        const produto = data.produto_mais_saida;
        const labels = ['Produto Mais Vendido', 'Estoque Atual', 'Projeção de 1 Ano', 'Tempo Estimado de Esgotamento'];
        const dataValues = [
            produto.total_saida, // Produto mais vendido
            produto.tempo_estimado_dias, // Tempo estimado para acabar estoque
            produto.projecao_ano, // Projeção de 1 ano
            produto.tempo_estimado_dias // Tempo estimado em dias
        ];

        // Nome do produto
        const nomeProduto = produto.descricao;

        // Gerar o gráfico
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: nomeProduto,
                        data: dataValues,
                        backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(54, 162, 235, 0.2)'],
                        borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(153, 102, 255, 1)', 'rgba(54, 162, 235, 1)'],
                        borderWidth: 1
                    },
                    {
                        label: 'Projeção de Estoque em 1 Ano',
                        data: [produto.projecao_ano, produto.projecao_ano, produto.projecao_ano, produto.projecao_ano],
                        type: 'line',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(tooltipItems) {
                                // Customizar título do tooltip
                                return nomeProduto;
                            },
                            label: function(tooltipItem) {
                                // Exibir quantidade e estimativa no tooltip
                                const label = tooltipItem.dataset.label || '';
                                const value = tooltipItem.raw;
                                return label + ': ' + value + ' unidades';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
})
.catch(error => console.error('Erro ao carregar os dados:', error));