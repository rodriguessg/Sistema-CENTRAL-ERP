<?php
    // Iniciar sessão
    session_start();

    // Configurar logging de erros
    // ini_set('log_errors', 1);
    // ini_set('error_log', __DIR__ . '/logs/error.log');

    // Conectar ao banco de dados
    $host = 'localhost';
    $dbname = 'gm_sicbd';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        error_log("Erro ao conectar ao banco: " . $e->getMessage());
        die("Erro ao conectar ao banco de dados. Consulte o administrador.");
    }

    // Função para redirecionar com mensagem
    function setMessageAndRedirect($type, $message, $location) {
        $_SESSION[$type] = $message;
        header("Location: $location");
        exit;
    }

    // Verificar sessão
    if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
        setMessageAndRedirect('error', 'Sessão inválida. Faça login.', 'index.php');
    }

    // Gerar token CSRF
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;

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
            error_log("Erro ao marcar notificação: " . $e->getMessage());
            setMessageAndRedirect('error', 'Erro ao marcar notificação.', 'index.php');
        }
    }

    // Processar cadastro de contrato
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar_contrato'])) {
        // Validar CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            setMessageAndRedirect('error', 'Token CSRF inválido.', 'cadastrar_contratos.php');
        }

        // Campos obrigatórios
        $requiredFields = [
            'titulo', 'validade', 'assinatura', 'SEI', 'gestor', 'gestorsb',
            'fiscais', 'valor_contrato', 'valor_aditivo', 'publicacao',
            'date_service', 'contatos', 'n_despesas', 'valor_nf'
        ];
        $data = [];
        foreach ($requiredFields as $field) {
            $data[$field] = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
            if (empty($data[$field])) {
                setMessageAndRedirect('error', "Campo '$field' é obrigatório.", 'cadastrar_contratos.php');
            }
        }

        // Campos opcionais
        $data['descricao'] = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING) ?: null;
        $data['fonte'] = filter_input(INPUT_POST, 'fonte', FILTER_SANITIZE_STRING) ?: null;
        $data['objeto'] = filter_input(INPUT_POST, 'objeto', FILTER_SANITIZE_STRING) ?: null;
        $data['servicos'] = filter_input(INPUT_POST, 'servicos', FILTER_SANITIZE_STRING) ?: null;
        $data['num_parcelas'] = filter_input(INPUT_POST, 'num_parcelas', FILTER_VALIDATE_INT) ?: null;
        $data['account_bank'] = filter_input(INPUT_POST, 'account_bank', FILTER_SANITIZE_STRING) ?: null;

        // Validar formatos
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['validade'])) {
            setMessageAndRedirect('error', 'Data de validade inválida.', 'cadastrar_contratos.php');
        }
        if (!is_numeric($data['valor_contrato']) || $data['valor_contrato'] < 0) {
            setMessageAndRedirect('error', 'Valor do contrato deve ser um número positivo.', 'cadastrar_contratos.php');
        }
        if (!is_numeric($data['valor_aditivo']) || $data['valor_aditivo'] < 0) {
            setMessageAndRedirect('error', 'Valor aditivo deve ser um número positivo.', 'cadastrar_contratos.php');
        }

        try {
            $sql = "INSERT INTO gestao_contratos (
                titulo, descricao, assinatura, validade, SEI, gestor, gestorsb, fiscais,
                valor_contrato, valor_aditivo, fonte, objeto, publicacao, date_service,
                contatos, n_despesas, valor_nf, servicos, num_parcelas, account_bank
            ) VALUES (
                :titulo, :descricao, :assinatura, :validade, :SEI, :gestor, :gestorsb, :fiscais,
                :valor_contrato, :valor_aditivo, :fonte, :objeto, :publicacao, :date_service,
                :contatos, :n_despesas, :valor_nf, :servicos, :num_parcelas, :account_bank
            )";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);

            // Inserir notificação
            $usuario = $_SESSION['username'];
            $setor = $_SESSION['setor'];
            $mensagem = "Contrato '{$data['titulo']}' prestes a expirar.";
            $situacao = 'não lida';
            $dataNotificacao = date('Y-m-d H:i:s');

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
            error_log("Erro ao cadastrar contrato: " . $e->getMessage());
            setMessageAndRedirect('error', 'Erro ao cadastrar contrato.', 'cadastrar_contratos.php');
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
        error_log("Erro ao buscar notificações: " . $e->getMessage());
        $_SESSION['error'] = "Erro ao buscar notificações.";
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
                error_log("Erro ao buscar processo: " . $e->getMessage());
                $_SESSION['error'] = "Erro ao buscar detalhes do processo.";
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
        error_log("Erro ao buscar contratos: " . $e->getMessage());
        $_SESSION['error'] = "Erro ao buscar contratos.";
        $options = "<option value=\"\">Erro ao carregar contratos</option>";
    }

    include 'header.php';
    include 'verificar_notificacoes.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Contratos</title>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script> -->
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
    <link rel="stylesheet" href="src/contratos/style/consultar-contratos.css">
    <link rel="stylesheet" href="src/contratos/style/cadastro-contratos.css">   
    <link rel="stylesheet" href="src/contratos/style/analise.css">
     <link rel="stylesheet" href="src/contratos/style/gerenciar-pagamentos.css">
      <link rel="stylesheet" href="src/contratos/style/relatorio.css">
         <link rel="stylesheet" href="src/contratos/style/modal-contratos.css">
    <!-- Carregar jQuery (se necessário) -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    <!-- Carregar Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
     <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
