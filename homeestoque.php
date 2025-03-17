<?php
    include 'header.php';
?>

<!DOCTYPE html>
<html lang="Pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque</title>
    <!-- <link rel="stylesheet" href="src/style/style.css"> -->
    <link rel="stylesheet" href="src/style/estoque.css">
    
<style>
  
</style>

</head>
<body>
    <h1>Gerencimento de Almoxarifado</h1>

<div class="tabs">
    <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">Cadastrar Materiais</div>
    <div class="tab" data-tab="retirar" onclick="showTab('consulta')">Consulta de produtos</div>
    <div class="tab" data-tab="levantamento" onclick="showTab('Estoque')">Estoque</div>
    <div class="tab" data-tab="DPRE" onclick="showTab('retirar')">Retirar material</div>
    <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">Relatorio</div>
    <div class="tab" data-tab="galeria" onclick="showTab('galeria')">Galeria</div>
</div>


<!-- Conteúdo das abas -->
<div class="form-container3" id="cadastrar" style="display:none;">
    <h3>Cadastrar Produto</h3>
    <form id="form-cadastrar-produto" action="cadastrar_produto.php" method="POST" enctype="multipart/form-data" style="display: ruby;">
        <div class="form-group3">
            <label for="produto">Produto:</label>
            <input type="text" id="produto" name="produto" placeholder="Digite o nome do produto" required>
            
            <label for="classificacao">Classificação:</label>
            <input type="text" id="classificacao" name="classificacao" placeholder="Digite a classificação" required>
        </div>
        <div class="form-group3">
            <label for="natureza">Natureza:</label>
            <input type="text" id="natureza" name="natureza" placeholder="Digite a natureza do produto" required>
            
            <label for="contabil">Contábil:</label>
            <input type="text" id="contabil" name="contabil" placeholder="Digite o código contábil" required>
        </div>  
        <div class="form-group3">
            <label for="codigo">Código:</label>
            <input type="text" id="codigo" name="codigo" placeholder="Código do produto" required>
            
            <label for="unidade">Unidade:</label>
            <input type="text" id="unidade" name="unidade" placeholder="Unidade de medida" required>
        </div>
        <div class="form-group3">
            <label for="localizacao">Local:</label>
            <select id="localizacao" name="localizacao" required>
                <option value="" disabled selected>Selecione o local</option>
                <option value="xm1">XM1</option>
                <option value="xm2">XM2</option>
            </select>
            
            <label for="custo">Custo:</label>
            <input type="text" id="custo" name="custo" placeholder="Digite o custo" step="0.01" required>
        </div>
        <div class="form-group3">
            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" placeholder="Digite a quantidade" required>
        
            <label for="preco_medio">Preço Médio:</label>
            <input type="number" id="preco_medio" name="preco_medio" placeholder="Digite o preço médio" step="0.01" readonly>
        </div>
        <div class="form-group3">
            <label for="nf">Nota Fiscal:</label>
            <input type="text" id="nf" name="nf" placeholder="Digite a Nota Fiscal">
        </div>
        <div class="form-group3">
            <button type="submit">Cadastrar</button>
            <button type="button" id="limpar-formulario">Limpar</button>
        </div>
    </form>
</div>

<script>
// Função para calcular o preço médio
        function calcularPrecoMedio() {
            const custoInput = document.getElementById('custo');
            const quantidadeInput = document.getElementById('quantidade');
            const precoMedioInput = document.getElementById('preco_medio');

            // Função para converter o formato brasileiro (0.00,00) para número
            function converterParaNumero(valor) {
                if (!valor) return 0; // Retorna 0 se o valor for vazio ou inválido
                return parseFloat(valor.replace(/\./g, '').replace(',')) || 0;
            }

            // Obtém os valores de custo e quantidade no formato correto
            const custo = converterParaNumero(custoInput.value);
            const quantidade = parseFloat(quantidadeInput.value) || 0;

            // Valida os valores
            if (custo < 0 || quantidade < 0) {
                precoMedioInput.value = '0'; // Define o valor padrão
                return;
            }

            // Calcula o preço médio apenas se a quantidade for maior que zero
            const precoMedio = quantidade > 0 ? (custo / quantidade).toFixed(2) : '0';

            // Atualiza o campo de preço médio
            precoMedioInput.value = precoMedio;
        }

        // Adiciona eventos aos campos de custo e quantidade
        document.getElementById('custo').addEventListener('input', calcularPrecoMedio);
        document.getElementById('quantidade').addEventListener('input', calcularPrecoMedio);


    // Função para limpar o formulário
    function limparFormulario() {
        const form = document.getElementById('form-cadastrar-produto');
        form.reset(); // Reseta todos os campos do formulário
        document.getElementById('preco_medio').value = ''; // Reseta o campo de preço médio
    }

    // Evento no botão de limpar
    document.getElementById('limpar-formulario').addEventListener('click', limparFormulario);
