<?php
// Incluir a conexão com o banco de dados
include 'banco.php';

// Recuperar os dados iniciais da tabela log_eventos
$query = "SELECT * FROM log_eventos ORDER BY data_operacao DESC";  // Ordena por data de operação (mais recente primeiro)
$resultado = $con->query($query);

// Verifica se a consulta retornou algum resultado
if ($resultado === false) {
    die("Erro ao recuperar os logs: " . $con->error);
}

include 'header.php'; // Inclui o cabeçalho
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs do Sistema</title>
    <link rel="stylesheet" href="style.css"> <!-- Link para a folha de estilos -->
    <link rel="stylesheet" href="./src/style/log.css">
   
</head>
<body>
    <div class="container">
        <h1>Logs do Sistema</h1>
        
        <!-- Filtro de Pesquisa -->
        <div class="filter-container">
            <h3>Filtros de Pesquisa</h3>
            <form id="filterForm">
                <label for="data_operacao">Data:</label>
                <input type="date" id="data_operacao" name="data_operacao">

                <label for="matricula">Nome:</label>
                <input type="text" id="matricula" name="matricula" placeholder="Digite a nome">

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

    <script>
        // Função para realizar a pesquisa via AJAX em tempo real
        function filterLogs() {
            const dataOperacao = document.getElementById('data_operacao').value;
            const matricula = document.getElementById('matricula').value;
            const tipoOperacao = document.getElementById('tipo_operacao').value;

            // Enviar dados para o servidor via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `filtrar_logs.php?data_operacao=${dataOperacao}&matricula=${matricula}&tipo_operacao=${tipoOperacao}`, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Atualiza a tabela com os resultados
                    document.getElementById('logTable').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Adicionando eventos para filtrar conforme o usuário digita
        document.getElementById('data_operacao').addEventListener('input', filterLogs);
        document.getElementById('matricula').addEventListener('input', filterLogs);
        document.getElementById('tipo_operacao').addEventListener('input', filterLogs);

        // Função para limpar os filtros
        document.getElementById('clearFilters').addEventListener('click', function() {
            // Limpar os campos
            document.getElementById('data_operacao').value = '';
            document.getElementById('matricula').value = '';
            document.getElementById('tipo_operacao').value = '';

            // Recarregar a tabela com todos os itens
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'filtrar_logs.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Atualiza a tabela com todos os registros
                    document.getElementById('logTable').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        });
    </script>
</body>
</html>

<?php
// Fecha a conexão ao banco de dados
$con->close();
?>
