<?php
// Incluir o arquivo de conexão com o banco de dados
include 'banco.php';



// Definir o código gerado e a categoria selecionada
$novoCodigo = "";
$categoriaSelecionada = "";

// Código PHP para gerar o código automaticamente
// Se necessário, implemente a lógica de geração de código baseada na categoria selecionada
// Aqui está apenas um exemplo de como gerar um código fictício.
?>
<?php include 'header.php'?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Patrimônio</title>
    <link rel="stylesheet" href="./src/style/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>    
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="src/patrimonio/style/homepatrimonio.css">


</head>
<body class="caderno">

<!-- Menu das abas -->
<div class="tabs">
    <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">   <i class="fas fa-plus-circle"></i> Cadastrar BP</div>
    <div class="tab" data-tab="retirar" onclick="showTab('retirar')">Movimentação BP</div>
    <div class="tab" data-tab="levantamento" onclick="showTab('levantamento')"> <i class="fas fa-search"></i> Levantamento de Bens</div>
    <div class="tab" data-tab="DPRE" onclick="showTab('DPRE')">DPRE</div>
    <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">        <i class="fas fa-file-alt"></i>Relatorio</div>
    <div class="tab" data-tab="galeria" onclick="showTab('galeria')">Galeria</div>
</div>

<!-- Conteúdo das abas -->
<div class="form-container" id="cadastrar" >
    <h3>Cadastrar Patrimônio</h3>
    <form action="./patrimonio/cadastrar_patrimonio.php" method="POST" enctype="multipart/form-data">
        <!-- Checkbox para Categoria -->
        <div class="form-group">
        <div class="photo-upload-container">
            <img src="default.png" alt="Foto do Usuário" id="preview">

            <input type="file" name="foto" id="foto" accept="image/*">
            <label for="foto"><i class="fas fa-user"></i> Adicionar Foto</label>
        </div>
            <label for="categoria">Categoria do Patrimônio:</label>
            <select id="categoria" name="categoria" required onchange="gerarCodigo(this.value)">
                <option value="selecione">Selecione a categoria</option>
                <option value="equipamentos_informatica">Equipamentos de Informática</option>
                <option value="bens_achados">Bens Achados</option>
                <option value="moveis_utensilios">Móveis e Utensílios</option>
                <option value="reserva_bens_moveis">Reserva de Bens Móveis</option>
                <option value="bens_com_baixo_valor">Bens sem patrimônio</option>
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
                <option value="em processo de bai">Em processo de baixa</option>
            </select>

            <label for="localizacao">Localização:</label>
            <input type="text" id="localizacao" name="localizacao" required>

            <button type="submit">Cadastrar</button>
        </div>
        </form>
</div>


<div class="form-container2" id="galeria" style="display: none;">
<div id="cards-container" class="carousel-items">
    <!-- Os cards serão adicionados dinamicamente via JavaScript -->

</div>


</div>



<script>
        document.getElementById('foto').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

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
 

 <form action="./patrimonio/registrar_remessa.php" method="POST">
 <div class="form-group">

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
 </div>
</form>

</div>
<div class="form-container" id="relatorio">
    <form action="./patrimonio/gerar_relatorio.php" method="GET" target="_blank">
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


<div class="form-container" id="levantamento">
    <h3>Levantamento de Bens</h3>
    
    <!-- Filtros -->
    <div class="filters">
        <label for="filtro-identificacao">Filtrar por Identificação</label>
        <input type="text" id="filtro-identificacao" name="filtro-identificacao" placeholder="Digite a identificação" oninput="filtrarTabela()">
        
        <label for="filtro-situacao">Filtrar por Situação</label>
        <select id="filtro-situacao" name="filtro-situacao" onchange="filtrarTabela()">
            <option value="">Selecione</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
            <option value="Em uso">Em Uso</option>
            <option value="Em Processo de baixa">Em Processo de baixa</option>
        </select>
        
        <label for="filtro-operacao">Filtrar por Operação</label>
        <input type="text" id="filtro-operacao" name="filtro-operacao" placeholder="Digite a operação" oninput="filtrarTabela()">
    
        
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
                     <!-- <th>Operação</th>  -->
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
        <!-- Cabeçalho com o logotipo -->
        <div class="">
        <img src="./src/img/Logo CENTRAL (colorida).png" alt="logo-central" class="e">
            <span class="modal-close" onclick="fecharModal()">&times;</span>
        </div>
        
        <!-- Conteúdo do modal -->
        <div id="modal-informacoes">
            <!-- As informações do patrimônio serão carregadas aqui -->
        </div>
        
        <!-- Botão de impressão -->
        <button id="imprimir-btn" onclick="imprimirDetalhes()">Imprimir</button>
    </div>
