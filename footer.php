<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP CENTRAL</title>
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

        body {
            margin: 0;
            padding-bottom: 80px;
            font-family: 'Arial', sans-serif;
            background-color: var(--cor-fundo);
        }

        footer {
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

            color:black;
            z-index: 10;
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
        .modal-footer {
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

        .modal-content-footer {
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

        .modal-content-footer.show {
            opacity: 1;
            transform: scale(1);
        }

        .modal-content-footer h2 {
            margin-top: 0;
            color: var(--cor-primaria);
            font-size: 1.5em;
        }

        .modal-content-footer p, .modal-content-footer label {
            font-size: 14px;
            line-height: 1.5;
        }

        .modal-content-footer input, .modal-content-footer textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .modal-content-footer button {
            background-color: var(--cor-primaria);
            color: var(--cor-secundaria);
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
            transition: background-color 0.3s ease;
        }

        .modal-content-footer button:hover {
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
        .modal-content-footer:focus-within, button:focus, .version:focus, .company-info:focus, .newsletter:focus, .social-link:focus {
            outline: 2px solid var(--cor-accent);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
    <footer role="contentinfo">
        <p>
            Desenvolvido por Gabriel Rodrigues e Maik Alves | 
            ERP CENTRAL Sistema - Versão: <span class="version" onclick="showVersionInfo()" role="button" tabindex="0" aria-label="Ver informações da versão">1.0.0</span> | 
            <span class="company-info" onclick="showCompanyInfo()" role="button" tabindex="0" aria-label="Ver informações da empresa">Sobre a Empresa</span> | 
            <span class="newsletter" onclick="showNewsletterForm()" role="button" tabindex="0" aria-label="Inscrever-se na newsletter">Newsletter</span> |
            <a class="social-link" href="https://www.instagram.com/central_rj/" target="_blank" aria-label="Instagram do Governo RJ">
                Instagram<img src="https://img.icons8.com/ios-filled/50/ffffff/instagram.png" alt="">
            </a> |
            <a class="social-link" href="https://www.rj.gov.br/central/node/379" target="_blank" aria-label="Portal da Transparência">Transparência</a> |
            © 2025 Todos os direitos reservados
        </p>
    </footer>

    <!-- Version Info Modal -->
    <div id="versionModal" class="modal-footer" role="dialog" aria-labelledby="versionModalTitle">
        <div class="modal-content-footer">
            <span class="close" onclick="closeModal('versionModal')" role="button" tabindex="0" aria-label="Fechar modal">×</span>
            <h2 id="versionModalTitle">Informações da Versão</h2>
            <p><strong>ERP CENTRAL</strong> - Versão: <span id="versionNumber">1.0.0</span></p>
            <p><strong>Última atualização:</strong> <span id="lastUpdate">21/05/2025</span></p>
            <p><strong>Desenvolvido por:</strong> Gabriel Rodrigues e Maik Alves</p>
            <p>Este sistema está em constante evolução para atender às necessidades da gestão pública do Rio de Janeiro.</p>
        </div>
    </div>

    <!-- Company Info Modal -->
    <div id="companyModal" class="modal-footer" role="dialog" aria-labelledby="companyModalTitle">
        <div class="modal-content-footer">
            <span class="close" onclick="closeModal('companyModal')" role="button" tabindex="0" aria-label="Fechar modal">×</span>
            <h2 id="companyModalTitle">Sobre a ERP CENTRAL</h2>
            <p><strong>ERP CENTRAL</strong> é um sistema integrado de gestão pública desenvolvido para otimizar os processos administrativos do Governo do Estado do Rio de Janeiro. Nossa missão é promover eficiência, transparência e inovação na administração pública, em alinhamento com as diretrizes do portal oficial do governo (<a href="https://www.rj.gov.br" target="_blank">www.rj.gov.br</a>).</p>
            <p><strong>Fundação:</strong> 2024</p>
            <p><strong>Sede:</strong>Av. Nossa Senhora de Copacabana, 493, Rio de Janeiro-RJ CEP: 22031-000</p>
            <p><strong>Missão:</strong> Simplificar a gestão pública com tecnologia avançada e dados abertos.</p>
            <p><strong>Contato:</strong> <a href="mailto:contato@erpcentral.rj.gov.br">contato@erpcentral.rj.gov.br</a></p>
            <button onclick="openContactForm()" role="button" aria-label="Abrir formulário de contato">Entre em Contato</button>
        </div>
    </div>

    <!-- Contact Form Modal -->
    <div id="contactModal" class="modal-footer" role="dialog" aria-labelledby="contactModalTitle">
        <div class="modal-content-footer">
            <span class="close" onclick="closeModal('contactModal')" role="button" tabindex="0" aria-label="Fechar modal">×</span>
            <h2 id="contactModalTitle">Entre em Contato</h2>
            <p>Envie sua mensagem para a equipe da ERP CENTRAL:</p>
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
    <div id="newsletterModal" class="modal-footer" role="dialog" aria-labelledby="newsletterModalTitle">
        <div class="modal-content-footer">
            <span class="close" onclick="closeModal('newsletterModal')" role="button" tabindex="0" aria-label="Fechar modal">×</span>
            <h2 id="newsletterModalTitle">Inscreva-se na Newsletter</h2>
            <p>Receba atualizações sobre o ERP CENTRAL e notícias do Governo do Rio de Janeiro.</p>
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
            // Simulated API response
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        version: '1.0.0',
                        lastUpdate: new Date().toLocaleDateString('pt-BR')
                    });
                }, 1000);
            });
        }

        // Show version info modal
        function showVersionInfo() {
            const modal = document.getElementById('versionModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.modal-content-footer').classList.add('show'), 10);
        }

        // Show company info modal
        function showCompanyInfo() {
            const modal = document.getElementById('companyModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.modal-content-footer').classList.add('show'), 10);
        }

        // Open contact form modal
        function openContactForm() {
            closeModal('companyModal');
            const modal = document.getElementById('contactModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.modal-content-footer').classList.add('show'), 10);
        }

        // Open newsletter form modal
        function showNewsletterForm() {
            const modal = document.getElementById('newsletterModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.querySelector('.modal-content-footer').classList.add('show'), 10);
        }

        // Close modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.querySelector('.modal-content-footer').classList.remove('show');
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