<?php
// Inicie a sessão, caso ainda não tenha sido iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifique se a sessão está ativa
if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
    header("Location: index.php");
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
                 ['link' => 'certidoes.php', 'nome' => 'Certidões', 'icon' => 'envelope'],
                ['link' => 'homefinanceiro.php', 'nome' => 'Financeiro', 'icon' => 'money-bill'],
                ['link' => 'homepatrimonio.php', 'nome' => 'Patrimônio', 'icon' => 'building'],
                   ['link' => 'calendar.php', 'nome' => 'Calendário / Tarefas', 'icon' => 'calendar-alt'],
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
                  ['link' => 'conferencia.php', 'nome' => 'Conferencia', 'icon' => 'home'],
                ['link' => 'rh.php', 'nome' => 'Assinatura webmail', 'icon' => 'envelope'],
                ['link' => 'prestacaoestoque.php', 'nome' => 'Prestação de Contas', 'icon' => 'envelope'],
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
            header("Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=setor_nao_reconhecido&pagina=/Sistema-CENTRAL-ERP/login.php");
             
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
    <link rel="stylesheet" href="src/style/sino.css">
    <link rel="stylesheet" href="src/style/modal-perfil.css">
    <!-- Incluindo o Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluindo o CSS customizado -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <!-- Menu Lateral -->
        <nav id="sidebar" class="active">
            <!-- Sistema de Notificações Aprimorado -->
            <div class="notification-system">
                <div class="notification-container">
                    <button class="notification-bell" id="notificacaoLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="bell-icon">
                            <i class="fa fa-bell"></i>
                            <div class="bell-animation"></div>
                        </div>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-badge" id="notificationCount"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </button>

                    <!-- Dropdown Aprimorado para Notificações -->
                    <div class="notification-dropdown" id="notificationList" aria-labelledby="notificacaoLink">
                        <div class="notification-header">
                            <h6><i class="fas fa-bell"></i> Notificações</h6>
                            <?php if ($unreadCount > 0): ?>
                                <button class="mark-all-read" onclick="markAllAsRead()">
                                    <i class="fas fa-check-double"></i> Marcar todas como lidas
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="notification-body">
                            <?php if ($unreadCount > 0): ?>
                                <?php foreach ($notifications as $notification): ?>
                                    <div class="notification-item" data-id="<?= $notification['id'] ?>" onclick="markAsRead(<?= $notification['id'] ?>)">
                                        <div class="notification-content">
                                            <div class="notification-icon">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <div class="notification-text">
                                                <p><?= htmlspecialchars($notification['mensagem']) ?></p>
                                                <span class="notification-time">Agora</span>
                                            </div>
                                        </div>
                                        <button class="notification-action" onclick="event.stopPropagation(); markAsRead(<?= $notification['id'] ?>)">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="no-notifications">
                                    <div class="no-notifications-icon">
                                        <i class="fas fa-bell-slash"></i>
                                    </div>
                                    <p>Sem novas notificações</p>
                                    <span>Você está em dia!</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($unreadCount > 0): ?>
                            <div class="notification-footer">
                                <button class="view-all-btn" onclick="viewAllNotifications()">
                                    <i class="fas fa-eye"></i> Ver todas as notificações
                                </button>
                            </div>
                        <?php endif; ?>
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
                        <span>Sair</span>
                    </a>
                </li>
            </ul>
        </nav>

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
                        <p><strong>Nome</strong> <?= htmlspecialchars($username) ?></p>
                        <p><strong>Email</strong> <span id="modal-email"></span></p>
                        <p><strong>Setor</strong> <span id="modal-setor"></span></p>
                        <p><strong>Tempo de Registro</strong> <span id="modal-tempo-registro"></span></p>
                        <p><strong>Movimentações Realizadas</strong> <span id="modal-movimentacoes"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Notificações Completo -->
        <div class="modal fade" id="allNotificationsModal" tabindex="-1" aria-labelledby="allNotificationsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="allNotificationsModalLabel">
                            <i class="fas fa-bell"></i> Todas as Notificações
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="allNotificationsContent">
                            <!-- Conteúdo será carregado via JavaScript -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" onclick="markAllAsRead()">
                            <i class="fas fa-check-double"></i> Marcar todas como lidas
                        </button>
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
       <script src="./src/header/js/modal-perfil.js"></script>

    <script>
        // Sistema de Notificações Aprimorado
        function markAsRead(notificationId) {
            fetch('marcar_notificacao_lida.php?id=' + notificationId, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar o contador de notificações
                    const countElement = document.getElementById('notificationCount');
                    const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
                    
                    if (countElement) {
                        let currentCount = parseInt(countElement.textContent);
                        if (currentCount > 0) {
                            currentCount--;
                            if (currentCount === 0) {
                                countElement.remove();
                                // Atualizar para mostrar "sem notificações"
                                updateNotificationDisplay();
                            } else {
                                countElement.textContent = currentCount;
                            }
                        }
                    }

                    // Animar remoção da notificação
                    if (notificationItem) {
                        notificationItem.style.transform = 'translateX(100%)';
                        notificationItem.style.opacity = '0';
                        setTimeout(() => {
                            notificationItem.remove();
                            if (document.querySelectorAll('.notification-item').length === 0) {
                                updateNotificationDisplay();
                            }
                        }, 300);
                    }

                    // Mostrar feedback visual
                    showNotificationFeedback('Notificação marcada como lida!', 'success');
                } else {
                    showNotificationFeedback('Erro ao marcar notificação como lida', 'error');
                }
            })
            .catch(error => {
                console.error('Erro na requisição AJAX:', error);
                showNotificationFeedback('Erro de conexão', 'error');
            });
        }

        function markAllAsRead() {
            const notificationItems = document.querySelectorAll('.notification-item');
            const notificationIds = Array.from(notificationItems).map(item => item.dataset.id);
            
            if (notificationIds.length === 0) return;

            fetch('marcar_todas_notificacoes_lidas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids: notificationIds })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remover badge
                    const countElement = document.getElementById('notificationCount');
                    if (countElement) {
                        countElement.remove();
                    }

                    // Animar remoção de todas as notificações
                    notificationItems.forEach((item, index) => {
                        setTimeout(() => {
                            item.style.transform = 'translateX(100%)';
                            item.style.opacity = '0';
                        }, index * 100);
                    });

                    setTimeout(() => {
                        updateNotificationDisplay();
                        showNotificationFeedback('Todas as notificações foram marcadas como lidas!', 'success');
                    }, notificationItems.length * 100 + 300);
                } else {
                    showNotificationFeedback('Erro ao marcar notificações como lidas', 'error');
                }
            })
            .catch(error => {
                console.error('Erro na requisição AJAX:', error);
                showNotificationFeedback('Erro de conexão', 'error');
            });
        }

        function updateNotificationDisplay() {
            const notificationBody = document.querySelector('.notification-body');
            const notificationHeader = document.querySelector('.notification-header');
            const notificationFooter = document.querySelector('.notification-footer');
            
            notificationBody.innerHTML = `
                <div class="no-notifications">
                    <div class="no-notifications-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <p>Sem novas notificações</p>
                    <span>Você está em dia!</span>
                </div>
            `;
            
            // Remover botões de ação quando não há notificações
            const markAllBtn = notificationHeader.querySelector('.mark-all-read');
            if (markAllBtn) markAllBtn.remove();
            if (notificationFooter) notificationFooter.remove();
        }

        function viewAllNotifications() {
            $('#allNotificationsModal').modal('show');
            // Aqui você pode carregar todas as notificações via AJAX se necessário
        }

        function showNotificationFeedback(message, type) {
            const feedback = document.createElement('div');
            feedback.className = `notification-feedback ${type}`;
            feedback.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;
            
            document.body.appendChild(feedback);
            
            setTimeout(() => {
                feedback.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                feedback.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(feedback);
                }, 300);
            }, 3000);
        }

        // Toggle dropdown de notificações
        document.getElementById('notificacaoLink').addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = document.getElementById('notificationList');
            const isOpen = dropdown.classList.contains('show');
            
            // Fechar outros dropdowns
            document.querySelectorAll('.notification-dropdown.show').forEach(d => {
                if (d !== dropdown) d.classList.remove('show');
            });
            
            dropdown.classList.toggle('show');
            
            // Adicionar animação de sino
            if (!isOpen) {
                const bellIcon = this.querySelector('.bell-icon');
                bellIcon.classList.add('ring');
                setTimeout(() => bellIcon.classList.remove('ring'), 600);
            }
        });

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('notificationList');
            const trigger = document.getElementById('notificacaoLink');
            
            if (!trigger.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Função para toggle do sidebar em mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const caderno = document.querySelector('.caderno');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            sidebar.classList.toggle('open');
            if (caderno) caderno.classList.toggle('sidebar-open');
            
            // Mudar ícone do botão
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('i');
                if (sidebar.classList.contains('open')) {
                    icon.className = 'fas fa-times';
                } else {
                    icon.className = 'fas fa-bars';
                }
            }
        }

        // Fechar sidebar ao clicar fora (mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth <= 768 && 
                sidebar && toggleBtn &&
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target) && 
                sidebar.classList.contains('open')) {
                toggleSidebar();
            }
        });

        // Ajustar layout ao redimensionar a janela
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const caderno = document.querySelector('.caderno');
            
            if (window.innerWidth > 768 && sidebar) {
                sidebar.classList.remove('open');
                if (caderno) caderno.classList.remove('sidebar-open');
                const toggleBtn = document.querySelector('.sidebar-toggle');
                if (toggleBtn) {
                    const icon = toggleBtn.querySelector('i');
                    if (icon) icon.className = 'fas fa-bars';
                }
            }
        });

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar animação inicial ao badge se houver notificações
            const badge = document.getElementById('notificationCount');
            if (badge) {
                setTimeout(() => {
                    badge.classList.add('pulse');
                }, 1000);
            }
        });
    </script>
</body>
</html>
