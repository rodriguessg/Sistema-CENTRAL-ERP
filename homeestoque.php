<?php
    // Inicia a sessão
    session_start();

    // Conexão com o banco de dados
    include 'banco.php';

    // Função para verificar o login do usuário
    function verificarLogin() {
        if (!isset($_SESSION['username'])) {
            header('Location: index.php');
            exit();
        }
    }

    // Função para inserir um novo produto no banco
    function inserirProduto($custo) {
        global $con;

        // Formatando o custo para 4 casas decimais
        $custo = number_format($custo, 4, '.', '');

        // Prepara a consulta SQL com um statement preparado (prevenção contra SQL Injection)
        $query = "INSERT INTO produtos (custo) VALUES (?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("d", $custo); // 'd' para double, que é o tipo de dado para custo
        if ($stmt->execute()) {
            echo "Produto inserido com sucesso!";
        } else {
            echo "Erro ao inserir produto: " . $con->error;
        }
        $stmt->close();
    }

    // Função para verificar o estoque e gerar notificações e ordens de compra
    function verificarEstoque() {
        global $con;

        $query_produtos = "SELECT id, produto, quantidade, estoque_minimo FROM produtos";
        $resultado_produtos = $con->query($query_produtos);

        while ($produto = $resultado_produtos->fetch_assoc()) {
            if ($produto['quantidade'] <= $produto['estoque_minimo']) {
                $data_criacao = date('Y-m-d H:i:s');
                
                // Verifica se já existe uma notificação para o produto
                $query_notificacao_existente = "SELECT * FROM notificacoes WHERE mensagem LIKE ? AND situacao = 'nao lida'";
                $stmt = $con->prepare($query_notificacao_existente);
                $mensagem = "%" . $produto['produto'] . "%";
                $stmt->bind_param("s", $mensagem);
                $stmt->execute();
                $resultado_notificacao = $stmt->get_result();
                $stmt->close();

                // Se não existir notificação, insere uma nova
                if ($resultado_notificacao->num_rows == 0) {
                    $username = 'estoque';
                    $setor = 'estoque';
                    $mensagem = "#{$produto['produto']} chegou ao seu limite de estoque.";
                    $situacao = 'nao lida';
                    
                    $query_notificacao = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                                        VALUES (?, ?, ?, ?, ?)";
                    $stmt = $con->prepare($query_notificacao);
                    $stmt->bind_param("sssss", $username, $setor, $mensagem, $situacao, $data_criacao);
                    $stmt->execute();
                    $stmt->close();
                }
                
                // Gerar ordem de compra
                $query_ordem_compra = "INSERT INTO ordens_compra (produto_id, quantidade, data_criacao) 
                                    VALUES (?, ?, ?)";
                $stmt = $con->prepare($query_ordem_compra);
                $stmt->bind_param("iis", $produto['id'], $produto['estoque_minimo'], $data_criacao);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // Função para gerenciar a transição de estoque
    function gerenciarTransicao() {
        global $con;

        // Verifica se o mês mudou e limpa a tabela 'transicao'
        $current_month = date('Y-m');
        $query_check_month = "SELECT mes FROM controle_transicao ORDER BY id DESC LIMIT 1";
        $result_check_month = $con->query($query_check_month);

        if ($result_check_month->num_rows > 0) {
            $last_month = $result_check_month->fetch_assoc()['mes'];
            if ($last_month !== $current_month) {
                $con->query("TRUNCATE TABLE transicao");
                $con->query("INSERT INTO controle_transicao (mes) VALUES ('$current_month')");
            }
        } else {
            $con->query("INSERT INTO controle_transicao (mes) VALUES ('$current_month')");
        }

        // Se um produto foi retirado, insere na tabela 'transicao' e atualiza a quantidade em 'produtos'
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['material-nome']) && isset($_POST['material-quantidade'])) {
            $material_id = $_POST['material-nome'];
            $quantidade = (int) $_POST['material-quantidade'];
            $data = date('Y-m-d');

            // Atualiza a quantidade no estoque
            $query_update = "UPDATE produtos SET quantidade = quantidade - ? WHERE id = ? AND quantidade >= ?";
            $stmt = $con->prepare($query_update);
            $stmt->bind_param("iii", $quantidade, $material_id, $quantidade);
            if ($stmt->execute()) {
                $query_transicao = "INSERT INTO transicao (material_id, quantidade, data, tipo) VALUES (?, ?, ?, 'Saída')";
                $stmt = $con->prepare($query_transicao);
                $stmt->bind_param("iis", $material_id, $quantidade, $data);
                $stmt->execute();
            } else {
                echo "<script>alert('Erro: Estoque insuficiente!');</script>";
            }
            $stmt->close();
        }
    }

    // Chama a função de verificação de login ao acessar a página
    verificarLogin();

    // Exemplo de uso das funções acima
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['custo'])) {
            inserirProduto($_POST['custo']);
        }
        gerenciarTransicao();
        verificarEstoque();
    }
    include 'header.php';
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


