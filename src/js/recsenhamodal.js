// Função para abrir o modal (mostrar o modal)
function abrirModal() {
    // Seleciona o modal pelo ID
    const modal = document.getElementById('modal-esqueci-senha');
    
    // Exibe o modal, configurando seu estilo para ser visível (com display 'flex')
    modal.style.display = 'flex';
    
    // Inicializa o modal com opacidade 0 (invisível) e deslocado 50px para cima (fora da tela)
    modal.style.opacity = '0';
    modal.style.transform = 'translateY(-50px)';

    // Define um pequeno atraso para garantir que a transição de animação seja aplicada
    setTimeout(() => {
        // Define a transição de opacidade e transformação para criar o efeito de animação
        modal.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        
        // Anima a opacidade para 1 (tornando o modal visível) e move o modal para sua posição original
        modal.style.opacity = '1';
        modal.style.transform = 'translateY(0)';
    }, 10); // O pequeno atraso de 10 milissegundos é adicionado para que o navegador aplique a animação
}

// Função para fechar o modal (ocultar o modal)
function fecharModal() {
    // Seleciona o modal pelo ID
    const modal = document.getElementById('modal-esqueci-senha');
    
    // Define a transição de opacidade e transformação para criar o efeito de animação de fechamento
    modal.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    
    // Anima a opacidade para 0 (tornando o modal invisível) e move o modal 50px para cima (fora da tela)
    modal.style.opacity = '0';
    modal.style.transform = 'translateY(-50px)';

    // Define um tempo de espera para garantir que a animação de fechamento seja concluída antes de esconder o modal
    setTimeout(() => {
        // Oculta o modal ao definir o display como 'none'
        modal.style.display = 'none';
    }, 300); // A duração do tempo de espera é correspondente à duração da animação (300ms)
}


// Função para verificar o usuário com o e-mail
function verificarUsuario() {
    const username = document.getElementById('username-recover').value;
    const email = document.getElementById('email-recover').value;

    if (username && email) {
        // Fazer requisição para verificar usuário
        fetch('verificar_usuario.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Usuário encontrado. Digite sua nova senha.');
                // Esconde o formulário de verificação e mostra o de nova senha
                document.getElementById('form-esqueci-senha').style.display = 'none';
                document.getElementById('form-nova-senha').style.display = 'block';
            } else {
                alert('Usuário ou e-mail não encontrado.');
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Erro ao verificar usuário. Tente novamente.');
        });
    } else {
        alert('Por favor, preencha ambos os campos.');
    }
}

// Função para atualizar a senha
function atualizarSenha() {
    const novaSenha = document.getElementById('nova-senha').value;
    const username = document.getElementById('username-recover').value;

    if (novaSenha) {
        // Fazer requisição para atualizar a senha
        fetch('atualizar_senha.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, novaSenha })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Senha atualizada com sucesso.');
                fecharModal();
            } else {
                alert('Erro ao atualizar senha.');
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Erro ao atualizar a senha. Tente novamente.');
        });
    } else {
        alert('Por favor, insira uma nova senha.');
    }
}

// Função para alternar entre visualizar e ocultar a senha
// Função para alternar entre visualizar e ocultar a senha
function togglePassword() {
    const passwordInput = document.getElementById('senha'); // Obtém o campo de senha
    const showPasswordCheckbox = document.getElementById('mostrar-senha'); // Obtém o checkbox

    // Alterna o tipo de input entre 'text' (mostrar senha) e 'password' (ocultar senha)
    passwordInput.type = showPasswordCheckbox.checked ? 'text' : 'password';
}