</head>

<body class="caderno">
   
 
        <div class="tabs">
            <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">
                <i class="fas fa-plus-circle"></i>
                <div class="tab-content">
                    <p class="tab-title">Cadastro</p>
                    <p class="tab-description">Cadastramento de Contratos</p>
                </div>
            </div>

            <div class="tab" data-tab="consultar" onclick="showTab('consultar')">
                <i class="fas fa-search"></i>
                <div class="tab-content">
                    <p class="tab-title">Consultar</p>
                    <p class="tab-description">Consultar Contratos</p>
                </div>
            </div>

            <div class="tab" data-tab="gerenciar" onclick="showTab('gerenciar')">
                <i class="fas fa-edit"></i>
                <div class="tab-content">
                    <p class="tab-title">Pagamentos</p>
                    <p class="tab-description">Gerenciar Pagamentos</p>
                </div>
            </div>

            <div class="tab" data-tab="prestacao" onclick="showTab('prestacao')">
                <i class="fas fa-money-check-alt"></i>
                <div class="tab-content">
                    <p class="tab-title">Prestação</p>
                    <p class="tab-description">Prestação de Contas</p>
                </div>
            </div>

            <div class="tab" data-tab="andamento" onclick="showTab('andamento')">
                <i class="fas fa-sync-alt"></i>
                <div class="tab-content">
                    <p class="tab-title">Andamento</p>
                    <p class="tab-description">Andamento dos Contratos</p>
                </div>
            </div>

            <div class="tab" data-tab="relatorio" onclick="showTab('relatorio')">
                <i class="fas fa-file-alt"></i>
                <div class="tab-content">
                    <p class="tab-title">Relatórios</p>
                    <p class="tab-description">Relatórios de Contratos</p>
                </div>
            </div>
        </div>



 