<!DOCTYPE html>
<html lang="Pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almoxarifado</title>
    <link rel="stylesheet" href="src/estoque/style/estoque2.css">
    <link rel="stylesheet" href="src/estoque/style/linhadotempo.css">
     <link rel="stylesheet" href="src/style/tabs.css">


<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

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
        <i class="fas fa-file-alt"></i> Relatórios
    </div>
    <div class="tab" data-tab="fechamento" onclick="showTab('fechamento')">
        <i class="fas fa-image"></i> Fechamento
    </div>
</div>


<!-- Seção: Lançamento de Materiais -->
<div class="form-container" id="cadastrar" style="display:none;">
    <div class="section-header">
        <div class="header-icon">
            <i class="fas fa-plus-circle"></i>
        </div>
        <div class="header-content">
            <h3>Lançamento de materiais</h3>
            <p>Cadastre novos produtos no sistema de estoque</p>
        </div>
    </div>
    
    <form id="form-cadastrar-produto" action="./almoxarifado/cadastrar_produto.php" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-row">
                <div class="input-group">
                    <label for="produto">
                        <i class="fas fa-cube"></i>
                        Produto
                    </label>
                    <div class="input-wrapper">
                        <select id="produto" name="produto" required onchange="preencherCampos()">
                            <option value="" disabled selected>Selecione um produto</option>
                            <?php echo $options; ?>
                        </select>
                        <i class="fas fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="descricao">
                        <i class="fas fa-align-left"></i>
                        Descrição
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="descricao" name="descricao" placeholder="Descrição do produto" required readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="classificacao">
                        <i class="fas fa-tags"></i>
                        Classificação
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="classificacao" name="classificacao" placeholder="Classificação do produto" required readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="natureza">
                        <i class="fas fa-leaf"></i>
                        Natureza
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="natureza" name="natureza" placeholder="Natureza do produto" required readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="material-DATA">
                        <i class="fas fa-calendar-alt"></i>
                        Validade
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="material-natureza" name="material-natureza" placeholder="Preenchido automaticamente" readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="contabil">
                        <i class="fas fa-calculator"></i>
                        Código Contábil
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="contabil" name="contabil" placeholder="Código contábil" required readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="codigo">
                        <i class="fas fa-barcode"></i>
                        Código do Produto
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="codigo" name="codigo" placeholder="Código do produto" required readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="unidade">
                        <i class="fas fa-ruler"></i>
                        Unidade de Medida
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="unidade" name="unidade" placeholder="Ex: UN, KG, M" required>
                        <i class="fas fa-edit input-status editable"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="localizacao">
                        <i class="fas fa-map-marker-alt"></i>
                        Localização
                    </label>
                    <div class="input-wrapper">
                        <select id="localizacao" name="localizacao" required>
                            <option value="" disabled selected>Selecione o local</option>
                            <option value="xm1">XM1 - Almoxarifado Principal</option>
                            <option value="xm2">XM2 - Almoxarifado Secundário</option>
                        </select>
                        <i class="fas fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="custo">
                        <i class="fas fa-dollar-sign"></i>
                        Custo Unitário
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="custo" name="custo" placeholder="R$ 0,00" required oninput="calcularPrecoMedio()">
                        <i class="fas fa-edit input-status editable"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="quantidade">
                        <i class="fas fa-sort-numeric-up"></i>
                        Quantidade
                    </label>
                    <div class="input-wrapper">
                        <input type="number" id="quantidade" name="quantidade" placeholder="0" required oninput="calcularPrecoMedio()">
                        <i class="fas fa-edit input-status editable"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="preco_medio">
                        <i class="fas fa-chart-line"></i>
                        Preço Médio
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="preco_medio" name="preco_medio" placeholder="Calculado automaticamente" readonly>
                        <i class="fas fa-calculator input-status"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="nf">
                        <i class="fas fa-file-invoice"></i>
                        Nota Fiscal
                    </label>
                    <div class="input-wrapper">
                        <input type="number" id="nf" name="nf" placeholder="Número da NF" required>
                        <i class="fas fa-edit input-status editable"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i>
                Cadastrar Produto
            </button>
            <button type="button" id="limpar-formulario" class="btn-secondary">
                <i class="fas fa-eraser"></i>
                Limpar Formulário
            </button>
        </div>
    </form>
