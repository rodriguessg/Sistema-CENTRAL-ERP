<?php
// Incluir o arquivo de conexão com o banco de dados
include 'banco.php';


// if(empty($_COOKIE['admin'])){ 
//     header("Location:index.php"); 
// }
// if(isset($_COOKIE['usuario'])){
//     header("Location:homeusuario.php");
// }
// if(isset($_COOKIE['tecnico'])){
//     header("Location:hometech.php");
// }
// Definir o código gerado e a categoria selecionada
$novoCodigo = "";
$categoriaSelecionada = "";

// Código PHP para gerar o código automaticamente
// Se necessário, implemente a lógica de geração de código baseada na categoria selecionada
// Aqui está apenas um exemplo de como gerar um código fictício.
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Patrimônio</title>
    <link rel="stylesheet" href="./src/style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include 'header.php'; ?>

<!-- Menu das abas -->
<div class="tabs">
    <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">Cadastrar BP</div>
    <div class="tab" data-tab="retirar" onclick="showTab('retirar')">Movimentação BP</div>
    <div class="tab" data-tab="levantamento" onclick="showTab('levantamento')">Levantamento de Bens</div>
    <div class="tab" data-tab="DPRE" onclick="showTab('DPRE')">DPRE</div>
    <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">Relatorio</div>
</div>

<!-- Conteúdo das abas -->
<div class="form-container" id="cadastrar" >
    <h3>Cadastrar Patrimônio</h3>
    <form action="cadastrar_patrimonio.php" method="POST">
        <!-- Checkbox para Categoria -->
        <div class="form-group">
            <label for="categoria">Categoria do Patrimônio:</label>
            <select id="categoria" name="categoria" required onchange="gerarCodigo(this.value)">
                <option value="selecione">Selecione a categoria</option>
                <option value="equipamentos_informatica">Equipamentos de Informática</option>
                <option value="bens_achados">Bens Achados</option>
                <option value="moveis_utensilios">Móveis e Utensílios</option>
                <option value="reserva_bens_moveis">Reserva de Bens Móveis</option>
            </select>

            <label for="codigo">Código do Patrimônio:</label>
            <input type="text" id="codigo" name="codigo" readonly value="<?php echo $novoCodigo; ?>">

            <label for="nome">Nome do Patrimônio:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" required></textarea>

            <label for="valor">Valor:</label>
            <input type="number" id="valor" name="valor" step="0.01" required>

            <label for="data_aquisicao">Data de Aquisição:</label>
            <input type="date" id="data_aquisicao" name="data_aquisicao" required>

            <label for="status">Status:</label>
            <select id="status" name="situacao" required>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
                <option value="em uso">Em Uso</option>
            </select>

            <label for="localizacao">Localização:</label>
            <input type="text" id="localizacao" name="localizacao" required>

            <button type="submit">Cadastrar</button>
        </div>
        </form>
</div>

<?php
// Conexão com o banco de dados
include('banco.php');