<!-- Formulário de Cadastro de Contratos -->
<div class="form-container" id="cadastrar" style="display:block;">
    <div class="novo-contrato-container">
        <h1 class="novo-contrato-titulo">
            <i class="fas fa-clipboard-list" id="icon-novo"></i> Cadastrar Contratos
        </h1>
        <p class="consultar-subtitulo">Preencha os dados do contrato abaixo:</p>
    </div>

    <form action="cadastrar_contratos.php" method="POST" enctype="multipart/form-data" class="form-cadastro">
        <h1 class="cadastrar-contratos">
            <i class="fas fa-plus-circle" id="icon-cadastrar"></i> Novo Contrato
        </h1>

        <div class="cadastro">
            <div class="grupo1">
                <!-- Título do Contrato -->
                <div class="mb-3">
                    <label for="titulo" class="form-label">
                        Título do Contrato <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <input type="text" id="titulo" name="titulo" class="form-control" placeholder="Coloque o título do contrato" required>
                        <i class="fas fa-pencil-alt"></i>
                    </div>
                </div>

                <!-- Conta Bancária -->
                <div class="mb-3">
                    <label for="account-bank" class="form-label">Conta Bancária</label>
                    <div class="input-icon">
                        <input type="text" id="account-bank" name="account-bank" class="form-control" placeholder="Digite o número e agência da conta bancária" required >
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>

                <!-- Fonte -->
                <div class="mb-3">
                    <label for="fonte" class="form-label">Fonte</label>
                    <div class="input-icon">
                        <input type="text" id="fonte" name="fonte" class="form-control" placeholder="Digite a fonte" required >
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>

                <!-- Nº SEI -->
                <div class="mb-3">
                    <label for="SEI" class="form-label">
                        Nº SEI <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <input type="text" id="SEI" name="SEI" class="form-control" placeholder="Digite o número do SEI" required>
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>

                <!-- Objeto -->
                <div class="mb-3">
                    <label for="objeto" class="form-label">
                        Objeto <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <input type="text" id="objeto" name="objeto" class="form-control" placeholder="Descreva o objeto do contrato" required>
                        <i class="fas fa-cogs"></i>
                    </div>
                </div>

                <!-- Campo de Garantia (select com valores 0%, 5%, 10% e 20%) -->
                <div class="mb-3">
                    <label for="garantia" class="form-label">
                        Garantia <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <select id="garantia" name="garantia" class="form-control" onchange="atualizarValorContrato()">
                            <option value="0">0%</option>
                            <option value="5">5%</option>
                            <option value="10">10%</option>
                            <option value="20">20%</option>
                        </select>
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>

               <!-- Nova Categoria do Contrato -->
                <div class="mb-3">
                    <label for="categoria" class="form-label">
                        Categoria do Contrato <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <select id="categoria" name="categoria" class="form-control">
                            <option value="obra">Obra</option>
                            <option value="servico">Serviço</option>
                        </select>
                        <i class="fas fa-list"></i>
                    </div>
                </div>

            </div>


             <div class="grupo2">
                    <!-- Contatos -->
                <div class="mb-3">
                    <label for="contatos" class="form-label">
                        Contatos <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <input type="text" id="contatos" name="contatos" class="form-control" placeholder="Digite o número de contato ou email " required>
                        <i class="fas fa-phone-alt"></i>
                    </div>
                </div>

                <!-- Natureza de Despesas -->
                <div class="mb-3">
                    <label for="n_despesas" class="form-label">Natureza de Despesas <span class="text-danger">*</span></label>
                    <div class="input-icon">
                        <input type="text" id="n_despesas" name="contatos" class="form-control" placeholder="Digite a natureza de despesa" required>
                        <i class="fas fa-phone-alt"></i>
                    </div>
                </div>
                <!-- Vigência -->
                <div class="mb-3">
                    <label for="validade" class="form-label">
                        Vigência <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <input type="date" id="validade" name="validade" class="form-control" required onchange="atualizarParcelas()">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>

                <!-- Data de Publicação -->
                <div class="mb-3">
                    <label for="publicacao" class="form-label">Data de Publicação <span class="text-danger">*</span></label>
                    <div class="input-icon">
                        <input type="date" id="publicacao" name="publicacao" class="form-control" required >
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>

                <!-- Data de Serviço -->
                <div class="mb-3">
                    <label for="date_service" class="form-label">Data de Serviço <span class="text-danger">*</span></label>
                    <div class="input-icon">
                        <input type="date" id="date_service" name="date_service" class="form-control" required >
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>

                <!-- Valor da Nota Fiscal -->
                <div class="mb-3">
                    <label for="valor-valor" class="form-label">Valor da Nota fiscal <span class="text-danger">*</span></label>
                    <div class="input-icon">
                        <input type="text" id="valor-NF" name="valor" class="form-control" placeholder="Digite o número da nota fiscal" required>
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>

                <!-- Valor do Contrato -->
                <div class="mb-3">
                    <label for="valor-contrato" class="form-label">
                        Valor do Contrato <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <input type="text" id="valor-contrato" name="valor_contrato" class="form-control" placeholder="Digite o valor do contrato" required>
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>

            <div class="grupo2">
                <!-- Gestor -->
                <div class="mb-3">
                    <label for="gestor" class="form-label">
                        Gestor <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <input type="text" id="gestor" name="gestor" class="form-control" placeholder="Digite o nome do gestor" required oninput="verificarGestorEFiscal()">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <!-- Gestor Substituto -->
                <div class="mb-3">
                    <label for="gestorsb" class="form-label">
                        Gestor Substituto <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <input type="text" id="gestorsb" name="gestorsb" class="form-control" placeholder="Digite o nome do gestor substituto" required>
                        <i class="fas fa-user-slash"></i>
                    </div>
                </div>

                <!-- Fiscais -->
                <div class="mb-3">
                    <label for="fiscais" class="form-label">
                        Fiscais <span class="text-danger">*</span>
                    </label>
                    <div class="input-icon">
                        <input type="text" id="fiscais" name="fiscais" class="form-control" placeholder="Digite os fiscais responsáveis" required oninput="verificarGestorEFiscal()">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                </div>

                <!-- Nº Portaria Gestor -->
                <div class="mb-3" id="gestor-portaria-container" style="display:none;">
                    <label for="gestor_portaria" class="form-label">
                        Nº Portaria Gestor
                    </label>
                    <div class="input-icon">
                        <input type="text" id="gestor_portaria" name="gestor_portaria" class="form-control" placeholder="Digite o número da portaria do gestor">
                        <i class="fas fa-key"></i>
                    </div>
                </div>

                <!-- Nº Portaria Fiscais -->
                <div class="mb-3" id="fiscal-portaria-container" style="display:none;">
                    <label for="fiscal_portaria" class="form-label">
                        Nº Portaria Fiscais
                    </label>
                    <div class="input-icon">
                        <input type="text" id="fiscal_portaria" name="fiscal_portaria" class="form-control" placeholder="Digite o número da portaria dos fiscais">
                        <i class="fas fa-key"></i>
                    </div>
                </div>

            
            </div>

           
        </div>

        <div class="button-group">
            <button class="btn-submit-adicionar" type="button" class="btn-submit" onclick="toggleComplementares()">
                <i class="fas fa-save blue-icon"></i> Adicionar Informações Complementares
            </button>

            <button type="submit" name="cadastrar_contrato" class="btn-submit">
                <i class="fas fa-plus-circle white-icon"></i> Cadastrar Contrato
            </button>
        </div>

        <!-- Informações Complementares -->
        <div id="complementares" style="display:none;">
            <div class="mb-4">
                <input type="checkbox" id="parcelamento" name="parcelamento" onchange="toggleParcelas()">
                <label for="parcelamento">Este contrato é um parcelamento?</label>
                <input type="checkbox" id="outros" name="outros" onchange="toggleOutros()">
                <label for="outros">Outros</label>
            </div>

            <div class="mb-3" id="parcelas-container" style="display:none;">
                <label for="num-parcelas" class="form-label">Número de Parcelas</label>
                <input type="number" id="num-parcelas" name="num_parcelas" class="form-control">
            </div>

            <div class="mb-3" id="outros-container" style="display:none;">
                <label for="servicos" class="form-label">Escolha os serviços</label>
                <select id="servicos" name="servicos" class="form-control">
                    <option value="servico1">Serviço 1</option>
                    <option value="servico2">Serviço 2</option>
                    <option value="servico3">Serviço 3</option>
                    <option value="servico4">Serviço 4</option>
                </select>
            </div>

            <div class="mb-3" id="aditivos-container">
                <label for="descricao" class="form-label">Observação</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="3" required></textarea>
            </div>
        </div>

        <input type="hidden" id="assinatura" name="assinatura">
    </form>
