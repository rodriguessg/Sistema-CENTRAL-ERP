<?php
// Parâmetros recebidos pela URL
$mensagem = isset($_GET['mensagem']) ? $_GET['mensagem'] : 'Mensagem padrão';
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : './mensagem.php'; // Página padrão

// Mensagens configuradas
$mensagens = [
    'inativo' => 'Usuário inativo. Entre em contato com o administrador.',
    '404' => 'Erro de conexão com o servidor.',
    'Cadastrado_contratos_sucesso' => 'Contrato cadastrado com sucesso.',
    'Erro_ao_cadastrar_contrato' => 'Erro ao cadastrar o contrato. Verifique os dados e tente novamente.',
    'Contrato_atualizado' => 'O Contrato foi atualizado!',
    'acesso_negado' => 'Você não tem acesso a este perfil.',
    'deslogado' => 'Logout realizado com sucesso.',
    'senha_invalida' => 'Usuário ou senha inválida.',
    'usuario_invalido' => 'Usuário não encontrado.',
    'setor_nao_reconhecido' => 'Setor não reconhecido.',
    'event' => 'Evento cadastrado com sucesso.',
    'erro_adicionar_usuario' => 'Já existe um usuário com este e-mail ou matrícula.',
    'novo_usuario_adicionado' => 'O novo usuário foi adicionado com sucesso.',
    'sucesso' => 'BP cadastrado com sucesso e registrado no log de eventos.',
    'sucesso2' => 'Produto cadastrado e registrado com sucesso',
    'sucesso3' => 'Seu lembre foi agendado e encaminhado por email com sucesso.',
    'produto_existente' => 'Produto já existe no sistema. Quantidade atualizada com sucesso.',
    'produto_adicionado' => 'Produto cadastrado com sucesso.',
    'produto_retirado' => 'Produto retirado com sucesso!',
    'estoque_insuficiente' => 'Estoque insuficiente para retirada.',
    'produto_nao_encontrado' => 'Produto não encontrado no estoque.',
    'padrao' => 'Ocorreu um erro inesperado. Chame o T.i!'
];

// Verifica a mensagem passada e utiliza uma mensagem padrão, caso não tenha sido definida
$message = isset($mensagens[$mensagem]) ? $mensagens[$mensagem] : $mensagens['padrao'];

