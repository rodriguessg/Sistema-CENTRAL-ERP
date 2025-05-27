<?php
// Parâmetros recebidos pela URL
$mensagem = isset($_GET['mensagem']) ? $_GET['mensagem'] : 'Mensagem padrão';
$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 'home.php'; // Página padrão

// Mensagens configuradas
$mensagens = [
    'inativo' => 'Usuário inativo. Entre em contato com o administrador.',
    '404' => 'Erro de conexão com o servidor.',
    'acesso_negado' => 'Você não tem acesso a este perfil.',
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagem</title>
     <link rel="stylesheet" href="src/style/mensagem.css">
   
</head>
<body>
    <!-- Modal -->
    <div id="customModal" class="modal-background" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">Aviso</div>
            <div class="modal-body" id="modalMessage"><?php echo $message; ?></div>
            <div class="modal-footer">
                <button onclick="closeModal()">OK</button>
            </div>
        </div>
    </div>

    <script>
        // Função para exibir o modal
        function showModal(message) {
            document.getElementById("modalMessage").innerText = message;
            document.getElementById("customModal").style.display = "flex";
        }

        // Função para fechar o modal e redirecionar para a página dinâmica
        function closeModal() {
            document.getElementById("customModal").style.display = "none";
            // Redireciona para a página capturada pelo PHP
            window.location.href = "<?php echo $pagina; ?>"; // Redirecionamento para a página capturada pela URL
        }

        // Exibe o modal ao carregar a página com a mensagem do PHP
        showModal('<?php echo $message; ?>');
    </script>
</body>
</html>