</div>

<script>
// Função que verifica se o Gestor e Fiscais foram preenchidos
function verificarGestorEFiscal() {
    const gestor = document.getElementById('gestor').value;
    const fiscais = document.getElementById('fiscais').value;
    const gestorPortariaContainer = document.getElementById('gestor-portaria-container');
    const fiscalPortariaContainer = document.getElementById('fiscal-portaria-container');

    // Se o Gestor for preenchido, mostrar o campo de Nº Portaria Gestor
    if (gestor) {
        gestorPortariaContainer.style.display = 'block';
    } else {
        gestorPortariaContainer.style.display = 'none';
    }

    // Se os Fiscais forem preenchidos, mostrar o campo de Nº Portaria Fiscais
    if (fiscais) {
        fiscalPortariaContainer.style.display = 'block';
    } else {
        fiscalPortariaContainer.style.display = 'none';
    }
}

// // Função que atualiza o valor do contrato considerando a garantia selecionada
// function atualizarValorContrato() {
//     const valorContratoInput = document.getElementById('valor-contrato');
//     const garantiaSelect = document.getElementById('garantia');
//     const valorContrato = parseFloat(valorContratoInput.value.replace(/[^0-9.-]+/g,"")); // Remove símbolos, se houver
//     const garantiaPercentual = parseInt(garantiaSelect.value);

