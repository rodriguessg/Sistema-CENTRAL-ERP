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
<?php include 'header.php'?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Patrimônio</title>
    <link rel="stylesheet" href="./src/style/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>    
    <!-- <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css"> -->
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
            <div class="mb-3" >
                <label for="descricao" class="form-label">Discrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="3" required></textarea>
            </div>

            <button type="submit">Cadastrar</button>
        </div>
        </form>
</div>


<div class="form-container" id="galeria" style="display: none;">
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
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    // Criação da conexão PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Definir modo de erro para exceção
} catch (PDOException $e) {
    // Em caso de erro na conexão, loga o erro e exibe uma mensagem amigável
    error_log("Erro ao conectar ao banco: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Consulte o administrador.");
}


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
    <label for="descricao">Descriação:</label>
    <input type="text" name="descricao" id="descricao" required>
    </div>
    <div class="form-group">
    <label for="categoria">Categoria:</label>
    <input type="text" name="categoria" id="categoria" required>
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
                    <th>Cógido BP</th>
                                     <th>NOME</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Localização</th>
                    <th>Situação</th>
                     <!-- <th>Operação</th>  -->
                    <th>Cadastrado Por</th>
                    <th>Categoria</th>
   
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
<script src="./src/patrimonio/js/gerar-codigo-filtros-outros.js"></script>

<script src="src/js/script.js"></script>
<?php include 'footer.php'; ?>
</body>
</html>
