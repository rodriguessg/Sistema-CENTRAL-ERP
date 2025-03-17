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
                ['link' => 'homefinanceiro.php', 'nome' => 'Financeiro'],
                ['link' => 'homeestoque.php', 'nome' => 'Estoque'],
                ['link' => 'homepatrimonio.php', 'nome' => 'Patrimônio'],
                ['link' => 'cadastro_usuario.php', 'nome' => 'Cadastrar Usuário'],
                ['link' => 'gerenciarpermissao.php', 'nome' => 'Gerenciar Permissões'],
                ['link' => 'ia.php', 'nome' => 'IA CENTRAL'],
                ['link' => 'homecontratos.php', 'nome' => 'Contratos'],
                ['link' => 'rh.php', 'nome' => 'Assinatura webmail'],
                ['link' => 'homeRh.php', 'nome' => 'Recursos Humanos'],
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
                    ['link' => 'painelestoque.php', 'nome' => 'Painel'],
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

<style>
    header {
        color: #fff;
        padding: 15px 0;
        text-align: center;
        width: 100%;
    }

    header h1 {
        font-size: 1.8rem;
    }

    /* Estilo da barra de navegação */
    .navbar {
        margin-top: 10px;
    }

    /* Efeito para os links do menu */
    .navbar-nav .nav-link {
        color: #fff;
        font-weight: bold;
        position: relative;
        display: inline-block;
        text-decoration: none;
        overflow: hidden;
        transition: color 0.3s ease;
    }

    .navbar-nav .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: #f8f9fa; /* Cor do efeito */
        transition: left 0.4s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #f8f9fa; /* Cor do texto ao passar o mouse */
    }

    .navbar-nav .nav-link:hover::after {
        left: 0; /* Move a linha para o início */
    }
    .navbar .container, .navbar .container-fluid, .navbar .container-lg, .navbar .container-md, .navbar .container-sm, .navbar .container-xl {
    display: -ms-flexbox;
    display: contents;
    /* -ms-flex-wrap: wrap; */
    /* flex-wrap: wrap; */
    /* -ms-flex-align: center; */
    /* align-items: center; */
    -ms-flex-pack: justify;
    }

    /* Estilo do perfil do usuário */
    .user-profile {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .user-profile .pro {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .pro {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .modal-header, .modal-footer {
        background-color: #343a40;
        color: white;
    }

    .dropdown-item {
        display: block;
        width: 100%;
        padding: .25rem 1.5rem;
        clear: both;
        font-weight: 400;
        color: #333;
        text-align: inherit;
        white-space: nowrap;
        background-color: transparent;
        border: 0;
    }

    .modal-backdrop {
        z-index: auto !important; /* Remove o z-index definido pelo Bootstrap */
    }

    .modal-body {
        position: relative;
        -ms-flex: 1 1 auto;
        flex: 1 1 auto;
        text-align: left;
        padding: 1rem;
    }
    /* Estilo geral do cabeçalho */
    header {
        color: #fff;
        padding: 15px 0;
        text-align: center;
        width: 100%;
        background-color: #343a40; /* Adiciona um fundo ao cabeçalho */
    }

    header h1 {
        font-size: 1.8rem;
        margin: 0;
    }

    /* Estilo da barra de navegação */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        padding: 0 20px;
    }

    .navbar-nav {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    /* Links do menu */
    .navbar-nav .nav-link {
        color: #fff;
        font-weight: bold;
        position: relative;
        text-decoration: none;
        cursor: pointer;
        overflow: hidden;
        margin: 0 10px;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .navbar-nav .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: -100%;
        width: 100%;
        height: 2px;
        background: #f8f9fa; /* Cor do efeito */
        cursor: pointer;
        transition: left 0.4s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #f8f9fa; /* Cor do texto ao passar o mouse */
        cursor: pointer;
    }

    .navbar-nav .nav-link:hover::after {
        left: 0; /* Move a linha para o início */
    }

    /* Estilo do perfil do usuário */
    .user-profile {
        display: flex;
        align-items: center;
    }

    .user-profile .pro {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
    }

    /* Estilo do modal */
    .modal-header,
    .modal-footer {
        background-color: #343a40;
        color: white;
    }

    .modal-body {
        position: relative;
        flex: 1 1 auto;
        text-align: left;
        padding: 1rem;
    }

    /* MODAL CHAT BOT */

    .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
        }
        .modalchat {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .modal-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .modal-body {
            /* max-height: 300px; */
            overflow-y: auto;
            margin-bottom: 10px;
        }
        .message {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .message.user {
            background-color: #e0f7fa;
            align-self: flex-end;
        }
        .message.bot {
            background-color: #f1f1f1;
            align-self: flex-start;
        }
        .chat-form {
            display: flex;
            gap: 10px;
        }
        .chat-form input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .chat-form button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .chat-form button:hover {
            background-color: #0056b3;
        }

        .option {
            padding: 10px;
            margin: 5px 0;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .option:hover {
            background-color: #e0e0e0;
        }
        #response {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
    @media (min-width: 992px) {
        /* .navbar-expand-lg, .container, .navbar-expand-lg>.container-fluid, .navbar-expand-lg.container-lg, .navbar-expand-lg>.container-md, .navbar-expand-lg>.container-sm, .navbar-expand-lg>.container-xl {
            /* -ms-flex-wrap: nowrap; */
            /* flex-wrap: nowrap;
            */
        }

    /* Responsividade para dispositivos móveis */
    @media (max-width: 768px) {
        header {
            padding: 10px 0;
        }

        header h1 {
            font-size: 1.5rem;
        }

        .navbar {
            flex-direction: column;
            align-items: center;
        }

        .navbar-nav {
            flex-direction: column;
            align-items: center;
            display: none; /* Oculta o menu por padrão */
        }

        .navbar-nav.active {
            display: flex; /* Mostra o menu quando ativo */
        }

        .navbar-nav .nav-link {
            margin: 10px 0;
        }

        .navbar-toggle {
            display: block;
            font-size: 1.5rem;
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
        }

        .user-profile {
            margin-top: 10px;
        }

        .pro {
            width: 35px;
            height: 35px;
        }
    }

    @media (max-width: 480px) {
        header h1 {
            font-size: 1.2rem;
        }

        .navbar-nav .nav-link {
            font-size: 0.9rem;
        }

        .pro {
            width: 30px;
            height: 30px;
        }

        .modal-body {
            padding: 0.5rem;
            font-size: 0.9rem;
        }
    }


    </style>
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
        <h1>Sistema de Controle de Estoque</h1>
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

<script>
$(document).ready(function() {
    const initialOptions = [
        'Gerar relatório do sistema',
        'Quantos usuários estão cadastrados?',
        'Quantos produtos estão cadastrados?',
        'Informações sobre o patrimônio',
        'Quantos setores estão ativos?',
        'Quantos funcionários estão cadastrados?',
        'O que você pode fazer?'
    ];

    function addOptions(options) {
        $('#chat-options').empty();
        options.forEach(option => {
            const button = $('<button>')
                .addClass('btn btn-outline-primary m-1')
                .text(option)
                .on('click', function() {
                    sendMessage(option);
                });
            $('#chat-options').append(button);
        });
    }

    function appendMessage(sender, message) {
        const messageDiv = $('<div>').html(`<strong>${sender}:</strong> ${message}`);
        $('#chat-messages').append(messageDiv);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }

    function sendMessage(message) {
        appendMessage('Você', message);
        $('#chat-input').val('');

        $.ajax({
            url: 'ia.php',
            type: 'POST',
            data: JSON.stringify({ message: message }),
            contentType: 'application/json',
            success: function(response) {
                appendMessage('IA', response.reply);
                if (response.options && response.options.length > 0) {
                    addOptions(response.options);
                }
            },
            error: function(xhr, status, error) {
                appendMessage('Erro', 'Não foi possível obter resposta da IA.');
            }
        });
    }

    $('#send-message').on('click', function() {
        const message = $('#chat-input').val();
        if (message.trim() !== '') {
            sendMessage(message);
        }
    });

    $('#iaModal').on('show.bs.modal', function() {
        addOptions(initialOptions);
    });
});
</script>
<script>
//Este código realiza a atualização das informações sobre o perfil
$(document).ready(function() {
    $('#perfilModal').on('show.bs.modal', function () {
        $.ajax({
            url: 'perfil.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                $('#modal-email').text(data.email);
                $('#modal-setor').text(data.setor);
                $('#modal-tempo-registro').text(data.tempo_registro);
                $('#modal-movimentacoes').text(data.movimentacoes);
            },
            error: function(xhr, status, error) {
                alert('Erro ao carregar dados: ' + error);
            }
        });
    });
});

</script>



</body>
</html>