//     if (!isNaN(valorContrato)) {
//         const valorGarantia = (valorContrato * garantiaPercentual) / 100;
//         const novoValorContrato = valorContrato - valorGarantia;

//         // Atualiza o campo de valor do contrato com o novo valor após o desconto
//         valorContratoInput.value = novoValorContrato.toFixed(2);
//     }
// }
</script>




 <div class="form-container" id="consultar">

 <div class="novo-contrato-container">
    
<h2 class="novo-contrato-titulo">
    <span class="icon-before fas fa-search"></span> Consultar Contratos
</h2>
<p class="consultar-subtitulo">Pesquise e visualize os contratos cadastrados no sistema.</p>
</div>
        <!-- Pesquisa -->
        <div class="search-bar">
            <div class="search-filters">
                <input type="text" id="searchInput" class="input-field" placeholder="Digite o título ou descrição do contrato" oninput="searchContracts()">
                <select id="statusSelect" class="input-field" onchange="searchContracts()">
                    <option value="">Todos</option>
                    <option value="Ativo">Ativo</option>
                    <option value="Inativo">Inativo</option>
                    <option value="Renovado">Renovado</option>
                    <option value="Encerrado">Encerrado</option>
                </select>
                <button class="btn-filters" onclick="openFilterModal()">Configurar Filtro</button>
            </div>
        </div>
        <!-- Lista de Contratos -->
        <div class="table-container-contratos">
            <h2 class="titulo-tabela">
    <i class="fas fa-box"></i> Lista de Contratos
</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-file-alt"></i> Nome</th>
                        <th><i class="fas fa-align-left"></i> Descrição</th>
                        <th><i class="fas fa-calendar-alt"></i> Vigência</th>
                        <th><i class="fas fa-circle"></i> Status</th>
                        <th><i class="fas fa-cogs"></i> Ações</th>
                    </tr>
                </thead>
                <tbody id="contractTableBody"></tbody>
            </table>
        </div>
    </div>
 </div>
    <!-- Modal de Visualização -->
    <div class="modal fade" id="modalContrato" tabindex="-1" aria-labelledby="modalContratoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalContratoLabel">Detalhes do Contrato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Título:</strong> <span id="modalTituloContrato"></span></p>
                    <p><strong>Descrição:</strong> <span id="modalDescricao"></span></p>
                    <p><strong>Validade:</strong> <span id="modalValidade"></span></p>
                    <p><strong>SEI:</strong> <span id="modalSEI"></span></p>
                    <p><strong>Gestor:</strong> <span id="modalGestor"></span></p>
                    <p><strong>Fiscais:</strong> <span id="modalFiscais"></span></p>
                    <p><strong>Valor do Contrato:</strong> <span id="modalValorContrato"></span></p>
                    <p><strong>Número de Parcelas:</strong> <span id="modalNumParcelas"></span></p>
                    <p><strong>Valor Aditivo:</strong> <span id="modalValorAditivo"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
  <!-- Modal de Edição -->
 <div class="modal fade" id="modalEditContrato" tabindex="-1" aria-labelledby="modalEditContratoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditContratoLabel">Editar Contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formEditContrato">
                    <!-- ID do Contrato (campo oculto) -->
                    <input type="hidden" id="id_contrato" name="id_contrato">

                    <!-- Título do contrato -->
                    <div class="mb-3">
                        <label for="titulos" class="form-label">Título</label>
                        <input type="titulos" class="form-control" id="titulos" name="titulos" required>
                    </div>

                    <!-- Descrição do contrato -->
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        
                        <textarea class="form-control" id="descricao" name="descricao" required></textarea>
                    </div>

                    <!-- Validade do contrato -->
                    <div class="mb-3">
                        <label for="validades" class="form-label">Validade</label>
                        <input type="date" class="form-control" id="validades" name="validades" required>
                    </div>

                    <!-- Situação do contrato -->
                    <div class="mb-3">
                        <label for="situacao" class="form-label">Situação</label>
                        <select class="form-select" id="situacao" name="situacao" required>
                            <option value="Ativo">Ativo</option>
                             <option value="Renovado">Renovado</option>
                            <option value="Inativo">Inativo</option>
                            <option value="Encerrado">Encerrado</option>
                        </select>
                    </div>

                    <!-- Valores aditivos -->
                    <div class="mb-3">
                        <label class="form-label">Valores Aditivos</label>
                        <div id="aditivos-container3"></div>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="addAditivo()">Adicionar Aditivo</button>
                    </div>

                    <!-- Botão para salvar as alterações -->
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
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

      <div id="andamento" class="form-container" style="display: none;">
        <div class="header-gradient">
    <h2 class="mb-1 d-flex align-items-center">
        <i class="bi bi-kanban me-2"></i>
        Andamento dos Contratos
    </h2>
    <p class="mb-0 opacity-75 small">Visualize e gerencie as etapas do contrato selecionado</p>
