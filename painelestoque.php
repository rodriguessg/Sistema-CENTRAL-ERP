<?php
include 'header.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Estoque</title>
    <link rel="stylesheet" href="./src/style/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
         /* Estilo do painel */
        .painelestoque { padding: 20px; font-family: Arial, sans-serif;    margin-bottom: 111px; display: contents; gap: 25px; }
        .cardss { display: flex; gap: 20px; margin-bottom: 20px; }
        .card { 
            flex: 1;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
    display: flex;
    margin-bottom:8.01%;
;
    flex-direction: row;
    flex-wrap: nowrap;
    align-content: center;
    justify-content: center;
    align-items: center;
        }
        .card2 { flex: 1; padding: 20px; background: #dc3737; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); text-align: center; }
        .grafico-container { max-width: 800px; margin: 0 auto;  height: 300px; width: 600px;}
        #totalProdutos {
            margin-top: 0;
    font-size: 23px;
    margin-bottom: 2rem;
        } 
        .modal {
    /* background: #fff; */
    padding: 20px;
    border-radius: 8px;
    /* width: 400px; */
    /* margin-left: 30%; */
    /* max-width: 90%; */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);

}
.card-estoque-produtos {
    display: flex;
    gap: 20px;
}

    </style>
</head>
<body>

<h1>Painel de Estoque</h1>
<div class="painelestoque">
         <!-- Cards -->
        <div class="cardss">
            <div class="card">
                <div>
                <h3>Total de Produtos</h3>
                <p id="totalProdutos">Carregando...</p>
                </div>
            </div>
            <div class="card2" id="cardProdutoAcabando" style="display: none;">
                <h3>Produto Acabando</h3>
                <p id="produtoAcabando">Carregando...</p>
                <ul id="listaProdutosAcabando"></ul> <!-- Lista de produtos com pouca unidade -->
            </div>
            <div class="card">
            <div>
                <?php
                // Configurações do banco de dados
                $host = 'localhost'; // Substitua pelo host do banco
                $dbname = 'supat';   // Nome do banco de dados
                $user = 'root';      // Nome de usuário do banco
                $password = '';      // Senha do banco

                try {
                    // Conexão com o banco de dados usando PDO
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Consulta SQL para calcular o valor total no estoque
                    $sql = "SELECT SUM(custo * quantidade) AS valor_total_estoque FROM produtos";
                    $stmt = $pdo->query($sql);

                    // Obter o valor total do estoque
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $valor_total_estoque = $row['valor_total_estoque'] ?? 0;

                    echo "<h3>Valor Total no Estoque: R$ " . number_format($valor_total_estoque, 2, ',', '.') . "</h3>";
                } catch (PDOException $e) {
                    echo "Erro ao conectar ao banco: " . $e->getMessage();
                }
                ?>
            </div>
</div>

</div>
    

<div  class="  card-estoque-produtos">
    <!-- Gráfico -->
    <div class="card">
    <div class="grafico-container">
    <label for="filterType">Filtrar por:</label>
    <select id="filterType">
        <option value="year">Por Ano</option>
        <option value="month" selected>Por Mês</option>
    </select>
    <canvas id="graficoProdutos"></canvas>
</div>
    </div>
    <div id="modalAno" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3>Produtos do Ano <span id="anoSelecionado"></span></h3>
        <table border="1">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Código</th>
                    <th>Data de Cadastro</th>
                </tr>
            </thead>
            <tbody id="tabelaProdutosAno">
                <!-- Dados serão preenchidos dinamicamente -->
            </tbody>
        </table>
    </div>
</div>
    <div class="card">