// Obter os dados reais da tabela 'patrimonio'
$query = "SELECT id, codigo FROM patrimonio WHERE situacao = 'ativo'";
$result = $con->query($query);
?>
<!-- Formulário para retirar materiais -->
<div class="form-container" id="retirar" style="display: none;">
  <h2>Remessa de Patrimônio</h2>
 

 <form action="registrar_remessa.php" method="POST">

 <label for="patrimonio_id">ID do Patrimônio:</label>
    <select name="patrimonio_id" id="patrimonio_id" required>
    <option value="" disabled selected>Selecione o patrimônio</option>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['codigo'] . "</option>";
        }
    } else {
        echo "<option value=''>Nenhum patrimônio disponível</option>";
    }
    ?>
 </select>


    
    <label for="destino">Destino (Área):</label>
    <select id="destino" name="destino" required>
        <option value="" disabled selected>Escolha a Área</option>
        <option value="COMEL">COMEL</option>
        <option value="CONFIS">CONFIS</option>
        <option value="CONDAM">CONDAM</option>
        <option value="COMAUD">COMAUD</option>
        <option value="AUD">AUD</option>
        <option value="DIREXE">DIREXE</option>
        <option value="PRESIDENCIA">PRESIDÊNCIA</option>
        <option value="DIRPLA">DIRPLA</option>
        <option value="DIRAF">DIRAF</option>
        <option value="DIREO">DIREO</option>
        <option value="CEHAB">CHEGAB</option>
        <option value="ASSGER">ASSGER</option>
        <option value="ASSCON">ASSCON</option>
        <option value="OUVI">OUVI</option>
        <option value="ASSTAD">ASSTAD</option>
        <option value="ASSJUR">ASSJUR</option>
        <option value="ASSPRIN">ASSPRIN</option>
        <option value="GERCOM">GERCOM</option>
        <option value="SUPLAN">SUPLAN</option>
        <option value="GERPLA">GERPLA</option>
        <option value="GERORT">GERORT</option>
        <option value="SUPTIN">SUPTIN</option>
        <option value="GERTIN">GERTIN</option>
        <option value="SUPDAM">SUPDAM</option>
        <option value="GERADM">GERADM</option>
        <option value="GERLIC">GERLIC</option>
        <option value="SUPGEP">SUPGEP</option>
        <option value="GERGEP">GERGEP</option>
        <option value="GERMST">GERMST</option>
        <option value="SUPFIC">SUPFIC</option>
        <option value="GERFIN">GERFIN</option>
        <option value="GERCOT">GERCOT</option>
        <option value="SUPAT">SUPAT</option>
        <option value="GERADP">GERADP</option>
        <option value="GERFIP">GERFIP</option>
        <option value="SUPMRS">SUPMRS</option>
        <option value="GERSIS">GERSIS</option>
        <option value="GERMAR">GERMAR</option>
        <option value="SUPVIP">SUPVIP</option>
        <option value="GERVIP">GERVIP</option>
        <option value="GERMAP">GERMAP</option>
        <option value="SUPTRA">SUPTRA</option>
        <option value="GERMAT">GERMAT</option>
        <option value="GEROPT">GEROPT</option>
        <option value="ASSPRE">ASSPRE</option>
    </select>
    <div class="form-group">
    <label for="responsavel">Responsável pela Transferência:</label>
    <input type="text" name="responsavel" id="responsavel" required>
    </div>
    <div class="form-group">
    <label for="data_transferencia">Data da Transferência:</label>
    <input type="date" name="data_transferencia" id="data_transferencia" required>
    </div>
    <div class="form-group">
    <label for="motivo">Motivo da Transferência:</label>
    <textarea name="motivo" id="motivo" required></textarea>
    </div>
    <button type="submit">Transferir</button>
</form>

</div>
<div class="form-container" id="relatorio">
    <form action="gerar_relatorio.php" method="GET" target="_blank">
        <h3>Emitir Relatório de Patrimônios</h3>

        <!-- Filtro por status -->
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="" selected>Todos</option>
            <option value="disponível">Disponível</option>
            <option value="transferido">Transferido</option>
            <option value="em manutenção">Em Manutenção</option>
            <option value="inativo">Inativo</option>
        </select>

        <!-- Filtro por destino/área -->
        <label for="destino">Destino (Área):</label>
        <select id="destino" name="destino">
            <option value="" selected>Todos</option>
            <option value="COMEL">COMEL</option>
            <option value="CONFIS">CONFIS</option>
            <option value="CONDAM">CONDAM</option>
            <option value="COMAUD">COMAUD</option>
            <option value="AUD">AUD</option>
            <option value="DIREXE">DIREXE</option>
            <option value="PRESIDENCIA">PRESIDÊNCIA</option>
            <option value="DIRPLA">DIRPLA</option>
            <option value="DIRAF">DIRAF</option>
            <option value="DIREO">DIREO</option>
            <option value="CEHAB">CHEGAB</option>
            <option value="ASSGER">ASSGER</option>
            <option value="ASSCON">ASSCON</option>
            <option value="OUVI">OUVI</option>
            <option value="ASSTAD">ASSTAD</option>
            <option value="ASSJUR">ASSJUR</option>
            <option value="ASSPRIN">ASSPRIN</option>
            <option value="GERCOM">GERCOM</option>
            <option value="SUPLAN">SUPLAN</option>
            <option value="GERPLA">GERPLA</option>
            <option value="GERORT">GERORT</option>
            <option value="SUPTIN">SUPTIN</option>
            <option value="GERTIN">GERTIN</option>
            <option value="SUPDAM">SUPDAM</option>
            <option value="GERADM">GERADM</option>
            <option value="GERLIC">GERLIC</option>
            <option value="SUPGEP">SUPGEP</option>
            <option value="GERGEP">GERGEP</option>
            <option value="GERMST">GERMST</option>
            <option value="SUPFIC">SUPFIC</option>
            <option value="GERFIN">GERFIN</option>
            <option value="GERCOT">GERCOT</option>
            <option value="SUPAT">SUPAT</option>
            <option value="GERADP">GERADP</option>
            <option value="GERFIP">GERFIP</option>
            <option value="SUPMRS">SUPMRS</option>
            <option value="GERSIS">GERSIS</option>
            <option value="GERMAR">GERMAR</option>
            <option value="SUPVIP">SUPVIP</option>
            <option value="GERVIP">GERVIP</option>
            <option value="GERMAP">GERMAP</option>
            <option value="SUPTRA">SUPTRA</option>
            <option value="GERMAT">GERMAT</option>
            <option value="GEROPT">GEROPT</option>
            <option value="ASSPRE">ASSPRE</option>
        </select>

        <!-- Filtro por intervalo de datas -->
        <label for="data_inicio">Data Início:</label>
        <input type="date" name="data_inicio" id="data_inicio">

        <label for="data_fim">Data Fim:</label>
        <input type="date" name="data_fim" id="data_fim">

        <button type="submit">Gerar Relatório</button>
    </form>
