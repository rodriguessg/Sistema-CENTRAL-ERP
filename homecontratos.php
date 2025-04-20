<?php
session_start();


// Conexão com o banco de dados
$dsn = 'mysql:host=localhost;dbname=gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Marcar a notificação como lida quando o usuário clicar
if (isset($_GET['mark_read'])) {
    $notificationId = $_GET['mark_read'];

    // Verifique se o usuário é 'contratos' para permitir marcar como lida
    if ($_SESSION['username'] == 'contratos') {
        $sqlMarkRead = "UPDATE notificacoes SET situacao = 'lida' WHERE id = :id";
        $stmtMarkRead = $pdo->prepare($sqlMarkRead);
        $stmtMarkRead->execute([':id' => $notificationId]);

        $_SESSION['success'] = "Notificação marcada como lida.";
    }
}

// Processar assinatura do contrato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_contrato'])) {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $validade = $_POST['validade'];
    $assinatura = $_POST['assinatura'];

    // Inserir contrato
    $sql = "INSERT INTO gestao_contratos (titulo, descricao, assinatura, validade) VALUES (:titulo, :descricao, :assinatura, :validade)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':titulo' => $titulo,
            ':descricao' => $descricao,
            ':assinatura' => $assinatura,
            ':validade' => $validade
        ]);

        $_SESSION['success'] = "Contrato cadastrado com sucesso!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Erro ao cadastrar contrato: " . $e->getMessage();
    }

   // Após o cadastro do contrato, insira a notificação
$nomeContrato = $titulo; // Nome do contrato
$usuario = $_SESSION['username']; // Nome do usuário da sessão
$setor = $_SESSION['setor']; // Setor do usuário da sessão
$situacao = 'não lida'; // Situação como não lida
$dataNotificacao = date('Y-m-d H:i:s'); // Data e hora atual da notificação

// Verificar se já existe uma notificação para o mesmo contrato e usuário
$sqlVerificacao = "SELECT COUNT(*) FROM notificacoes WHERE username = :username AND mensagem = :mensagem";
$stmtVerificacao = $pdo->prepare($sqlVerificacao);
$stmtVerificacao->execute([
    ':username' => $usuario,
    ':mensagem' => "Contrato '{$nomeContrato}' prestes a expirar." // Certifique-se de que a mensagem é a que você deseja
]);

// Se não houver uma notificação, insira a nova
if ($stmtVerificacao->fetchColumn() == 0) {
    // Inserir a notificação se não existir
    $sqlNotificacao = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                       VALUES (:username, :setor, :mensagem, :situacao, :data_criacao)";
    $stmtNotificacao = $pdo->prepare($sqlNotificacao);

    try {
        // Tente executar a inserção da notificação
        $stmtNotificacao->execute([
            ':username' => $usuario,
            ':setor' => $setor,
            ':mensagem' => "Contrato '{$nomeContrato}' prestes a expirar.",
            ':situacao' => $situacao,
            ':data_criacao' => $dataNotificacao
        ]);
        
        $_SESSION['success'] = "Notificação inserida com sucesso.";
    } catch (Exception $e) {
        // Caso ocorra um erro, mostre uma mensagem de erro
        $_SESSION['error'] = "Erro ao adicionar notificação: " . $e->getMessage();
    }
} else {
    // Se a notificação já existe, não faz nada
    $_SESSION['info'] = "Notificação para o contrato '{$nomeContrato}' já foi registrada.";
}
}

// Buscar contratos próximos de expirar
$notificacoes = [];
$sql = "SELECT * FROM gestao_contratos WHERE validade <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH) AND validade >= CURDATE()";
$stmt = $pdo->query($sql);
$notificacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica se um ID de processo foi enviado via GET
$processId = isset($_GET['processId']) ? $_GET['processId'] : null;

// Consulta para obter os detalhes do processo com base no ID
$processDetails = null;
if ($processId) {
    $sql = "SELECT * FROM gestao_contratos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$processId]);
    $processDetails = $stmt->fetch(PDO::FETCH_ASSOC);
}

include 'header.php';

