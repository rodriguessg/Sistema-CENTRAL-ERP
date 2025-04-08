<?php
    include 'header.php';


    // Incluir a conexão com o banco de dados
include 'banco.php'; 

// Verificar se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar o valor de custo do formulário
    $custo = isset($_POST['custo']) ? $_POST['custo'] : 0;

    // Verificar se o custo está em um formato correto (por exemplo, um número válido)
    if (!is_numeric($custo)) {
        echo "Custo inválido!";
        exit();
    }

    // Formatando o custo para 4 casas decimais antes de salvar no banco de dados
    $custo = number_format($custo, 4, '.', ''); // Formata para 4 casas decimais

    // Consulta SQL para inserir o valor de custo na tabela produtos
    $query = "INSERT INTO produtos (custo) VALUES ('$custo')";

    // Executar a consulta
    if ($con->query($query) === TRUE) {
        echo "Produto inserido com sucesso!";
    } else {
        echo "Erro ao inserir produto: " . $con->error;
    }

    // Fechar a conexão com o banco de dados
    $con->close();
}
?>
<?php
// Conexão com o banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Buscar todos os produtos da tabela materiais
$sql = "SELECT codigo, descricao, natureza, classificacao, contabil FROM materiais";
$result = $conn->query($sql);

// Gerar as opções do select com os dados 'data-*'
$options = '';
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='" . $row['codigo'] . "' 
                    data-descricao='" . $row['descricao'] . "' 
                    data-natureza='" . $row['natureza'] . "' 
                    data-classificacao='" . $row['classificacao'] . "' 
                    data-contabil='" . $row['contabil'] . "'>
                    " . $row['descricao'] . "
                </option>";
}

$conn->close();
?>
<?php
include 'banco.php'; // Conexão com o banco de dados

// Verifica se o mês mudou e limpa a tabela 'transicao'
$current_month = date('Y-m');
$query_check_month = "SELECT mes FROM controle_transicao ORDER BY id DESC LIMIT 1";
$result_check_month = $con->query($query_check_month);

if ($result_check_month->num_rows > 0) {
    $last_month = $result_check_month->fetch_assoc()['mes'];
    if ($last_month !== $current_month) {
        $con->query("TRUNCATE TABLE transicao"); // Limpa a tabela
        $con->query("INSERT INTO controle_transicao (mes) VALUES ('$current_month')"); // Atualiza o controle
    }
} else {
    $con->query("INSERT INTO controle_transicao (mes) VALUES ('$current_month')");
}

// Se um produto foi retirado, insere na tabela 'transicao' e atualiza a quantidade em 'produtos'
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $material_id = $_POST['material-nome'];
    $quantidade = (int) $_POST['material-quantidade'];
    $data = date('Y-m-d');

    // Atualizar a quantidade no estoque
    $query_update = "UPDATE produtos SET quantidade = quantidade - $quantidade WHERE id = '$material_id' AND quantidade >= $quantidade";
    if ($con->query($query_update)) {
        $con->query("INSERT INTO transicao (material_id, quantidade, data, tipo) VALUES ('$material_id', '$quantidade', '$data', 'Saída')");
    } else {
        echo "<script>alert('Erro: Estoque insuficiente!');</script>";
    }
}

// Consulta os registros da tabela 'transicao'
$query_transicao = "SELECT t.id, p.produto, p.classificacao, p.localizacao, p.descricao, p.natureza, t.quantidade, p.preco_medio, t.data, t.tipo FROM transicao t INNER JOIN produtos p ON t.material_id = p.id";
$resultado_transicao = $con->query($query_transicao);
?>

<!DOCTYPE html>
<html lang="Pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almoxarifado</title>
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
        <link rel="stylesheet" href="src/estoque/style/linhadotempo.css">

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



</head>
<body>
    
<div class="caderno">

<div class="tabs">
    <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">
        <i class="fas fa-plus-circle"></i> Lançamento de Materiais
    </div>
    <div class="tab" data-tab="consulta" onclick="showTab('consulta')">
        <i class="fas fa-search"></i> Consulta de produtos
    </div>
    <div class="tab" data-tab="Estoque" onclick="showTab('Estoque')">
        <i class="fas fa-cogs"></i> Estoque
    </div>
    <div class="tab" data-tab="retirar" onclick="showTab('retirar')">
        <i class="fas fa-minus-circle"></i> Retirar material
    </div>
    <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">
        <i class="fas fa-file-alt"></i> Relatório
    </div>
    <div class="tab" data-tab="fechamento" onclick="showTab('fechamento')">
        <i class="fas fa-image"></i> Fechamento
    </div>
