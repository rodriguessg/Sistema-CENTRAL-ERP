<?php
    // Inicie a sessão, caso ainda não tenha sido iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifique se o usuário está logado
    if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
        header("Location: login.php");
        exit();
    }

    // Inclua a conexão com o banco de dados
    require_once 'bancoo.php';

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
            : $fotoBasePath . 'perfil-user-673e5672cac27.png';

        // Configure as permissões de navegação com base no setor do usuário
        $menuItens = [];
        switch ($setor) {
            case 'administrador':
                $menuItens = [
                    ['link' => 'painel.php', 'nome' => 'Painel'],
                    ['link' => 'homeRh.php', 'nome' => 'Recursos Humanos'],
                    ['link' => 'homefinanceiro.php', 'nome' => 'Financeiro'],
                    ['link' => 'homecontratos.php', 'nome' => 'Contratos'],
                    ['link' => 'homeestoque.php', 'nome' => 'Estoque'],
                    ['link' => 'homepatrimonio.php', 'nome' => 'Patrimônio'],
                    ['link' => 'cadastro_usuario.php', 'nome' => 'Cadastrar Usuário'],
                    ['link' => 'gerenciarpermissao.php', 'nome' => 'Gerenciar Permissões'],
                    ['link' => 'ia.php', 'nome' => 'IA CENTRAL'],
                    
                    ['link' => 'rh.php', 'nome' => 'Assinatura webmail'],
                    
                    ['link' => 'perfil.php', 'nome' => 'Perfil'],
                ];
                break;

            case 'financeiro':
                $menuItens = [
                    ['link' => 'homefinanceiro.php', 'nome' => 'Home'],
                    ['link' => 'painelfinanceiro.php', 'nome' => 'Painel'],
                    ['link' => 'rh.php', 'nome' => 'Assinatura webmail'],
                    ['link' => 'perfil.php', 'nome' => 'Perfil'],
                    ['link' => 'sair.php', 'nome' => 'Sair'],
                ];
                break;

            case 'patrimonio':
                $menuItens = [
                    ['link' => 'painelpatrimonio.php', 'nome' => 'Painel'],
                    ['link' => 'homepatrimonio.php', 'nome' => 'Home'],
                    ['link' => 'rh.php', 'nome' => 'Assinatura webmail'],
                    ['link' => 'perfil.php', 'nome' => 'Perfil'],
                ];
                break;
                case 'estoque':
                    $menuItens = [
                        ['link' => 'painelalmoxarifado.php', 'nome' => 'Painel'],
                        ['link' => 'homeestoque.php', 'nome' => 'Home'],
                        ['link' => 'rh.php', 'nome' => 'Assinatura webmail'],
                        ['link' => 'perfil.php', 'nome' => 'Perfil'],
                    ];
                    break;
                case 'recursos_humanos':
                    $menuItens = [
                        ['link' => 'painelRh.php', 'nome' => 'Painel'],
                        ['link' => 'homeRh.php', 'nome' => 'Home'],
                        ['link' => 'cracha.php', 'nome' => 'Gerador Cracha'],
                        ['link' => 'rh.php', 'nome' => 'Assinatura webmail'],
                        ['link' => 'perfil.php', 'nome' => 'Perfil'],
                    ];
                    break;
                    case 'contratos':
                        $menuItens = [

                            ['link' => 'painelcontratos.php', 'nome' => 'Painel'],
                            ['link' => 'homecontratos.php', 'nome' => 'Home'],
                            ['link' => 'rh.php', 'nome' => 'Assinatura webmail'],
                            ['link' => 'perfil.php', 'nome' => 'Perfil'],
                        ];
                        break;
        


            default:
                // Caso o setor não seja reconhecido, redireciona para a página de erro
                header("Location: mensagem.php?mensagem=setor_nao_reconhecido&pagina=index.php");
                exit();
        }
    } catch (PDOException $e) {
        // Caso haja erro ao buscar informações do banco de dados, exibe a mensagem de erro
        die("Erro ao acessar as informações do usuário: " . $e->getMessage());
    }
