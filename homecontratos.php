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

        // Agora insere a notificação apenas após o cadastro do contrato
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
            ':mensagem' => $nomeContrato
        ]);

        // Se não houver uma notificação, insira a nova
        if ($stmtVerificacao->fetchColumn() == 0) {
            // Inserir a notificação se não existir
            $sqlNotificacao = "INSERT INTO notificacoes (username, setor, mensagem, situacao, data_criacao) 
                               VALUES (:username, :setor, :mensagem, :situacao, :data_criacao)";
            $stmtNotificacao = $pdo->prepare($sqlNotificacao);

            try {
                $stmtNotificacao->execute([
                    ':username' => $usuario,
                    ':setor' => $setor,
                    ':mensagem' => "Contrato '{$nomeContrato}' prestes a expirar.",
                    ':situacao' => $situacao,
                    ':data_criacao' => $dataNotificacao
                ]);
            } catch (Exception $e) {
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


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Contratos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    
 
</head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<link rel="stylesheet" href="./src/style/form-cadastro-contratos.css">
<link rel="stylesheet" href="./src/style/notificacao.css">

<body>
<div class="container">
        <h1 class="text-center text-success">Gestão de Contratos</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Notificação de contratos próximos de expirar -->
        <div class="notification">
            <i class="bi bi-bell-fill" style="font-size: 24px;"></i>
            <?php if (count($notificacoes) > 0): ?>
                <span class="notification-badge"><?= count($notificacoes) ?></span>
            <?php endif; ?>
        </div>

        <?php if (count($notificacoes) > 0): ?>
            <div class="alert alert-warning mt-3">
                <strong>Contratos próximos de expirar:</strong>
                <ul>
                    <?php foreach ($notificacoes as $notificacao): ?>
                        <li><?= $notificacao['titulo'] ?> (Expira em: <?= $notificacao['validade'] ?>)</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    
        <div class="tabs">
    <div class="tab active" data-tab="cadastrar" onclick="showTab('cadastrar')">Cadastro de contratos</div>
    <div class="tab" data-tab="retirar" onclick="showTab('consultar')">Consultar contratos</div>
    <div class="tab" data-tab="levantamento" onclick="showTab('agenda')">Agendamento</div>
     <!-- <div class="tab" data-tab="processo" onclick="showTab('processos')">Processos</div> -->
     <div class="tab" data-tab="resumo_processo" onclick="showTab('resumo_processo')" style="display: none;">Resumo</div> 
    <!-- <div class="tab" data-tab="galeria" onclick="showTab('galeria')">Galeria</div> -->
</div>


<div class="form-container3" id="cadastrar" style="display:none;">
    <form action="cadastrar_contratos.php" method="POST" enctype="multipart/form-data">
        <div class="cadastro">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título do Contrato</label>
                <input type="text" id="titulo" name="titulo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="SEI" class="form-label">Nº SEI</label>
                <input type="text" id="SEI" name="SEI" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="objeto" class="form-label">Objeto</label>
                <input type="text" id="objeto" name="objeto" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="gestor" class="form-label">Gestor</label>
                <input type="text" id="gestor" name="gestor" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="gestorsb" class="form-label">Gestor Substituto</label>
                <input type="text" id="gestorsb" name="gestorsb" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="fiscais" class="form-label">Fiscais</label>
                <input type="text" id="fiscais" name="fiscais" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="validade" class="form-label">Vigência</label>
                <input type="date" id="validade" name="validade" class="form-control" required onchange="atualizarParcelas()">
            </div>
            <div class="mb-3">
                <label for="contatos" class="form-label">Contatos</label>
                <input type="text" id="contatos" name="contatos" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="valor-contrato" class="form-label">Valor do Contrato</label>
                <input type="text" id="valor-contrato" name="valor_contrato" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="valor-aditivo" class="form-label">Valor Aditivo</label>
                <input type="text" id="valor-aditivo" name="valor_aditivo" class="form-control" required>
            </div>
        </div>

        <button type="button" class="btn btn-info mt-2" onclick="toggleComplementares()">Adicionar Informações Complementares</button>

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
        <button type="submit" name="cadastrar_contrato" class="btn btn-success mt-3">Cadastrar Contrato</button>
    </form>
</div>

<script src="./src/js/cadastro_contato.js">
</script>

<div class="form-container3" id="processos">
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
</div>

<div class="form-container3" id="consultar" style="display:none;">
    <!-- Pesquisa -->
    <div class="search-container my-4 text-center">
        <div class="input-group w-75 mx-auto" style="display: flex; gap:10px;">
            <input type="text" id="filterInput" class="form-control" placeholder="Digite o título ou descrição do contrato" oninput="searchContracts()" style="display: block;">
            <select id="statusFilter" class="form-control" onchange="searchContracts()">
                <option value="">Todos</option>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
            <button class="btn btn-secondary" onclick="openFilterModal()">Configurar Filtro</button>
        </div>
    </div>

    <!-- Lista de Contratos -->
    <h2 class="text-center mt-3">Lista de Contratos</h2>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Validade</th>
                <th>Status</th>
                <th>Ações</th>
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
            echo "<tr style='cursor:pointer;' onclick='showResumoProcesso(" . json_encode($row) . ")'>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['titulo']}</td>";
            echo "<td>{$row['descricao']}</td>";
            echo "<td>{$row['validade']}</td>";
            echo "<td>{$row['situacao']}</td>";
            // O botão "Visualizar" terá um evento independente
          // Aqui estamos passando os dados do contrato para o botão via JSON
            echo "<td>";
            echo "<button class='btn btn-info btn-sm' onclick='openModal(" . json_encode($row) . "); event.stopPropagation();'>Visualizar</button>";
            echo "<button class='btn btn-primary btn-sm' onclick='generateReport()'>Relatório</button>";
            echo "<button class='btn btn-success btn-sm' onclick='editProcess(event, " . json_encode($row) . ")'>Editar processo</button>";
            echo "</td>";
            echo "</tr>";

            
        }
        ?>

        </tbody>
    </table>
</div>


<!-- Container do resumo do processo
<div class="form-container3" id="resumo_processo" style="display: none;">
    <h2>Resumo do Processo</h2>
    <div id="processoDetalhes"> -->
        <!-- Os detalhes do processo serão carregados aqui
    </div>
    <div class="process-actions">
        

    </div>
</div> -->
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

<script>
   // Função chamada quando o botão "Editar processo" for clicado
function editProcess(event, contractData) {
    // Impede que o evento de clique se propague
    event.stopPropagation();

    // Chama a função para abrir o modal e preencher as abas com os dados do contrato
    openEditModal(contractData);
}

// Função para abrir o modal de edição e preencher os campos com os dados do contrato
function openEditModal(contractData) {
    // Preenche a aba de Detalhes com os dados do contrato
    document.getElementById('contractTitulo').textContent = contractData.titulo;
    document.getElementById('contractDescricao').textContent = contractData.descricao;
    document.getElementById('contractValidade').textContent = contractData.validade;
    document.getElementById('contractSituacao').textContent = contractData.situacao;

    // Preenche a aba de Edição com os dados do contrato
    document.getElementById('editTitulo').value = contractData.titulo;
    document.getElementById('editDescricao').value = contractData.descricao;
    document.getElementById('editValidade').value = contractData.validade;
    document.getElementById('editSituacao').value = contractData.situacao;

    // Exibe o modal
    $('#editProcessModal').modal('show');
}

// Função para salvar as alterações
document.getElementById('editProcessForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Previne o envio normal do formulário

    // Coleta os dados do formulário
    var updatedData = {
        titulo: document.getElementById('editTitulo').value,
        descricao: document.getElementById('editDescricao').value,
        validade: document.getElementById('editValidade').value,
        situacao: document.getElementById('editSituacao').value
    };

    // Aqui você pode fazer uma requisição para salvar os dados atualizados no banco de dados
    // Exemplo com Fetch API:
    /*
    fetch('/path/to/your/api', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(updatedData),
    })
    .then(response => response.json())
    .then(data => {
        // Fechar o modal após salvar os dados
        $('#editProcessModal').modal('hide');
    })
    .catch((error) => {
        console.error('Erro:', error);
    });
    */

    // Fechar o modal após salvar os dados
    $('#editProcessModal').modal('hide');
});


