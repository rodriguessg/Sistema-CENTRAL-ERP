<?php
    include 'header.php';
?>


<!DOCTYPE html>
<html lang="Pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almoxarifado</title>
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo.css">
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



</head>
<body>
    
<div class="caderno">

<div class="tabs">
    <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">
        <i class="fas fa-plus-circle"></i> Cadastrar Materiais
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
    <div class="tab" data-tab="galeria" onclick="showTab('galeria')">
        <i class="fas fa-image"></i> Galeria
    </div>
</div>




<div class="form-container" id="cadastrar" style="display:none;">
    <h3>Cadastrar Produto</h3>
    <form id="form-cadastrar-produto" action="cadastrar_produto.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <div class="input-group">
                <label for="produto">Produto:</label>
                <i class="fas fa-cogs"></i> <!-- Ícone de engrenagem -->
                <input type="text" id="produto" name="produto" placeholder="Digite o nome do produto" required>
            </div>
            
            <div class="input-group">
                <label for="classificacao">Classificação:</label>
                <i class="fas fa-tag"></i> <!-- Ícone de etiqueta -->
                <input type="text" id="classificacao" name="classificacao" placeholder="Digite a classificação" required>
            </div>
            
            <div class="input-group">
                <label for="natureza">Natureza:</label>
                <i class="fas fa-flask"></i> <!-- Ícone de frasco -->
                <input type="text" id="natureza" name="natureza" placeholder="Digite a natureza do produto" required>
            </div>
            
            <div class="input-group">
                <label for="contabil">Contábil:</label>
                <i class="fas fa-calculator"></i> <!-- Ícone de calculadora -->
                <input type="text" id="contabil" name="contabil" placeholder="Digite o código contábil" required>
            </div>

            <div class="input-group">
                <label for="codigo">Código:</label>
                <i class="fas fa-barcode"></i> <!-- Ícone de código de barras -->
                <input type="text" id="codigo" name="codigo" placeholder="Código do produto" required>
            </div>

            <div class="input-group">
                <label for="unidade">Unidade:</label>
                <i class="fas fa-box"></i> <!-- Ícone de caixa -->
                <input type="text" id="unidade" name="unidade" placeholder="Unidade de medida" required>
            </div>
        </div>
        
        <div class="form-group">
            <div class="input-group">
                <label for="localizacao">Local:</label>
                <i class="fas fa-map-marker-alt"></i> <!-- Ícone de local -->
                <select id="localizacao" name="localizacao" required>
                    <option value="" disabled selected>Selecione o local</option>
                    <option value="xm1">XM1</option>
                    <option value="xm2">XM2</option>
                </select>
            </div>
            
            <div class="input-group">
                <label for="custo">Custo:</label>
                <i class="fas fa-dollar-sign"></i> <!-- Ícone de símbolo de dólar -->
                <input type="text" id="custo" name="custo" placeholder="Digite o custo" step="0.01" required>
            </div>
            
            <div class="input-group">
                <label for="quantidade">Quantidade:</label>
                <i class="fas fa-cogs"></i> <!-- Ícone de engrenagem -->
                <input type="number" id="quantidade" name="quantidade" placeholder="Digite a quantidade" required>
            </div>
            
            <div class="input-group">
                <label for="preco_medio">Preço Médio:</label>
                <i class="fas fa-money-bill-wave"></i> <!-- Ícone de dinheiro -->
                <input type="number" id="preco_medio" name="preco_medio" placeholder="Digite o preço médio" step="0.01" readonly>
            </div>

            <div class="input-group">
                <label for="nf">Nota Fiscal:</label>
                <i class="fas fa-file-invoice"></i> <!-- Ícone de nota fiscal -->
                <input type="number" id="nf" name="nf" placeholder="Digite o Codigo" required>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" class="btn-submit">Cadastrar</button>
            <button type="button" id="limpar-formulario" class="btn-clear">Limpar</button>
        </div>

    </form>
</div>





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
                    <th>Produto</th>
                    <th>Classificação</th>
                    <th>Local</th>
                    <th>Código</th>
                    <th>Natureza</th>
                    <th>Quantidade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaProdutos"></tbody>
        </table>
        <div class="pagination"></div>
    </div>
</div>


