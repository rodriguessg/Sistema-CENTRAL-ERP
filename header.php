<?php
// Inicie a sessão, caso ainda não tenha sido iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifique se a sessão está ativa
if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
    header("Location: login.php");
    exit();
}
// Incluindo a conexão com o banco de dados, verificando se o arquivo existe
require_once 'bancoo.php';  // Atualize o caminho conforme necessário


// Verifique se a conexão foi estabelecida
try {
    if (!isset($pdo)) {
        die("A conexão com o banco de dados não foi estabelecida.");
    }
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

try {
    // Recupere as informações do usuário da sessão
    $username = $_SESSION['username'];
    $setor = $_SESSION['setor'];

    // Diretório base das fotos dos usuários
    $fotoBasePath = 'uploads/';

    // Busque as informações do usuário no banco de dados (foto)
    $query = $pdo->prepare("SELECT foto FROM usuario WHERE username = :username");
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    // Caminho completo da foto ou uma imagem padrão
    $foto = (!empty($result['foto']) && file_exists($fotoBasePath . $result['foto']))
        ? $fotoBasePath . $result['foto']
        : $fotoBasePath . 'man.png';

    // Configure as permissões de navegação com base no setor do usuário
    $menuItens = [];
    switch ($setor) {
        case 'administrador':
            $menuItens = [
                ['link' => 'painel.php', 'nome' => 'Painel', 'icon' => 'tachometer-alt'],
                ['link' => 'homeestoque.php', 'nome' => 'Estoque', 'icon' => 'box'],
                ['link' => 'homecontratos.php', 'nome' => 'Contratos', 'icon' => 'file-contract'],
                ['link' => 'homefinanceiro.php', 'nome' => 'Financeiro', 'icon' => 'money-bill'],
                ['link' => 'homepatrimonio.php', 'nome' => 'Patrimônio', 'icon' => 'building'],
                ['link' => 'cadastro_usuario.php', 'nome' => 'Cadastrar Usuário', 'icon' => 'user-plus'],
                ['link' => 'gerenciarpermissao.php', 'nome' => 'Usuários', 'icon' => 'key'],
                ['link' => 'homeRh.php', 'nome' => 'Recursos H', 'icon' => 'users'],
                ['link' => 'log.php', 'nome' => 'LOG', 'icon' => 'users'],
                ['link' => 'configuracao.php', 'nome' => 'Configurações', 'icon' => 'cogs'],

            ];
            break;

            
        case 'estoque':
            $menuItens = [
                ['link' => 'painelalmoxarifado.php', 'nome' => 'Painel', 'icon' => 'cogs'],
                ['link' => 'homeestoque.php', 'nome' => 'Home', 'icon' => 'home'],
                ['link' => 'rh.php', 'nome' => 'Assinatura webmail', 'icon' => 'envelope'],
            ];
            break;


            case 'contratos':
                $menuItens = [
                    ['link' => 'painelcontratos.php', 'nome' => 'Painel', 'icon' => 'cogs'],
                    ['link' => 'homecontratos.php', 'nome' => 'Home', 'icon' => 'home'],
                    ['link' => 'calendar.php', 'nome' => 'Calendário / Tarefas', 'icon' => 'calendar-alt'],
                    ['link' => 'rh.php', 'nome' => 'Assinatura webmail', 'icon' => 'envelope'],
                    ['link' => 'certidoes.php', 'nome' => 'Certidões', 'icon' => 'envelope'],
                ];
                break;

        

        case 'patrimonio':
            $menuItens = [
                ['link' => 'painelpatrimonio.php', 'nome' => 'Painel', 'icon' => 'cogs'],
                ['link' => 'homepatrimonio.php', 'nome' => 'Home', 'icon' => 'home'],
                ['link' => 'rh.php', 'nome' => 'Assinatura webmail', 'icon' => 'envelope'],
            ];
            break;


            case 'financeiro':
                $menuItens = [
                    ['link' => 'homefinanceiro.php', 'nome' => 'Home', 'icon' => 'home'],
                    ['link' => 'painelfinanceiro.php', 'nome' => 'Painel', 'icon' => 'chart-line'],
                    ['link' => 'rh.php', 'nome' => 'Gerador de assinatura de email', 'icon' => 'envelope'],
                    ['link' => 'perfil.php', 'nome' => 'Perfil', 'icon' => 'user'],
                    ['link' => 'sair.php', 'nome' => 'Sair', 'icon' => 'sign-out-alt'],
                ];
                break;


        case 'recursos_humanos':
            $menuItens = [
                ['link' => 'painelRh.php', 'nome' => 'Painel', 'icon' => 'cogs'],
                ['link' => 'homeRh.php', 'nome' => 'Home', 'icon' => 'home'],
                ['link' => 'cracha.php', 'nome' => 'Gerador Cracha', 'icon' => 'id-card'],
                ['link' => 'rh.php', 'nome' => 'Assinatura webmail', 'icon' => 'envelope'],
            ];
            break;

          

        default:
            // Caso o setor não seja reconhecido, redireciona para a página de erro
            header("Location: mensagem.php?mensagem=setor_nao_reconhecido&pagina=login.php");
            exit();
    }
} catch (PDOException $e) {
    // Caso haja erro ao buscar informações do banco de dados, exibe a mensagem de erro
    die("Erro ao acessar as informações do usuário: " . $e->getMessage());
}
?>

<?php

// Supondo que a variável $setor contém o setor do usuário logado (essa variável já deve estar definida)
$setor = $_SESSION['setor']; // Supondo que o setor está armazenado na sessão

// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$user = 'root';
$password = '';

try {
    // Conectando ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar setores para determinar as notificações visíveis
    if ($setor == 'administrador') {
        // Administrador pode ver todas as notificações
        $query = "SELECT id, mensagem FROM notificacoes WHERE situacao = 'nao lida' ORDER BY data_criacao DESC";
        $stmt = $pdo->query($query);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $unreadCount = count($notifications);
    } elseif ($setor == 'estoque') {
        // Estoque pode ver notificações de 'estoque' e 'administrador'
        $query = "SELECT id, mensagem FROM notificacoes WHERE situacao = 'nao lida' AND (setor = 'estoque' OR setor = 'administrador') ORDER BY data_criacao DESC";
        $stmt = $pdo->query($query);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $unreadCount = count($notifications);
    } elseif ($setor == 'contratos') {
        // Contratos pode ver notificações de 'contratos' e 'administrador'
        $query = "SELECT id, mensagem FROM notificacoes WHERE situacao = 'nao lida' AND (setor = 'contratos' OR setor = 'administrador') ORDER BY data_criacao DESC";
        $stmt = $pdo->query($query);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $unreadCount = count($notifications);
    } elseif ($setor == 'financeiro') {
        // Financeiro pode ver notificações de 'financeiro' e 'administrador'
        $query = "SELECT id, mensagem FROM notificacoes WHERE situacao = 'nao lida' AND (setor = 'financeiro' OR setor = 'administrador') ORDER BY data_criacao DESC";
        $stmt = $pdo->query($query);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $unreadCount = count($notifications);
    } else {
        // Se o setor não for um dos acima, não há notificações
        $unreadCount = 0;
        $notifications = [];
    }

    // Agora o $unreadCount e $notifications estão prontos para serem enviados para o frontend
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA INTEGRADO CENTRAL</title>
    <link rel="stylesheet" href="src/style/lateral-menu.css">
    <!-- Incluindo o Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluindo o CSS customizado -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <!-- Menu Lateral -->
        <nav id="sidebar" class="active">
            <!-- Notificações -->
<li class="nav-item">
    <a class="nav-link" href="#" id="notificacaoLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-bell"></i>
        <span class="badge badge-danger" id="notificationCount"><?= $unreadCount ?></span> <!-- Número de notificações -->
    </a>

    <!-- Dropdown para exibir as notificações -->
    <div class="dropdown-menu" aria-labelledby="notificacaoLink" id="notificationList" style="min-width: 600px; max-height: 300px; overflow-y: auto;">
        <h6 class="dropdown-header">Notificações</h6>
        <?php 
        // Exibir notificações somente para os setores "administrador" ou "estoque"
        if ($unreadCount > 0) {
            foreach ($notifications as $notification) {
        ?>
            <div class="dropdown-item" style="cursor: pointer;" onclick="markAsRead(<?= $notification['id'] ?>)">
                <?= htmlspecialchars($notification['mensagem']) ?>
            </div>
        <?php 
            }
        } else { 
        ?>
            <p class="dropdown-item">Sem novas notificações.</p>
        <?php } ?>
    </div>
</li>



<!-- Modal de notificações -->
<div class="modal" id="notificationModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Notificações</h5>
            <span class="modal-close" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <!-- Conteúdo das notificações será gerado aqui -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Fechar</button>
        </div>
    </div>
</div>


            <!-- Seção de perfil -->
            <div class="profile-section">
                <a href="#" data-toggle="modal" data-target="#perfilModal">
                    <img src="<?= htmlspecialchars(!empty($foto) ? $foto : '/default.png') ?>" alt="Perfil" class="rounded-circle">
                    <p><?= htmlspecialchars($username) ?></p>
                </a>
            </div>
            
            <!-- Itens do menu -->
            <ul class="list-unstyled components">
                <?php foreach ($menuItens as $item): ?>
                    <li>
                        <a href="<?= htmlspecialchars($item['link']) ?>">
                            <i class="fa fa-<?= htmlspecialchars($item['icon']) ?>"></i> 
                            <span class="menu-text"><?= htmlspecialchars($item['nome']) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Outros itens do menu -->
            <ul class="list-unstyled components">
                <!-- Botão Sair -->
                <li class="exit-btn">
                    <a href="sair.php" class="exit-btn-link">
                        <i class="fa fa-sign-out-alt exit-icon"></i>
                        <span>Sair</span> <!-- O texto Sair será ocultado quando o menu estiver minimizado -->
                    </a>
                </li>
            </ul>
        </nav> <!-- Fechando a tag nav -->

        <!-- Modal Perfil -->
        <div class="modal fade" id="perfilModal" tabindex="-1" aria-labelledby="perfilModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="perfilModalLabel">Perfil do Usuário</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img src="<?= htmlspecialchars(!empty($foto) ? $foto : '/default.png') ?>" alt="Perfil" class="rounded-circle mb-3" style="width: 70px; height: 70px;">
                        <p><strong>Nome:</strong> <?= htmlspecialchars($username) ?></p>
                        <p><strong>Email:</strong> <span id="modal-email"></span></p>
                        <p><strong>Setor:</strong> <span id="modal-setor"></span></p>
                        <p><strong>Tempo de Registro:</strong> <span id="modal-tempo-registro"></span></p>
                        <p><strong>Movimentações Realizadas:</strong> <span id="modal-movimentacoes"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery e Bootstrap 4 JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS CONVERSAO IA -->
    <script src="./src/header/js/ia.js"></script>
    <!-- JS PREENCHIMENTO INFORMAÇÕES PERFIL -->
    <script src="./src/header/js/perfil.js"></script>
    <!-- JS ATUALIZACAO ICONE INFORMAÇÕES -->
   <!-- <script src="./src/header/js/icon-notificacao.js"></script>  -->
    <script>
        <!-- Script JavaScript para marcar notificações como lidas via AJAX -->

function markAsRead(notificationId) {
    fetch('marcar_notificacao_lida.php?id=' + notificationId, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar o contador de notificações
            const countElement = document.getElementById('notificationCount');
            let currentCount = parseInt(countElement.textContent);
            if (currentCount > 0) {
                currentCount--;
                countElement.textContent = currentCount;
                if (currentCount === 0) {
                    countElement.style.display = 'none'; // Ocultar badge se não houver notificações
                }
            }

            // Opcional: Remover a notificação da lista ou marcar como lida visualmente
            const notificationItem = document.querySelector(`div[onclick="markAsRead(${notificationId})"]`);
            if (notificationItem) {
                notificationItem.style.backgroundColor = '#f0f0f0'; // Destacar como lida
                notificationItem.style.opacity = '0.6';
                notificationItem.onclick = null; // Desativar clique
            }

            // Se não houver mais notificações, exibir mensagem
            if (currentCount === 0) {
                const notificationList = document.getElementById('notificationList');
                notificationList.innerHTML = '<h6 class="dropdown-header">Notificações</h6><p class="dropdown-item">Sem novas notificações.</p>';
            }
        } else {
            console.error('Erro ao marcar notificação como lida:', data.message);
        }
    })
    .catch(error => {
        console.error('Erro na requisição AJAX:', error);
    });
}
</script>


<!-- <?php
include 'footer.php';
?> -->
</body>


</html>