</div>

<!-- Seção: Consulta de Produtos -->
<div class="form-container" id="consulta">
    <div class="section-header">
        <div class="header-icon">
            <i class="fas fa-search"></i>
        </div>
        <div class="header-content">
            <h2>Lista de Produtos</h2>
            <p>Consulte e gerencie produtos cadastrados</p>
        </div>
    </div>
    
    <div class="search-section">
        <div class="search-wrapper">
            <label for="filtroProduto">
                <i class="fas fa-filter"></i>
                Pesquisar Produto
            </label>
            <div class="search-input-group">
                <input type="text" id="filtroProduto" placeholder="Digite o nome do produto para filtrar...">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>
        
        <div class="search-actions">
            <button class="btn-filter" id="filtrar">
                <i class="fas fa-filter"></i>
                Filtrar
            </button>
            <button class="btn-clear" id="limpar">
                <i class="fas fa-times"></i>
                Limpar
            </button>
        </div>
    </div>

    <div class="table-section" data-total-registros="<?php echo $result->num_rows; ?>">
        <div class="table-header">
            <h3>
                <i class="fas fa-table"></i>
                Produtos Cadastrados
            </h3>
            <div class="table-info">
                <span class="record-count">
                    <i class="fas fa-info-circle"></i>
                    <span id="totalRegistros"><?php echo $result->num_rows; ?> registros encontrados</span>
                </span>
            </div>
        </div>
        
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-barcode"></i> Código</th>
                        <th><i class="fas fa-tags"></i> Classificação</th>
                        <th><i class="fas fa-align-left"></i> Descrição</th>
                        <th><i class="fas fa-leaf"></i> Natureza</th>
                        <th><i class="fas fa-sort-numeric-up"></i> Quantidade</th>
                        <th><i class="fas fa-map-marker-alt"></i> Local</th>
                        <th><i class="fas fa-dollar-sign"></i> Custo</th>
                        <th><i class="fas fa-chart-line"></i> Preço Médio</th>
                        <th><i class="fas fa-cogs"></i> Ações</th>
                    </tr>
                </thead>
                <tbody id="tabelaProdutos">
                <?php
                    // Função para determinar a classe do badge de quantidade
                    function getStockClass($quantidade) {
                        $qtd = (int)$quantidade;
                        if ($qtd > 50) return 'good-stock';
                        if ($qtd > 10) return 'medium-stock';
                        return 'low-stock';
                    }

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
                            $quantidadeClass = getStockClass($row['quantidade']);
                            echo "<tr>
                                    <td><span class='id-badge'>{$row['id']}</span></td>
                                    <td><code>" . htmlspecialchars($row['produto']) . "</code></td>
                                    <td><span class='tag'>" . htmlspecialchars($row['classificacao']) . "</span></td>
                                    <td>" . ($row['descricao'] ? htmlspecialchars($row['descricao']) : 'Descrição não encontrada') . "</td>
                                    <td><span class='nature-badge'>" . htmlspecialchars($row['natureza']) . "</span></td>
                                    <td><span class='quantity-badge $quantidadeClass'>{$row['quantidade']}</span></td>
                                    <td><span class='location-badge'>" . htmlspecialchars($row['localizacao']) . "</span></td>
                                    <td><span class='currency'>R$ {$row['custo']}</span></td>
                                    <td><span class='currency'>R$ {$row['preco_medio']}</span></td>
                                    <td>
                                        <div class='action-buttons'>
                                            <button class='btn-action btn-details' onclick='abrirModalDetalhes({$row['id']})' title='Ver detalhes'>
                                                <i class='fas fa-eye'></i>
                                            </button>
                                            <button class='btn-action btn-edit' onclick='abrirModalAtualizar({$row['id']})' title='Editar'>
                                                <i class='fas fa-edit'></i>
                                            </button>
                                        </div>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr class='no-data'>
                                <td colspan='10' class='text-center'>
                                    <i class='fas fa-inbox'></i>
                                    <span>Nenhum produto cadastrado</span>
                                </td>
                              </tr>";
                    }

                    $conn->close();
                ?>
                </tbody>
            </table>
        </div>
        
        <div class="pagination-wrapper">
            <div class="pagination"></div>
        </div>
    </div>