</script>

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
<script>
// Variáveis globais
        let paginaAtual = 1;
        const itensPorPagina = 7;

        // Função para carregar os dados da tabela
        function carregarTabela(pagina, filtro = "") {
            fetch(`paginasTabelaestoque.php?pagina=${pagina}&itensPorPagina=${itensPorPagina}&filtro=${filtro}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.dados) {
                        preencherTabela(data.dados);
                        criarPaginacao(data.total_paginas);
                    } else {
                        console.error("Estrutura de dados inesperada:", data);
                    }
                })
                .catch(error => console.error("Erro ao carregar dados:", error));
        }

        // Função para preencher a tabela
        function preencherTabela(dados) {
            const tbody = document.getElementById("tabelaProdutos");
            tbody.innerHTML = ""; // Limpar a tabela

            if (dados.length === 0) {
                const row = document.createElement("tr");
                row.innerHTML = `<td colspan="8">Nenhum produto encontrado.</td>`;
                tbody.appendChild(row);
                return;
            }

            dados.forEach(dado => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${dado.id}</td>
                    <td>${dado.produto}</td>
                    <td>${dado.classificacao}</td>
                    <td>${dado.localizacao}</td>
                    <td>${dado.codigo}</td>
                    <td>${dado.natureza}</td>
                    <td>${dado.quantidade}</td>
                    <td class="actions">
                        <button class="btn-estoque1" onclick="abrirModalDetalhes('${dado.id}')">+ Detalhes</button>
                        <button class="btn-estoque" onclick="abrirModalAtualizar('${dado.id}')">Atualizar</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Função para criar a paginação com botões "<<" e ">>"
        function criarPaginacao(totalPaginas) {
            const paginacaoContainer = document.querySelector(".pagination");
            paginacaoContainer.innerHTML = ""; // Limpar os botões de paginação

            const maxBotoes = 5;
            let inicio = Math.max(1, paginaAtual - Math.floor(maxBotoes / 2));
            let fim = Math.min(totalPaginas, inicio + maxBotoes - 1);

            if (fim - inicio + 1 < maxBotoes) {
                inicio = Math.max(1, fim - maxBotoes + 1);
            }

            if (inicio > 1) {
                const primeiro = document.createElement("button");
                primeiro.textContent = "<<";
                primeiro.addEventListener("click", () => {
                    paginaAtual = 1;
                    carregarTabela(paginaAtual, document.getElementById("filtroProduto").value);
                });
                paginacaoContainer.appendChild(primeiro);
            }

            for (let i = inicio; i <= fim; i++) {
                const button = document.createElement("button");
                button.textContent = i;
                button.className = i === paginaAtual ? "active" : "";
                button.addEventListener("click", () => {
                    paginaAtual = i;
                    carregarTabela(paginaAtual, document.getElementById("filtroProduto").value);
                });
                paginacaoContainer.appendChild(button);
            }

            if (fim < totalPaginas) {
                const ultimo = document.createElement("button");
                ultimo.textContent = ">>";
                ultimo.addEventListener("click", () => {
                    paginaAtual = fim + 1;
                    carregarTabela(paginaAtual, document.getElementById("filtroProduto").value);
                });
                paginacaoContainer.appendChild(ultimo);
            }
        }

        // Eventos de filtro e limpeza
        document.getElementById("filtrar").addEventListener("click", () => {
            carregarTabela(1, document.getElementById("filtroProduto").value);
        });

        document.getElementById("limpar").addEventListener("click", () => {
            document.getElementById("filtroProduto").value = "";
            carregarTabela(1);
        });

        // Carregar tabela na inicialização
        window.addEventListener("load", () => carregarTabela(paginaAtual));
</script>


</div><div class="form-container3" id="retirar">
    <h3>Retirar Material do Estoque</h3>
    <form id="retirar-form" action="retirar_materialestoque.php" method="POST">
        <div class="form-group3">
            <label for="material-nome">Nome do Material:</label>
            <input type="text" id="material-nome" name="material-nome" placeholder="Digite o nome do material" required>

            <label for="material-codigo">Código do Material:</label>
            <input type="text" id="material-codigo" name="material-codigo" placeholder=" preenchido automaticamente" readonly>
        </div>

        <div class="form-group3">
            <label for="material-classificacao">Classificação:</label>
            <input type="text" id="material-classificacao" name="material-classificacao" placeholder=" preenchido automaticamente" readonly>

            <label for="material-natureza">Natureza:</label>
            <input type="text" id="material-natureza" name="material-natureza" placeholder=" preenchido automaticamente" readonly>
        </div>

        <div class="form-group3">
            <label for="material-localizacao">Localização:</label>
            <input type="text" id="material-localizacao" name="material-localizacao" placeholder=" preenchido automaticamente" readonly>

            <label for="material-quantidade">Quantidade:</label>
            <input type="number" id="material-quantidade" name="material-quantidade" min="1" placeholder="Digite a quantidade a retirar" required>
        </div>

        <div class="form-group3">
            <button type="submit">Retirar</button>
        </div>
    </form>
    <div id="mensagem" style="color: red; margin-top: 10px;"></div>
</div>

<script>
    const nomeInput = document.getElementById('material-nome');
    const codigoInput = document.getElementById('material-codigo');
    const classificacaoInput = document.getElementById('material-classificacao');
    const naturezaInput = document.getElementById('material-natureza');
    const localizacaoInput = document.getElementById('material-localizacao');
    const quantidadeInput = document.getElementById('material-quantidade');
    const mensagemDiv = document.getElementById('mensagem');

    // Função para buscar material e preencher os campos automaticamente
    nomeInput.addEventListener('input', () => {
        const nomeMaterial = nomeInput.value.trim();

        if (nomeMaterial.length > 0) {
            fetch(`buscar_codigo.php?nome=${encodeURIComponent(nomeMaterial)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Preenche os campos com as informações do produto
                        codigoInput.value = data.codigo || "Não encontrado";
                        classificacaoInput.value = data.classificacao || "Não encontrado";
                        naturezaInput.value = data.natureza || "Não encontrado";
                        localizacaoInput.value = data.localizacao || "Não encontrado";
                        mensagemDiv.innerText = "";  // Limpa a mensagem de erro
                    } else {
                        // Caso o produto não seja encontrado
                        codigoInput.value = "";
                        classificacaoInput.value = "";
                        naturezaInput.value = "";
                        localizacaoInput.value = "";
                        mensagemDiv.innerText = "Material não encontrado.";
                    }
                })
                .catch(err => {
                    console.error('Erro ao buscar os dados:', err);
                    mensagemDiv.innerText = "Erro na busca. Tente novamente.";
                });
        } else {
            // Limpa os campos se o nome do material for apagado
            codigoInput.value = "";
            classificacaoInput.value = "";
            naturezaInput.value = "";
            localizacaoInput.value = "";
            mensagemDiv.innerText = "";
        }
    });

    // Função para validação do formulário
    document.getElementById('retirar-form').addEventListener('submit', function(event) {
        // Verifica se todos os campos estão preenchidos
        if (!codigoInput.value || !classificacaoInput.value || !naturezaInput.value || !localizacaoInput.value) {
            event.preventDefault();  // Impede o envio do formulário
            mensagemDiv.innerText = "Preencha corretamente todos os campos antes de continuar.";
        } else if (quantidadeInput.value <= 0) {
            event.preventDefault();  // Impede o envio do formulário
            mensagemDiv.innerText = "A quantidade a ser retirada deve ser maior que 0.";
        }
    });
