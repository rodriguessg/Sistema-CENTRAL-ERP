<?php
session_start();

// Incluir a conexão com o banco de dados
include 'banco.php';

// Recuperar os dados iniciais da tabela log_eventos
$query = "SELECT * FROM log_eventos ORDER BY data_operacao DESC"; // Ordena por data de operação (mais recente primeiro)
$resultado = $con->query($query);

// Verifica se a consulta retornou algum resultado
if ($resultado === false) {
    die("Erro ao recuperar os logs: " . $con->error);
}

// Recuperar mensagens de depuração do envio de e-mail
$emailDebugMessages = isset($_SESSION['email_debug']) ? $_SESSION['email_debug'] : [];

// Limpar as mensagens de depuração após exibi-las (opcional)
// unset($_SESSION['email_debug']);

include 'header.php'; // Inclui o cabeçalho
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs e Debug do Sistema</title>
    <link rel="stylesheet" href="style.css"> <!-- Link para a folha de estilos -->
    <link rel="stylesheet" href="./src/style/log.css">
    <style>
        /* Estilo das abas */
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .tab-button {
            padding: 10px 20px;
            cursor: pointer;
            background: #f4f4f4;
            border: 1px solid #ddd;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
            margin-right: 5px;
            transition: background 0.3s;
        }
        .tab-button.active {
            background: #fff;
            font-weight: bold;
        }
        .tab-content {
            display: none;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .tab-content.active {
            display: block;
        }
        /* Estilo para mensagens de depuração */
        .debug-message {
            padding: 10px;
            margin-bottom: 10px;
            background: #e7f3fe;
            color: #31708f;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
        }
        /* Estilo para a tabela de logs (mantido do original) */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .filter-container {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .filter-container h3 {
            margin-top: 0;
        }
        .filter-container form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        .filter-container label {
            font-weight: bold;
        }
        .filter-container input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
        }
        .btn-clear {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-clear:hover {
            background: #c82333;
        }
        .log-table-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        #logTable {
            width: 100%;
            border-collapse: collapse;
        }
        #logTable th, #logTable td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        #logTable th {
            background: #007bff;
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        #logTable tr:hover {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Logs e Debug do Sistema</h1>

        <!-- Sistema de Abas -->
        <div class="tabs">
            <div class="tab-button active" data-tab="logs">Logs</div>
            <div class="tab-button" data-tab="debug">Debug</div>
        </div>

        <!-- Conteúdo da Aba Logs -->
        <div class="tab-content active" id="logs">
            <!-- Filtro de Pesquisa -->
            <div class="filter-container">
                <h3>Filtros de Pesquisa</h3>
                <form id="filterForm">
                    <label for="data_operacao">Data:</label>
                    <input type="date" id="data_operacao" name="data_operacao">

                    <label for="matricula">Nome:</label>
                    <input type="text" id="matricula" name="matricula" placeholder="Digite o nome">

                    <label for="tipo_operacao">Tipo de Operação:</label>
                    <input type="text" id="tipo_operacao" name="tipo_operacao" placeholder="Digite o tipo de operação">

                    <!-- Botão para Limpar os Filtros -->
                    <button type="button" class="btn-clear" id="clearFilters">Limpar Filtros</button>
                </form>
            </div>

            <!-- Contêiner da Tabela com Barra de Rolagem -->
            <div class="log-table-container" id="logTableContainer">
                <table id="logTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Tipo de Operação</th>
                            <th>Data da Operação</th>
                            <!-- <th>Detalhes</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Exibe os dados da tabela log_eventos
                        while ($row = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['matricula']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tipo_operacao']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['data_operacao']) . "</td>";
                            // echo "<td>" . htmlspecialchars($row['detalhes']) . "</td>"; // Se houver uma coluna 'detalhes' na tabela
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Conteúdo da Aba Debug -->
        <div class="tab-content" id="debug">
            <h3>Depuração de Envio de E-mail</h3>
            <?php if (empty($emailDebugMessages)): ?>
                <p>Nenhuma mensagem de depuração disponível. Tente enviar um e-mail a partir da página de eventos.</p>
            <?php else: ?>
                <div class="debug-messages">
                    <?php foreach ($emailDebugMessages as $debugMessage): ?>
                        <div class="debug-message"><?= htmlspecialchars($debugMessage) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="./src/js/log.js"></script>
    <script>
        // Função para alternar entre abas
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // Remove a classe 'active' de todos os botões e conteúdos
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

                // Adiciona a classe 'active' ao botão clicado e ao conteúdo correspondente
                button.classList.add('active');
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
    </script>
</body>
</html>

<?php
// Fecha a conexão ao banco de dados
$con->close();
?>