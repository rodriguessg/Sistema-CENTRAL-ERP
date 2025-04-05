<?php
include 'header.php'; 
include 'verificar_quantidade_produtos.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Estoque</title>
    <link rel="stylesheet" href="./src/style/style.css">
    <link rel="stylesheet" href="./src/estoque/style/painelalmoxarifado.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                $dbname = 'gm_sicbd';   // Nome do banco de dados
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
                <th>Código do Material</th>
                <th>Quantidade</th>
                <th>Descricao</th>
                <th>Data de Cadastro</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Configurações do banco de dados
            $host = 'localhost'; // Substitua pelo host do banco
            $dbname = 'gm_sicbd';   // Nome do banco de dados
            $user = 'root';      // Nome de usuário do banco
            $password = '';      // Senha do banco
            
            try {
                // Conexão com o banco de dados usando PDO
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Consulta SQL para buscar os últimos produtos cadastrados
                $sql = "SELECT produto, quantidade, descricao, data_cadastro FROM produtos ORDER BY data_cadastro DESC LIMIT 5";
                $stmt = $pdo->query($sql);

                // Exibindo os resultados na tabela
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['produto']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
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
        $conn = new mysqli("localhost", "root", "", "gm_sicbd");

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
<div class="card">
    <div class="car-estoque-pr">
        <h2>Produto Mais Vendido e Estimativa de Estoque</h2>
        </div>
        <canvas id="salesChart"></canvas>
    </div>
</div>
</div>
<script src="./src/estoque/js/analise-estoque.js" ></script>
       

<script src="./src/estoque/js/painelalmoxarifado.js"></script>
<script src="./src/estoque/js/grafico.js">
  
</script>

</body>
</html>


<?php include 'footer.php'; ?>