</script>



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

<script>
    // Função para abrir o modal e carregar os detalhes
    function abrirModalDetalhes(id) {
        const linha = [...document.querySelectorAll('#tabelaProdutos tr')].find(tr => tr.children[0].textContent == id);
        if (!linha) return;

        const dados = {
            id: linha.children[0].textContent,
            produto: linha.children[1].textContent,
            classificacao: linha.children[2].textContent,
            localizacao: linha.children[3].textContent,
            codigo: linha.children[4].textContent,
            natureza: linha.children[5].textContent,
            quantidade: linha.children[6].textContent,
        };

        const modalConteudo = document.getElementById('modal-informacoes');
        modalConteudo.innerHTML = `
            <h3>Detalhes do Produto</h3>
            <p><strong>ID:</strong> ${dados.id}</p>
            <p><strong>Produto:</strong> ${dados.produto}</p>
            <p><strong>Classificação:</strong> ${dados.classificacao}</p>
            <p><strong>Localização:</strong> ${dados.localizacao}</p>
            <p><strong>Código:</strong> ${dados.codigo}</p>
            <p><strong>Natureza:</strong> ${dados.natureza}</p>
            <p><strong>Quantidade:</strong> ${dados.quantidade}</p>
        `;

        document.getElementById('modal-detalhes').style.display = 'block';
    }

    // Função para abrir o modal de atualização
    function abrirModalAtualizar(id) {
        const linha = [...document.querySelectorAll('#tabelaProdutos tr')].find(tr => tr.children[0].textContent == id);
        if (!linha) return;

        const dados = {
            id: linha.children[0].textContent,
            produto: linha.children[1].textContent,
            classificacao: linha.children[2].textContent,
            localizacao: linha.children[3].textContent,
            codigo: linha.children[4].textContent,
            natureza: linha.children[5].textContent,
            quantidade: linha.children[6].textContent,
        };

        const modalConteudo = document.getElementById('modal-atualizacao');
        modalConteudo.innerHTML = `
            <h3>Atualizar Produto</h3>
            <form id="formAtualizar">
                <input type="hidden" name="id" value="${dados.id}">
                <p><label>Produto:</label><input type="text" name="produto" value="${dados.produto}" readonly></p>
                <p><label>Classificação:</label><input type="text" name="classificacao" value="${dados.classificacao}"readonly></p>
                <p><label>Localização:</label><input type="text" name="localizacao" value="${dados.localizacao}"readonly></p>
                <p><label>Código:</label><input type="text" name="codigo" value="${dados.codigo} " readonly></p>
                <p><label>Natureza:</label><input type="text" name="natureza" value="${dados.natureza}" readonly></p>
                <p><label>Quantidade:</label><input type="number" name="quantidade" value="${dados.quantidade}"></p>
                <button type="button" onclick="salvarAlteracoes()">Salvar Alterações</button>
            </form>
        `
        document.getElementById('modal-atualizar').style.display = 'block';
    }

    // Função para salvar alterações
    function salvarAlteracoes() {
        const form = document.getElementById('formAtualizar');
        const dadosAtualizados = new FormData(form);

        // Enviar os dados para o backend via fetch ou outra requisição AJAX
        fetch('atualizar_produto.php', {
            method: 'POST',
            body: dadosAtualizados
        })
        .then(response => response.text())
        .then(result => {
            alert('Produto atualizado com sucesso!');
            fecharModal('modal-atualizar');
            location.reload(); // Recarrega a página para atualizar a tabela
        })
        .catch(error => alert('Erro ao atualizar produto.'));
    }

    // Função para fechar o modal
    function fecharModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