</div>





<div class="form-container" id="cadastrar" style="display:none;">
    <h3>Lançamento de materiais</h3>
    <form id="form-cadastrar-produto" action="cadastrar_produto.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <div class="input-group">
                <label for="produto">Produto:</label>
                <i class="fas fa-cogs"></i> <!-- Ícone de engrenagem -->
                <!-- Gerando o select com as opções -->
                <select id="produto" name="produto" required onchange="preencherCampos()">
                    <option value="" disabled selected>Selecione um produto</option>
                    <?php echo $options; ?>
                </select>
            </div>

            <!-- Campo de descrição visível -->
            <div class="input-group">
                <label for="descricao">Descrição:</label>
                <i class="fas fa-barcode"></i>
                <input type="text" id="descricao" name="descricao" placeholder="Descrição" required readonly>
            </div>

            <div class="input-group">
                <label for="classificacao">Classificação:</label>
                <i class="fas fa-tag"></i>
                <input type="text" id="classificacao" name="classificacao" placeholder="Digite a classificação" required readonly>
            </div>

            <div class="input-group">
                <label for="natureza">Natureza:</label>
                <i class="fas fa-flask"></i>
                <input type="text" id="natureza" name="natureza" placeholder="Digite a natureza do produto" required readonly>
            </div>

            <div class="input-group">
                <label for="contabil">Contábil:</label>
                <i class="fas fa-calculator"></i>
                <input type="text" id="contabil" name="contabil" placeholder="Digite o código contábil" required readonly>
            </div>

            <div class="input-group">
                <label for="codigo">Código:</label>
                <i class="fas fa-barcode"></i>
                <input type="text" id="codigo" name="codigo" placeholder="Código do produto" required readonly>
            </div>

           
        </div>

        <div class="form-group">

            <div class="input-group">
                <label for="unidade">Unidade:</label>
                <i class="fas fa-box"></i>
                <input type="text" id="unidade" name="unidade" placeholder="Unidade de medida" required>
            </div>

            <div class="input-group">
                <label for="localizacao">Local:</label>
                <i class="fas fa-map-marker-alt"></i>
                <select id="localizacao" name="localizacao" required>
                    <option value="" disabled selected>Selecione o local</option>
                    <option value="xm1">XM1</option>
                    <option value="xm2">XM2</option>
                </select>
            </div>

            <div class="input-group">
                <label for="custo">Custo:</label>
                <i class="fas fa-dollar-sign"></i>
                <input type="text" id="custo" name="custo" placeholder="Digite o custo" required oninput="calcularPrecoMedio()">
            </div>

            <div class="input-group">
                <label for="quantidade">Quantidade:</label>
                <i class="fas fa-cogs"></i>
                <input type="number" id="quantidade" name="quantidade" placeholder="Digite a quantidade" required oninput="calcularPrecoMedio()">
            </div>

            <div class="input-group">
                <label for="preco_medio">Preço Médio:</label>
                <i class="fas fa-money-bill-wave"></i>
                <input type="text" id="preco_medio" name="preco_medio" placeholder="Preço médio" readonly>
            </div>

            <div class="input-group">
                <label for="nf">Nota Fiscal:</label>
                <i class="fas fa-file-invoice"></i>
                <input type="number" id="nf" name="nf" placeholder="Digite o Código" required>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" class="btn-submit">Cadastrar</button>
            <button type="button" id="limpar-formulario" class="btn-clear">Limpar</button>
        </div>
    </form>
</div>


<!-- SCRIPT FINAL - DEVE FICAR DEPOIS DOS CAMPOS -->

<!-- <script>
    function sincronizarProduto() {
        // Copia o valor selecionado do select para o campo de input
        document.getElementById("produto_input").value = document.getElementById("produto").value;
    }
</script> -->