?>
<?php
// Configuração da conexão com o banco de dados
$host = 'localhost'; // Seu host do banco de dados
$user = 'root';      // Seu usuário do banco de dados
$pass = '';          // Sua senha do banco de dados
$db   = 'gm_sicbd'; // Nome do banco de dados

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $pass, $db);

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Consultar os contratos na tabela gestao_contratos
$sql = "SELECT titulo FROM gestao_contratos";
$result = $conn->query($sql);

// Verificar se há resultados
$options = "";
if ($result->num_rows > 0) {
    // Preencher as opções do select
    while ($row = $result->fetch_assoc()) {
        $titulo = $row['titulo'];
        $options .= "<option value=\"$titulo\">$titulo</option>";
    }
} else {
    $options = "<option value=\"\">Nenhum contrato encontrado</option>";
}

// Fechar a conexão com o banco de dados
$conn->close();
?>




<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Contratos</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<!-- <link rel="stylesheet" href="./src/style/form-cadastro-contratos.css"> -->
<!-- <link rel="stylesheet" href="./src/style/notificacao.css"> -->
<link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
<link rel="stylesheet" href="src/contratos/style/consultar-contratos.css">
<link rel="stylesheet" href="src/contratos/style/cadastro-contratos.css">

<body>
<div class="caderno">
        <!-- <h1 class="text-center text-success">Gestão de Contratos</h1> -->

   
    <div class="tabs">
    <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">
        <i class="fas fa-plus-circle"></i> Cadastro de contratos
    </div>
    <div class="tab" data-tab="consultar" onclick="showTab('consultar')">
        <i class="fas fa-search"></i> Consultar contratos
    </div>
    <div class="tab" data-tab="agenda" onclick="showTab('agenda')">
        <i class="fas fa-calendar-alt"></i> Agendamento
    </div>
    <div class="tab" data-tab="resumo_processo" onclick="showTab('resumo_processo')" style="display: none;">
        <i class="fas fa-info-circle"></i> Resumo
    </div>
    <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">
        <i class="fas fa-file-alt"></i> Relatório
    </div>  
    <!-- <div class="tab" data-tab="galeria" onclick="showTab('galeria')"><i class="fas fa-image"></i> Galeria</div> -->
</div>



<div class="form-container" id="cadastrar" style="display:none;">
    <form action="cadastrar_contratos.php" method="POST" enctype="multipart/form-data">
    <h1 class="cadastrar-contratos">
    <i class="fas fa-plus-circle" id="icon-cadastrar"></i> Cadastrar Contratos
