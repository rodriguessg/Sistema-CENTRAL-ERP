<?php
// Incluir a conexão com o banco de dados
include 'banco.php';

// Verificar se o usuário está logado
session_start();
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Você precisa estar logado para acessar esta página.'); window.location.href='login.php';</script>";
    exit();
}

// Recuperar configurações gerais do sistema
$query_config = "SELECT * FROM configuracoes WHERE id = 1";
$resultado_config = $con->query($query_config);

if ($resultado_config === false) {
    echo "<script>alert('Erro ao verificar configurações do sistema.'); window.location.href='login.php';</script>";
    exit();
}

if ($resultado_config->num_rows > 0) {
    $config = $resultado_config->fetch_assoc();
} else {
    $config = [
        'nome_sistema' => '',
        'email_sistema' => '',
        'logotipo_sistema' => '',
        'tema_sistema' => 'claro',
        'painelalmoxarifado' => 1,
        'painelfinanceiro' => 1,
        'painelrh' => 1,
        'descricao_sistema' => ''
    ];
}

// Processar atualização do estoque mínimo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['estoque_minimo'])) {
    foreach ($_POST['estoque_minimo'] as $produto_id => $estoque_minimo) {
        $estoque_minimo = (int)$estoque_minimo;
        $query_update = "UPDATE produtos SET estoque_minimo = $estoque_minimo WHERE id = $produto_id";
        $con->query($query_update);
    }
    echo "<script>alert('Configurações de estoque mínimo atualizadas com sucesso!');</script>";
}

// Obter todos os produtos
$query_produtos = "SELECT id, classificacao, quantidade, estoque_minimo FROM produtos";
$resultado_produtos = $con->query($query_produtos);