</div>


<!-- Formulário para levantamento de bens -->
<div class="form-container" id="levantamento">
    <h3>Levantamento de Bens</h3>
    
    <!-- Filtros -->
    <div class="filters">
        <div>
            <label for="filtro-identificacao">Filtrar por Identificação</label>
            <input type="text" id="filtro-identificacao" name="filtro-identificacao" placeholder="Digite a identificação">
        </div>
        <div>
            <label for="filtro-situacao">Filtrar por Situação</label>
            <select id="filtro-situacao" name="filtro-situacao">
                <option value="">Selecione</option>
                <option value="ativo">Ativo</option>
                <option value="processo_baixa">Processo de Baixa</option>
                <option value="morto">Morto</option>
            </select>
        </div>
        <div>
            <label for="filtro-operacao">Filtrar por Operação</label>
            <input type="text" id="filtro-operacao" name="filtro-operacao" placeholder="Digite a operação">
        </div>
        <button onclick="filtrarTabela()">Filtrar</button>
    </div>
    
    <!-- Tabela de levantamento -->
    <div class="table-container">
        <table id="tabela-levantamento">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Localização</th>
                    <th>Situação</th>
                    <th>Cadastrado Por</th>
                    <th>Categoria</th>
                    <th>Código</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dados carregados dinamicamente -->
            </tbody>
        </table>
    </div>

    <!-- Modal de Detalhes -->
<div class="modal" id="modal-detalhes">
    <div class="modal-content">
        <span class="modal-close" onclick="fecharModal()">&times;</span>
        <div id="modal-informacoes">
            <!-- As informações do patrimônio serão carregadas aqui -->
        </div>
        <button id="imprimir-btn" onclick="imprimirDetalhes()">Imprimir</button>
    </div>
</div>


    <!-- Paginação -->
    <div class="pagination"></div>
</div>

<div class="form-container" id="DPRE">
    <h2>Calcular Depreciação do Patrimônio</h2>

    <!-- Formulário para inserir dados do patrimônio -->
    <div class="form-group">

        <label for="codigo">Código do Patrimônio:</label>
        <select id="codigo" onchange="preencherCampos()" required>
            <option value="">Selecione um código</option>
            <?php
            include 'banco.php';
            $query = "SELECT codigo, nome, data_aquisicao, valor FROM patrimonio";
            $result = $con->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') . "' 
                          data-nome='" . htmlspecialchars($row['nome'], ENT_QUOTES, 'UTF-8') . "' 
                          data-data_aquisicao='" . htmlspecialchars($row['data_aquisicao'], ENT_QUOTES, 'UTF-8') . "' 
                          data-valor='" . htmlspecialchars($row['valor'], ENT_QUOTES, 'UTF-8') . "'>" 
                         . htmlspecialchars($row['codigo'], ENT_QUOTES, 'UTF-8') . 
                         "</option>";
                }
            }
            ?>
        </select>
        
        <label for="nome">Nome do Patrimônio:</label>
        <input type="text" id="nome" placeholder="Nome do patrimônio" >
        
        <label for="data_aquisicao">Data de Aquisição:</label>
        <input type="date" id="data_aquisicao" readonly>
        
        <label for="valor">Valor do Patrimônio:</label>
        <input type="number" id="valor" placeholder="Valor do patrimônio" >

        <button onclick="calcularDepreciacao()">Calcular Depreciação</button>

    </div>
    <!-- Exibição do resultado do cálculo -->
    <div class="result" id="result"></div>
</div>