?>
<!-- PHP GERENCIA CONEXAO NOTIFICAO PREENCHIMENTO BD -->
<?php
    // Supondo que a variável $setor contém o setor do usuário logado (essa variável já deve estar definida)
    // Verifique o setor do usuário
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

        // Verifica se o setor do usuário é "administrador" ou "estoque"
        if ($setor == 'administrador' || $setor == 'estoque') {
            // Consulta para pegar as notificações não lidas
            $query = "SELECT id, mensagem FROM notificacoes WHERE status = 'nao lida' ORDER BY data_criacao DESC";
            $stmt = $pdo->query($query);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Conta a quantidade de notificações não lidas
            $unreadCount = count($notifications);
        } else {
            // Se o setor não for administrador nem estoque, definimos $unreadCount como 0
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
    <!-- <title>SISTEMA INTEGRADO CENTRAL</title> -->
    <!-- Incluindo o Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluindo o seu arquivo de estilos customizados -->
    <link rel="stylesheet" href="./src/style/style.css">
    <link rel="stylesheet" href="./src/style/nav.css">
    <link rel="stylesheet" href="./src/style/icon-notificacao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
    
<header class="header">
    <!-- Exibição condicional do título -->
    <?php if ($setor === 'administrador'): ?>
        <h1>Sistema de controle web CENTRAL</h1>
    <?php endif; ?>
    <?php if ($setor === 'patrimonio'): ?>
        <h1>Sistema de Controle Patrimonial</h1>
    <?php endif; ?>
    <?php if ($setor === 'financeiro'): ?>
        <h1>Sistema de Controle Financeiro</h1>
    <?php endif; ?>
    <?php if ($setor === 'estoque'): ?>
        <h1>Sistema de Controle de Almoxarifado</h1>
    <?php endif; ?>
    <?php if ($setor === 'recursos_humanos'): ?>
        <h1>Sistema de RH</h1>
    <?php endif; ?>
    <?php if ($setor === 'contratos'): ?>
        <h1>Sistema de Gestão de contratos</h1>
    <?php endif; ?>
<nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Botão para dispositivos móveis -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Conteúdo do menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Itens do menu à esquerda -->
                <ul class="navbar-nav mr-auto">
                    <?php foreach ($menuItens as $item): ?>
                        <?php if ($item['nome'] !== 'Perfil'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= htmlspecialchars($item['link']) ?>" <?php if ($item['nome'] === 'IA CENTRAL'): ?>data-toggle="modal" data-target="#iaModal"<?php endif; ?>><?= htmlspecialchars($item['nome']) ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <!-- Link do perfil à direita -->
     <ul class="navbar-nav ml-auto">
                     <!-- Ícone de notificações -->    
                  <!-- Ícone de notificações -->
        <li class="nav-item">
                <a class="nav-link" href="#" id="notificacaoLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bell"></i>
                    <span class="badge badge-danger" id="notificationCount"><?= $unreadCount ?></span> <!-- Número de notificações -->
                </a>

                <!-- Dropdown para exibir as notificações -->
                <div class="dropdown-menu" aria-labelledby="notificacaoLink" id="notificationList" style="min-width: 300px; max-height: 300px; overflow-y: auto;">
                    <h6 class="dropdown-header">Notificações</h6>
                    <?php 
                    // Exibir notificações somente para os setores "administrador" ou "estoque"
                    if ($unreadCount > 0) {
                        foreach ($notifications as $notification) {
                    ?>
                            <a class="dropdown-item" href="#" onclick="markAsRead(<?= $notification['id'] ?>)">
                                <?= htmlspecialchars($notification['mensagem']) ?>
                            </a>
                    <?php 
                        }
                    } else { 
                    ?>
                        <p class="dropdown-item">Sem novas notificações.</p>
                    <?php } ?>
                </div>
            </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img
                                src="<?= htmlspecialchars(!empty($foto) ? $foto : '/default.png') ?>"
                                alt=""
                                class="rounded-circle"
                                style="width: 30px; height: 30px;"
                            >
                            <?= htmlspecialchars($username) ?>
                            <img src="./src/img/image.png" alt="" style="width: 20px; height: 10px;" class="pro">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="perfilDropdown">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#perfilModal">Perfil</a>
                            <a class="dropdown-item" href="sair.php">Sair</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
</nav>
</header>

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
            <img
                src="<?= htmlspecialchars(!empty($foto) ? $foto : '/default.png') ?>"
                alt=""
                class="rounded-circle"
                style="width: 70px; height: 70px;"
            >
            <div class="modal-body">
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

<!-- Modal IA -->
<div class="modal fade" id="iaModal" tabindex="-1" aria-labelledby="iaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="iaModalLabel">Chat com IA Central</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="chat-container">
                    <div id="chat-messages" style="height: 300px; overflow-y: scroll; border: 1px solid #ddd; padding: 10px;">
                        <!-- Mensagens serão inseridas dinamicamente aqui -->
                    </div>
                    <div id="chat-options" class="mt-3">
                        <!-- Botões de opções aparecerão aqui -->
                    </div>
                    <div class="input-group mt-3">
                        <input type="text" id="chat-input" class="form-control" placeholder="Digite sua mensagem">
                        <div class="input-group-append">
                            <button class="btn btn-primary" id="send-message">Enviar</button>
                        </div>
                    </div>
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
<script src="./src/header/js/icon-notificacao.js"></script>
</body>
</html>