include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Sistema - <?php echo htmlspecialchars($config['nome_sistema']); ?></title>
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css">
    <link rel="stylesheet" href="./src/style/configura.css">
    <style>
        :root {
            --cor-primaria: #005a92;
            --cor-secundaria: #ffffff;
            --cor-hover: #003f66;
            --cor-accent: #00aaff;
            --cor-texto: #333;
            --cor-fundo: #f5f5f5;
            --cor-gradiente: linear-gradient(135deg, #005a92, #007bc1);
        }

        [data-theme="escuro"] {
            --cor-secundaria: #1a1a1a;
            --cor-texto: #ffffff;
            --cor-fundo: #333;
        }

        body {
            margin: 0;
            padding-bottom: 80px;
            font-family: 'Arial', sans-serif;
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
        }

        .configuracao-estoque {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: var(--cor-secundaria);
        }

        .configuracao-estoque h3 {
            margin-bottom: 15px;
        }

        .configuracao-estoque table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .configuracao-estoque th, .configuracao-estoque td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .configuracao-estoque th {
            background-color: #f2f2f2;
        }

        .configuracao-estoque input[type="number"] {
            width: 100px;
        }

        .configuracao-estoque button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .configuracao-estoque button:hover {
            background-color: #45a049;
        }

        footer {
            background: var(--cor-gradiente);
            color: var(--cor-secundaria);
            text-align: center;
            padding: 15px;
            width: 100%;
            position: fixed;
            bottom: 0;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            transition: background 0.3s ease;
            z-index: 10;
        }

        footer:hover {
            background: var(--cor-hover);
        }

        footer p {
            margin: 0;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .version, .company-info, .newsletter, .social-link {
            cursor: pointer;
            text-decoration: underline;
            transition: color 0.3s ease;
        }

        .version:hover, .company-info:hover, .newsletter:hover, .social-link:hover {
            color: var(--cor-accent);
        }

        .social-link img {
            width: 20px;
            vertical-align: middle;
            margin-left: 5px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: var(--cor-secundaria);
            color: var(--cor-texto);
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            position: relative;
            opacity: 0;
            transform: scale(0.7);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .modal-content.show {
            opacity: 1;
            transform: scale(1);
        }

        .modal-content h2 {
            margin-top: 0;
            color: var(--cor-primaria);
            font-size: 1.5em;
        }

        .modal-content p, .modal-content label {
            font-size: 14px;
            line-height: 1.5;
        }

        .modal-content input, .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .modal-content button {
            background-color: var(--cor-primaria);
            color: var(--cor-secundaria);
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.3s ease;
        }

        .modal-content button:hover {
            background-color: var(--cor-hover);
        }

        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: var(--cor-texto);
            transition: color 0.2s ease;
        }

        .close:hover {
            color: var(--cor-accent);
        }

        /* Accessibility */
        .modal-content:focus-within, button:focus, .version:focus, .company-info:focus, .newsletter:focus, .social-link:focus {
            outline: 2px solid var(--cor-accent);
            outline-offset: 2px;
        }
    </style>
</head>
<body data-theme="<?php echo htmlspecialchars($config['tema_sistema']); ?>">
    <div class="container">
        <h1>Configurações do Sistema</h1>

        <!-- Configurações Gerais -->
        <div class="configuracoes-gerais">
            <h3>Configurações Gerais</h3>
            <form action="salvar_configuracoes.php" method="POST" enctype="multipart/form-data">
                <p>
                    <label for="nome_sistema">Nome do Sistema:</label>
                    <input type="text" id="nome_sistema" name="nome_sistema" value="<?php echo htmlspecialchars($config['nome_sistema']); ?>" required>
                </p>
                <p>
                    <label for="logotipo_sistema">Logotipo do Sistema:</label>
                    <input type="file" id="logotipo_sistema" name="logotipo_sistema">
                </p>
                <p>
                    <label for="email_sistema">Email do Sistema:</label>
                    <input type="email" id="email_sistema" name="email_sistema" value="<?php echo htmlspecialchars($config['email_sistema']); ?>" required>
                </p>
                <p>
                    <label for="descricao_sistema">Descrição do Sistema:</label>
                    <textarea id="descricao_sistema" name="descricao_sistema"><?php echo htmlspecialchars($config['descricao_sistema']); ?></textarea>
                </p>
                <button type="submit">Salvar Configurações</button>
            </form>
        </div>

        <!-- Configuração de Estoque Mínimo -->
        <div class="configuracao-estoque">
            <h3>Configurar Estoque Mínimo dos Produtos</h3>
            <form method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Classificação</th>
                            <th>Quantidade Atual</th>
                            <th>Estoque Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($produto = $resultado_produtos->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($produto['classificacao']); ?></td>
                                <td><?php echo htmlspecialchars($produto['quantidade']); ?></td>
                                <td>
                                    <input type="number" name="estoque_minimo[<?php echo $produto['id']; ?>]" 
                                           value="<?php echo htmlspecialchars($produto['estoque_minimo']); ?>" min="0">
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <button type="submit">Salvar Configurações de Estoque</button>
            </form>
        </div>

        <!-- Alteração de Tema -->
        <div class="configuracao-tema">
            <h3>Alterar Tema do Sistema</h3>
            <form action="salvar_tema.php" method="POST">
                <label for="tema_sistema">Selecione o Tema:</label>
                <select id="tema_sistema" name="tema_sistema">
                    <option value="claro" <?php echo ($config['tema_sistema'] == 'claro') ? 'selected' : ''; ?>>Claro</option>
                    <option value="escuro" <?php echo ($config['tema_sistema'] == 'escuro') ? 'selected' : ''; ?>>Escuro</option>
                </select>
                <button type="submit">Salvar Tema</button>
            </form>
        </div>

        <!-- Gerenciamento de Visibilidade dos Painéis -->
        <div class="gerenciamento-paineis">
            <h3>Gerenciar Visibilidade dos Painéis</h3>
            <form action="salvar_painel.php" method="POST">
                <p>
                    <label for="painelalmoxarifado">Exibir Painel de Almoxarifado:</label>
                    <input type="checkbox" id="painelalmoxarifado" name="painelalmoxarifado" 
                           <?php echo ($config['painelalmoxarifado'] == 1) ? 'checked' : ''; ?>>
                </p>
                <p>
                    <label for="painelfinanceiro">Exibir Painel Financeiro:</label>
                    <input type="checkbox" id="painelfinanceiro" name="painelfinanceiro" 
                           <?php echo ($config['painelfinanceiro'] == 1) ? 'checked' : ''; ?>>
                </p>
                <p>
                    <label for="painelrh">Exibir Painel RH:</label>
                    <input type="checkbox" id="painelrh" name="painelrh" 
                           <?php echo ($config['painelrh'] == 1) ? 'checked' : ''; ?>>
                </p>
                <button type="submit">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <footer role="contentinfo">
        <p>
            Desenvolvido por Gabriel Rodrigues e Maik Alves | 
            <?php echo htmlspecialchars($config['nome_sistema']); ?> - Versão: <span class="version" onclick="showVersionInfo()" role="button" tabindex="0" aria-label="Ver informações da versão">1.0.0</span> | 
            <span class="company-info" onclick="showCompanyInfo()" role="button" tabindex="0" aria-label="Ver informações da empresa">Sobre a Empresa</span> | 
            <span class="newsletter" onclick="showNewsletterForm()" role="button" tabindex="0" aria-label="Inscrever-se na newsletter">Newsletter</span> |
            <a class="social-link" href="https://twitter.com/govrj" target="_blank" aria-label="Twitter do Governo RJ">
                Twitter<img src="https://img.icons8.com/ios-filled/50/ffffff/twitter.png" alt="">
            </a> |
            <a class="social-link" href="https://www.rj.gov.br/transparencia" target="_blank" aria-label="Portal da Transparência">Transparência</a> |
            © 2025 Todos os direitos reservados
        </p>
    </footer>

    <!-- Version Info Modal -->
    <div id="versionModal" class="modal" role="dialog" aria-labelledby="versionModalTitle">
        <div class="modal-content">
            <span class="close" onclick="closeModal('versionModal')" role="button" tabindex="0" aria-label="Fechar modal">×</span>
            <h2 id="versionModalTitle">Informações da Versão</h2>
            <p><strong><?php echo htmlspecialchars($config['nome_sistema']); ?></strong> - Versão: <span id="versionNumber">1.0.0</span></p>
            <p><strong>Última atualização:</strong> <span id="lastUpdate"><?php echo date('d/m/Y'); ?></span></p>
            <p><strong>Desenvolvido por:</strong> Gabriel Rodrigues e Maik Alves</p>
            <p>Este sistema está em constante evolução para atender às necessidades da gestão pública do Rio de Janeiro.</p>
        </div>
    </div>

    <!-- Company Info Modal -->
    <div id="companyModal" class="modal" role="dialog" aria-labelledby="companyModalTitle">
        <div class="modal-content">
            <span class="close" onclick="closeModal('companyModal')" role="button" tabindex="0" aria-label="Fechar modal">×</span>
            <h2 id="companyModalTitle">Sobre a <?php echo htmlspecialchars($config['nome_sistema']); ?></h2>
            <p><strong><?php echo htmlspecialchars($config['nome_sistema']); ?></strong> é um sistema integrado de gestão pública desenvolvido para otimizar os processos administrativos do Governo do Estado do Rio de Janeiro. Nossa missão é promover eficiência, transparência e inovação na administração pública, em alinhamento com as diretrizes do portal oficial do governo (<a href="https://www.rj.gov.br" target="_blank">www.rj.gov.br</a>).</p>
            <p><strong>Fundação:</strong> 2024</p>
            <p><strong>Sede:</strong> Palácio Guanabara, Rio de Janeiro, RJ</p>
            <p><strong>Missão:</strong> Simplificar a gestão pública com tecnologia avançada e dados abertos.</p>
            <p><strong>Contato:</strong> <a href="mailto:<?php echo htmlspecialchars($config['email_sistema']); ?>"><?php echo htmlspecialchars($config['email_sistema']); ?></a></p>
            <button onclick="openContactForm()" role="button" aria-label="Abrir formulário de contato">Entre em Contato</button>
        </div>
    </div>

    <!-- Contact Form Modal -->
    <div id="contactModal" class="modal" role="dialog" aria-labelledby="contactModalTitle">
        <div class="modal-content">
            <span class="close" onclick="closeModal('contactModal')" role="button" tabindex="0" aria-label="Fechar modal">×</span>
            <h2 id="contactModalTitle">Entre em Contato</h2>
            <p>Envie sua mensagem para a equipe da <?php echo htmlspecialchars($config['nome_sistema']); ?>:</p>
            <form id="contactForm">
                <label for="name">Nome:</label>
                <input type="text" id="name" placeholder="Seu nome" required aria-required="true">
                <label for="email">E-mail:</label>
                <input type="email" id="email" placeholder="Seu e-mail" required aria-required="true">
                <label for="message">Mensagem:</label>
                <textarea id="message" rows="4" placeholder="Sua mensagem" required aria-required="true"></textarea>
                <button type="button" onclick="submitContactForm()" aria-label="Enviar mensagem">Enviar</button>
            </form>
        </div>
    </div>

    <!-- Newsletter Subscription Modal -->
    <div id="newsletterModal" class="modal" role="dialog" aria-labelledby="newsletterModalTitle">
        <div class="modal-content">
            <span class="close" onclick="closeModal('newsletterModal')" role="button" tabindex="0" aria-label="Fechar modal">×</span>
            <h2 id="newsletterModalTitle">Inscreva-se na Newsletter</h2>
            <p>Receba atualizações sobre o <?php echo htmlspecialchars($config['nome_sistema']); ?> e notícias do Governo do Rio de Janeiro.</p>
            <form id="newsletterForm">
                <label for="newsletterEmail">E-mail:</label>
                <input type="email" id="newsletterEmail" placeholder="Seu e-mail" required aria-required="true">
                <button type="button" onclick="submitNewsletterForm()" aria-label="Inscrever-se na newsletter">Inscrever-se</button>
            </form>
        </div>
    </div>

    <script>
        // Simulate API call for version and update date
        async function fetchVersionInfo() {
            // Simulated API response (replace with actual DB query if needed)
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        version: '1.0.0',
                        lastUpdate: '<?php echo date('d/m/Y'); ?>'
                    });
                }, 1000);
            });
        }

        // Show version info modal
        function showVersionInfo() {
            const modal = document.getElementById('versionModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.modal-content').classList.add('show'), 10);
        }

        // Show company info modal
        function showCompanyInfo() {
            const modal = document.getElementById('companyModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.modal-content').classList.add('show'), 10);
        }

        // Open contact form modal
        function openContactForm() {
            closeModal('companyModal');
            const modal = document.getElementById('contactModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.modal-content').classList.add('show'), 10);
        }

        // Open newsletter form modal
        function showNewsletterForm() {
            const modal = document.getElementById('newsletterModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.modal-content').classList.add('show'), 10);
        }

        // Close modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.querySelector('.modal-content').classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        // Simulate contact form submission
        function submitContactForm() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('message').value;

            if (name && email && message) {
                alert(`Mensagem enviada com sucesso!\nNome: ${name}\nE-mail: ${email}\nMensagem: ${message}`);
                closeModal('contactModal');
                document.getElementById('contactForm').reset();
            } else {
                alert('Por favor, preencha todos os campos.');
            }
        }

        // Simulate newsletter form submission
        function submitNewsletterForm() {
            const email = document.getElementById('newsletterEmail').value;
            if (email) {
                alert(`Inscrição realizada com sucesso!\nE-mail: ${email}`);
                closeModal('newsletterModal');
                document.getElementById('newsletterForm').reset();
            } else {
                alert('Por favor, insira um e-mail válido.');
            }
        }

        // Update version dynamically
        document.addEventListener('DOMContentLoaded', async () => {
            const versionSpan = document.querySelector('.version');
            const versionNumber = document.getElementById('versionNumber');
            const lastUpdate = document.getElementById('lastUpdate');

            try {
                const { version, lastUpdate: updateDate } = await fetchVersionInfo();
                versionSpan.textContent = version;
                versionNumber.textContent = version;
                lastUpdate.textContent = updateDate;
            } catch (error) {
                console.error('Erro ao buscar versão:', error);
            }
        });

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                closeModal('versionModal');
                closeModal('companyModal');
                closeModal('contactModal');
                closeModal('newsletterModal');
            }
        };

        // Keyboard accessibility
        document.querySelectorAll('.version, .company-info, .newsletter, .close, button').forEach(element => {
            element.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.target.click();
                }
            });
        });
    </script>
</body>
</html>