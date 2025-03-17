
    function abrirModal() {
        document.getElementById('modal-esqueci-senha').style.display = 'flex';
    }

    function fecharModal() {
        document.getElementById('modal-esqueci-senha').style.display = 'none';
    }

    function verificarUsuario() {
        const username = document.getElementById('username-recover').value;
        const email = document.getElementById('email-recover').value;

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
                document.getElementById('form-esqueci-senha').style.display = 'none';
                document.getElementById('form-nova-senha').style.display = 'block';
            } else {
                alert('Usuário ou e-mail não encontrado.');
            }
        });
    }

    function atualizarSenha() {
        const novaSenha = document.getElementById('nova-senha').value;
        const username = document.getElementById('username-recover').value;

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
        });
    }
    function togglePassword() {
      const passwordInput = document.getElementById('senha');
      const showPasswordCheckbox = document.getElementById('mostrar-senha');

      passwordInput.type = showPasswordCheckbox.checked ? 'text' : 'password';
    }