</div>

<div class="progress-container">
    <h5 class="mb-2">Progresso do Contrato</h5>
    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="progress-label">
        <span>0 de 0 etapas concluídas</span>
        <span>0%</span>
    </div>
</div>
           <div class="add-step-form">
            <div class="row g-3">
                  <option value="">Selecione um contrato</option>
                <div class="col-md-6">
                   
                    <div class="form-floating">
                         <select id="contractSelect" class="form-select" onchange="exibirFluxoContratos()">
                   
                    <?php
                    try {
                        $pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $stmt = $pdo->query("SELECT id, titulo FROM gestao_contratos");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$row['id']}'>" . htmlspecialchars($row['titulo']) . "</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<option value=''>Erro ao carregar contratos</option>";
                    }
                    ?>
                </select>
                        <label for="contractSelect">Selecione o Contrato</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="newStepInput" placeholder="Digite o nome da nova etapa">
                        <label for="newStepInput">Nova Etapa</label>
                    </div>
                </div>
                <div class="col-12">
                    <button class="nova-etapa" type="button" id="addStepBtn">
                        <i class="bi bi-plus-lg me-1"></i> Adicionar Nova Etapa
                    </button>
                </div>
            </div>
        </div>
            <!-- Substitua seu div timeline por este -->
<div class="contract-timeline">
    <div id="timeline"></div>
</div>
        </div>



 <!-- Ícone de Loading -->
 <div class="loading" style="display:none;"></div>
 <!-- Dentro do body, substitua a seção da aba "gerenciar" por: -->


   <div class="form-container" id="gerenciar" style="display:none;">
  <div class="novo-contrato-container">
    <h2 id="contractTitleHeader" class="payment-title">
        <i class="fa fa-credit-card"></i> Parcelas de Contratos
    </h2>
    <p id="contractSubtitle" class="consultar-subtitulo">Abaixo você visualiza todas as parcelas de contratos.</p>
</div>


     <table id="contratosTable" class="table table-bordered">
        <thead>
        
        </thead>
        <tbody id="contratosTableBody">
            <!-- Dados serão preenchidos dinamicamente -->
        </tbody>
     </table>
    
    </div>