</h1>

    <div class="cadastro">
        <div class="grupo1">
         <div class="mb-3">
            <label for="titulo" class="form-label">Título do Contrato</label>
            <div class="input-icon">
                <input type="text" id="titulo" name="titulo" class="form-control" required>
                <i class="fas fa-pencil-alt"></i> <!-- Ícone dentro do input -->
            </div>
         </div>

        <div class="mb-3">
            <label for="SEI" class="form-label">Nº SEI</label>
            <div class="input-icon">
                <input type="text" id="SEI" name="SEI" class="form-control" required>
                <i class="fas fa-file-alt"></i> <!-- Ícone dentro do input -->
            </div>
        </div>

        <div class="mb-3">
            <label for="objeto" class="form-label">Objeto</label>
            <div class="input-icon">
                <input type="text" id="objeto" name="objeto" class="form-control" required>
                <i class="fas fa-cogs"></i> <!-- Ícone dentro do input -->
            </div>
        </div>

        <div class="mb-3">
            <label for="gestor" class="form-label">Gestor</label>
            <div class="input-icon">
                <input type="text" id="gestor" name="gestor" class="form-control" required>
                <i class="fas fa-user"></i> <!-- Ícone dentro do input -->
            </div>
        </div>

        <div class="mb-3">
            <label for="gestorsb" class="form-label">Gestor Substituto</label>
            <div class="input-icon">
                <input type="text" id="gestorsb" name="gestorsb" class="form-control" required>
                <i class="fas fa-user-slash"></i> <!-- Ícone dentro do input -->
            </div>
        </div>
        </div>

        
        <div class="grupo2">
        <div class="mb-3">
            <label for="fiscais" class="form-label">Fiscais</label>
            <div class="input-icon">
                <input type="text" id="fiscais" name="fiscais" class="form-control" required>
                <i class="fas fa-balance-scale"></i> <!-- Ícone dentro do input -->
            </div>
        </div>

        <div class="mb-3">
            <label for="validade" class="form-label">Vigência</label>
            <div class="input-icon">
                <input type="date" id="validade" name="validade" class="form-control" required onchange="atualizarParcelas()">
                <i class="fas fa-calendar-alt"></i> <!-- Ícone dentro do input -->
            </div>
        </div>

        <div class="mb-3">
            <label for="contatos" class="form-label">Contatos</label>
            <div class="input-icon">
                <input type="text" id="contatos" name="contatos" class="form-control" required>
                <i class="fas fa-phone-alt"></i> <!-- Ícone dentro do input -->
            </div>
        </div>

        <div class="mb-3">
            <label for="valor-contrato" class="form-label">Valor do Contrato</label>
            <div class="input-icon">
                <input type="text" id="valor-contrato" name="valor_contrato" class="form-control" required>
                <i class="fas fa-dollar-sign"></i> <!-- Ícone dentro do input -->
            </div>
        </div>

        <div class="mb-3">
            <label for="valor-aditivo" class="form-label">Valor Aditivo</label>
            <div class="input-icon">
                <input type="text" id="valor-aditivo" name="valor_aditivo" class="form-control" required>
                <i class="fas fa-plus-circle"></i> <!-- Ícone dentro do input -->
            </div>
        </div>
        </div>
        </div>

        <div class="button-group" >
        <button type="button" class="btn-submit" onclick="toggleComplementares()">Adicionar Informações Complementares</button>
        <button type="submit" name="cadastrar_contrato" class="btn-submit" >Cadastrar Contrato</button>
        </div>
        <div id="complementares" style="display:none;">
            <div class="mb-3">
                <input type="checkbox" id="parcelamento" name="parcelamento" onchange="toggleParcelas()">
                <label for="parcelamento">Este contrato é um parcelamento?</label>
            </div>
            <div class="mb-3" id="parcelas-container" style="display:none;">
                <label for="num-parcelas" class="form-label">Número de Parcelas</label>
                <input type="number" id="num-parcelas" name="num_parcelas" class="form-control" max="12">
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Observação</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="3" required></textarea>
            </div>
        </div>

        <input type="hidden" id="assinatura" name="assinatura">
       
      
    </form>
</div>

<script src="./src/contratos/js/cadastro_contato.js">
</script>

<!-- <div class="form-container3" id="processos">
    <?php
    // Conexão com o banco de dados
    include 'banco.php';

    // Consulta para contar o número de processos na tabela gestao_contratos
    $sql = "SELECT COUNT(*) as total FROM gestao_contratos";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    $total_processos = $row['total'];
    ?>

    <div class="card">
        <div class="card-body text-center">
            <h5 class="card-title">Total de Processos</h5>
            <p class="card-text display-4"><?php echo $total_processos; ?></p>
        </div>
    </div>
</div> -->
<!-- Formulário para selecionar contrato e tipo de relatório -->
<div class="form-container" id="relatorio">
  <form id="relatorio-form">
    <div class="form-group">
      <div class="input-group">
        <!-- Seletor de contrato -->
        <label for="tipo_relatorio">Nome do Contrato</label>
        <select name="contrato" id="tipo_relatorio" onchange="mostrarTipoRelatorio()">
          <option value="">Selecione o Contrato</option>
          <?php echo $options; ?> <!-- Supondo que $options seja preenchido com contratos -->
        </select>

        <!-- Seletor de tipo de relatório (inicialmente oculto) -->
        <div id="tipo-relatorio-container" style="display: none;">
          <label for="relatorio_tipo">Relatório por contratos</label>
          <select name="relatorio_tipo" id="relatorio_tipo">
            <option value="completo">Relatório Completo</option>
            <option value="compromissos_futuros">Compromissos Futuros</option>
            <option value="pagamentos">Relatório de pagamentos</option>
            <option value="mensal">Relatório Mensal</option>
            <option value="anual">Relatório Anual</option>
          </select>
        </div>

        <!-- Botão para gerar o relatório -->
        <button type="button" id="gerar-relatorio" onclick="gerarRelatorio()">Gerar Relatório</button>
      </div>
    </div>
  </form>

  <!-- Tabela para Relatório Completo -->
  <div id="relatorio-completo-tabela" style="display: none;">
    <table id="relatorio-completo" style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th>Título do Contrato</th>
          <th>Vigência</th>
          <th>Gestor</th>
          <th>Gestor Substituto</th>
          <th>Situação</th>
          <th>Nº de Parcelas</th>
          <th>Data de Cadastro</th>
        </tr>
      </thead>
      <tbody>
        <!-- Dados serão preenchidos via JavaScript -->
      </tbody>
    </table>
  </div>

  <!-- Tabela para Compromissos Futuros -->
  <div id="compromissos-futuros-tabela" style="display: none;">
    <table id="compromissos-futuros" style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th>Título do Contrato</th>
          <th>Vigência</th>
          <th>Gestor</th>
          <th>Gestor Substituto</th>
          <th>Situação</th>
          <th>Nº de Parcelas</th>
          <th>Próximos Pagamentos</th>
        </tr>
      </thead>
      <tbody>
        <!-- Dados serão preenchidos via JavaScript -->
      </tbody>
    </table>
  </div>

  <!-- Tabela para Relatório de Pagamentos -->
  <div id="relatorio-pagamentos-tabela" style="display: none;">
    <table id="relatorio-pagamentos" style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr>
          <th>Título do Contrato</th>
          <th>Pagamentos Efetuados</th>
        </tr>
      </thead>
      <tbody>
        <!-- Dados serão preenchidos via JavaScript -->
      </tbody>
    </table>
  </div>