<div class="form-container" id="retirar">
    <h3>Retirar Material do Estoque</h3>
    <form id="retirar-form" action="retirar_materialestoque.php" method="POST">
    <div class="form-group3">
  <div class="input-group">
    <label for="material-nome">Nome do Material:</label>
    <i class="fas fa-cogs"></i> <!-- Ícone ao lado do campo -->
    <input type="text" id="material-nome" name="material-nome" placeholder="Digite o nome do material" required>
  </div>

  <div class="input-group">
    <label for="material-codigo">Código do Material:</label>
    <i class="fas fa-barcode"></i> <!-- Ícone ao lado do campo -->
    <input type="text" id="material-codigo" name="material-codigo" placeholder="preenchido automaticamente" readonly>
  </div>

  <div class="input-group">
    <label for="material-classificacao">Classificação:</label>
    <i class="fas fa-tags"></i> <!-- Ícone ao lado do campo -->
    <input type="text" id="material-classificacao" name="material-classificacao" placeholder="preenchido automaticamente" readonly>
  </div>

  <div class="input-group">
    <label for="material-natureza">Natureza:</label>
    <i class="fas fa-flask"></i> <!-- Ícone ao lado do campo -->
    <input type="text" id="material-natureza" name="material-natureza" placeholder="preenchido automaticamente" readonly>
  </div>

  <div class="input-group">
    <label for="material-localizacao">Localização:</label>
    <i class="fas fa-map-marker-alt"></i> <!-- Ícone ao lado do campo -->
    <input type="text" id="material-localizacao" name="material-localizacao" placeholder="preenchido automaticamente" readonly>
  </div>

  <div class="input-group">
    <label for="material-quantidade">Quantidade:</label>
    <i class="fas fa-cogs"></i> <!-- Ícone ao lado do campo -->
    <input type="number" id="material-quantidade" name="material-quantidade" min="1" placeholder="Digite a quantidade a retirar" required>
  </div>

    <div  class="button-group" >
    <button class="btn-submit" type="submit">Retirar</button>
    </div>
</div>

    </form>
    <div id="mensagem" style="color: red; margin-top: 10px;"></div>
</div>


<!-- Modal para Detalhes -->
<div class="modal" id="modal-detalhes">
    <div class="modal-content">
        <span class="modal-close" onclick="fecharModal('modal-detalhes')">&times;</span>
        <div id="modal-informacoes">
            <!-- O conteúdo será carregado dinamicamente -->
        </div>
    </div>
</div>

<!-- Modal para Atualização -->
<div class="modal" id="modal-atualizar">
    <div class="modal-content">
        <span class="modal-close" onclick="fecharModal('modal-atualizar')">&times;</span>
        <div id="modal-atualizacao">
            <!-- O conteúdo será carregado dinamicamente -->
        </div>
    </div>
</div>


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
                    <th>Quantidade</th>
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
                                    <td>{$row['quantidade']}</td>
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
        </select>
    </div>

    <!-- Seletor de Exercício (Ano) -->
    <label for="exercicio">Selecione o Exercício:</label>
    <div class="relatorio-group" id="exercicio-group" style="display: none;">
        <select id="exercicio" name="exercicio">
            <option value="" disabled selected>Carregando...</option>
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

</div>
<!-- AO RETIRAR ESTE SCRIPT APRESEMTA ERRO NO PREENCHIMENTO DO NOME DO USUÁRIO NO RELATÓRIO -->
<script>
    // Exibir o seletor de exercício apenas se a opção anual for selecionada
    function toggleExercicioSelector(periodo) {
        const exercicioGroup = document.getElementById('exercicio-group');
        if (periodo === 'anual') {
            exercicioGroup.style.display = 'block';
            fetchExercicios(); // Carregar exercícios via AJAX
        } else {
            exercicioGroup.style.display = 'none';
        }
    }

    // Função para carregar os exercícios disponíveis
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
        const incluirQuantidade = document.getElementById('incluir_quantidade').checked;
        const usuario = document.getElementById('usuario').value;

        if (!periodo) {
            alert('Por favor, selecione o período.');
            return;
        }

        if (periodo === 'anual' && !exercicio) {
            alert('Por favor, selecione um exercício para o relatório anual.');
            return;
        }

        try {
            const response = await fetch('gerar_relatorioestoque.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    periodo,
                    exercicio,
                    incluir_quantidade: incluirQuantidade ? '1' : '',
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

<!-- PREENCHIMENTO AUTOMÁTICO RETIRADA DE PRODUTO -->
<script src="./src/estoque/js/preencher-produto-retirada.js"></script>
<!--  JS PREENHE OS DETALHES DA LINHA SELECIONADA NO MODAL -->
<script src="./src/estoque/js/modal-estoque.js"></script>

<!-- JS ATIVAÇÃO DAS ABAS -->
<script src="./src/estoque/js/active-tab-estoque.js"></script>


</body>
</html>

<!-- <?php include 'footer.php'; ?> -->