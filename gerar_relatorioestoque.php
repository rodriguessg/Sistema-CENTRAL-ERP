<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$user = 'root';
$password = '';

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit;
}

// Recupera os dados do POST
$periodo = $_POST['periodo'] ?? '';
$exercicio = $_POST['exercicio'] ?? '';
$incluir_quantidade = isset($_POST['incluir_quantidade']) ? true : false;
$usuario = $_POST['usuario'] ?? 'Desconhecido';

// Validação básica
if (empty($periodo)) {
    echo "Período não especificado.";
    exit;
}

// Inicia o conteúdo do relatório
$relatorioConteudo = "<h3>Relatório " . ucfirst($periodo) . "</h3>";
$relatorioConteudo .= "<p><strong>Usuário:</strong> " . htmlspecialchars($usuario) . "</p>";
$relatorioConteudo .= "<p><strong>Data:</strong> " . date('d/m/Y') . "</p>";

// Adiciona o exercício se for relatório anual
if ($periodo === 'anual') {
    $relatorioConteudo .= "<p><strong>Exercício:</strong> " . htmlspecialchars($exercicio) . "</p>";
}

// Define a query com base no período
$query = "
    SELECT 
        produto, 
        natureza, 
        codigo, 
        contabil, 
        unidade, 
        localizacao, 
        custo, 
        quantidade, 
        preco_medio, 
        nf, 
        data_cadastro 
    FROM produtos 
    WHERE 1=1";

if ($periodo === 'semanal') {
    $query .= " AND data_cadastro >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
} elseif ($periodo === 'mensal') {
    $query .= " AND data_cadastro >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
} elseif ($periodo === 'anual') {
    if (empty($exercicio)) {
        echo "Exercício não especificado para o relatório anual.";
        exit;
    }
    $query .= " AND YEAR(data_cadastro) = :exercicio";
}

// Executa a query
try {
    $stmt = $pdo->prepare($query);
    if ($periodo === 'anual') {
        $stmt->bindParam(':exercicio', $exercicio, PDO::PARAM_INT);
    }
    $stmt->execute();
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar dados: " . $e->getMessage();
    exit;
}

// Gera o cabeçalho do relatório
echo $relatorioConteudo;

// Gera a tabela
if (!empty($resultados)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%; text-align: left;'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Produto</th>";
    echo "<th>Natureza</th>";
    echo "<th>Código</th>";
    echo "<th>Contábil</th>";
    echo "<th>Unidade</th>";
    echo "<th>Localização</th>";
    echo "<th>Custo</th>";
    echo "<th>Quantidade</th>";
    echo "<th>Preço Médio</th>";
    echo "<th>NF</th>";
    echo "<th>Data de Cadastro</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $quantidade_total = 0;
    foreach ($resultados as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['produto']) . "</td>";
        echo "<td>" . htmlspecialchars($row['natureza']) . "</td>";
        echo "<td>" . htmlspecialchars($row['codigo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['contabil']) . "</td>";
        echo "<td>" . htmlspecialchars($row['unidade']) . "</td>";
        echo "<td>" . htmlspecialchars($row['localizacao']) . "</td>";
        echo "<td>R$ " . number_format($row['custo'], 2, ',', '.') . "</td>";
        echo "<td>" . htmlspecialchars($row['quantidade']) . "</td>";
        echo "<td>R$ " . number_format($row['preco_medio'], 2, ',', '.') . "</td>";
        echo "<td>" . htmlspecialchars($row['nf']) . "</td>";
        echo "<td>" . htmlspecialchars($row['data_cadastro']) . "</td>";
        echo "</tr>";

        $quantidade_total += $row['quantidade'];
    }

    echo "</tbody>";
    echo "</table>";

    if ($incluir_quantidade) {
        echo "<p><strong>Quantidade Total:</strong> $quantidade_total</p>";
    }
} else {
    echo "Nenhum dado encontrado para o período selecionado.";
}
?>