<div class="form-container" id="consulta">
    <h2>Lista de Produtos</h2>
    <label for="filtroProduto">Pesquisar Produto:</label>
    <div class="search-container">
        <input type="text" id="filtroProduto" placeholder="Digite o nome do produto">
    </div>
    <div class="button-group">
            <button class="btn-estoque2" id="filtrar">Filtrar</button>
            <button class="btn-estoque2" id="limpar">Limpar</button>
        </div>
    <!-- Tabela com botões "Detalhes" e "Atualizar" -->
    <div class="table-container">
        <table class="tabela-produtos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Material</th>
                    <th>Classificação</th>                   
                    <th>Descricao</th> 
                    <th>Natureza</th> 
                    <th>Quantidade</th>
                    <th>Local</th>              
                    <th>Custo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaProdutos">
            <?php
                // Conexão com o banco de dados
                $conn = new mysqli('localhost', 'root', '', 'gm_sicbd');

                // Verifica se houve erro na conexão
                if ($conn->connect_error) {
                    die("Falha na conexão: " . $conn->connect_error);
                }

                // Consulta SQL para buscar os produtos
                $sql = "SELECT * FROM produtos";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['produto']}</td>
                             <td>{$row['classificacao']}</td>
                               <td>{$row['localizacao']}</td>
                             <td>{$row['descricao']}</td>
                             <td>{$row['natureza']}</td>
                             <td>{$row['quantidade']}</td>
                             <td>{$row['custo']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='12'>Nenhum produto cadastrado.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
        <div class="pagination"></div>
    </div>
</div>

<div class="form-container" id="retirar">
    <h3>Retirar Material do Estoque</h3>
    <form id="retirar-form" action="retirar_materialestoque.php" method="POST">
        <div class="form-group3">
            <!-- Select para o Nome do Material -->
            <div class="input-group">
                <label for="material-nome">Código do Material:</label>
                <i class="fas fa-cogs"></i> <!-- Ícone ao lado do campo -->
                <select id="material-nome" name="material-nome" required>
                    <option value="">Selecione o material</option>
                    <?php
                    // Conectar ao banco de dados
                    include 'banco.php'; 

                    // Consulta para buscar os produtos na tabela "produtos"
                    $query_produtos = "SELECT id, produto FROM produtos";
                    $resultado_produtos = $con->query($query_produtos);

                    // Verifica se a consulta retornou resultados
                    if ($resultado_produtos->num_rows > 0) {
                        // Preenche o select com os produtos
                        while ($produto = $resultado_produtos->fetch_assoc()) {
                            echo '<option value="' . $produto['id'] . '">' . $produto['produto'] . '</option>';
                        }
                    } else {
                        echo '<option value="">Nenhum material encontrado</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- Código do Material -->
            <div class="input-group">
                <label for="material-codigo">Descrição do Material:</label>
                <i class="fas fa-barcode"></i> <!-- Ícone ao lado do campo -->
                <input type="text" id="material-codigo" name="material-codigo" placeholder="preenchido automaticamente" readonly>
            </div>

            <!-- Classificação -->
            <div class="input-group">
                <label for="material-classificacao">Classificação:</label>
                <i class="fas fa-tags"></i> <!-- Ícone ao lado do campo -->
                <input type="text" id="material-classificacao" name="material-classificacao" placeholder="preenchido automaticamente" readonly>
            </div>

            <!-- Natureza -->
            <div class="input-group">
                <label for="material-natureza">Natureza:</label>
                <i class="fas fa-flask"></i> <!-- Ícone ao lado do campo -->
                <input type="text" id="material-natureza" name="material-natureza" placeholder="preenchido automaticamente" readonly>
            </div>
            

            <!-- Localização -->
            <div class="input-group">
                <label for="material-localizacao">Localização:</label>
                <i class="fas fa-map-marker-alt"></i> <!-- Ícone ao lado do campo -->
                <input type="text" id="material-localizacao" name="material-localizacao" placeholder="preenchido automaticamente" readonly>
            </div>

            <!-- Quantidade -->
            <div class="input-group">
                <label for="material-quantidade">Quantidade:</label>
                <i class="fas fa-cogs"></i> <!-- Ícone ao lado do campo -->
                <input type="number" id="material-quantidade" name="material-quantidade" min="1" placeholder="Digite a quantidade a retirar" required>
            </div>
            <!-- Preço Médio -->
            <div class="input-group">
                <label for="material-preco-medio">Preço Médio:</label>
                <i class="fas fa-dollar-sign"></i> <!-- Ícone ao lado do campo -->
                <input type="text" id="material-preco-medio" name="material-preco-medio" placeholder="preenchido automaticamente" readonly>
            </div>
        </div>
        <div class="button-group">
                <button class="btn-submit" type="submit">Retirar</button>
        </div>
   

        <?php
// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'gm_sicbd');

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Defina o número de itens por página
$itens_por_pagina = 5;

// Obtenha a página atual, caso contrário defina como 1
$pagina_atual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

// Calcule o índice inicial para a consulta
$inicio = ($pagina_atual - 1) * $itens_por_pagina;

// Consulta para obter os dados de transição com informações do produto (usando JOIN)
$query = "
    SELECT t.id, t.material_id, t.quantidade, t.data, t.tipo, 
           p.produto, p.classificacao, p.natureza, p.contabil, p.descricao, p.unidade, 
           p.localizacao, p.custo, p.quantidade AS produto_quantidade, p.nf, p.preco_medio, p.tipo_operacao
    FROM transicao t
    LEFT JOIN produtos p ON t.material_id = p.id
    LIMIT $inicio, $itens_por_pagina
";
$resultado_transicao = $conn->query($query);

// Obtenha o total de registros para calcular o número de páginas
$total_registros = $conn->query("SELECT COUNT(*) FROM transicao")->fetch_row()[0];
$total_paginas = ceil($total_registros / $itens_por_pagina);
?>

<div class="table-container">
    <table class="tabela-transicao">
        <thead>
            <tr>
                <th>ID</th>
                <th>Material</th>
                <th>Classificação</th>
                <th>Local</th>
                <th>Descrição</th>
                <th>Natureza</th>
                <th>Quantidade</th>
                <th>Custo</th>
                <th>Data</th>
                <th>Entrada/Saída</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado_transicao->fetch_assoc()) { ?>
                <tr id="row_<?= $row['id'] ?>">
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['produto'] ?></td>
                    <td><?= $row['classificacao'] ?></td>
                    <td><?= $row['localizacao'] ?></td>
                    <td><?= $row['descricao'] ?></td>
                    <td><?= $row['natureza'] ?></td>
                    <td><?= $row['quantidade'] ?></td>
                    <td><?= number_format($row['preco_medio'], 2, ',', '.') ?></td>
                    <td><?= $row['data'] ?></td>
                    <td class="<?= strtolower($row['tipo']) ?>"><?= $row['tipo'] ?></td>
                    <td>
                        <button class="acoes-button excluir-button" data-id="<?= $row['id'] ?>">Excluir</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Navegação de páginas -->
    <div class="pagination">
    <a href="?pagina=1#">&laquo; Primeira</a>
    <a href="?pagina=<?= $pagina_atual > 1 ? $pagina_atual - 1 : 1 ?>#">Anterior</a>
    <span>Página <?= $pagina_atual ?> de <?= $total_paginas ?></span>
    <a href="?pagina=<?= $pagina_atual < $total_paginas ? $pagina_atual + 1 : $total_paginas ?>#">Próxima</a>
    <a href="?pagina=<?= $total_paginas ?>#">Última &raquo;</a>
</div>


</div>

<?php
// Fechar a conexão com o banco de dados
$conn->close();
?>


    </form>
        <div id="mensagem" style="color: red; margin-top: 10px;"></div>
</div>

<script>
    // Adicionar evento de clique nos botões de excluir
document.querySelectorAll('.excluir-button').forEach(button => {
    button.addEventListener('click', function() {
        // Pega o ID do produto a ser excluído
        const produtoId = this.getAttribute('data-id');

        // Perguntar ao usuário se ele realmente quer excluir
        if (confirm('Tem certeza que deseja excluir este item?')) {
            // Enviar uma requisição AJAX para excluir o produto
            fetch('excluir_produto.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${produtoId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Se a exclusão foi bem-sucedida, remover a linha da tabela
                    const row = document.getElementById(`row_${produtoId}`);
                    row.remove();
                    alert('Produto excluído com sucesso!');
                } else {
                    alert('Erro ao excluir o produto!');
                }
            })
            .catch(error => {
                console.error('Erro ao excluir o produto:', error);
                alert('Erro ao excluir o produto!');
            });
        }
    });
});