</div>

<!-- Seção: Retirar Material -->
<div class="form-container" id="retirar">
    <div class="section-header">
        <div class="header-icon">
            <i class="fas fa-minus-circle"></i>
        </div>
        <div class="header-content">
            <h3>Retirar Material do Estoque</h3>
            <p>Registre a saída de materiais do almoxarifado</p>
        </div>
    </div>
    
    <form id="retirar-form" action="./almoxarifado/retirar_materialestoque.php" method="POST">
        <div class="form-grid">
            <div class="form-row">
                <div class="input-group">
                    <label for="material-nome">
                        <i class="fas fa-cube"></i>
                        Código do Material
                    </label>
                    <div class="input-wrapper">
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
                        <i class="fas fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="material-codigo">
                        <i class="fas fa-align-left"></i>
                        Descrição do Material
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="material-codigo" name="material-codigo" placeholder="Preenchido automaticamente" readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="material-classificacao">
                        <i class="fas fa-tags"></i>
                        Classificação
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="material-classificacao" name="material-classificacao" placeholder="Preenchido automaticamente" readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="material-natureza1">
                        <i class="fas fa-leaf"></i>
                        Natureza
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="material-natureza1" name="material-natureza1" placeholder="Preenchido automaticamente" readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="material-localizacao">
                        <i class="fas fa-map-marker-alt"></i>
                        Localização
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="material-localizacao" name="material-localizacao" placeholder="Preenchido automaticamente" readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label for="material-quantidade">
                        <i class="fas fa-minus"></i>
                        Quantidade a Retirar
                    </label>
                    <div class="input-wrapper">
                        <input type="number" id="material-quantidade" name="material-quantidade" min="1" placeholder="Digite a quantidade" required>
                        <i class="fas fa-edit input-status editable"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="material-preco-medio">
                        <i class="fas fa-chart-line"></i>
                        Preço Médio
                    </label>
                    <div class="input-wrapper">
                        <input type="text" id="material-preco-medio" name="material-preco-medio" placeholder="Preenchido automaticamente" readonly>
                        <i class="fas fa-lock input-status"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn-primary" type="submit">
                <i class="fas fa-minus-circle"></i>
                Retirar Material
            </button>
            <button class="btn-export" id="btnExportPDF">
                <i class="fas fa-file-pdf"></i>
                Exportar PDF
            </button>
        </div>
    </form>

   <!-- Histórico de Transações -->