// Determina o tipo de mensagem para aplicar estilos diferentes
$tipo_mensagem = 'info'; // padrão
if (strpos($mensagem, 'sucesso') !== false || strpos($mensagem, 'Cadastrado') !== false || 
    strpos($mensagem, 'atualizado') !== false || strpos($mensagem, 'adicionado') !== false ||
    strpos($mensagem, 'event') !== false || strpos($mensagem, 'retirado') !== false ||
    strpos($mensagem, 'deslogado') !== false) {
    $tipo_mensagem = 'success';
} elseif (strpos($mensagem, 'erro') !== false || strpos($mensagem, 'Erro') !== false || 
         strpos($mensagem, 'invalida') !== false || strpos($mensagem, 'negado') !== false ||
         strpos($mensagem, 'insuficiente') !== false || strpos($mensagem, 'nao_encontrado') !== false ||
         $mensagem === '404') {
    $tipo_mensagem = 'error';
} elseif (strpos($mensagem, 'inativo') !== false || strpos($mensagem, 'existente') !== false) {
    $tipo_mensagem = 'warning';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema ERP - Notificação</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../src/style/mensagem.css">
</head>
<body>
    <!-- Partículas flutuantes -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-container <?php echo $tipo_mensagem; ?>">
            <div class="modal-glow"></div>
            
            <button class="close-btn" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-header">
                <div class="modal-icon-container">
                    <div class="modal-icon <?php echo $tipo_mensagem; ?>" id="modalIcon">
                        <i class="fas fa-check" id="iconSymbol"></i>
                    </div>
                </div>
                <h2 class="modal-title" id="modalTitle">Notificação do Sistema</h2>
                <p class="modal-subtitle">Sistema ERP Integrado</p>
            </div>

            <div class="modal-body">
                <p class="modal-message" id="modalMessage"><?php echo htmlspecialchars($message); ?></p>
                
                <div class="modal-actions">
                    <button class="btn btn-primary" onclick="closeModal()">
                        <i class="fas fa-check"></i> Continuar
                    </button>
                </div>
            </div>

            <div class="progress-container">
                <div class="progress-bar" id="progressBar"></div>
            </div>
        </div>
    </div>

    <script>
        // Configurações dos tipos de mensagem
        const messageTypes = {
            success: {
                icon: 'fas fa-check-circle',
                title: 'Operação Realizada!',
                subtitle: 'Sucesso'
            },
            error: {
                icon: 'fas fa-exclamation-triangle',
                title: 'Ops! Algo deu errado',
                subtitle: 'Erro'
            },
            warning: {
                icon: 'fas fa-exclamation-circle',
                title: 'Atenção Necessária',
                subtitle: 'Aviso'
            },
            info: {
                icon: 'fas fa-info-circle',
                title: 'Informação',
                subtitle: 'Sistema'
            }
        };

        // Configurar o modal baseado no tipo de mensagem
        function setupModal() {
            const tipo = '<?php echo $tipo_mensagem; ?>';
            const config = messageTypes[tipo];
            
            if (config) {
                document.getElementById('iconSymbol').className = config.icon;
                document.getElementById('modalTitle').textContent = config.title;
            }

            // Adicionar efeitos sonoros (opcional)
            playNotificationSound(tipo);
        }

        // Função para tocar som de notificação
        function playNotificationSound(type) {
            // Criar contexto de áudio para efeitos sonoros
            if (typeof AudioContext !== 'undefined' || typeof webkitAudioContext !== 'undefined') {
                const audioContext = new (AudioContext || webkitAudioContext)();
                
                const frequencies = {
                    success: [523.25, 659.25, 783.99], // C5, E5, G5
                    error: [220, 185, 165], // A3, F#3, E3
                    warning: [440, 554.37], // A4, C#5
                    info: [523.25, 659.25] // C5, E5
                };

                const freq = frequencies[type] || frequencies.info;
                
                freq.forEach((frequency, index) => {
                    setTimeout(() => {
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        
                        oscillator.frequency.setValueAtTime(frequency, audioContext.currentTime);
                        oscillator.type = 'sine';
                        
                        gainNode.gain.setValueAtTime(0, audioContext.currentTime);
                        gainNode.gain.linearRampToValueAtTime(0.1, audioContext.currentTime + 0.01);
                        gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.2);
                        
                        oscillator.start(audioContext.currentTime);
                        oscillator.stop(audioContext.currentTime + 0.2);
                    }, index * 100);
                });
            }
        }

        // Função para fechar o modal
        function closeModal() {
            const overlay = document.getElementById('modalOverlay');
            const container = overlay.querySelector('.modal-container');
            
            // Animação de saída
            container.style.animation = 'modalSlideOut 0.4s cubic-bezier(0.4, 0, 1, 1) forwards';
            overlay.style.animation = 'fadeOut 0.5s ease-in 0.1s forwards';
            
            setTimeout(() => {
                window.location.href = '<?php echo $pagina; ?>';
            }, 600);
        }

        // Auto-close após 6 segundos
        let autoCloseTimer;
        function startAutoClose() {
            autoCloseTimer = setTimeout(() => {
                closeModal();
            }, 6000);
        }

        // Pausar auto-close quando hover no modal
        document.querySelector('.modal-container').addEventListener('mouseenter', () => {
            clearTimeout(autoCloseTimer);
            document.getElementById('progressBar').style.animationPlayState = 'paused';
        });

        document.querySelector('.modal-container').addEventListener('mouseleave', () => {
            startAutoClose();
            document.getElementById('progressBar').style.animationPlayState = 'running';
        });

        // Fechar com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Adicionar animações de saída ao CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes modalSlideOut {
                to {
                    transform: scale(0.8) translateY(40px);
                    opacity: 0;
                }
            }
            
            @keyframes fadeOut {
                to { opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Criar mais partículas dinamicamente
        function createParticles() {
            const particles = document.querySelector('.particles');
            for (let i = 0; i < 15; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 6 + 's';
                particle.style.animationDuration = (Math.random() * 3 + 4) + 's';
                particles.appendChild(particle);
            }
        }

        // Inicializar o modal
        document.addEventListener('DOMContentLoaded', () => {
            setupModal();
            startAutoClose();
            createParticles();
        });
    </script>
</body>
</html>