</div>

<script>
    // Função para mostrar ou esconder o seletor de tipo de relatório
function mostrarTipoRelatorio() {
  const contrato = document.getElementById('tipo_relatorio').value;
  const tipoRelatorioContainer = document.getElementById('tipo-relatorio-container');
  
  // Se houver um contrato selecionado, exibe o seletor de tipo de relatório
  if (contrato) {
    tipoRelatorioContainer.style.display = 'block';
  } else {
    tipoRelatorioContainer.style.display = 'none';
  }
}

// Função para gerar o relatório
function gerarRelatorio() {
  const contrato = document.getElementById('tipo_relatorio').value;
  const tipoRelatorio = document.getElementById('relatorio_tipo').value;

  if (!contrato || !tipoRelatorio) {
    alert('Por favor, selecione o contrato e o tipo de relatório.');
    return;
  }

  // Requisição AJAX para buscar os dados do contrato
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'processar_relatorio.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  // Envia o título do contrato e o tipo de relatório
  xhr.send('contrato=' + encodeURIComponent(contrato) + '&relatorio_tipo=' + encodeURIComponent(tipoRelatorio));

  // Quando a resposta estiver pronta
  xhr.onload = function () {
    console.log(xhr.responseText); // Depuração da resposta

    if (xhr.status === 200) {
      try {
        const resposta = JSON.parse(xhr.responseText);

        if (resposta.sucesso) {
          // Limpa as tabelas antes de exibir a nova
          document.getElementById('relatorio-completo-tabela').style.display = 'none';
          document.getElementById('compromissos-futuros-tabela').style.display = 'none';
          document.getElementById('relatorio-pagamentos-tabela').style.display = 'none'; // Tabela de Pagamentos

          if (tipoRelatorio === 'completo') {
            preencherTabelaCompleta(resposta.dados);
          } else if (tipoRelatorio === 'compromissos_futuros') {
            preencherCompromissosFuturos(resposta.dados);
          } else if (tipoRelatorio === 'pagamentos') {
            preencherTabelaPagamentos(resposta.dados);
          }
        } else {
          alert(resposta.mensagem);
        }
      } catch (e) {
        console.error('Erro ao processar a resposta JSON:', e);
        alert('Erro ao processar os dados. Resposta inesperada.');
      }
    } else {
      alert('Erro ao gerar relatório.');
    }
  };
}