</script>


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
                    $conn = new mysqli('localhost', 'root', '', 'supat');
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

<script>
    // Função para filtrar a tabela com base no valor do input
    function filtrarTabela() {
        const filtro = document.getElementById('filtroestoque').value.toLowerCase().trim();
        const linhas = document.querySelectorAll('#tabelaestoque tr');

        linhas.forEach(linha => {
            const produto = linha.cells[1]?.textContent.toLowerCase().trim() || '';
            // Exibe a linha se o produto contém o filtro, ou oculta se não contém
            linha.style.display = produto.includes(filtro) ? '' : 'none';
        });
    }

    // Função para limpar o campo de input
    function limparFiltro() {
        document.getElementById('filtroestoque').value = ''; // Limpar o campo de texto
        filtrarTabela(); // Reaplicar o filtro
    }

    // Função para limitar a tabela a 7 linhas e adicionar scroll
    window.onload = function() {
        const tabelaBody = document.getElementById('tabelaestoque');
        const linhas = tabelaBody.getElementsByTagName('tr');

        // Limitar a 7 linhas
        const limite = 7;
        for (let i = 0; i < linhas.length; i++) {
            if (i >= limite) {
                linhas[i].style.display = 'none';
            }
        }

        // Adicionar barra de rolagem
        tabelaBody.style.maxHeight = '300px';
        tabelaBody.style.overflowY = 'auto';
    };
</script>

<style>
    /* Estilos para a tabela e a barra de rolagem */
    .table-container2 {
        max-width: 100%;
        height: 800px;
        overflow-x: auto;
    }

    .tabela-estoque {
        width: 100%;
        border-collapse: collapse;
    }

    .tabela-estoque th, .tabela-estoque td {
        padding: 8px;
        text-align: left;
        border: 1px solid #ddd;
    }

    /* Estilos da barra de rolagem */
    #tabelaestoque {
        max-height: 300px; /* Limita a altura da tabela */
        overflow-y: auto; /* Adiciona rolagem vertical */
    }
</style>


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






<script src="./src/js/active.js">
    // Função para alternar entre as abas
    function showTab(tabName) {
    // Esconder todas as abas do tipo form-container e form-container2
    const tabs = document.querySelectorAll('.form-container, .form-container3');
    tabs.forEach(tab => tab.style.display = 'none');

    // Exibir a aba selecionada (form-container ou form-container2)
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = 'block';
    }

    // Atualizar o estilo das abas para mostrar qual está ativa
    const tabLinks = document.querySelectorAll('.tab');
    tabLinks.forEach(tab => tab.classList.remove('active'));
    const activeTabLink = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeTabLink) {
        activeTabLink.classList.add('active');
    }
    }


    // Mostrar a aba 'cadastrar' como padrão quando a página for carregada
    window.onload = function() {
        showTab('cadastrar');
    };



</script>
</body>
</html>

<?php include 'footer.php'; ?>