<?php
// Configuração da conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    // Conexão com o banco de dados usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Consultando contratos encerrados
    $sql = "SELECT id, titulo, validade, valor_contrato, data_cadastro 
            FROM gestao_contratos 
            WHERE situacao = 'encerrado'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $contratos = $stmt->fetchAll();

    // Inicializa variável para evitar undefined variable
    $contrato = null;
    $prestacao = null;

    // Se um contrato for selecionado, buscar os dados do contrato e criar/verificar registro na prestacao_contas
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['contrato_id'])) {
        $contrato_id = filter_input(INPUT_POST, 'contrato_id', FILTER_VALIDATE_INT);
        if ($contrato_id !== false) {
            // Buscar dados do contrato
            $sql_contrato = "SELECT id, titulo, valor_contrato, data_cadastro, validade 
                           FROM gestao_contratos 
                           WHERE id = :id AND situacao = 'encerrado'";
            $stmt_contrato = $pdo->prepare($sql_contrato);
            $stmt_contrato->bindParam(':id', $contrato_id, PDO::PARAM_INT);
            $stmt_contrato->execute();
            $contrato = $stmt_contrato->fetch();

            if ($contrato) {
                // Verificar se já existe registro na prestacao_contas
                $sql_prestacao = "SELECT id, status FROM prestacao_contas WHERE contrato_id = :contrato_id";
                $stmt_prestacao = $pdo->prepare($sql_prestacao);
                $stmt_prestacao->bindParam(':contrato_id', $contrato_id, PDO::PARAM_INT);
                $stmt_prestacao->execute();
                $prestacao = $stmt_prestacao->fetch();

                // Se não existe, criar registro com status Pendente
                if (!$prestacao) {
                    $sql_insert = "INSERT INTO prestacao_contas (contrato_id, status) 
                                 VALUES (:contrato_id, 'Pendente')";
                    $stmt_insert = $pdo->prepare($sql_insert);
                    $stmt_insert->bindParam(':contrato_id', $contrato_id, PDO::PARAM_INT);
                    $stmt_insert->execute();
                    $prestacao = ['id' => $pdo->lastInsertId(), 'status' => 'Pendente'];
                }
            }
        }
    }
} catch (PDOException $e) {
    error_log("Erro de conexão: " . $e->getMessage());
    die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
}
?>
<div class="form-container" id="prestacao">

  <div class="container">
        <h2 class="mb-4">Prestação de Contas</h2>

        <!-- Tabela de Contratos -->
        <div class="table-container">
            <h4>Contratos Encerrados</h4>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Título</th>
                        <th scope="col">Validade</th>
                        <th scope="col">Valor (R$)</th>
                        <th scope="col">Status Prestação</th>
                        <th scope="col">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($contratos)): ?>
                        <?php foreach ($contratos as $c): ?>
                            <?php
                            // Buscar status da prestação para cada contrato
                            $sql_status = "SELECT status FROM prestacao_contas WHERE contrato_id = :contrato_id";
                            $stmt_status = $pdo->prepare($sql_status);
                            $stmt_status->bindParam(':contrato_id', $c['id'], PDO::PARAM_INT);
                            $stmt_status->execute();
                            $status_prestacao = $stmt_status->fetchColumn() ?: 'Pendente';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($c['titulo']); ?></td>
                                <td><?= htmlspecialchars($c['validade']); ?></td>
                                <td>R$ <?= number_format($c['valor_contrato'], 2, ',', '.'); ?></td>
                                <td>
                                    <?php
                                    $badge_class = match ($status_prestacao) {
                                        'Pendente' => 'bg-warning',
                                        'Em Andamento' => 'bg-primary',
                                        'Concluída' => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badge_class; ?>"><?= htmlspecialchars($status_prestacao); ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary select-contrato" 
                                            data-id="<?= htmlspecialchars($c['id']); ?>" 
                                            data-titulo="<?= htmlspecialchars($c['titulo']); ?>" 
                                            data-valor="<?= number_format($c['valor_contrato'], 2, '.', ''); ?>" 
                                            data-inicio="<?= htmlspecialchars($c['data_cadastro']); ?>" 
                                            data-validade="<?= htmlspecialchars($c['validade']); ?>"
                                            data-status="<?= htmlspecialchars($status_prestacao); ?>"
                                            aria-label="Selecionar contrato <?= htmlspecialchars($c['titulo']); ?>">
                                        Selecionar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Nenhum contrato encontrado</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Formulário de Prestação de Contas -->
        <div class="form-container fade-in" id="form-prestacao-container">
            <h4>Detalhes do Contrato Selecionado</h4>
            <div id="contrato-detalhes">
                <p><strong>Nome do Contrato:</strong> <span id="detalhe-titulo"></span></p>
                <p><strong>Valor do Contrato:</strong> R$ <span id="detalhe-valor"></span></p>
                <p><strong>Data de Início:</strong> <span id="detalhe-inicio"></span></p>
                <p><strong>Data de Encerramento:</strong> <span id="detalhe-validade"></span></p>
            </div>

            <h4 class="mt-4">Realizar Prestação de Contas</h4>
            <form id="form-prestacao" method="POST" action="processar_prestacao.php">
                <input type="hidden" name="contrato_id" id="contrato_id">

                <div class="mb-3">
                    <label for="valor_pago" class="form-label">Valor Pago:</label>
                    <input type="number" class="form-control" id="valor_pago" name="valor_pago" step="0.01" required 
                           aria-describedby="valor_pago_error">
                    <div class="error" id="valor_pago_error"></div>
                </div>

                <div class="mb-3">
                    <label for="descricaoprestacao" class="form-label">Descrição da Prestação de Contas:</label>
                    <textarea class="form-control" id="descricaoprestacao" name="descricaoprestacao" rows="4" required 
                              aria-describedby="descricao_error"></textarea>
                    <div class="error" id="descricao_error"></div>
                </div>

                <div class="mb-3">
                    <label for="data_pagamento" class="form-label">Data de Pagamento:</label>
                    <input type="date" class="form-control" id="data_pagamento" name="data_pagamento" required 
                           aria-describedby="data_pagamento_error">
                    <div class="error" id="data_pagamento_error"></div>
                </div>

                <div class="mb-3">
                    <label for="prestacao_status" class="form-label">Status da Prestação:</label>
                    <select class="form-control" id="prestacao_status" name="prestacao_status" required>
                        <option value="Pendente">Pendente</option>
                        <option value="Em Andamento">Em Andamento</option>
                        <option value="Concluída">Concluída</option>
                    </select>
                </div>


                <button type="submit" class="btn btn-success">Salvar Prestação de Contas</button>
                <button type="button" class="btn btn-secondary ms-2" id="cancelar-form">Cancelar</button>
            </form>
        </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Mostrar formulário ao selecionar contrato
            $('.select-contrato').click(function () {
                const id = $(this).data('id');
                const titulo = $(this).data('titulo');
                const valor = $(this).data('valor');
                const inicio = $(this).data('inicio');
                const validade = $(this).data('validade');
                const status = $(this).data('status');

                $('#contrato_id').val(id);
                $('#detalhe-titulo').text(titulo);
                $('#detalhe-valor').text(parseFloat(valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
                $('#detalhe-inicio').text(inicio);
                $('#detalhe-validade').text(validade);
                $('#valor_pago').val(valor);
                $('#prestacao_status').val(status);
                $('#form-prestacao-container').fadeIn();
                $('#form-prestacao').trigger('reset'); // Reseta campos não preenchidos
                $('#valor_pago').val(valor); // Restaura valor após reset
                $('#prestacao_status').val(status); // Restaura status após reset
                $('.error').text(''); // Limpa mensagens de erro
            });

            // Esconder formulário ao clicar em Cancelar
            $('#cancelar-form').click(function () {
                $('#form-prestacao-container').fadeOut();
            });

            // Validação do formulário
            $('#form-prestacao').submit(function (e) {
                let valid = true;
                $('.error').text('');

                const valorPago = $('#valor_pago').val();
                if (!valorPago || valorPago <= 0) {
                    $('#valor_pago_error').text('Por favor, insira um valor válido maior que zero.');
                    valid = false;
                }

                const descricaoprestacao = $('#descricaoprestacao').val().trim();
                if (!descricaoprestacao) {
                    $('#descricao_error').text('Por favor, insira uma descrição.');
                    valid = false;
                }

                const dataPagamento = $('#data_pagamento').val();
                if (!dataPagamento) {
                    $('#data_pagamento_error').text('Por favor, selecione uma data.');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            });
        });
    </script>
 


  <!-- Formulário para selecionar contrato e tipo de relatório -->



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
                    <th> Valores pagos </th>
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
                    <!-- <th>Quantidade de Pagamentos</th> -->
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- BOTÔES -->
<script src="./src/js/active.js"></script>
<!--  // Função editar modal -->
 <script src="./src/contratos/js/edit-process-modal.js"></script> 
 <!-- JS RELATÓRIO AVANÇADO -->
<script src="./src/contratos/js/relatorio-avancado.js"></script>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS CADASTRO -->
<script src="./src/contratos/js/cadastro_contato.js"></script>
<script src="./src/contratos/js/fluxo-andamento.js"> </script>
<script src="./src/contratos/js/prestacao-contas.js"></script>
<!--  FUNCTION DE API - CAMPOS EDITAVEIS DE TABELA E INSERÇÃO DE DADOS -->
<script src="./src/contratos/js/gerenciar-pagamentos.js"></script>
<script src="./src/contratos/js/modal-contratos.js"></script>

<?php
include 'footer.php'
?>
</body>
</html>
