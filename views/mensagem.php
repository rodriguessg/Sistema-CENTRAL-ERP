<?php
// Parâmetros recebidos pela URL
$mensagem = isset($_GET['mensagem']) ? $_GET['mensagem'] : 'Mensagem padrão';
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home.php'; // Página padrão

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
    'padrao' => 'Ocorreu um erro inesperado.'
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: url('../img/bk.png') center/cover no-repeat fixed;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Adicionar overlay para melhor contraste */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            z-index: 0;
            pointer-events: none;
        }

        /* Garantir que o modal fique acima do overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(20px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .modal-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.2),
                0 16px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            max-width: 520px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            transform: scale(0.8) translateY(40px);
            animation: modalSlideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s forwards;
            position: relative;
        }

        .modal-glow {
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, 
                rgba(74, 144, 226, 0.3), 
                rgba(80, 39, 122, 0.3), 
                rgba(74, 144, 226, 0.3));
            border-radius: 26px;
            z-index: -1;
            animation: glowPulse 3s ease-in-out infinite;
        }

        .modal-header {
            padding: 40px 40px 20px;
            text-align: center;
            position: relative;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        }

        .modal-icon-container {
            position: relative;
            display: inline-block;
            margin-bottom: 24px;
        }

        .modal-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            color: white;
            position: relative;
            z-index: 2;
            animation: iconBounce 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.3s both;
        }

        .modal-icon::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border-radius: 50%;
            background: inherit;
            opacity: 0.3;
            animation: iconRipple 2s ease-out infinite;
        }

        .modal-icon.success {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.3);
        }

        .modal-icon.error {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 20px 40px rgba(239, 68, 68, 0.3);
        }

        .modal-icon.warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 20px 40px rgba(245, 158, 11, 0.3);
        }

        .modal-icon.info {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.3);
        }

        .modal-title {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #1f2937, #374151);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .modal-subtitle {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modal-body {
            padding: 0 40px 40px;
            text-align: center;
        }

        .modal-message {
            font-size: 17px;
            line-height: 1.7;
            color: #374151;
            margin-bottom: 32px;
            font-weight: 400;
        }

        .modal-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: none;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
            min-width: 140px;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a90e2, #5027a0);
            color: white;
            box-shadow: 0 8px 24px rgba(74, 144, 226, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(74, 144, 226, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            color: #6b7280;
            border: 2px solid rgba(107, 114, 128, 0.2);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .progress-container {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #4a90e2, #5027a0);
            border-radius: 0 0 24px 24px;
            animation: progressAnimation 6s linear;
            box-shadow: 0 -2px 8px rgba(74, 144, 226, 0.3);
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            font-size: 18px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #374151;
            transform: rotate(90deg);
        }

        /* Animações */
        @keyframes fadeIn {
            to { opacity: 1; }
        }

        @keyframes modalSlideIn {
            to {
                transform: scale(1) translateY(0);
            }
        }

        @keyframes iconBounce {
            0% { transform: scale(0) rotate(180deg); }
            50% { transform: scale(1.1) rotate(0deg); }
            100% { transform: scale(1) rotate(0deg); }
        }

        @keyframes iconRipple {
            0% { transform: scale(1); opacity: 0.3; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        @keyframes glowPulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 0.8; }
        }

        @keyframes progressAnimation {
            from { width: 100%; }
            to { width: 0%; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(-10px) rotate(240deg); }
        }

        @keyframes modalSlideOut {
            to {
                transform: scale(0.8) translateY(40px);
                opacity: 0;
            }
        }

        @keyframes fadeOut {
            to { opacity: 0; }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .modal-container {
                width: 95%;
                margin: 20px;
            }

            .modal-header {
                padding: 30px 25px 15px;
            }

            .modal-body {
                padding: 0 25px 30px;
            }

            .modal-icon {
                width: 80px;
                height: 80px;
                font-size: 35px;
            }

            .modal-title {
                font-size: 24px;
            }

            .modal-message {
                font-size: 15px;
            }

            .btn {
                padding: 12px 28px;
                font-size: 15px;
                min-width: 120px;
            }
        }

        @media (max-width: 480px) {
            .modal-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .modal-header {
                padding: 25px 20px 15px;
            }

            .modal-body {
                padding: 0 20px 25px;
            }
        }

        /* Efeitos especiais para diferentes tipos */
        .modal-container.success .modal-glow {
            background: linear-gradient(45deg, 
                rgba(16, 185, 129, 0.3), 
                rgba(5, 150, 105, 0.3), 
                rgba(16, 185, 129, 0.3));
        }

        .modal-container.error .modal-glow {
            background: linear-gradient(45deg, 
                rgba(239, 68, 68, 0.3), 
                rgba(220, 38, 38, 0.3), 
                rgba(239, 68, 68, 0.3));
        }

        .modal-container.warning .modal-glow {
            background: linear-gradient(45deg, 
                rgba(245, 158, 11, 0.3), 
                rgba(217, 119, 6, 0.3), 
                rgba(245, 158, 11, 0.3));
        }
    </style>
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
