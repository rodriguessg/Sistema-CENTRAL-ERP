<?php
    include 'header.php';
?>
<!DOCTYPE html>
<html lang="Pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Almoxarifado</title>
     <link rel="stylesheet" href="src/style/modal.css">
    <link rel="stylesheet" href="src/estoque/style/estoque.css">
</head>
<body>

<div class="caderno">

<div class="tabs">
    <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">Cadastrar Materiais</div>
    <div class="tab" data-tab="retirar" onclick="showTab('consulta')">Consulta de produtos</div>
    <div class="tab" data-tab="levantamento" onclick="showTab('Estoque')">Estoque</div>
    <div class="tab" data-tab="DPRE" onclick="showTab('retirar')">Retirar material</div>
    <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">Relatorio</div>
    <div class="tab" data-tab="galeria" onclick="showTab('galeria')">Galeria</div>
</div>
<!-- Conteúdo das abas -->
<div class="form-container" id="cadastrar" style="display:none;">
    
    <h3>Cadastrar Produto</h3>
    <form id="form-cadastrar-produto" action="./include/estoque/cadastrar_produto.php" method="POST" enctype="multipart/form-data" style="display: ruby;">
        <div class="form-group3">
            <label for="produto">Produto:</label>
            <input type="text" id="produto" name="produto" placeholder="Digite o nome do produto" required>
            
            <label for="classificacao">Classificação:</label>
            <input type="text" id="classificacao" name="classificacao" placeholder="Digite a classificação" required>
        
       
            <label for="natureza">Natureza:</label>
            <input type="text" id="natureza" name="natureza" placeholder="Digite a natureza do produto" required>
            
            <label for="contabil">Contábil:</label>
            <input type="text" id="contabil" name="contabil" placeholder="Digite o código contábil" required>
  
        
            <label for="codigo">Código:</label>
            <input type="text" id="codigo" name="codigo" placeholder="Código do produto" required>
            
            <label for="unidade">Unidade:</label>
            <input type="text" id="unidade" name="unidade" placeholder="Unidade de medida" required>
       
      
            <label for="localizacao">Local:</label>
            <select id="localizacao" name="localizacao" required>
                <option value="" disabled selected>Selecione o local</option>
                <option value="xm1">XM1</option>
                <option value="xm2">XM2</option>
            </select>
            
            <label for="custo">Custo:</label>
            <input type="text" id="custo" name="custo" placeholder="Digite o custo" step="0.01" required>
      
      
            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" placeholder="Digite a quantidade" required>
        
            <label for="preco_medio">Preço Médio:</label>
            <input type="number" id="preco_medio" name="preco_medio" placeholder="Digite o preço médio" step="0.01" readonly>
 
      
            <label for="nf">Nota Fiscal:</label>

    
            <button type="submit">Cadastrar</button>
            <button type="button" id="limpar-formulario">Limpar</button>
        </div>
    </form>
</div>


<div class="form-container" id="consulta">

    <h2>Lista de Produtos</h2>

    <div class="search-container">
        <label for="filtroProduto">Pesquisar Produto:</label>
        <input type="text" id="filtroProduto" placeholder="Digite o nome do produto">
        <button class="btn-estoque2" id="filtrar">Filtrar</button>
        <button class="btn-estoque2" id="limpar">Limpar</button>
    </div>
    <!-- Tabela com botões "Detalhes" e "Atualizar" -->
    <div class="table-container">
        <table border="1">
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
    <form id="retirar-form" action="./include/estoque/retirar_materialestoque.php" method="POST">
        <div class="form-group3">
            <label for="material-nome">Nome do Material:</label>
            <input type="text" id="material-nome" name="material-nome" placeholder="Digite o nome do material" required>
            <label for="material-codigo">Código do Material:</label>
            <input type="text" id="material-codigo" name="material-codigo" placeholder=" preenchido automaticamente" readonly>
            <label for="material-classificacao">Classificação:</label>
            <input type="text" id="material-classificacao" name="material-classificacao" placeholder=" preenchido automaticamente" readonly>
            <label for="material-natureza">Natureza:</label>
            <input type="text" id="material-natureza" name="material-natureza" placeholder=" preenchido automaticamente" readonly>
            <label for="material-localizacao">Localização:</label>
            <input type="text" id="material-localizacao" name="material-localizacao" placeholder=" preenchido automaticamente" readonly>
            <label for="material-quantidade">Quantidade:</label>
            <input type="number" id="material-quantidade" name="material-quantidade" min="1" placeholder="Digite a quantidade a retirar" required>
            <button type="submit">Retirar</button>
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

    <!-- Campo de pesquisa -->
    <div class="search-container">
        <label for="filtroestoque">Pesquisar Produto:</label>
        <input type="text" id="filtroestoque" placeholder="Digite o nome do produto">
        <button class="btn-estoque2" onclick="filtrarTabela()">Filtrar</button>
        <button class="btn-estoque2" onclick="limparFiltro()">Limpar</button>
    </div>

    <div class="table-container2">
        <h3>Lista de Produtos</h3>
        <table class="tabela-estoque" border="1">
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
    <form id="form-relatorio" style="display: flex; flex-direction: column; gap: 15px;">
        <!-- Seletor de Período -->
        <div class="form-group">
            <label for="periodo">Selecione o Período:</label>
            <select id="periodo" name="periodo" required onchange="toggleExercicioSelector(this.value)">
                <option value="" disabled selected>Escolha uma opção</option>
                <option value="semanal">Semanal</option>
                <option value="mensal">Mensal</option>
                <option value="anual">Anual</option>
            </select>
        </div>

        <!-- Seletor de Exercício (Ano) -->
        <div class="form-group" id="exercicio-group" style="display: none;">
            <label for="exercicio">Selecione o Exercício:</label>
            <select id="exercicio" name="exercicio">
                <option value="" disabled selected>Carregando...</option>
            </select>
        </div>

        <!-- Campo de Usuário Logado -->
        <div class="form-group">
            <label for="usuario">Usuário Logado:</label>
            <input type="text" id="usuario" name="usuario" value="" readonly>
        </div>

        <!-- Botão de Submissão -->
        <div class="form-group">
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
            const response = await fetch('./include/estoque/buscar_exercicios.php');
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
            const response = await fetch('./include/estoque/gerar_relatorioestoque.php', {
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
<!-- JS DE PAGINA E FILTRO DA TABELA -->
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