<script>
    function preencherCampos() {
        const select = document.getElementById('codigo');
        const selectedOption = select.options[select.selectedIndex];

        if (selectedOption) {
            // Obtém os atributos data-* do option selecionado
            const nome = selectedOption.getAttribute('data-nome');
            const dataAquisicao = selectedOption.getAttribute('data-data_aquisicao');
            const valor = selectedOption.getAttribute('data-valor');

            // Preenche os campos com os dados
            document.getElementById('nome').value = nome || '';
            document.getElementById('data_aquisicao').value = dataAquisicao || '';
            document.getElementById('valor').value = valor || '';
        } else {
            // Limpa os campos se nenhum código for selecionado
            document.getElementById('nome').value = '';
            document.getElementById('data_aquisicao').value = '';
            document.getElementById('valor').value = '';
        }
    }

    function calcularDepreciacao() {
        const nome = document.getElementById('nome').value;
        const dataAquisicao = document.getElementById('data_aquisicao').value;
        const valor = parseFloat(document.getElementById('valor').value);

        if (!nome || !dataAquisicao || isNaN(valor)) {
            alert("Por favor, selecione um patrimônio válido.");
            return;
        }

        const vidaUtil = 10;  // Vida útil padrão de 10 anos

        // Calcular a depreciação anual
        const depreciacaoAnual = valor / vidaUtil;
        const dataAquisicaoDate = new Date(dataAquisicao);
        const anoAquisicao = dataAquisicaoDate.getFullYear();
        const anoAtual = new Date().getFullYear();
        const anosUso = anoAtual - anoAquisicao;

        // Calcular a depreciação acumulada
        let depreciacaoAcumulada = depreciacaoAnual * anosUso;

        if (depreciacaoAcumulada > valor) {
            depreciacaoAcumulada = valor;  // Garantir que a depreciação não ultrapasse o valor do bem
        }

        const valorResidual = valor - depreciacaoAcumulada;

        // Exibir o resultado
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = `
            <strong>Patrimônio: </strong>${nome} <br>
            <strong>Valor Inicial: </strong>R$ ${valor.toFixed(2)} <br>
            <strong>Depreciação Anual: </strong>R$ ${depreciacaoAnual.toFixed(2)} <br>
            <strong>Depreciação Acumulada: </strong>R$ ${depreciacaoAcumulada.toFixed(2)} <br>
            <strong>Valor Residual: </strong>R$ ${valorResidual.toFixed(2)} <br>
        `;
    }
</script>




<!-- Scripts -->
<script>
// Função para gerar o código automaticamente via AJAX
function gerarCodigo(categoria) {
    if (categoria) {
        fetch(`gerar_codigo.php?categoria=${categoria}`)
            .then(response => response.text())
            .then(data => {
                // Preencher o campo de código com o valor retornado
                document.getElementById("codigo").value = data;
            })
            .catch(error => console.error('Erro ao gerar o código:', error));
    }
}