// Função para preencher a tabela de Relatório Completo
function preencherTabelaCompleta(dados) {
  const tabela = document.querySelector('#relatorio-completo tbody');
  tabela.innerHTML = ''; // Limpa a tabela antes de preencher

  // Preenche a tabela com os dados do contrato
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>${dados.titulo}</td>
    <td>${dados.validade}</td>
    <td>${dados.gestor}</td>
    <td>${dados.gestorsb}</td>
    <td>${dados.situacao}</td>
    <td>${dados.num_parcelas}</td>
    <td>${dados.data_cadastro}</td>
  `;
  tabela.appendChild(tr);

  // Exibe a tabela de Relatório Completo
  document.getElementById('relatorio-completo-tabela').style.display = 'block';
}

// Função para preencher a tabela de Compromissos Futuros
function preencherCompromissosFuturos(dados) {
  const tabela = document.querySelector('#compromissos-futuros tbody');
  tabela.innerHTML = ''; // Limpa a tabela antes de preencher

  // Preenche a tabela com os compromissos futuros
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td>${dados.titulo}</td>
    <td>${dados.validade}</td>
    <td>${dados.gestor}</td>
    <td>${dados.gestorsb}</td>
    <td>${dados.situacao}</td>
    <td>${dados.num_parcelas}</td>
    <td>${dados.proximos_pagamentos}</td>
  `;
  tabela.appendChild(tr);

  // Exibe a tabela de Compromissos Futuros
  document.getElementById('compromissos-futuros-tabela').style.display = 'block';
}