</script>


<!--  // Função chamada ao clicar na linha da tabela -->
<script>
    function showResumoProcesso(data) {
        // Exibe a div do resumo do processo
        document.getElementById('consultar').style.display = 'none'; // Esconde a lista de contratos
        document.getElementById('resumo_processo').style.display = 'block'; // Exibe o resumo do processo

        // Preenche os detalhes do processo na div
        const processoDetalhes = document.getElementById('processoDetalhes');
        processoDetalhes.innerHTML = `
            <p><strong>ID:</strong> ${data.id}</p>
            <p><strong>Título:</strong> ${data.titulo}</p>
            <p><strong>Descrição:</strong> ${data.descricao}</p>
            <p><strong>Validade:</strong> ${data.validade}</p>
            <p><strong>Status:</strong> ${data.situacao}</p>
        `;
    }
</script>


<script>
    // Função para redirecionar para a aba "resumo_processo"
    function redirectTo(tab) {
        // Altera a aba para "resumo_processo"
        showTab(tab);
    }

    // Função para exibir a aba específica
    function showTab(tabName) {
        const tabs = document.querySelectorAll('.tab');
        tabs.forEach(tab => {
            if (tab.dataset.tab === tabName) {
                tab.classList.add('active');  // Marca a aba como ativa
            } else {
                tab.classList.remove('active');
            }
        });
        // Adicione a lógica de exibição do conteúdo da aba se necessário
        console.log("Aba exibida: " + tabName);
    }
</script>



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
<!-- visualizar contrato modal -->
<script>

    function openModal(contrato) {
        document.getElementById('modalTituloContrato').innerText = contrato.titulo;
        document.getElementById('modalDescricao').innerText = contrato.descricao;
        document.getElementById('modalValidade').innerText = contrato.validade;
        document.getElementById('modalSEI').innerText = contrato.SEI;
        document.getElementById('modalGestor').innerText = contrato.gestor;
        document.getElementById('modalFiscais').innerText = contrato.fiscais;
        document.getElementById('modalValorContrato').innerText = contrato.valor_contrato;
        document.getElementById('modalNumParcelas').innerText = contrato.num_parcelas ? contrato.num_parcelas : 'N/A';

        var modal = new bootstrap.Modal(document.getElementById('modalContrato'));
        modal.show();
    }

</script>

<
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
<div class="container mt-5">
        <div class="form-container3 " id="agenda" style="display:flex;">
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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

</body>
</html>
<?php
include 'footer.php';
?>