</div>


<!-- Modal de Atualização -->
<div class="modal" id="modal-atualizar">
    <div class="modal-content">
        <span class="modal-close" onclick="fecharModalAtualizar()">&times;</span>
        <h3>Atualizar Patrimônio</h3>
        <form id="form-atualizar" >
            <label for="atualizar-id">ID:</label>
            <input type="text" id="atualizar-id" name="id" readonly oninput="sincronizarValor()">

            <label for="atualizar-nome">Nome:</label>
            <input type="text" id="atualizar-nome" name="nome" required>

            <label for="atualizar-descricao">Descrição:</label>
            <input type="text" id="atualizar-descricao" name="descricao" required>

            <label for="atualizar-valor">Valor:</label>
            <input type="text" id="atualizar-valor" name="valor" step="0.01" readonly>

            <label for="atualizar-localizacao">Localização:</label>
            <input type="text" id="atualizar-localizacao" name="localizacao" disabled>

            <label for="atualizar-situacao">Situação:</label>
            <select id="atualizar-situacao" name="situacao">
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
                <option value="Em uso">Em Uso</option>
                <option value="Em Processo de baixa">Em Processo de baixa</option>
            </select>
            

            <label for="atualizar-cadastrado-por">Cadastrado Por:</label>
            <input type="text" id="atualizar-cadastrado-por" name="cadastrado_por" disabled>

            <label for="atualizar-categoria">Categoria:</label>
            <input type="text" id="atualizar-categoria" name="categoria" required>

            <label for="atualizar-codigo">Código:</label>
            <input type="text" id="atualizar-codigo" name="codigo" disabled>

            <div class="button-group">
                <button  type="submit">Salvar Alterações</button>
                <button  onclick="fecharModalAtualizar()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    //     // Função para buscar o valor pelo ID e preencher o campo Valor
    //  function carregarValorPorId() {
    //     const idField = document.getElementById('atualizar-id');
    //     const valorField = document.getElementById('atualizar-valor');
    //     const id = idField.value;

    //     if (id) {
    //         fetch(`getValor.php?id=${id}`)
    //             .then((response) => response.json())
    //             .then((data) => {
    //                 if (data.valor !== null) {
    //                     valorField.value = data.valor;
    //                 } else {
    //                     valorField.value = 'Valor não encontrado';
    //                 }
    //             })
    //             .catch((error) => {
    //                 console.error('Erro ao buscar valor:', error);
    //                 valorField.value = 'Erro ao carregar';
    //             });
    //     } else {
    //         valorField.value = '';
    //         console.warn('ID não preenchido');
    //     }
    // }


</script>





    <!-- Paginação -->
<div class="pagination"></div>
 </div>

<?php
    // Conexão com o banco de dados
    $host = 'localhost';
    $dbname = 'gm_sicbd';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Erro na conexão: ' . $e->getMessage();
            exit;
        }

    // Buscar dados da tabela patrimonio
    $sql = "SELECT id, codigo, nome, valor, data_registro FROM patrimonio";
    $stmt = $pdo->query($sql);
    $patrimonios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Variáveis para armazenar os valores
    $codigo = $nome = $valor = $data_registro = '';
    $depreciacao = $vida_util = 0;  // Inicializando a depreciação e vida útil
    $depreciacoes_por_ano = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['patrimonio_id'])) {
        $id_selecionado = $_POST['patrimonio_id'];
        $sql = "SELECT codigo, nome, valor, data_registro FROM patrimonio WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_selecionado]);
        $patrimonio = $stmt->fetch(PDO::FETCH_ASSOC);

        $codigo = $patrimonio['codigo'];
        $nome = $patrimonio['nome'];
        $valor = $patrimonio['valor'];
        $data_registro = $patrimonio['data_registro'];

        // Definir vida útil (em anos)
        $vida_util = 5; // Exemplo genérico: vida útil de 5 anos

        // Cálculo de depreciação com base na data de registro
        $data_registro_obj = new DateTime($data_registro);

        // Depreciação linear
        $valor_residual = 0; // Supondo valor residual zero
        $depreciacao_anual = ($valor - $valor_residual) / $vida_util;

        // Cálculo da depreciação acumulada por ano
        for ($ano = 0; $ano <= $vida_util; $ano++) {
            $data_anual = clone $data_registro_obj;
            $data_anual->modify("+$ano years");

            if ($ano === $vida_util || $ano > (new DateTime())->diff($data_registro_obj)->y) {
                $depreciacao_atual = $valor;
            } else {
                $depreciacao_atual = $depreciacao_anual * $ano;
            }

            $depreciacoes_por_ano[] = [
                'ano' => $data_anual->format('Y'),
                'depreciacao' => min($depreciacao_atual, $valor)
            ];
        }
    }