<div class="card-estoque-pr">
    <h3>Últimos Produtos Cadastrados</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Código</th>
                <th>Data de Cadastro</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Configurações do banco de dados
            $host = 'localhost'; // Substitua pelo host do banco
            $dbname = 'supat';   // Nome do banco de dados
            $user = 'root';      // Nome de usuário do banco
            $password = '';      // Senha do banco
            
            try {
                // Conexão com o banco de dados usando PDO
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Consulta SQL para buscar os últimos produtos cadastrados
                $sql = "SELECT produto, quantidade, codigo, data_cadastro FROM produtos ORDER BY data_cadastro DESC LIMIT 5";
                $stmt = $pdo->query($sql);

                // Exibindo os resultados na tabela
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['produto']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['codigo']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['data_cadastro']) . "</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "Erro ao conectar ao banco: " . $e->getMessage();
            }
            ?>
        </tbody>
        </table>
</div>
    </div>


<div class="card">
    <div>
    <h2>Usuários com Mais Movimentações</h2>
   
        <?php
        // Conexão com o banco de dados
        $conn = new mysqli("localhost", "root", "", "supat");

        // Verifique a conexão
        if ($conn->connect_error) {
            die("Erro na conexão com o banco de dados: " . $conn->connect_error);
        }

        // Consulta para contar movimentações por usuário
        $sql ="SELECT matricula, COUNT(*) AS total_movimentacoes 
            FROM log_eventos 
            WHERE tipo_operacao IN ('cadastrou no estoque', 'retirou do estoque') 
            GROUP BY matricula 
            ORDER BY total_movimentacoes DESC 
            LIMIT 5";

        $result = $conn->query($sql);

        // Verifique se a consulta foi bem-sucedida
        if ($result) {
            if ($result->num_rows > 0) {
                echo "<table border='1'>
                        <tr>
                            <th>Usuário (Matrícula)</th>
                            <th>Total de Movimentações</th>
                        </tr>";
                
                // Exibe os resultados na tabela
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['matricula']}</td>
                            <td>{$row['total_movimentacoes']}</td>
                        </tr>";
                }
                
                echo "</table>";
            } else {
                echo "Nenhuma movimentação encontrada.";
            }
        } else {
            echo "Erro na consulta: " . $conn->error;
        }

        // Fechar a conexão
        $conn->close();
        ?>

        </tbody>
    </table>
    </div>
</div>

</div>
</div>

       

<script>
        document.addEventListener("DOMContentLoaded", () => {
    // Função para atualizar os cards com os dados do backend
    function atualizarCards(data) {
        const totalProdutosCard = document.getElementById('totalProdutos');
        const produtoAcabandoCard = document.getElementById('produtoAcabando');
        const cardProdutoAcabando = document.getElementById('cardProdutoAcabando');
        const listaProdutosAcabando = document.getElementById('listaProdutosAcabando');
        
        // Atualiza o total de produtos
        totalProdutosCard.textContent = data.totalProdutos;

        // Verifica se há produtos acabando
        const produtosAcabando = data.produtos.filter(produto => produto.quantidade < 10);
        
        if (produtosAcabando.length > 0) {
            // Exibe o card de produtos acabando
            cardProdutoAcabando.style.display = 'block';

            // Preenche a lista de produtos com pouca unidade
            listaProdutosAcabando.innerHTML = ''; // Limpa a lista antes de adicionar os novos itens
            produtosAcabando.forEach(produto => {
                const li = document.createElement('li');
                li.textContent = `${produto.nome} - ${produto.quantidade} unidades`;
                listaProdutosAcabando.appendChild(li);
            });

            // Atualiza o texto do produto que está acabando
            produtoAcabandoCard.textContent = `${produtosAcabando.length} produto(s) acabando`;
        } else {
            // Se não houver produtos acabando, oculta o card
            cardProdutoAcabando.style.display = 'none';
        }
    }

    // Função para buscar dados do backend
    function buscarDados() {
        fetch('dados_estoque.php')
            .then(response => response.json())
            .then(data => {
                atualizarCards(data);
            })
            .catch(error => console.error('Erro ao carregar dados:', error));
    }

    // Iniciar o carregamento dos dados
    buscarDados();
    });

</script>
<script>
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
</script>

</body>
</html>


<?php include 'footer.php'; ?>