// Função para alternar entre as abas
function showTab(tabName) {
    // Esconder todas as abas
    const tabs = document.querySelectorAll('.form-container');
    tabs.forEach(tab => tab.style.display = 'none');

    // Exibir a aba selecionada
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


 // Função de filtro da tabela - Exemplo para ilustrar o funcionamento
 // Função para filtrar a tabela
function filtrarTabela() {
    const identificacao = document.getElementById('filtro-identificacao').value.toLowerCase().trim();
    const situacao = document.getElementById('filtro-situacao').value.toLowerCase().trim();
    const operacao = document.getElementById('filtro-operacao').value.toLowerCase().trim();

    const linhas = document.querySelectorAll('#tabela-levantamento tbody tr');

    linhas.forEach(linha => {
        const colunaIdentificacao = linha.cells[1].textContent.toLowerCase().trim();
        const colunaSituacao = linha.cells[3].textContent.toLowerCase().trim();
        const colunaOperacao = linha.cells[2].textContent.toLowerCase().trim();

        // Verificar se cada filtro é válido ou está vazio
        const matchIdentificacao = !identificacao || colunaIdentificacao.includes(identificacao);
        const matchSituacao = !situacao || colunaSituacao.includes(situacao);
        const matchOperacao = !operacao || colunaOperacao.includes(operacao);

        // Exibir ou ocultar a linha com base nos filtros
        linha.style.display = matchIdentificacao && matchSituacao && matchOperacao ? '' : 'none';
    });
}


   

     // Função para abrir o modal e carregar conteúdo de modaldetalhes.php
     document.querySelectorAll('.detalhes-btn').forEach(button => {
        button.addEventListener('click', function() {
            const patrimonioId = this.getAttribute('data-id');
            abrirModal(patrimonioId);
        });
    });

  // Função para abrir o modal e carregar os detalhes
function abrirModalDetalhes(id) {
    // Encontrar a linha da tabela que corresponde ao id do patrimônio
    const linhas = document.querySelectorAll('tbody tr');
    let patrimonio = {};

    linhas.forEach(linha => {
        const tdId = linha.querySelector('td:first-child').textContent; // ID da linha
        if (tdId == id) {
            patrimonio = {
                id: tdId,
                nome: linha.cells[1].textContent, // Nome
                descricao: linha.cells[2].textContent, // Descrição
                valor: linha.cells[3].textContent, // Valor
                localizacao: linha.cells[4].textContent, // Localização
                situacao: linha.cells[5].textContent, // Situação
                cadastrado_por: linha.cells[6].textContent, // Cadastrado Por
                categoria: linha.cells[7].textContent, // Categoria
                codigo: linha.cells[8].textContent // Código
            };
        }
    });

    // Preencher as informações no modal com os dados do patrimônio
    const modalConteudo = document.getElementById('modal-informacoes');
    modalConteudo.innerHTML = `
        <h3>Detalhes do Patrimônio</h3>
        <p><strong>ID:</strong> ${patrimonio.id}</p>
        <p><strong>Nome:</strong> ${patrimonio.nome}</p>
        <p><strong>Descrição:</strong> ${patrimonio.descricao}</p>
        <p><strong>Valor:</strong> ${patrimonio.valor}</p>
        <p><strong>Localização:</strong> ${patrimonio.localizacao}</p>
        <p><strong>Situação:</strong> ${patrimonio.situacao}</p>
        <p><strong>Cadastrado Por:</strong> ${patrimonio.cadastrado_por}</p>
        <p><strong>Categoria:</strong> ${patrimonio.categoria}</p>
        <p><strong>Código:</strong> ${patrimonio.codigo}</p>
    `;

    // Exibir o modal
    const modal = document.getElementById('modal-detalhes');
    modal.style.display = 'block';
}

// Função para fechar o modal
function fecharModal() {
    const modal = document.getElementById('modal-detalhes');
    modal.style.display = 'none';
}

// Função para imprimir os detalhes do patrimônio
function imprimirDetalhes() {
    const conteudoImpressao = document.getElementById('modal-informacoes').innerHTML;
    
    // Criar uma nova janela de impressão
    const janelaImpressao = window.open('', '', 'height=500,width=800');
    janelaImpressao.document.write('<html><head><title>Imprimir Detalhes</title></head><body>');
    janelaImpressao.document.write(conteudoImpressao);
    janelaImpressao.document.write('</body></html>');
    janelaImpressao.document.close();
    janelaImpressao.print();
}


let paginaAtual = 1; // Página inicial
const itensPorPagina = 5;

// Função para carregar dados do servidor
async function carregarDados(pagina) {
    try {
        const response = await fetch(`paginasTabela.php?pagina=${pagina}`); // Substitua pelo caminho correto do PHP
        const resultado = await response.json();

        atualizarTabela(resultado.dados);
        atualizarBotoes(resultado.total_paginas);
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
    }
}

// Atualizar a tabela com os dados recebidos
function atualizarTabela(dados) {
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = ''; // Limpar a tabela

    dados.forEach(dado => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${dado.id}</td>
            <td>${dado.nome}</td>
            <td>${dado.descricao}</td>
            <td>${parseFloat(dado.valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</td>
            <td>${dado.localizacao}</td>
            <td>${dado.situacao}</td>
            <td>${dado.cadastrado_por}</td>
            <td>${dado.categoria}</td>
            <td>${dado.codigo}</td>
            <td class='actions'>
                <button onclick="abrirModalDetalhes('${dado.id}')">+ Detalhes</button>

                <button>Atualizar</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Atualizar os botões de paginação
function atualizarBotoes(totalPaginas) {
    const paginacao = document.querySelector('.pagination');
    paginacao.innerHTML = ''; // Limpar os botões

    for (let i = 1; i <= totalPaginas; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.classList.toggle('active', i === paginaAtual);
        button.onclick = () => {
            paginaAtual = i;
            carregarDados(paginaAtual);
        };
        paginacao.appendChild(button);
    }
}

// Inicializar a página
window.onload = () => {
    carregarDados(paginaAtual);
};
 
</script>

<script src="src/js/script.js"></script>
</body>
</html>
