<?php
include 'banco.php'; // Inclua a conexão com o banco de dados

// Consultas para contar os diferentes tipos de bens
$totalBensQuery = "SELECT COUNT(*) AS total FROM patrimonio";
$ativosQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'ativo'";
$emBaixaQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'Em Processo de baixa'";
$mortosQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'inativo'";

// Consulta para obter os últimos patrimônios cadastrados
$ultimosPatrimoniosQuery = "SELECT id, codigo, nome, descricao, data_registro, situacao AS status FROM patrimonio ORDER BY data_registro DESC LIMIT 5";

// Consulta para obter os usuários que mais cadastraram patrimônio no mês atual
$query_usuarios_mes = "
    SELECT p.cadastrado_por AS usuario, u.setor, COUNT(*) AS quantidade_cadastros
    FROM patrimonio p
    INNER JOIN usuario u ON p.cadastrado_por = u.username  -- Junção com a tabela usuario
    WHERE MONTH(p.data_registro) = MONTH(CURRENT_DATE()) 
      AND YEAR(p.data_registro) = YEAR(CURRENT_DATE())
      AND p.cadastrado_por IS NOT NULL
    GROUP BY p.cadastrado_por, u.setor  -- Agrupar também pelo setor
    ORDER BY quantidade_cadastros DESC
    LIMIT 5
";

$result_usuarios_mes = $con->query($query_usuarios_mes);

try {
    // Executa as consultas e obtém os resultados
    $totalBens = $con->query($totalBensQuery)->fetch_assoc()['total'] ?? 0;
    $bensAtivos = $con->query($ativosQuery)->fetch_assoc()['total'] ?? 0;
    $bensEmBaixa = $con->query($emBaixaQuery)->fetch_assoc()['total'] ?? 0;
    $bensMortos = $con->query($mortosQuery)->fetch_assoc()['total'] ?? 0;

    // Executa a consulta para os últimos patrimônios
    $result = $con->query($ultimosPatrimoniosQuery);
    $ultimosPatrimonios = [];
    if ($result) {
        $ultimosPatrimonios = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    echo "Erro ao consultar o banco de dados: " . $e->getMessage();
    exit();
}

// Fecha a conexão
include 'header.php';
$con->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Bens</title>
    <link rel="stylesheet" href="src/patrimonio/style/painel-patrimonio.css">
</head>
<body>

    <!-- Lado esquerdo: Cards -->
<div class="cards-container">
        <div class="card">
            <h3>Total de Bens Cadastrados</h3>
            <p><?php echo $totalBens; ?></p>
        </div>
        <div class="card2">
            <h3>Bens Ativos</h3>
            <p><?php echo $bensAtivos; ?></p>
        </div>
        <div class="card3">
            <h3>Bens em Processo de Baixa</h3>
            <p><?php echo $bensEmBaixa; ?></p>
        </div>
        <div class="card4">
            <h3>Bens Mortos</h3>
            <p><?php echo $bensMortos; ?></p>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Lado direito: Tabela -->
        <div class="table-container">
            <h3>Últimos Patrimônios Cadastrados</h3>
            <table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Data de Cadastro</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ultimosPatrimonios as $patrimonio): ?>
            <tr>
                <td><?php echo htmlspecialchars($patrimonio['codigo']); ?></td>
                <td><?php echo htmlspecialchars($patrimonio['nome']); ?></td>
                <td><?php echo htmlspecialchars($patrimonio['descricao']); ?></td>
                <td><?php echo htmlspecialchars($patrimonio['data_registro']); ?></td>
                <td>
                    <span class="
                        <?php 
                            if ($patrimonio['status'] == 'ativo') {
                                echo 'status-ativo';
                            } elseif ($patrimonio['status'] == 'inativo') {
                                echo 'status-inativo';
                            } elseif ($patrimonio['status'] == 'Em Processo de baixa') {
                                echo 'status-em-baixa';
                            }
                        ?>
                    ">
                        <?php echo htmlspecialchars($patrimonio['status']); ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

        </div>

        <!-- Tabela de usuários que mais cadastraram patrimônio no mês -->
<div class="table-container">
            <h3>Usuários que Mais Cadastraram Bens no mês</h3>
            <table>
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Setor</th>
                        <th>Status</th>
                        <th>Quantidade de Cadastros</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $result_usuarios_mes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['setor']); ?></td>
                            <td>
                    <span class="
                        <?php 
                            if ($patrimonio['status'] == 'ativo') {
                                echo 'status-ativo';
                            } elseif ($patrimonio['status'] == 'inativo') {
                                echo 'status-inativo';
                            } 
                            
                        ?>
                    ">
                        <?php echo htmlspecialchars($patrimonio['status']); ?>
                    </span>
                </td>
                            <td><?php echo htmlspecialchars($usuario['quantidade_cadastros']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
<?php include 'footer.php'; ?>