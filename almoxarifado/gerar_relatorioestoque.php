<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<p style='color: red;'>Erro ao conectar ao banco de dados: " . $e->getMessage() . "</p>");
}

// Recupera os dados do POST de forma segura
$periodo = $_POST['periodo'] ?? '';
$exercicio = $_POST['exercicio'] ?? '';
$mes = $_POST['mes'] ?? '';
$usuario = $_POST['usuario'] ?? 'Desconhecido';

// Validação do período
if (empty($periodo)) {
    die("<p style='color: red;'>Erro: Período não especificado.</p>");
}

// Formata o cabeçalho do relatório
$relatorioConteudo = "<h3>Relatório " . ucfirst($periodo) . "</h3>";
$relatorioConteudo .= "<p><strong>Usuário:</strong> " . htmlspecialchars($usuario) . "</p>";

$relatorioConteudo .= "<p><strong>Data:</strong> " . date('d/m/Y') . "</p>";

// Define a query SQL com base no período selecionado
// Define a query SQL com base no período selecionado
if ($periodo === 'fechamento') {
    // Consulta para pegar todos os registros da tabela fechamento
    $query = "SELECT natureza, classificacao, saldo_anterior, total_entrada, total_saida, saldo_atual FROM fechamento ORDER BY id DESC";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $fechamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("<p style='color: red;'>Erro ao buscar dados: " . $e->getMessage() . "</p>");
    }

    echo $relatorioConteudo;
    if ($fechamentos) {
        $totalSaldoAnterior = 0;
        $totalEntrada = 0;
        $totalSaida = 0;
        $totalSaldoAtual = 0;

        echo "<table border='1' style='border-collapse: collapse; width: 100%; text-align: left;'>";
        echo "<thead>
                <tr>
                    <th>Natureza</th>
                    <th>Classificação</th>
                    <th>Saldo Anterior</th>
                    <th>Total de Entrada</th>
                    <th>Total de Saída</th>
                    <th>Saldo Atual</th>
                </tr>
              </thead>";
        echo "<tbody>";

        // Loop para exibir todas as linhas de fechamento
        foreach ($fechamentos as $fechamento) {
            // Acumula os totais
            $totalSaldoAnterior += $fechamento['saldo_anterior'];
            $totalEntrada += $fechamento['total_entrada'];
            $totalSaida += $fechamento['total_saida'];
            $totalSaldoAtual += $fechamento['saldo_atual'];

            echo "<tr>
                    <td>" . htmlspecialchars($fechamento['natureza']) . "</td>
                    <td>" . htmlspecialchars($fechamento['classificacao']) . "</td>
                    <td>R$ " . number_format($fechamento['saldo_anterior'], 5) . "</td>
                    <td>R$ " . number_format($fechamento['total_entrada'], 5,',', '.') . "</td>
                    <td>R$ " . number_format($fechamento['total_saida'], 5, ',', '.') . "</td>
                    <td>R$ " . number_format($fechamento['saldo_atual'], 5, ',', '.') . "</td>
                </tr>";
        }

        // Linha com os totais
        echo "<tr style='font-weight: bold;'>
                <td colspan='2'>Total</td>
                <td>R$ " . number_format($totalSaldoAnterior, 5, ',', '.') . "</td>
                <td>R$ " . number_format($totalEntrada, 5,) . "</td>
                <td>R$ " . number_format($totalSaida, 5, '.', '.') . "</td>
                <td>R$ " . number_format($totalSaldoAtual, 5, '.', '.') . "</td>
              </tr>";

        echo "</tbody></table>";
    } else {
        echo "<p style='color: red;'>Nenhum dado de fechamento encontrado.</p>";
    }


} else {
    $query = "SELECT produto, natureza, descricao, contabil, unidade, localizacao, custo, quantidade, preco_medio, nf, data_cadastro FROM produtos WHERE 1=1";
    $params = [];

    if ($periodo === 'semanal') {
        $query .= " AND data_cadastro >= CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY";
    } elseif ($periodo === 'mensal' && !empty($mes)) {
        $query .= " AND MONTH(data_cadastro) = :mes AND YEAR(data_cadastro) = YEAR(CURDATE())";
        $params[':mes'] = $mes;
    } elseif ($periodo === 'anual' && !empty($exercicio)) {
        $query .= " AND YEAR(data_cadastro) = :exercicio";
        $params[':exercicio'] = $exercicio;
    }

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("<p style='color: red;'>Erro ao buscar dados: " . $e->getMessage() . "</p>");
    }

    echo $relatorioConteudo;

    if (!empty($resultados)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; text-align: left;'>";
        echo "<thead>
                <tr>
                    <th>Produto</th>
                    <th>Natureza</th>
                    <th>Descrição</th>
                    <th>Contábil</th>
                    <th>Unidade</th>
                    <th>Localização</th>
                    <th>Custo</th>
                    <th>Quantidade</th>
                    <th>Preço Médio</th>
                    <th>NF</th>
                    <th>Data de Cadastro</th>
                </tr>
              </thead>";
        echo "<tbody>";

        foreach ($resultados as $row) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['produto']) . "</td>
                    <td>" . htmlspecialchars($row['natureza']) . "</td>
                    <td>" . htmlspecialchars($row['descricao']) . "</td>
                    <td>" . htmlspecialchars($row['contabil']) . "</td>
                    <td>" . htmlspecialchars($row['unidade']) . "</td>
                    <td>" . htmlspecialchars($row['localizacao']) . "</td>
                    <td>R$ " . number_format($row['custo'], 2, ',', '.') . "</td>
                    <td>" . htmlspecialchars($row['quantidade']) . "</td>
                    <td>R$ " . number_format($row['preco_medio'], 5, ',', '.') . "</td>
                    <td>" . htmlspecialchars($row['nf']) . "</td>
                    <td>" . date('d/m/Y', strtotime($row['data_cadastro'])) . "</td>
                  </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p style='color: red;'>Nenhum dado encontrado para o período selecionado.</p>";
    }
}
?>