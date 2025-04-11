<?php
// Incluir a conexão com o banco de dados
include 'banco.php';

// Verificar se o usuário está logado (opcional, mas necessário se a página for restrita a administradores)
session_start();

// Caso não esteja logado, redireciona para a página de login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Você precisa estar logado para acessar esta página.'); window.location.href='login.php';</script>";
    exit();
}

// Recuperar configurações gerais do sistema
$query_config = "SELECT * FROM configuracoes WHERE id = 1";
$resultado_config = $con->query($query_config);

// Verificar se a consulta retornou resultados
if ($resultado_config === false) {
    echo "<script>alert('Erro ao verificar configurações do sistema.'); window.location.href='login.php';</script>";
    exit();
}

if ($resultado_config->num_rows > 0) {
    // Caso a configuração já tenha sido realizada, recuperar os dados
    $config = $resultado_config->fetch_assoc();
} else {
    // Caso não exista configuração, inicializar a configuração com valores padrões
    $config = [
        'nome_sistema' => '',
        'email_sistema' => '',
        'logotipo_sistema' => '',
        'tema_sistema' => 'claro', // Valor padrão do tema
        'painelalmoxarifado' => 1,     // Ativar painel de almoxarifado por padrão
        'painelfinanceiro' => 1,  // Ativar painel financeiro por padrão
        'painelrh' => 1,          // Ativar painel de RH por padrão
        'descricao_sistema' => ''
    ];
}

include 'header.php'; // Inclui o cabeçalho
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações do Sistema</title>
    <link rel="stylesheet" href="src/estoque/style/estoque-conteudo2.css"> <!-- Link para a folha de estilos -->
    <link rel="stylesheet" href="./src/style/configura.css">
</head>
<body>
    <div class="container">
        <h1>Configurações do Sistema</h1>

        <!-- Configurações Gerais -->
        <div class="configuracoes-gerais">
            <h3>Configurações Gerais</h3>
            <form action="salvar_configuracoes.php" method="POST" enctype="multipart/form-data">
                <p><label for="nome_sistema">Nome do Sistema:</label>
                <input type="text" id="nome_sistema" name="nome_sistema" value="<?php echo htmlspecialchars($config['nome_sistema']); ?>" required></p>

                <p><label for="logotipo_sistema">Logotipo do Sistema:</label>
                <input type="file" id="logotipo_sistema" name="logotipo_sistema"></p>

                <p><label for="email_sistema">Email do Sistema:</label>
                <input type="email" id="email_sistema" name="email_sistema" value="<?php echo htmlspecialchars($config['email_sistema']); ?>" required></p>

                <p><label for="descricao_sistema">Descrição do Sistema:</label>
                <textarea id="descricao_sistema" name="descricao_sistema"><?php echo htmlspecialchars($config['descricao_sistema']); ?></textarea></p>

                <button type="submit">Salvar Configurações</button>
            </form>
        </div>
        <div class="form-container">
        <?php
// Conexão com o banco de dados
include 'banco.php';

// Obter todos os produtos
$query_produtos = "SELECT id, classificacao, quantidade, estoque_minimo FROM produtos";
$resultado_produtos = $con->query($query_produtos);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar o estoque mínimo de cada produto
    foreach ($_POST['estoque_minimo'] as $produto_id => $estoque_minimo) {
        $estoque_minimo = (int)$estoque_minimo;
        $query_update = "UPDATE produtos SET estoque_minimo = $estoque_minimo WHERE id = $produto_id";
        $con->query($query_update);
    }
    echo "Configurações de estoque mínimo atualizadas com sucesso!";
}
?>

    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Classificação</th>
                    <th>Quantidade Atual</th>
                    <th>Estoque Mínimo</th>
                </tr>
            </thead>
            <!-- <tbody style="display: block; max-height: 200px; overflow-y: auto; display: block; height: 200px; width: 100%; overflow-x: hidden; overflow-y: scroll;"> -->
            <tbody>
                <?php while ($produto = $resultado_produtos->fetch_assoc()) { ?>
                    <tr>
                        <!-- Exibe a classificação do produto -->
                        <td><?= $produto['classificacao'] ?></td>
                        
                        <!-- Exibe a quantidade atual do produto -->
                        <td><?= $produto['quantidade'] ?></td>
                        
                        <!-- Campo para definir o estoque mínimo -->
                        <td><input type="number" name="estoque_minimo[<?= $produto['id'] ?>]" value="<?= $produto['estoque_minimo'] ?>" min="0"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="submit">Salvar Configurações</button>
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
                <p><label for="painelalmoxarifado">Exibir Painel de Almoxarifado:</label>
                <input type="checkbox" id="painelalmoxarifado" name="painelalmoxarifado" <?php echo ($config['painelalmoxarifado'] == 1) ? 'checked' : ''; ?>></p>

                <p><label for="painelfinanceiro">Exibir Painel Financeiro:</label>
                <input type="checkbox" id="painelfinanceiro" name="painelfinanceiro" <?php echo ($config['painelfinanceiro'] == 1) ? 'checked' : ''; ?>></p>

                <p><label for="painelrh">Exibir Painel RH:</label>
                <input type="checkbox" id="painelrh" name="painelrh" <?php echo ($config['painelrh'] == 1) ? 'checked' : ''; ?>></p>

                <button type="submit">Salvar Alterações</button>
            </form>
        </div>
    </div>
</body>
</html>
