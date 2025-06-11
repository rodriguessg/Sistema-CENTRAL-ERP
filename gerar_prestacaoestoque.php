<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
    header('Location: ../index.php');
    exit();
}

// Configurações do banco de dados (ajuste conforme necessário)
$servername = "localhost";
$username = "root"; // Substitua pelo seu usuário do banco
$password = ""; // Substitua pela sua senha do banco
$dbname = "gm_sicbd"; // Substitua pelo nome do seu banco

try {
    // Conexão com o banco de dados
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conn->exec("SET NAMES utf8");

    // Receber parâmetros do formulário
    $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT);
    $exercicio = filter_input(INPUT_POST, 'exercicio', FILTER_VALIDATE_INT);
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);

    // Validar parâmetros
    if (!$mes || !$exercicio || $mes < 1 || $mes > 12) {
        echo "<p>Parâmetros inválidos. Por favor, selecione um mês e ano válidos.</p>";
        exit();
    }

    // Definir intervalo de datas para o mês selecionado
    $dataInicio = "$exercicio-$mes-01";
    $dataFim = date("Y-m-t", strtotime($dataInicio)); // Último dia do mês

    // Consulta para obter itens distintos
    $sqlItens = "SELECT DISTINCT item_id, nome_item FROM fechamento ORDER BY nome_item";
    $stmtItens = $conn->query($sqlItens);
    $itens = $stmtItens->fetchAll();

    // Iniciar a saída da tabela HTML
    $output = "<table>";
    $output .= "<thead><tr><th>Item</th><th>Saldo Inicial</th><th>Entradas</th><th>Saídas</th><th>Saldo Final</th></tr></thead><tbody>";

    foreach ($itens as $item) {
        $item_id = $item['item_id'];
        $nome_item = htmlspecialchars($item['nome_item'], ENT_QUOTES, 'UTF-8');

        // Calcular saldo inicial (soma de entradas - saídas antes do período)
        $sqlSaldoInicial = "
            SELECT 
                COALESCE(SUM(CASE WHEN tipo_movimentacao = 'entrada' THEN quantidade ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN tipo_movimentacao = 'saida' THEN quantidade ELSE 0 END), 0) AS saldo_inicial
            FROM estoque_movimentacao
            WHERE item_id = :item_id AND data_movimentacao < :data_inicio
        ";
        $stmtSaldoInicial = $conn->prepare($sqlSaldoInicial);
        $stmtSaldoInicial->execute(['item_id' => $item_id, 'data_inicio' => $dataInicio]);
        $saldoInicial = $stmtSaldoInicial->fetchColumn();

        // Calcular entradas no período
        $sqlEntradas = "
            SELECT COALESCE(SUM(quantidade), 0) AS total_entradas
            FROM estoque_movimentacao
            WHERE item_id = :item_id AND tipo_movimentacao = 'entrada'
            AND data_movimentacao BETWEEN :data_inicio AND :data_fim
        ";
        $stmtEntradas = $conn->prepare($sqlEntradas);
        $stmtEntradas->execute(['item_id' => $item_id, 'data_inicio' => $dataInicio, 'data_fim' => $dataFim]);
        $entradas = $stmtEntradas->fetchColumn();

        // Calcular saídas no período
        $sqlSaidas = "
            SELECT COALESCE(SUM(quantidade), 0) AS total_saidas
            FROM estoque_movimentacao
            WHERE item_id = :item_id AND tipo_movimentacao = 'saida'
            AND data_movimentacao BETWEEN :data_inicio AND :data_fim
        ";
        $stmtSaidas = $conn->prepare($sqlSaidas);
        $stmtSaidas->execute(['item_id' => $item_id, 'data_inicio' => $dataInicio, 'data_fim' => $dataFim]);
        $saidas = $stmtSaidas->fetchColumn();

        // Calcular saldo final
        $saldoFinal = $saldoInicial + $entradas - $saidas;

        // Adicionar linha à tabela
        $output .= "<tr>";
        $output .= "<td>$nome_item</td>";
        $output .= "<td>$saldoInicial</td>";
        $output .= "<td>$entradas</td>";
        $output .= "<td>$saidas</td>";
        $output .= "<td>$saldoFinal</td>";
        $output .= "</tr>";
    }

    // Fechar a tabela
    $output .= "</tbody></table>";

    // Verificar se há dados
    if (empty($itens)) {
        $output = "<p>Nenhum dado encontrado para o período selecionado.</p>";
    }

    // Exibir o resultado
    echo $output;

} catch (PDOException $e) {
    echo "<p>Erro ao gerar relatório: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
}

// Fechar conexão
$conn = null;
?>