?>



<div class="form-container" id="DPRE" >
    <div class="form-section" style="width: 50%;">
        <h2>Calcular Depreciação</h2>
        <form method="POST">
            <div class="form-group">
                <label for="patrimonio_id">Selecione um Patrimônio:</label>
                <select name="patrimonio_id" id="patrimonio_id" onchange="this.form.submit()">
                    <option value="">-- Selecione --</option>
                    <?php foreach ($patrimonios as $patrimonio): ?>
                        <option value="<?= $patrimonio['id'] ?>" <?= isset($_POST['patrimonio_id']) && $_POST['patrimonio_id'] == $patrimonio['id'] ? 'selected' : '' ?>>
                            <?= $patrimonio['codigo'] ?> - <?= $patrimonio['nome'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="codigo">Código:</label>
                <input type="text" id="codigo" name="codigo" value="<?= htmlspecialchars($codigo) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="valor">Valor:</label>
                <input type="text" id="valor" name="valor" value="<?= htmlspecialchars($valor) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="data_registro">Data de Registro:</label>
                <input type="text" id="data_registro" name="data_registro" value="<?= htmlspecialchars($data_registro) ?>" readonly>
            </div>
        </form>
    </div>
    <div class="chart-section" style="width: 50%; display: flex; align-items: center; justify-content: center;">
        <canvas id="depreciationChart" style="width: 100%; height: auto; max-width: 555px; max-height: 277px;"></canvas>
    </div>
    </div>

    <div class="chart-container" style="width: 100%; margin-left: 20px;">
        <canvas id="depreciationChart" style="display: block; box-sizing: border-box; height: 400px; width: 555px;" width="555" height="400"></canvas>
    </div>

       
</div>




<script>
function showTab(tabId) {
    // Ocultar todo conteúdo de aba
    const tabsContent = document.querySelectorAll('.tab-content');
    tabsContent.forEach(content => content.style.display = 'none');

    // Exibir a aba correspondente
    const activeTab = document.getElementById(tabId);
    if (activeTab) {
        activeTab.style.display = 'block';
        loadCardData(); // Carregar os dados do patrimônio
    }
}
// Função para verificar se a imagem existe
async function imageExists(url) {
    try {
        const response = await fetch(url, { method: 'HEAD' });
        return response.ok; // Retorna true se a imagem existe
    } catch (error) {
        console.error(`Erro ao verificar a imagem: ${url}`, error);
        return false;
    }
}

async function loadCardData() {
    try {
        const response = await fetch('./patrimonio/getPatrimonios.php'); // Endpoint PHP
        const data = await response.json();

        const cardsContainer = document.getElementById('cards-container');
        cardsContainer.innerHTML = ''; // Limpar conteúdo anterior

        for (const item of data) {
            const card = document.createElement('div');
            card.className = 'card';

            // URL da imagem informada no item
            const imageUrl = `uploads/${item.foto}`;

            // Verifica se a imagem existe
            const validImageUrl = await imageExists(imageUrl) ? imageUrl : 'uploads/default.png';

            card.innerHTML = `
                <div class="card-inner">
                    <!-- Frente do card -->
                    <div class="card-front">
                        <img src="${validImageUrl}" alt="${item.nome}" class="card-image">
                    </div>
                    <!-- Verso do card -->
                    <div class="card-back">
                        <h3>${item.nome}</h3>
                        <p><strong>Código:</strong> ${item.codigo}</p>
                        <p><strong>Categoria:</strong> ${item.categoria}</p>
                        <p><strong>Data de Registro:</strong> ${item.data_registro}</p>
                    </div>
                </div>
            `;

            cardsContainer.appendChild(card); // Adicionar o card ao contêiner
        }
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
    }
}

// Chamar a função para carregar e exibir os dados ao carregar a página
document.addEventListener('DOMContentLoaded', loadCardData);


 
</script>

<script>
        const depreciationData = <?= json_encode($depreciacoes_por_ano) ?>;
        const years = depreciationData.map(data => data.ano);
        const values = depreciationData.map(data => data.depreciacao);

        const ctx = document.getElementById('depreciationChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: years,
                datasets: [{
                    label: 'Valor da Depreciação (R$)',
                    data: values,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `R$ ${context.parsed.y.toFixed(2)}`;
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
    </script>

<!-- Scripts -->
<script>
// Função para gerar o código automaticamente via AJAX
function gerarCodigo(categoria) {
    if (categoria) {
        fetch(`./patrimonio/gerar_codigo.php?categoria=${categoria}`)
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
    // Esconder todas as abas do tipo form-container e form-container2
    const tabs = document.querySelectorAll('.form-container, .form-container2');
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


 // Função de filtro da tabela - Exemplo para ilustrar o funcionamento
 function filtrarTabela() {
    // Obter os valores dos filtros
    const identificacao = document.getElementById('filtro-identificacao').value.toLowerCase().trim();
    const situacao = document.getElementById('filtro-situacao').value.toLowerCase().trim();
    const operacao = document.getElementById('filtro-operacao').value.toLowerCase().trim();

    // Selecionar todas as linhas do corpo da tabela
    const linhas = document.querySelectorAll('#tabela-levantamento tbody tr');

    linhas.forEach(linha => {
        // Obter os valores das colunas relevantes
        const colunaIdentificacao = linha.cells[1]?.textContent.toLowerCase().trim() || '';
        const colunaSituacao = linha.cells[5]?.textContent.toLowerCase().trim() || '';
        const colunaOperacao = linha.cells[2]?.textContent.toLowerCase().trim() || '';

        // Comparar os valores das colunas com os filtros
        const matchIdentificacao = !identificacao || colunaIdentificacao.includes(identificacao);
        const matchSituacao = !situacao || colunaSituacao.includes(situacao);
        const matchOperacao = !operacao || colunaOperacao.includes(operacao);

        // Exibir ou ocultar a linha com base nos critérios
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


// Função para abrir o modal de atualização
function abrirModalAtualizar(id) {
    // Encontrar a linha correspondente ao ID
    const linhas = document.querySelectorAll('tbody tr');
    let patrimonio = {};

    linhas.forEach(linha => {
        const tdId = linha.querySelector('td:first-child').textContent; // ID da linha
        if (tdId == id) {
            patrimonio = {
                id: tdId,
                nome: linha.cells[1].textContent, // Nome
                descricao: linha.cells[2].textContent, // Descrição
                valor: linha.cells[3].textContent.replace(/[^\d,.-]/g, ''), // Remover símbolos de moeda
                localizacao: linha.cells[4].textContent, // Localização
                situacao: linha.cells[5].textContent, // Situação
                cadastrado_por: linha.cells[6].textContent, // Cadastrado Por
                categoria: linha.cells[7].textContent, // Categoria
                codigo: linha.cells[8].textContent // Código
            };
        }
    });

    // Preencher os campos do formulário com os dados do patrimônio
    document.getElementById('atualizar-id').value = patrimonio.id;
    document.getElementById('atualizar-nome').value = patrimonio.nome;
    document.getElementById('atualizar-descricao').value = patrimonio.descricao;
    document.getElementById('atualizar-valor').value = patrimonio.valor;
    document.getElementById('atualizar-localizacao').value = patrimonio.localizacao;
    document.getElementById('atualizar-situacao').value = patrimonio.situacao.toLowerCase();
    document.getElementById('atualizar-cadastrado-por').value = patrimonio.cadastrado_por;
    document.getElementById('atualizar-categoria').value = patrimonio.categoria;
    document.getElementById('atualizar-codigo').value = patrimonio.codigo;

    // Exibir o modal de atualização
    document.getElementById('modal-atualizar').style.display = 'block';
}

// Função para fechar o modal de atualização
function fecharModalAtualizar() {
    document.getElementById('modal-atualizar').style.display = 'none';
}

document.getElementById('form-atualizar').addEventListener('submit', function (event) {
    event.preventDefault(); // Evita o comportamento padrão de envio do formulário

    // Coleta os dados do formulário
    const formData = new FormData(this);

    // Envia os dados via fetch para o script de atualização
    fetch('./patrimonio/modalatualizabp.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                alert('Dados atualizados com sucesso!');
                fecharModalAtualizar(); // Fecha o modal
                location.reload(); // Atualiza a tabela ou página
            } else {
                alert('Erro ao atualizar os dados: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Erro ao enviar os dados:', error);
            alert('Erro ao atualizar os dados. Tente novamente.');
        });
});


// Função para imprimir os detalhes do patrimônio
function imprimirDetalhes() {
    const conteudo = document.getElementById('modal-informacoes').innerHTML;
    const logoURL = './src/img/Logo CENTRAL (colorida).png'; // Substitua pelo caminho do logotipo
    const janelaImpressao = window.open('', '_blank');

    janelaImpressao.document.open();
    janelaImpressao.document.write(`
        <html>
            <head>
                
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        margin: 20px;
                    }
                    .header2 {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .header2 img {
                        max-height: 80px;
                    }
                </style>
            </head>
            <body>
                <div class="header2">
                    <img src="${logoURL}" alt="Logotipo da Empresa">
                </div>
                <div>${conteudo}</div>
            </body>
        </html>
    `);

    janelaImpressao.document.close();
    janelaImpressao.print();
}


let paginaAtual = 1; // Página inicial
const itensPorPagina = 3;

// Função para carregar dados do servidor
async function carregarDados(pagina) {
    try {
        const response = await fetch(`./patrimonio/paginasTabela.php?pagina=${pagina}`); // Substitua pelo caminho correto do PHP
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
                <td class="actions">
                    <button class="btn1" onclick="abrirModalDetalhes('${dado.id}')">+ Detalhes</button>
                    <button class="btn2" onclick="abrirModalAtualizar('${dado.id}')">Atualizar</button>
                </td>
            `;

        tbody.appendChild(row);
    });
}

// Atualizar os botões de paginação
function atualizarBotoes(totalPaginas) {
    const paginacao = document.querySelector('.pagination');
    paginacao.innerHTML = ''; // Limpar os botões

    const maxBotoes = 5; // Número máximo de botões a exibir de uma vez
    let inicio = Math.max(1, paginaAtual - Math.floor(maxBotoes / 2));
    let fim = Math.min(totalPaginas, inicio + maxBotoes - 1);

    // Ajusta a exibição de botões se houver menos de 5
    if (fim - inicio + 1 < maxBotoes) {
        inicio = Math.max(1, fim - maxBotoes + 1);
    }

    // Criar o botão de "<<"
    if (inicio > 1) {
        const primeiro = document.createElement('button');
        primeiro.textContent = '<<';
        primeiro.onclick = () => {
            paginaAtual = 1;
            carregarDados(paginaAtual);
        };
        paginacao.appendChild(primeiro);
    }

    // Criar os botões de páginas
    for (let i = inicio; i <= fim; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.classList.toggle('active', i === paginaAtual);
        button.onclick = () => {
            paginaAtual = i;
            carregarDados(paginaAtual);
        };
        paginacao.appendChild(button);
    }

    // Criar o botão de ">>"
    if (fim < totalPaginas) {
        const ultimo = document.createElement('button');
        ultimo.textContent = '>>';
        ultimo.onclick = () => {
            paginaAtual = fim + 1;
            carregarDados(paginaAtual);
        };
        paginacao.appendChild(ultimo);
    }
}

// Inicializar a página
window.onload = () => {
    carregarDados(paginaAtual);
};


</script>

<script src="src/js/script.js"></script>
<?php include 'footer.php'; ?>
</body>
</html>
