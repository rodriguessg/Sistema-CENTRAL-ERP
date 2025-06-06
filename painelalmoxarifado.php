<?php
include 'header.php'; 
include 'verificar_quantidade_produtos.php';


//  session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Estoque</title>
    <link rel="stylesheet" href="./src/style/style.css">
    <link rel="stylesheet" href="./src/estoque/style/painelalmoxarifado.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
        <link rel="stylesheet" href="src/estoque/style/painelalmoxarifado.css">

    <style>
        .cardss, .card-estoque-produtos {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .card, .card2 {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1 1 200px;
            min-width: 200px;
        }
        .card h3, .card2 h3 {
            margin: 0 0 10px;
            font-size: 1.2rem;
            color: #333;
        }
        .card p, .card2 p {
            margin: 0;
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }
        .grafico-container, .card-estoque-pr, .car-estoque-pr {
            margin-top: 20px;
        }
        .alertas-estoque-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1 1 400px;
            min-width: 300px;
        }
        .alertas-estoque-container h3 {
            margin: 0 0 10px;
            font-size: 1.2rem;
            color: #333;
        }
        .alertas-estoque-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .alertas-estoque-container th, .alertas-estoque-container td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .alertas-estoque-container th {
            background: #007bff;
            color: #fff;
        }
        .alertas-estoque-container .produto-nome {
            color: #333;
        }
        .alertas-estoque-container .quantidade-baixa {
            color: #ff0000;
        }
        .alertas-estoque-container .status-estoque-baixo {
            background-color: #ffcc00;
            color: #333;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .alertas-estoque-container .status-esgotado {
            background-color: #ff4444;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .alertas-estoque-container .ver-todos {
            margin-top: 10px;
            text-align: right;
        }
        .alertas-estoque-container .ver-todos a {
            color: #007bff;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .alertas-estoque-container .ver-todos a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .cardss, .card-estoque-produtos {
                flex-direction: column;
            }
            .card, .card2, .alertas-estoque-container {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>

<h1>Painel de Estoque</h1>
<div class="caderno">
    <div class="dashboard">
        <!-- Cards -->
        <div class="cards-container">
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
            <ul id="listaProdutosAcabando"></ul>
        </div>
        <div class="card">
            <div>
                <?php
                // Configurações do banco de dados
                $host = 'localhost';
                $dbname = 'gm_sicbd';
                $user = 'root';
                $password = '';

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

    <div class="card-estoque-produtos">
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

        <!-- Tabela de Alertas de Estoque -->
        <div class="alertas-estoque-container">
            <h3><i class="fas fa-exclamation-triangle"></i> Alertas de Estoque</h3>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        // Consulta SQL para buscar produtos com estoque baixo
                        $sql_alertas = "SELECT produto, quantidade, estoque_minimo 
                                        FROM produtos 
                                        WHERE quantidade <= estoque_minimo 
                                        ORDER BY quantidade ASC 
                                        LIMIT 3";
                        $stmt_alertas = $pdo->query($sql_alertas);

                        if ($stmt_alertas->rowCount() > 0) {
                            while ($row = $stmt_alertas->fetch(PDO::FETCH_ASSOC)) {
                                $status = $row['quantidade'] == 0 ? 'Esgotado' : 'Estoque Baixo';
                                $status_class = $row['quantidade'] == 0 ? 'status-esgotado' : 'status-estoque-baixo';
                                echo "<tr>";
                                echo "<td class='produto-nome'>" . htmlspecialchars($row['produto']) . "</td>";
                                echo "<td class='quantidade-baixa'>" . htmlspecialchars($row['quantidade']) . " unidades (mínimo: " . htmlspecialchars($row['estoque_minimo']) . ")</td>";
                                echo "<td><span class='$status_class'>" . $status . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Nenhum alerta de estoque encontrado.</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='3'>Erro ao buscar alertas: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="ver-todos">
                <a href="todos_alertas.php">Ver todos os alertas <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <!-- Tabela de Últimos Produtos Cadastrados -->
        <div class="card">
            <div class="card-estoque-pr">
                <h3>Últimos Produtos Cadastrados</h3>
                <table border="1">
                    <thead>
                        <tr>
                            <th>Código do Material</th>
                            <th>Quantidade</th>
                            <th>Descrição</th>
                            <th>Data de Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Consulta SQL para buscar os últimos produtos cadastrados
                            $sql = "SELECT produto, quantidade, descricao, data_cadastro 
                                    FROM produtos 
                                    ORDER BY data_cadastro DESC 
                                    LIMIT 5";
                            $stmt = $pdo->query($sql);

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

        <!-- Tabela de Usuários com Mais Movimentações -->
        <div class="card">
            <div>
                <h2>Usuários com Mais Movimentações</h2>
                <?php
                // Conexão com o banco de dados
                $conn = new mysqli("localhost", "root", "", "gm_sicbd");

                if ($conn->connect_error) {
                    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
                }

                $sql = "SELECT matricula, COUNT(*) AS total_movimentacoes 
                        FROM log_eventos 
                        WHERE tipo_operacao IN ('cadastrou no estoque', 'retirou do estoque') 
                        GROUP BY matricula 
                        ORDER BY total_movimentacoes DESC 
                        LIMIT 5";
                $result = $conn->query($sql);

                if ($result) {
                    if ($result->num_rows > 0) {
                        echo "<table border='1'>
                                <tr>
                                    <th>Usuário (Matrícula)</th>
                                    <th>Total de Movimentações</th>
                                </tr>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['matricula']) . "</td>
                                    <td>" . htmlspecialchars($row['total_movimentacoes']) . "</td>
                                </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "Nenhuma movimentação encontrada.";
                    }
                } else {
                    echo "Erro na consulta: " . $conn->error;
                }

                $conn->close();
                ?>
            </div>
        </div>

        <!-- Gráfico de Produto Mais Vendido -->
        <div class="card">
            <div class="car-estoque-pr">
                <h2>Produto Mais Vendido e Estimativa de Estoque</h2>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Modal para Produtos por Ano -->
    <div id="modalAno" class="modal">
        <div class="modal-content">
            <span class="close-button">×</span>
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
                <tbody id="tabelaProdutosAno"></tbody>
            </table>
        </div>
    </div>
        </div>
            </div>
</div>

<script src="./src/estoque/js/analise-estoque.js"></script>
<script src="./src/estoque/js/painelalmoxarifado.js"></script>
<script src="./src/estoque/js/grafico.js"></script>

</body>
</html>

<?php include 'footer.php'; ?>