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
<!-- Filtro da tabela com 3 campos -->
    <script src="./src/js/log.js">     // Função para realizar a pesquisa via AJAX em tempo real</script>
</body>
</html>

<?php
// Fecha a conexão ao banco de dados
$con->close();
?>