</script>
<!-- <script src="./src/estoque/js/select.js"></script> -->


<!-- Modal para Detalhes -->
<div class="modal" id="modal-detalhes">
  <div class="modal-overlay" onclick="fecharModal('modal-detalhes')"></div>
  <div class="modal-content atualizado">
    <span class="modal-close" onclick="fecharModal('modal-detalhes')">&times;</span>
    <div id="modal-informacoes">
      <!-- O conteúdo será carregado dinamicamente -->
    </div>
  </div>
</div>


<!-- Modal para Atualização -->
<div class="modal" id="modal-atualizar">
         <div class="modal-content atualizado">

         <span class="modal-close" onclick="fecharModal('modal-atualizar')">&times;</span>
         <div id="modal-atualizacao">
            <!-- O conteúdo será carregado dinamicamente -->
        </div>
    </div>
</div>

<!-- ABA PARA CONSULTAR QUANTIDADE DE MATERIAIS -->
<div class="form-container" id="Estoque">
    <h2>Consulta de Estoque</h2>
    <label for="filtroestoque">Pesquisar Produto:</label>
    <!-- Campo de pesquisa -->
    <div class="search-container">
        <input type="text" id="filtroestoque" placeholder="Digite o nome do produto">
    
    </div>

    <div class="button-group">
        <button class="btn-estoque2" onclick="filtrarTabela()">Filtrar</button>
        <button class="btn-estoque2" onclick="limparFiltro()">Limpar</button>
    </div>

    <div class="table-container">
        <h3>Lista de Produtos</h3>
        <table class="tabela-estoque">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produto</th>
                    <th>Classificação</th>
                    <th>Local</th>
                    <th> C.contabil</th>
                    <th>Quantidade</th>
                    <th>Custo</th>
                    <th>PreçoMédio </th>
                </tr>
            </thead>
            <tbody id="tabelaestoque">
                <?php
                // Função para conectar ao banco de dados
                function conectarBanco() {
                    $conn = new mysqli('localhost', 'root', '', 'gm_sicbd');
                    if ($conn->connect_error) {
                        die("Falha na conexão: " . $conn->connect_error);
                    }
                    return $conn;
                }

                // Função para buscar e exibir os produtos
                function exibirProdutos($conn) {
                    $sql = "SELECT * FROM produtos";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['produto']}</td>
                                    <td>{$row['classificacao']}</td>
                                    <td>{$row['localizacao']}</td>
                                     <td>{$row['contabil']}</td>
                                    <td>{$row['quantidade']}</td>
                                    <td>{$row['custo']}</td>
                                     <td>{$row['preco_medio']}</td>
                                   
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nenhum produto cadastrado.</td></tr>";
                    }
                }

                // Conectar ao banco e exibir os produtos
                $conn = conectarBanco();
                exibirProdutos($conn);
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ABA PARA GERAR RELATÓRIO -->
<div class="form-container" id="relatorio">
    <h3>Gerar Relatório</h3>
    <form id="form-relatorio" class="relatorio-form">
        <!-- Seletor de Período -->
        <label for="periodo">Selecione o Período:</label>
        <div class="relatorio-group">
            <select id="periodo" name="periodo" required onchange="toggleExercicioSelector(this.value)">
                <option value="" disabled selected>Escolha uma opção</option>
                <option value="semanal">Semanal</option>
                <option value="mensal">Mensal</option>
                <option value="anual">Anual</option>
                <option value="fechamento">Fechamento</option>
            </select>
        </div>

        <!-- Seletor de Exercício (Ano) -->
        <label for="exercicio">Selecione o Exercício:</label>
        <div class="relatorio-group" id="exercicio-group" style="display: none;">
            <select id="exercicio" name="exercicio">
                <option value="" disabled selected>Carregando...</option>
            </select>
        </div>

        <!-- Seletor de Mês (visível quando "Mensal" é selecionado) -->
        <label for="mes">Selecione o Mês:</label>
        <div class="relatorio-group" id="mes-group" style="display: none;">
            <select id="mes" name="mes">
                <option value="" disabled selected>Escolha um mês</option>
            </select>
        </div>

        <!-- Campo de Usuário Logado -->
        <div class="relatorio-group">
            <label for="usuario">Usuário Logado:</label>
            <input type="text" id="usuario" name="usuario" value="" readonly>
        </div>

        <!-- Botão de Submissão -->
        <div class="relatorio-group">
            <button type="button" id="incluir_quantidade" name="incluir_quantidade" onclick="gerarRelatorio()">Gerar Relatório</button>
        </div>
    </form>

    <!-- Área para exibição do relatório gerado -->
    <div id="resultadoRelatorio" style="margin-top: 20px;"></div>

    <!-- Botão de Impressão -->
    <button id="imprimirBtn" onclick="imprimirTabela()" style="display: none; margin-top: 10px;">Imprimir Tabela</button>
    
    <!-- Botão de Exportação para Excel -->
    <button id="exportarExcelBtn" onclick="exportarParaExcel()" style="display: none; margin-top: 10px;">Exportar para Excel</button>
