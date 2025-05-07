<?php
session_start();

// Configuração e conexão com o banco de dados (usando PDO)
$dsn = 'mysql:host=localhost;dbname=gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

// Função para redirecionar com mensagem
function setMessageAndRedirect($type, $message, $location) {
    $_SESSION[$type] = $message;
    header("Location: $location");
    exit;
}

// Marcar notificação como lida
if (isset($_GET['mark_read']) && $_SESSION['username'] === 'contratos') {
    $notificationId = filter_var($_GET['mark_read'], FILTER_VALIDATE_INT);
    if ($notificationId === false) {
        setMessageAndRedirect('error', 'ID de notificação inválido.', 'index.php');
    }

    try {
        $sqlMarkRead = "UPDATE notificacoes SET situacao = 'lida' WHERE id = :id";
        $stmtMarkRead = $pdo->prepare($sqlMarkRead);
        $stmtMarkRead->execute(['id' => $notificationId]);
        setMessageAndRedirect('success', 'Notificação marcada como lida.', 'index.php');
    } catch (PDOException $e) {
        setMessageAndRedirect('error', 'Erro ao marcar notificação: ' . $e->getMessage(), 'index.php');
    }
}

// Processar cadastro de contrato
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_contrato'])) {
    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
    $validade = filter_input(INPUT_POST, 'validade', FILTER_SANITIZE_STRING);
    $assinatura = filter_input(INPUT_POST, 'assinatura', FILTER_SANITIZE_STRING);

    if (!$titulo || !$validade || !$assinatura) {
        setMessageAndRedirect('error', 'Campos obrigatórios não preenchidos.', 'cadastro_contrato.php');
    }

    // Inserir contrato
    try {
        $sql = "INSERT INTO gestao_contratos (titulo, descricao, assinatura, validade) 
                VALUES (:titulo, :descricao, :assinatura, :validade)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titulo' => $titulo,
            'descricao' => $descricao,
            'assinatura' => $assinatura,
            'validade' => $validade
        ]);

        // Inserir notificação
        $usuario = $_SESSION['username'];
        $setor = $_SESSION['setor'];
        $mensagem = "Contrato '{$titulo}' prestes a expirar.";
        $situacao = 'não lida';
        $dataNotificacao = date('Y-m-d H:i:s');

        // Verificar se já existe uma notificação
        $sqlVerificacao = "SELECT COUNT(*) FROM notificacoes WHERE username = :username AND mensagem = :mensagem";
        $stmtVerificacao = $pdo->prepare($sqlVerificacao);
        $stmtVerificacao->execute(['username' => $usuario, 'mensagem' => $mensagem]);

        if ($stmtVerificacao->fetchColumn() == 0) {
            $sqlNotificacao = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                               VALUES (:username, :setor, :mensagem, :situacao, :data_criacao)";
            $stmtNotificacao = $pdo->prepare($sqlNotificacao);
            $stmtNotificacao->execute([
                'username' => $usuario,
                'setor' => $setor,
                'mensagem' => $mensagem,
                'situacao' => $situacao,
                'data_criacao' => $dataNotificacao
            ]);
        }

        setMessageAndRedirect('success', 'Contrato cadastrado com sucesso!', 'index.php');
    } catch (PDOException $e) {
        setMessageAndRedirect('error', 'Erro ao cadastrar contrato: ' . $e->getMessage(), 'cadastro_contrato.php');
    }
}

// Buscar contratos próximos de expirar
$notificacoes = [];
try {
    $sqlNotificacoes = "SELECT * FROM gestao_contratos 
                        WHERE validade <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH) 
                        AND validade >= CURDATE()";
    $stmtNotificacoes = $pdo->query($sqlNotificacoes);
    $notificacoes = $stmtNotificacoes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro ao buscar notificações: " . $e->getMessage();
}

// Buscar detalhes do processo
$processDetails = null;
if (isset($_GET['processId'])) {
    $processId = filter_var($_GET['processId'], FILTER_VALIDATE_INT);
    if ($processId !== false) {
        try {
            $sqlProcesso = "SELECT * FROM gestao_contratos WHERE id = :id";
            $stmtProcesso = $pdo->prepare($sqlProcesso);
            $stmtProcesso->execute(['id' => $processId]);
            $processDetails = $stmtProcesso->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erro ao buscar detalhes do processo: " . $e->getMessage();
        }
    }
}

