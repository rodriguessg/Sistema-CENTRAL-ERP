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
    'erro_adicionar_usuario' =>'Já existe um usuário com este e-mail ou matrícula.',
    'novo_usuario_adicionado' =>'O novo usuário foi adicionado com sucesso.',
    'sucesso' => 'BP cadastrado com sucesso e registrado no log de eventos.',
    'sucesso2'=> 'Produto cadastrado e registrado com sucesso',
    'sucesso3' => 'Seu lembre foi agendado e encaminhado por email com sucesso.',
    'produto_existente' => 'Produto já existe no sistema. Quantidade atualizada com sucesso.',
    'produto_adicionado' => 'Produto cadastrado com sucesso.',
    'produto_retirado' => 'Produto retirado com sucesso!',
   ' produto_retirado2' => 'Produto retirado com sucesso!',
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
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .modal-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .modal-content {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .modal-body {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .modal-footer button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-footer button:hover {
            background-color: #0056b3;
        }
    </style>
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
            window.location.href = "<?php echo $pagina; ?>";
        }

        // Exibe o modal ao carregar a página com a mensagem do PHP
        showModal('<?php echo $message; ?>');
    </script>
</body>
</html>