</div>

<script>
    // Função para exibir o seletor de exercício ou mês dependendo da opção selecionada
    function toggleExercicioSelector(periodo) {
        const mesGroup = document.getElementById('mes-group');
        const exercicioGroup = document.getElementById('exercicio-group');
        
        if (periodo === 'mensal') {
            // Exibe o seletor de meses
            mesGroup.style.display = 'block';
            exercicioGroup.style.display = 'none';

            // Preencher os meses
            const meses = [
                "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", 
                "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
            ];
            
            const mesSelect = document.getElementById('mes');
            mesSelect.innerHTML = '<option value="" disabled selected>Escolha um mês</option>'; // Limpar o conteúdo existente
            
            meses.forEach((mes, index) => {
                const option = document.createElement('option');
                option.value = index + 1;  // O valor será o número do mês (1-12)
                option.textContent = mes;
                mesSelect.appendChild(option);
            });
        } else if (periodo === 'anual') {
            // Exibe o seletor de exercício (ano)
            mesGroup.style.display = 'none';
            exercicioGroup.style.display = 'block';
            fetchExercicios(); // Carregar exercícios via AJAX
        } else {
            mesGroup.style.display = 'none';
            exercicioGroup.style.display = 'none';
        }
    }

    // Função para carregar os exercícios (anos) disponíveis
    async function fetchExercicios() {
        try {
            const response = await fetch('buscar_exercicios.php');
            const exercicios = await response.json();
            const exercicioSelect = document.getElementById('exercicio');

            exercicioSelect.innerHTML = '<option value="" disabled selected>Selecione o ano</option>';
            exercicios.forEach(ano => {
                const option = document.createElement('option');
                option.value = ano;
                option.textContent = ano;
                exercicioSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Erro ao carregar exercícios:', error);
        }
    }

    // Preencher o campo de usuário logado dinamicamente
    document.addEventListener("DOMContentLoaded", () => {
        const usuario = "<?php echo $_SESSION['username'] ?? 'Desconhecido'; ?>";
        document.getElementById("usuario").value = usuario;
    });

    // Função para gerar o relatório
    async function gerarRelatorio() {
        const periodo = document.getElementById('periodo').value;
        const exercicio = document.getElementById('exercicio').value;
        const mes = document.getElementById('mes').value;
        const usuario = document.getElementById('usuario').value;

        if (!periodo) {
            alert('Por favor, selecione o período.');
            return;
        }

        if (periodo === 'anual' && !exercicio) {
            alert('Por favor, selecione um exercício para o relatório anual.');
            return;
        }

        if (periodo === 'mensal' && !mes) {
            alert('Por favor, selecione um mês para o relatório mensal.');
            return;
        }

        try {
            const response = await fetch('gerar_relatorioestoque.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    periodo,
                    exercicio,
                    mes,
                    usuario
                })
            });

            const data = await response.text();
            const resultadoDiv = document.getElementById('resultadoRelatorio');
            resultadoDiv.innerHTML = data;

            // Exibe os botões de impressão e exportação se houver tabela no relatório
            const imprimirBtn = document.getElementById('imprimirBtn');
            const exportarExcelBtn = document.getElementById('exportarExcelBtn');
            if (data.includes('<table')) {
                imprimirBtn.style.display = 'block';
                exportarExcelBtn.style.display = 'block';
            } else {
                imprimirBtn.style.display = 'none';
                exportarExcelBtn.style.display = 'none';
            }
        } catch (error) {
            console.error('Erro ao gerar relatório:', error);
            alert('Erro ao gerar o relatório. Tente novamente.');
        }
    }

    // Função para imprimir a tabela
    function imprimirTabela() {
        const conteudo = document.getElementById('resultadoRelatorio').innerHTML;
        const janelaImpressao = window.open('', '', 'width=800,height=600');
        janelaImpressao.document.write(`
            <html>
            <head>
                <title>Impressão de Relatório</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f4f4f4; }
                </style>
            </head>
            <body>
                ${conteudo}
            </body>
            </html>
        `);
        janelaImpressao.document.close();
        janelaImpressao.print();
    }

    // Função para exportar o relatório para Excel
    function exportarParaExcel() {
        const conteudo = document.getElementById('resultadoRelatorio').innerHTML;
        const blob = new Blob([conteudo], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.download = 'relatorio.xls';
        link.click();

        URL.revokeObjectURL(url);
    }
</script>



<!-- FECHAMENTO DE ALOMXARIFADO MENSAL -->

<div class="form-container" id="fechamento" style="display: flex; justify-content: center; margin:15%; margin-top:20px">
    <button id="realizarFechamentoButton" onclick="realizarFechamento()">Realizar Fechamento</button>
    <h2>Histórico de Fechamentos</h2>
    
    <div id="linhaDoTempo"></div>
</div>
<!-- Modal de Fechamento -->
<div id="modalFechamento" style="display: none;">
    <div id="modalFechamentoContent">
        <table id="tabelaFechamentos">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Natureza</th>
                    <th>Total Entrada</th>
                    <th>Total Saída</th>
                    <th>Saldo Atual</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button onclick="gerarPDF()">Imprimir PDF</button>
        <button onclick="fecharModal()">Fechar</button>
    </div>
</div>



<script src="./src/estoque/js/escrevefechamento-linhadotempo.js"></script>




<!-- PREENCHE MATERIAIS PARA RETIRADA -->
<script>
    document.getElementById('material-nome').addEventListener('change', function() {
    const nomeMaterialId = this.value; // Obtém o ID do material selecionado

    // Verifica se os elementos existem antes de tentar acessá-los
    const descricaoInput = document.getElementById('material-codigo');
    const classificacaoInput = document.getElementById('material-classificacao');
    const naturezaInput = document.getElementById('material-natureza');
    const localizacaoInput = document.getElementById('material-localizacao');
    const precoMedioInput = document.getElementById('material-preco-medio');
    const mensagemDiv = document.getElementById('mensagem');

    if (!descricaoInput || !classificacaoInput || !naturezaInput || !localizacaoInput || !precoMedioInput) {
        console.error("Erro: Um ou mais elementos não existem no HTML.");
        return;
    }

    // Limpa os campos e a mensagem de erro
    descricaoInput.value = '';
    classificacaoInput.value = '';
    naturezaInput.value = '';
    localizacaoInput.value = '';
    precoMedioInput.value = '';
    mensagemDiv.innerText = '';

    if (nomeMaterialId) {
        fetch('buscar_dados_produto.php?id=' + nomeMaterialId)
            .then(response => response.json())
            .then(data => {
                console.log("Resposta da API:", data); // Depuração

                if (data.success) {
                    setTimeout(() => {
                        descricaoInput.value = data.descricao || ''; // Correção aqui
                        classificacaoInput.value = data.classificacao || '';
                        naturezaInput.value = data.natureza || '';
                        localizacaoInput.value = data.localizacao || '';
                        precoMedioInput.value = data.preco_medio || '';
                    }, 300);
                    mensagemDiv.innerText = '';
                } else {
                    mensagemDiv.innerText = 'Material não encontrado.';
                }
            })
            .catch(err => {
                console.error('Erro ao buscar os dados:', err);
                mensagemDiv.innerText = 'Erro na busca. Tente novamente.';
            });
    } else {
        mensagemDiv.innerText = ''; // Limpa a mensagem se nada for selecionado
    }
    });


</script>
<!-- AO RETIRAR ESTE SCRIPT APRESEMTA ERRO NO PREENCHIMENTO DO NOME DO USUÁRIO NO RELATÓRIO -->


<!-- JS CÁLCULO DE PREÇO MÉDIO -->
<script src="./src/estoque/js/calc-preco-medio.js"></script>
<!-- JS DE PAGINA E FILTRO DA TABELA-ESTOQUE -->
<script src="./src/estoque/js/paginacao-filtro.js"></script>
<script src="./src/estoque/js/retirada-pagina.js"></script>

<!-- PREENCHIMENTO AUTOMÁTICO RETIRADA DE PRODUTO -->
 <script src="./src/estoque/js/preencher-produto-retiradacodigoantigo.js"></script>
<!-- <script src="./src/estoque/js/preencher-produto-retirada.js"></script> -->
<!--  JS PREENHE OS DETALHES DA LINHA SELECIONADA NO MODAL -->
<script src="./src/estoque/js/modal-estoque.js"></script>

<!-- JS ATIVAÇÃO DAS ABAS -->
<script src="./src/estoque/js/active-tab-estoque.js"></script>


</body>
</html>

<!-- <?php include 'footer.php'; ?> -->