<div class="transaction-history">
    <div class="section-header">
        <div class="header-icon">
            <i class="fas fa-history"></i>
        </div>
        <div class="header-content">
            <h3>Histórico de Movimentações</h3>
            <p>Últimas transações de entrada e saída</p>
        </div>
    </div>

    <?php
        // Conectar ao banco de dados
        $conn = new mysqli('localhost', 'root', '', 'gm_sicbd');

        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        $itens_por_pagina = 25;
        $pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
        if ($pagina_atual < 1) $pagina_atual = 1;

        $inicio = ($pagina_atual - 1) * $itens_por_pagina;

        // ⚠ Consulta com valores diretamente no SQL (sem prepared statement)
        $query = "
            SELECT t.id, t.material_id, t.quantidade, t.data, t.tipo, t.valor_custo_total,
                   p.produto, p.classificacao, p.natureza, p.contabil, p.descricao, p.unidade,
                   p.localizacao, p.quantidade AS produto_quantidade, p.nf, p.preco_medio, p.tipo_operacao
            FROM transicao t
            LEFT JOIN produtos p ON t.material_id = p.id
            ORDER BY t.data DESC
            LIMIT $inicio, $itens_por_pagina
        ";

        $resultado_transicao = $conn->query($query);

        if (!$resultado_transicao) {
            die("Erro na consulta SQL: " . $conn->error);
        }

        // Total de páginas
        $total_result = $conn->query("SELECT COUNT(*) as total FROM transicao");
        $total_registros = $total_result->fetch_assoc()['total'];
        $total_paginas = ceil($total_registros / $itens_por_pagina);
    ?>

    <div class="table-container" style="max-height: 500px; overflow-y: auto;">
        <table class="data-table transaction-table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> ID</th>
                    <th><i class="fas fa-cube"></i> Material</th>
                    <th><i class="fas fa-tags"></i> Classificação</th>
                    <th><i class="fas fa-map-marker-alt"></i> Local</th>
                    <th><i class="fas fa-align-left"></i> Descrição</th>
                    <th><i class="fas fa-leaf"></i> Natureza</th>
                    <th><i class="fas fa-sort-numeric-up"></i> Quantidade</th>
                    <th><i class="fas fa-dollar-sign"></i> Custo Total</th>
                    <th><i class="fas fa-calendar"></i> Data</th>
                    <th><i class="fas fa-exchange-alt"></i> Tipo</th>
                    <th><i class="fas fa-cogs"></i> Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultado_transicao->fetch_assoc()) { ?>
                    <tr id="row_<?= $row['id'] ?>">
                        <td><span class="id-badge"><?= $row['id'] ?></span></td>
                        <td><code><?= $row['produto'] ?></code></td>
                        <td><span class="tag"><?= $row['classificacao'] ?></span></td>
                        <td><span class="location-badge"><?= $row['localizacao'] ?></span></td>
                        <td><?= $row['descricao'] ?></td>
                        <td><span class="nature-badge"><?= $row['natureza'] ?></span></td>
                        <td><span class="quantity"><?= $row['quantidade'] ?></span></td>
                        <td><span class="currency">R$ <?= number_format($row['valor_custo_total'], 2, ',', '.') ?></span></td>
                        <td><span class="date"><?= date('d/m/Y', strtotime($row['data'])) ?></span></td>
                        <td>
                            <span class="transaction-type <?= strtolower($row['tipo']) ?>">
                                <i class="fas fa-<?= $row['tipo'] == 'Entrada' ? 'arrow-up' : 'arrow-down' ?>"></i>
                                <?= $row['tipo'] ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn-action btn-delete" data-id="<?= $row['id'] ?>" title="Excluir transação">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php
    $conn->close();
    ?>
</div>


    <div id="mensagem" class="message-container"></div>
</div>

<!-- Seção: Estoque -->
<div class="form-container" id="Estoque">
    <div class="section-header">
        <div class="header-icon">
            <i class="fas fa-boxes"></i>
        </div>
        <div class="header-content">
            <h2>Consulta de Estoque</h2>
            <p>Visualize o inventário atual do almoxarifado</p>
        </div>
    </div>
    
    <div class="search-section">
        <div class="search-wrapper">
            <label for="filtroestoque">
                <i class="fas fa-filter"></i>
                Pesquisar no Estoque
            </label>
            <div class="search-input-group">
                <input type="text" id="filtroestoque" placeholder="Digite o nome do produto para filtrar...">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>
        
        <div class="search-actions">
            <button class="btn-filter" onclick="filtrarTabela()">
                <i class="fas fa-filter"></i>
                Filtrar
            </button>
            <button class="btn-clear" onclick="limparFiltro()">
                <i class="fas fa-times"></i>
                Limpar
            </button>
        </div>
    </div>

    <div class="table-section">
        <div class="table-header">
            <h3>
                <i class="fas fa-warehouse"></i>
                Inventário Atual
            </h3>
            <div class="table-info">
                <span class="record-count">
                    <i class="fas fa-info-circle"></i>
                    Produtos em estoque
                </span>
            </div>
        </div>
        
        <div class="table-container">
            <table class="data-table stock-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-barcode"></i> Código</th>
                        <th><i class="fas fa-tags"></i> Classificação</th>
                        <th><i class="fas fa-map-marker-alt"></i> Local</th>
                        <th><i class="fas fa-calculator"></i> C. Contábil</th>
                        <th><i class="fas fa-sort-numeric-up"></i> Quantidade</th>
                        <th><i class="fas fa-dollar-sign"></i> Custo</th>
                        <th><i class="fas fa-chart-line"></i> Preço Médio</th>
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
                        $sql = "SELECT * FROM produtos ORDER BY produto ASC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $status_class = $row['quantidade'] <= 10 ? 'low-stock' : ($row['quantidade'] <= 50 ? 'medium-stock' : 'good-stock');
                                echo "<tr class='$status_class'>
                                        <td><span class='id-badge'>{$row['id']}</span></td>
                                        <td><code>{$row['produto']}</code></td>
                                        <td><span class='tag'>{$row['classificacao']}</span></td>
                                        <td><span class='location-badge'>{$row['localizacao']}</span></td>
                                        <td><code>{$row['contabil']}</code></td>
                                        <td>
                                            <span class='quantity-badge $status_class'>
                                                <i class='fas fa-cube'></i>
                                                {$row['quantidade']}
                                            </span>
                                        </td>
                                        <td><span class='currency'>R$ {$row['custo']}</span></td>
                                        <td><span class='currency'>R$ {$row['preco_medio']}</span></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='no-data'>
                                    <i class='fas fa-inbox'></i>
                                    <span>Nenhum produto em estoque</span>
                                  </td></tr>";
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
</div>

<!-- Seção: Fechamento -->
<div class="form-container" id="fechamento">
    <div class="section-header">
        <div class="header-icon">
            <i class="fas fa-calendar-check"></i>
        </div>
        <div class="header-content">
            <h2>Fechamento do Almoxarifado</h2>
            <p>Realize o fechamento mensal do inventário</p>
        </div>
    </div>
    
    <div class="closure-actions">
        <button id="realizarFechamentoButton" class="btn-primary" onclick="realizarFechamento()">
            <i class="fas fa-calendar-check"></i>
            Realizar Fechamento
        </button>
    </div>
    
    <div class="closure-history">
        <h3>
            <i class="fas fa-history"></i>
            Histórico de Fechamentos
        </h3>
        <div id="linhaDoTempo"></div>
    </div>

    <!-- Modal de Fechamento -->
    <div id="modalFechamento" style="display: none;">
        <div id="modalFechamentoContent">
            <table id="tabelaFechamentos">
                <thead>
                    <tr>
                        <th><i class="fas fa-user"></i> Username</th>
                        <th><i class="fas fa-leaf"></i> Natureza</th>
                        <th><i class="fas fa-arrow-up"></i> Total Entrada</th>
                        <th><i class="fas fa-arrow-down"></i> Total Saída</th>
                        <th><i class="fas fa-balance-scale"></i> Saldo Atual</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div class="modal-actions">
                <button onclick="gerarPDF()" class="btn-primary">
                    <i class="fas fa-file-pdf"></i>
                    Imprimir PDF
                </button>
                <button onclick="fecharModal()" class="btn-secondary">
                    <i class="fas fa-times"></i>
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Seção: Relatórios -->
<div class="form-container" id="relatorio">
    <div class="section-header">
        <div class="header-icon">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="header-content">
            <h3>Gerar Relatório</h3>
            <p>Crie relatórios personalizados do sistema</p>
        </div>
    </div>
    
    <form id="form-relatorio" class="report-form">
        <div class="form-grid">
            <div class="input-group">
                <label for="periodo">
                    <i class="fas fa-calendar-alt"></i>
                    Período do Relatório
                </label>
                <div class="input-wrapper">
                    <select id="periodo" name="periodo" required onchange="toggleExercicioSelector(this.value)">
                        <option value="" disabled selected>Escolha uma opção</option>
                        <option value="semanal">
                            <i class="fas fa-calendar-week"></i>
                            Semanal
                        </option>
                        <option value="mensal">
                            <i class="fas fa-calendar-alt"></i>
                            Mensal
                        </option>
                        <option value="anual">
                            <i class="fas fa-calendar"></i>
                            Anual
                        </option>
                        <option value="fechamento">
                            <i class="fas fa-calendar-check"></i>
                            Fechamento
                        </option>
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <div class="input-group" id="exercicio-group" style="display: none;">
                <label for="exercicio">
                    <i class="fas fa-calendar"></i>
                    Exercício (Ano)
                </label>
                <div class="input-wrapper">
                    <select id="exercicio" name="exercicio">
                        <option value="" disabled selected>Carregando...</option>
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <div class="input-group" id="mes-group" style="display: none;">
                <label for="mes">
                    <i class="fas fa-calendar-alt"></i>
                    Mês
                </label>
                <div class="input-wrapper">
                    <select id="mes" name="mes">
                        <option value="" disabled selected>Escolha um mês</option>
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                </div>
            </div>
<!--  editar para que apareça o nome do usuário logado -->
            <div class="input-group">
                <label for="usuario">
                    <i class="fas fa-user"></i>
                    Usuário Logado
                </label>
                <div class="input-wrapper">
                    <input type="text" id="usuario" name="usuario" value="" readonly>
                    <i class="fas fa-lock input-status"></i>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" id="incluir_quantidade" name="incluir_quantidade" onclick="gerarRelatorio()" class="btn-primary">
                <i class="fas fa-chart-bar"></i>
                Gerar Relatório
            </button>
        </div>
    </form>

    <div id="resultadoRelatorio" class="report-results" style="margin-top: 20px;"></div>

    <div class="report-actions" style="display: none;">
        <button id="imprimirBtn" onclick="imprimirTabela()" class="btn-secondary">
            <i class="fas fa-print"></i>
            Imprimir Tabela
        </button>
        
        <button id="exportarExcelBtn" onclick="exportarParaExcel()" class="btn-success">
            <i class="fas fa-file-excel"></i>
            Exportar Excel
        </button>
    </div>
</div>

<!-- Modais -->
<div class="modal" id="modal-detalhes">
    <div class="modal-overlay" onclick="fecharModal('modal-detalhes')"></div>
    <div class="modal-content modern">
        <div class="modal-header">
            <h3>
                <i class="fas fa-info-circle"></i>
                Detalhes do Produto
            </h3>
            <button class="modal-close" onclick="fecharModal('modal-detalhes')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="modal-informacoes">
                <!-- O conteúdo será carregado dinamicamente -->
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modal-atualizar">
    <div class="modal-overlay" onclick="fecharModal('modal-atualizar')"></div>
    <div class="modal-content modern">
        <div class="modal-header">
            <h3>
                <i class="fas fa-edit"></i>
                Atualizar Produto
            </h3>
            <button class="modal-close" onclick="fecharModal('modal-atualizar')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="modal-atualizacao">
                <!-- O conteúdo será carregado dinamicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Scripts mantidos intactos -->
<script>
    document.getElementById('btnExportPDF').addEventListener('click', function() {
        // Redireciona para o script que gera o PDF
        window.location.href = 'exportar_pdf_moimentacaoestoque.php';
    });
</script>

<script>
   document.getElementById('material-nome').addEventListener('change', function() {
    const nomeMaterialId = this.value; // Obtém o ID do material selecionado

    // Verifica se os elementos existem antes de tentar acessá-los
    const descricaoInput = document.getElementById('material-codigo');
    const classificacaoInput = document.getElementById('material-classificacao');
    const naturezaInput = document.getElementById('material-natureza1');
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
        fetch('./buscar_dados_produto.php?id=' + nomeMaterialId)
            .then(response => response.json())
            .then(data => {
                console.log("Resposta da API:", data); // Depuração

                if (data.success) {
                    setTimeout(() => {
                        descricaoInput.value = data.descricao || ''; // Correção aqui
                        classificacaoInput.value = data.classificacao || '';
                        naturezaInput.value = data.natureza || ''; // Preenchendo o campo "Natureza"
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

<script>
    // Adicionar evento de clique nos botões de excluir
    document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function() {
        // Pega o ID do produto a ser excluído
        const produtoId = this.getAttribute('data-id');

        // Perguntar ao usuário se ele realmente quer excluir
        if (confirm('Tem certeza que deseja excluir este item?')) {
            // Enviar uma requisição AJAX para excluir o produto
            fetch('./almoxarifado/excluir_produto.php', {
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
            const response = await fetch('./almoxarifado/buscar_exercicios.php');
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
 // Verificar sessão
    if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
        setMessageAndRedirect('error', 'Sessão inválida. Faça login.', 'index.php');
    }
    // Preencher o campo de usuário logado dinamicamente
    document.addEventListener("DOMContentLoaded", () => {
    const usuarioInput = document.getElementById("usuario");
    // O valor já está definido no HTML via PHP, mas pode-se garantir que não esteja vazio
    if (!usuarioInput.value) {
        usuarioInput.value = "Desconhecido";
    }
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
            const response = await fetch('./almoxarifado/gerar_relatorioestoque.php', {
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



<!-- JS CÁLCULO DE PREÇO MÉDIO -->
<script src="./src/estoque/js/calc-preco-medio.js"></script>
<!-- JS DE PAGINA E FILTRO DA TABELA-ESTOQUE -->
<script src="./src/estoque/js/paginacao-filtro.js"></script>
<script src="./src/estoque/js/escrevefechamento-linhadotempo.js"></script>

<!-- PREENCHIMENTO AUTOMÁTICO RETIRADA DE PRODUTO -->
 <script src="./src/estoque/js/preencher-produto-retiradacodigoantigo.js"></script>

<!--  JS PREENHE OS DETALHES DA LINHA SELECIONADA NO MODAL -->
<script src="./src/estoque/js/modal-estoque.js"></script>

<!-- JS ATIVAÇÃO DAS ABAS -->
<script src="./src/estoque/js/active-tab-estoque.js"></script>

<!-- Ícone de Loading -->
<div class="loading" style="display:none;"></div>

 <?php include 'footer.php'; ?>
</body>
</html>