// Buscar contratos para o dropdown
$options = "";
try {
    $sqlContratos = "SELECT titulo FROM gestao_contratos";
    $stmtContratos = $pdo->query($sqlContratos);
    $contratos = $stmtContratos->fetchAll(PDO::FETCH_ASSOC);

    if ($contratos) {
        foreach ($contratos as $contrato) {
            $titulo = htmlspecialchars($contrato['titulo'], ENT_QUOTES, 'UTF-8');
            $options .= "<option value=\"$titulo\">$titulo</option>";
        }
    } else {
        $options = "<option value=\"\">Nenhum contrato encontrado</option>";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erro ao buscar contratos: " . $e->getMessage();
    $options = "<option value=\"\">Erro ao carregar contratos</option>";
}

include 'header.php';
// Incluir o código que já insere as notificações
include 'verificar_notificacoes.php';  // O código que já insere as notificações

// echo "Notificações inseridas com sucesso.";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    

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
    <!-- <div class="tab" data-tab="agenda" onclick="showTab('agenda')">
        <i class="fas fa-calendar-alt"></i> Agendamento
    </div> -->
    <div class="tab" data-tab="resumo_processo" onclick="showTab('resumo_processo')" style="display: none;">
        <i class="fas fa-info-circle"></i> Resumo
    </div>
    <div class="tab" data-tab="gerenciar" onclick="showTab('gerenciar')">
            <i class="fas fa-edit"></i> Gerenciar Contratos
        </div>
    <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">
        <i class="fas fa-file-alt"></i> Relatórios
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

<script src="./src/contratos/js/cadastro_contato.js"></script>

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


<div class="form-container" id="consultar" style="display:none;">
<h2 class="text-center mt-3">
    <span class="icon-before fas fa-box"></span> Lista de Forncedores
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
<!--  -->
<script src="./src/js/active.js"></script>



<!-- Ícone de Loading -->
<div class="loading" style="display:none;"></div>
<!-- Dentro do body, substitua a seção da aba "gerenciar" por: -->

<div class="form-container" id="gerenciar" style="display:none;">
<h2 id="contractTitleHeader">Pagamentos do</h2>
    <div class="button-group">
    <button class="btn-submit" onclick="savePayment()">Salvar Alterações</button>
   </div>
    <table id="contratosTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Mês</th>
                <th>Empenho</th>
                <th>Tipo</th>
                <th>Nota de Empenho</th>
                <th>Valor do Contrato</th>
                <th>Créditos Ativos</th>
                <th>SEI</th>
                <th>Nota Fiscal</th>
                <th>Envio Pagamento</th>
                <th>Vencimento da Fatura</th>
                <th>Valor Liquidado</th>
                <th>Valor Liquidado Ag</th>
                <th>Ordem Bancária</th>
                <th>Agência Bancária</th>
                <th>Data de Atualização</th>
                <th>Data de Pagamento</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="contratosTableBody">
            <!-- Dados serão preenchidos dinamicamente -->
        </tbody>
    </table>
</div>
<!--  FUNCTION DE API - CAMPOS EDITAVEIS DE TABELA E INSERÇÃO DE DADOS -->
<script src="./src/contratos/js/gerenciar-pagamentos.js"></script>

<div class="form-container" id="relatorio">
    <h2><i class="fas fa-file-alt"></i> Gerar Relatório</h2>
    <form id="relatorio-form">
        <div class="form-group">
            <div class="input-group-contratos">
                <!-- Seletor de contrato -->
                <div style="flex: 1;">
                    <label for="tipo_relatorio"><i class="fas fa-folder-open"></i> Nome do Contrato</label>
                    <select name="contrato" id="tipo_relatorio" onchange="mostrarTipoRelatorio()">
                        <option value="">Selecione o Contrato</option>
                        <?php echo $options; ?>
                    </select>
                </div>

                <!-- Seletor para relatórios de todos os contratos -->
                <div style="flex: 1;">
                    <label for="relatorio_todos"><i class="fas fa-globe"></i> Relatório de Todos os Contratos</label>
                    <select name="relatorio_todos" id="relatorio_todos" onchange="mostrarCamposRelatorioTodos()">
                        <option value="">Selecione o Tipo de Relatório</option>
                        <option value="mensal_todos">Relatório Mensal (Todos os Contratos)</option>
                        <option value="anual_todos">Relatório Anual (Todos os Contratos)</option>
                    </select>
                </div>

                <!-- Seletor de tipo de relatório (inicialmente oculto, para contratos individuais) -->
                <div id="tipo-relatorio-container" style="display: none; flex: 1;">
                    <label for="relatorio_tipo"><i class="fas fa-chart-line"></i> Relatório por Contratos</label>
                    <select name="relatorio_tipo" id="relatorio_tipo" onchange="mostrarCamposRelatorio()">
                        <option value="completo">Relatório Completo</option>
                        <!-- <option value="compromissos_futuros">Compromissos Futuros</option> -->
                        <option value="pagamentos">Relatório de Pagamentos</option>
                        <option value="mensal">Relatório Mensal</option>
                        <option value="anual">Relatório Anual</option>
                    </select>
                </div>

                <!-- Seletor de mês (oculto inicialmente, usado por ambos) -->
                <div id="mes-container" style="display: none; flex: 1;">
                    <label for="mes"><i class="fas fa-calendar"></i> Selecione o Mês</label>
                    <select name="mes" id="mes"></select>
                </div>

                <!-- Seletor de ano (oculto inicialmente, usado por ambos) -->
                <div id="ano-container" style="display: none; flex: 1;">
                    <label for="ano"><i class="fas fa-calendar"></i> Selecione o Ano</label>
                    <select name="ano" id="ano"></select>
                </div>
            </div>
        </div>

        <!-- Seção para agendamento de relatórios -->
        <!-- <div id="agendamento-container">
            <h3><i class="fas fa-clock"></i> Agendar Relatório</h3>
            <div class="email-group">
                <div style="flex: 1;">
                    <label for="email_destinatario_select">E-mails Salvos</label>
                    <select id="email_destinatario_select" name="email_destinatario_select" onchange="atualizarCampoEmail()">
                        <option value="">Selecione um e-mail salvo</option>
                        <?php echo $options_emails; ?>
                        <option value="novo">Digitar novo e-mail</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label for="email_destinatario">E-mail do Destinatário</label>
                    <input type="email" id="email_destinatario" name="email_destinatario" placeholder="Digite o e-mail" required>
                    <label style="display: none;" id="salvar_email_label">
                        <input type="checkbox" id="salvar_email" name="salvar_email"> Salvar este e-mail para uso futuro
                    </label>
                </div>
            </div>
            <label for="periodicidade">Periodicidade</label>
            <select id="periodicidade" name="periodicidade">
                <option value="diario">Diário</option>
                <option value="semanal">Semanal</option>
                <option value="mensal">Mensal</option>
            </select>
            <button type="button" class="btn-submit" onclick="agendarRelatorio()">Agendar Relatório</button>
        </div> -->

        <div class="button-group">
            <!-- Botão para gerar o relatório -->
            <button class="btn-submit" type="button" id="gerar-relatorio" onclick="gerarRelatorio()">Gerar Relatório</button>
            <!-- Botões para exportação -->
            <button class="btn-submit" type="button" id="btnExportPDF" onclick="exportarPDF()">Exportar para PDF</button>
            <button class="btn-submit" type="button" id="btnExportCSV" onclick="exportarCSV()">Exportar para CSV</button>
        </div>
    </form>

    <!-- Tabelas para Relatórios -->
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
            <tbody></tbody>
        </table>
    </div>

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
                    <th>Evento</th>
                    <th>Descrição</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Categoria</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="relatorio-pagamentos-tabela" style="display: none;">
        <table id="relatorio-pagamentos" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Título do Contrato</th>
                    <th>Mês</th>
                    <th> Data de pagamento </th>
                    <th>Pagamentos Efetuados</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="relatorio-mensal-tabela" style="display: none;">
        <table id="relatorio-mensal" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Título do Contrato</th>
                    <th>Nº de Parcelas</th>
                    <th>Histórico de Pagamentos (Data)</th>
                    <th>Histórico de Pagamentos (Valor)</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="relatorio-anual-tabela" style="display: none;">
        <table id="relatorio-anual" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Título do Contrato</th>
                    <th>Ano</th>
                    <th>Nº de Parcelas</th>
                    <th>Total Pago no Ano</th>
                    <th>Quantidade de Pagamentos</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="relatorio-mensal-todos-tabela" style="display: none;">
        <table id="relatorio-mensal-todos" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Título do Contrato</th>
                    <th>Nº de Parcelas</th>
                    <th>Histórico de Pagamentos (Data)</th>
                    <th>Histórico de Pagamentos (Valor)</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="relatorio-anual-todos-tabela" style="display: none;">
        <table id="relatorio-anual-todos" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Título do Contrato</th>
                    <th>Ano</th>
                    <th>Nº de Parcelas</th>
                    <th>Total Pago no Ano</th>
                    <th>Quantidade de Pagamentos</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script src="./src/contratos/js/relatorio-avancado.js"></script>
</body>
</html>