// Função para preencher a tabela de Relatório de Pagamentos
function preencherTabelaPagamentos(dados) {
  const tabela = document.querySelector('#relatorio-pagamentos tbody');
  tabela.innerHTML = ''; // Limpa a tabela antes de preencher

  // Preenche a tabela com os pagamentos
  dados.pagamentos.forEach(pagamento => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${pagamento.data_pagamento}</td>
      <td>${pagamento.valor}</td>
    `;
    tabela.appendChild(tr);
  });

  // Exibe a tabela de Relatório de Pagamentos
  document.getElementById('relatorio-pagamentos-tabela').style.display = 'block';
}

</script>





<div class="form-container" id="consultar" style="display:none;">
<h2 class="text-center mt-3">
    <span class="icon-before fas fa-box"></span> Lista de Produtos
</h2>
   <!-- Pesquisa -->
<div class="search-bar">
    <div class="search-filters">
        <!-- Campo de pesquisa por título ou descrição -->
        <input type="text" id="searchInput" class="input-field" placeholder="Digite o título ou descrição do contrato" oninput="searchContracts()">
        
        <!-- Filtro de status (Ativo/Inativo) -->
        <select id="statusSelect" class="input-field" onchange="searchContracts()">
            <option value="">Todos</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
        </select>
        
        <!-- Botão para abrir o modal de filtro -->
        <button class="btn-filters" onclick="openFilterModal()">Configurar Filtro</button>
    </div>
</div>



    <!-- Lista de Contratos -->
<div class="table-container-contratos">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
            <th><i class="fas fa-hashtag"></i> ID</th>
        <th><i class="fas fa-file-alt"></i> Nome</th>
        <th><i class="fas fa-align-left"></i> Descrição</th>
        <th><i class="fas fa-calendar-alt"></i> Validade</th>
        <th><i class="fas fa-circle"></i> Status</th>
        <th><i class="fas fa-cogs"></i> Ações</th>
            </tr>
        </thead>
        <tbody id="contractTableBody">
            <!-- Dados carregados via PHP -->
            <?php
            // Conexão com o banco de dados (supondo que $pdo já esteja configurado)

            // Verifica se há um filtro de situação via GET
            $situacao = isset($_GET['situacao']) ? $_GET['situacao'] : '';

            // Monta a consulta SQL com filtro opcional
            $sql = "SELECT * FROM gestao_contratos";
            if (!empty($situacao)) {
                $sql .= " WHERE situacao = :situacao";
            }
            $sql .= " ORDER BY validade "; //DESC ORDEM DECRESCENTE 

            $stmt = $pdo->prepare($sql);

            // Se houver filtro, vincula o valor da situação
            if (!empty($situacao)) {
                $stmt->bindParam(':situacao', $situacao, PDO::PARAM_STR);
            }

            $stmt->execute();

// Exibe os resultados
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $situacao = $row['situacao'];
    $situacaoClass = '';
    $situacaoIcon = '';
    $situacaoTextColor = '';
    
    // Verifica o valor de 'situacao' para aplicar o estilo adequado
    if ($situacao == 'Ativo') {
        $situacaoClass = 'Ativo'; // Classe para 'Ativo'
        $situacaoIcon = 'fa-arrow-up'; // Ícone da seta para cima
        $situacaoTextColor = 'green'; // Cor verde para 'Ativo'
    } else {
        $situacaoClass = 'Inativo'; // Classe para 'Inativo'
        $situacaoIcon = 'fa-arrow-down'; // Ícone da seta para baixo
        $situacaoTextColor = 'red'; // Cor vermelha para 'Inativo'
    }

    // Formatar a data de validade
    $validade = new DateTime($row['validade']);
    $validadeFormatted = $validade->format('d/m/Y');
    
    // Adicionando uma classe para a validade estilizada
    $validadeClass = '';
    $validadeTextColor = '';
    $validadeIcon = '';

    // Verifica se a data é válida, expirada ou próxima de expirar
    $today = new DateTime();
    $oneMonthLater = clone $today;
    $oneMonthLater->modify('+1 month'); // Cria uma data com o próximo mês

    if ($validade < $today) {
        // Data expirada
        $validadeClass = 'expired';
        $validadeTextColor = 'red';
        $validadeIcon = 'fa-times-circle';
    } elseif ($validade <= $oneMonthLater) {
        // Data próxima de expirar
        $validadeClass = 'approaching';
        $validadeTextColor = 'orange';
        $validadeIcon = 'fa-exclamation-circle';
    } else {
        // Data válida
        $validadeClass = 'valid';
        $validadeTextColor = 'green';
        $validadeIcon = 'fa-check-circle';
    }

    echo "<tr style='cursor:pointer;' onclick='showResumoProcesso(" . json_encode($row) . ")'>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['titulo']}</td>";
    echo "<td>{$row['descricao']}</td>";

    // Coloca a validade com a cor e o ícone correto
    echo "<td class='$validadeClass' style='color: $validadeTextColor;'>
            <i class='fas $validadeIcon'></i> 
            $validadeFormatted
          </td>";

    // Coloca a situação com a cor e o ícone correto
    echo "<td class='$situacaoClass' style='color: $situacaoTextColor;'>
            <i class='fas $situacaoIcon'></i> 
            $situacao
          </td>";

    // O botão "Visualizar" terá um evento independente
    echo "<td>";
    echo "<button class='btn btn-info btn-sm' onclick='openModal(" . json_encode($row) . "); event.stopPropagation();' title='Visualizar'>
            <i class='fas fa-eye'></i>
          </button>";
    echo "<button class='btn btn-primary btn-sm' onclick='generateReport()' title='Relatório'>
            <i class='fas fa-file-alt'></i>
          </button>";
    echo "<button class='btn btn-success btn-sm' onclick='editProcess(event, " . json_encode($row) . ")' title='Editar processo'>
            <i class='fas fa-edit'></i>
          </button>";
    echo "</td>";
    echo "</tr>";
}
?>
        </tbody>
    </table>
    </div>
    
</div>




<!-- Modal de Edição -->
<div class="modal fade" id="editProcessModal" tabindex="-1" role="dialog" aria-labelledby="editProcessModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProcessModalLabel">Editar Processo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Abas -->
                <ul class="nav nav-tabs" id="editModalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">Detalhes</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="edit-tab" data-toggle="tab" href="#edit" role="tab" aria-controls="edit" aria-selected="false">Editar</a>
                    </li>
                </ul>

                <div class="tab-content" id="editModalTabContent">
                    <!-- Primeira Aba: Detalhes do Contrato -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <h5 class="mt-3">Detalhes do Contrato</h5>
                        <p><strong>Título:</strong> <span id="contractTitulo"></span></p>
                        <p><strong>Descrição:</strong> <span id="contractDescricao"></span></p>
                        <p><strong>Validade:</strong> <span id="contractValidade"></span></p>
                        <p><strong>Situação:</strong> <span id="contractSituacao"></span></p>
                    </div>

                    <!-- Segunda Aba: Edição do Contrato -->
                    <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                        <h5 class="mt-3">Editar Contrato</h5>
                        <form id="editProcessForm">
                            <div class="form-group">
                                <label for="editTitulo">Título</label>
                                <input type="text" class="form-control" id="editTitulo" name="titulo" required>
                            </div>
                            <div class="form-group">
                                <label for="editDescricao">Descrição</label>
                                <textarea class="form-control" id="editDescricao" name="descricao" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="editValidade">Validade</label>
                                <input type="date" class="form-control" id="editValidade" name="validade" required>
                            </div>
                            <div class="form-group">
                                <label for="editSituacao">Situação</label>
                                <select class="form-control" id="editSituacao" name="situacao" required>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  // Função editar modal -->
<script src="./src/contratos/js/edit-process-modal.js"></script>







<!-- Modal exibi detalhes do contrato BOTAO VISUALIZAR DA TABELA -->
<div class="modal fade" id="modalContrato" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p><strong>Título:</strong> <span id="modalTituloContrato"></span></p>
                <p><strong>Descrição:</strong> <span id="modalDescricao"></span></p>
                <p><strong>Validade:</strong> <span id="modalValidade"></span></p>
                <p><strong>Nº SEI:</strong> <span id="modalSEI"></span></p>
                <p><strong>Gestor:</strong> <span id="modalGestor"></span></p>
                <p><strong>Fiscais:</strong> <span id="modalFiscais"></span></p>
                <p><strong>Valor do Contrato:</strong> R$ <span id="modalValorContrato"></span></p>
                <p><strong>Número de Parcelas:</strong> <span id="modalNumParcelas"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Configuração de Filtros -->
<div class="modal" id="filterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurar Filtros</h5>
                <button type="button" class="close" onclick="closeFilterModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-check">
                    <input type="radio" name="filterOption" class="form-check-input" id="filterSei" onchange="toggleFilterField('Digite o número SEI')">
                    <label class="form-check-label" for="filterSei">Filtrar por Nº SEI</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="filterOption" class="form-check-input" id="filterValor" onchange="toggleFilterField('Digite o valor')">
                    <label class="form-check-label" for="filterValor">Filtrar por Valor</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="filterOption" class="form-check-input" id="filterVigencia" onchange="toggleFilterField('Digite a vigência')">
                    <label class="form-check-label" for="filterVigencia">Filtrar por Vigência</label>
                </div>
                <p class="text-center mt-3">
                    <a href="#" onclick="clearFilters(event)">Limpar Filtros</a>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeFilterModal()">Fechar</button>
            </div>
        </div>
    </div>
</div>
<script src="./src/js/filtroModal.js"></script>

<!-- Lista de Agendamentos -->
<div>
        <div class="form-container " id="agenda">
            <!-- Formulário de Agendamento -->
            <div class="form-left  ">
                <h3>Agendar Tarefa</h3>
                <form id="formAgendamento" method="POST" action="salvar_agendamento.php">

                    <!-- Campo: Nome -->
                    <div class="form-group mb-3">
                        <label for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-control" placeholder="Digite o nome" required>
                    </div>
                    <!-- Campo: Descrição -->
                    <div class="form-group mb-3">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" class="form-control" placeholder="Digite a descrição" rows="3" required></textarea>
                    </div>
                    <!-- Campo: Data -->
                    <div class="form-group mb-3">
                        <label for="data_g">Data</label>
                        <input type="date" id="data_g" name="data_g" class="form-control" required>
                    </div>
                    <!-- Campo: E-mail -->
                    <div class="form-group mb-3">
                        <label for="email">E-mail para Envio</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Digite o e-mail" required>
                    </div>
                    <!-- Botão: Salvar -->
                    <button type="submit" class="btn btn-primary w-50">Salvar Agendamento</button>
                </form>
            </div>

<?php
        // Conexão com o banco de dados
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "gm_sicbd";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }
        // Recuperando os agendamentos do banco de dados
        $sql = "SELECT id, nome, descricao, data_g, email FROM agendamentos";
        $result = $conn->query($sql);
        $agendamentos = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data = $row['data_g'];
                $agendamentos[$data][] = $row; // Organiza os agendamentos por data
            }
        }
        $conn->close();
?>


<div class="form-right">
    <h3>Agendamentos de <?php echo date("F Y"); ?></h3> <!-- Exibe o mês e ano atual -->
    <div id="agendamentos" style="max-height: 500px; overflow-y: auto;">
        <div class="calendar-container">
            <?php
            // Exibindo o mês atual
            $currentMonth = date("m");
            $currentYear = date("Y");

            // Número de dias no mês atual
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

            // Começo do mês (dia da semana)
            $firstDayOfMonth = strtotime("$currentYear-$currentMonth-01");
            $firstDayWeekday = date("w", $firstDayOfMonth); // 0 (Domingo) a 6 (Sábado)

            // Cabeçalho da agenda
            echo '<table class="calendar-table">';
            echo '<tr>';
            echo '<th>Dom</th><th>Seg</th><th>Ter</th><th>Qua</th><th>Qui</th><th>Sex</th><th>Sáb</th>';
            echo '</tr><tr>';

            // Adiciona os dias em branco no início do mês
            for ($i = 0; $i < $firstDayWeekday; $i++) {
                echo '<td></td>';
            }

            // Exibe os dias do mês
            $day = 1;
            for ($i = $firstDayWeekday; $i < 7; $i++) {
                echo '<td>';
                echo '<span class="day-number" data-day="' . $day . '">' . $day . '</span>'; // Dia interativo
                $currentDate = "$currentYear-$currentMonth-" . str_pad($day, 2, '0', STR_PAD_LEFT);

                // Verifica se há agendamentos para esse dia
                if (isset($agendamentos[$currentDate])) {
                    echo '<div class="task-indicator" style="background-color: blue; color: white; font-size: 10px;">Tarefa Agendada</div>';
                }

                echo '</td>';
                $day++;
            }
            echo '</tr>';

            // Preenche o restante dos dias do mês
            while ($day <= $daysInMonth) {
                echo '<tr>';
                for ($i = 0; $i < 7; $i++) {
                    if ($day <= $daysInMonth) {
                        $currentDate = "$currentYear-$currentMonth-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                        echo '<td>';
                        echo '<span class="day-number" data-day="' . $day . '">' . $day . '</span>'; // Dia interativo

                        // Verifica se há agendamentos para esse dia
                        if (isset($agendamentos[$currentDate])) {
                            echo '<div class="task-indicator" style="background-color: blue; color: white; font-size: 10px;">Tarefa Agendada</div>';
                        }

                        echo '</td>';
                        $day++;
                    } else {
                        echo '<td></td>';
                    }
                }
                echo '</tr>';
            }
            echo '</table>';
            ?>
        </div>
    </div>
</div>
</div>
</div>



<!-- Modal para exibir as tarefas -->
<div id="taskModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h4>Agendamentos do Dia</h4>
        <div id="taskDetails"></div> <!-- Exibe as tarefas do dia selecionado -->
    </div>
</div>

<script src="./src/js/active.js"></script>

<!-- JavaScript para abrir o modal e exibir as tarefas -->
<script>
    // Variável para armazenar os agendamentos
    const agendamentos = <?php echo json_encode($agendamentos); ?>;

    // Modal
    const modal = document.getElementById("taskModal");
    const closeModal = document.getElementsByClassName("close-btn")[0];

    // Quando o usuário clica em um dia
    document.querySelectorAll(".day-number").forEach((dayElement) => {
        dayElement.addEventListener("click", function() {
            const selectedDay = this.innerText;
            const selectedDate = '<?php echo $currentYear . "-" . $currentMonth; ?>-' + ('0' + selectedDay).slice(-2);

            // Verificando se existem agendamentos para o dia
            if (agendamentos[selectedDate]) {
                const taskDetails = agendamentos[selectedDate].map(task => 
                    `<p><strong>${task.nome}</strong>: ${task.descricao}</p>`
                ).join("");
                document.getElementById("taskDetails").innerHTML = taskDetails;
                modal.style.display = "block";
            } else {
                document.getElementById("taskDetails").innerHTML = "<p>Não há tarefas agendadas para este dia.</p>";
                modal.style.display = "block";
            }
        });
    });

    // Fechar o modal quando o usuário clicar no "X"
    closeModal.onclick = function() {
        modal.style.display = "none";
    }

    // Fechar o modal quando o usuário clicar fora do conteúdo do modal
    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    }
</script>
<?php


// Incluir o código que já insere as notificações
include 'verificar_notificacoes.php';  // O código que já insere as notificações

// echo "Notificações inseridas com sucesso.";
?>
<!-- Ícone de Loading -->
<div class="loading" style="display:none;"></div>

</body>
</html>
<?php
include 'footer.php';
?>