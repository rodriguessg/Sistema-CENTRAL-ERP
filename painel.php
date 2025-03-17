<?php
session_start(); // Inicia a sessão

include 'banco.php'; // Inclua a conexão com o banco de dados

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "supat";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

try {
    // Consultas para contagem e dados gerais
    $totalBensQuery = "SELECT COUNT(*) AS total FROM patrimonio";
    $ativosQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'ativo'";
    $emBaixaQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'Em Processo de baixa'";
    $mortosQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'inativo'";
    $totalUsuariosQuery = "SELECT COUNT(*) AS total FROM usuario";
    $totalSetoresQuery = "SELECT COUNT(*) AS total FROM setores";

    // Consulta para obter o último usuário logado
    $ultimoUsuarioLogadoQuery = "
        SELECT u.username, le.data_operacao
        FROM log_eventos le
        INNER JOIN usuario u ON le.matricula = u.username
        WHERE le.tipo_operacao LIKE '%login%'
        ORDER BY le.data_operacao DESC
        LIMIT 1";

    $usernameLogado = $_SESSION['username']; // Pegue o username logado da sessão

    // Consulta SQL ajustada para filtrar "cadastrou" ou "atualizou" e pelo usuário logado
    $sql_ultimasAlteracoesSetores = "
        SELECT le.data_operacao AS data_hora, le.tipo_operacao, u.setor 
        FROM log_eventos le
        INNER JOIN usuario u ON le.matricula = u.username
        WHERE (le.tipo_operacao LIKE '%cadastrou%' OR le.tipo_operacao LIKE '%atualizou%')
        AND le.matricula = '$usernameLogado' 
        ORDER BY le.data_operacao DESC
        LIMIT 5"; 

    // Consulta para obter os usuários que mais cadastraram
    $sql_usuarios_cadastros = "
        SELECT le.matricula AS usuario, u.setor, COUNT(*) AS total_cadastros
        FROM log_eventos le
        INNER JOIN usuario u ON le.matricula = u.username
        WHERE le.tipo_operacao LIKE '%cadastrou%' OR le.tipo_operacao LIKE '%atualizou%'
        GROUP BY le.matricula, u.setor
        ORDER BY total_cadastros DESC
        LIMIT 5";

    // Executa as consultas
    $totalBens = $conn->query($totalBensQuery)->fetch_assoc()['total'] ?? 0;
    $bensAtivos = $conn->query($ativosQuery)->fetch_assoc()['total'] ?? 0;
    $bensEmBaixa = $conn->query($emBaixaQuery)->fetch_assoc()['total'] ?? 0;
    $bensMortos = $conn->query($mortosQuery)->fetch_assoc()['total'] ?? 0;
    $totalUsuarios = $conn->query($totalUsuariosQuery)->fetch_assoc()['total'] ?? 0;
    $totalSetores = $conn->query($totalSetoresQuery)->fetch_assoc()['total'] ?? 0;

    // Último usuário logado
    $result_ultimoUsuarioLogado = $conn->query($ultimoUsuarioLogadoQuery);
    if ($result_ultimoUsuarioLogado && $result_ultimoUsuarioLogado->num_rows > 0) {
        $ultimoUsuarioLogado = $result_ultimoUsuarioLogado->fetch_assoc();
        $ultimoUsuarioNome = $ultimoUsuarioLogado['username'];
        $ultimoUsuarioData = $ultimoUsuarioLogado['data_operacao'];
    } else {
        $ultimoUsuarioNome = 'Nenhum usuário encontrado';
        $ultimoUsuarioData = 'N/A';
    }

    // Executa a consulta de últimas alterações nos setores
    $result_ultimasAlteracoesSetores = $conn->query($sql_ultimasAlteracoesSetores);
    if (!$result_ultimasAlteracoesSetores) {
        die("Erro na consulta de últimas alterações nos setores: " . $conn->error);
    }

    // Executa a consulta de usuários que mais cadastraram
    $result_usuarios_cadastros = $conn->query($sql_usuarios_cadastros);
    if (!$result_usuarios_cadastros) {
        die("Erro na consulta de usuários que mais cadastraram: " . $conn->error);
    }

    // Consulta para buscar os últimos patrimônios
    $sql_ultimosPatrimonios = "SELECT * FROM patrimonio WHERE data_registro > NOW() ORDER BY data_registro DESC LIMIT 5";
    $result_ultimosPatrimonios = $conn->query($sql_ultimosPatrimonios);

    // Verifica se a consulta retornou resultados
    if ($result_ultimosPatrimonios->num_rows > 0) {
        // Atribui os resultados à variável como um array associativo
        $ultimosPatrimonios = $result_ultimosPatrimonios->fetch_all(MYSQLI_ASSOC);
    } else {
        // Se não houver resultados, define a variável como um array vazio
        $ultimosPatrimonios = [];
    }
} catch (Exception $e) {
    echo "Erro ao consultar o banco de dados: " . $e->getMessage();
    exit();
}

// Inclui o cabeçalho
include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Bens</title>
    <link rel="stylesheet" type="text/css" href="./src/style/painel.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Container Principal -->
    <div class="dashboard">
        <!-- Cards Resumo -->
        <div class="cards-charts-container">
            <!-- Cards Resumo -->
            <div class="cards-container">
                <div class="card">
                    <h3>Total de Bens Cadastrados</h3>
                    <p><?php echo $totalBens; ?></p>
                </div>
                <div class="card">
                    <h3>Bens Ativos</h3>
                    <p><?php echo $bensAtivos; ?></p>
                </div>
                <div class="card">
                    <h3>Bens em Processo de Baixa</h3>
                    <p><?php echo $bensEmBaixa; ?></p>
                </div>
                <div class="card">
                    <h3>Bens Mortos</h3>
                    <p><?php echo $bensMortos; ?></p>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="charts-section">
                <div class="chart-container">
                    <h3>Status dos Bens</h3>
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Usuários que Mais Cadastraram</h3>
                    <canvas id="usuariosChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="dashboard-container">
            <!-- Tabela de Últimos Patrimônios -->
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
                                    <span class="status-<?php echo strtolower(str_replace(' ', '-', $patrimonio['status'])); ?>">
                                        <?php echo htmlspecialchars($patrimonio['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

                    <!-- Tabela de Usuários que Mais Cadastraram -->
        <div class="table-container">
            <h3>Últimas Alterações nos Setores</h3>
            <table border="1">
                <thead>
                    <tr>
                        <th>Data e Hora</th>
                        <th>Operação</th>
                        <th>Setor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_ultimasAlteracoesSetores->num_rows > 0): ?>
                        <?php while ($alteracao = $result_ultimasAlteracoesSetores->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alteracao['data_hora']); ?></td>
                                <td><?php echo htmlspecialchars($alteracao['tipo_operacao']); ?></td>
                                <td><?php echo htmlspecialchars($alteracao['setor']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Nenhuma alteração encontrada.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        </div>
    </div>

    <script>
        // Passando os dados do PHP para o JavaScript
        const statusData = <?php echo json_encode($statusData, JSON_HEX_TAG); ?>;
        const usuariosData = <?php echo json_encode($usuariosData, JSON_HEX_TAG); ?>;

        // Inicializar o gráfico de status
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: statusData.map(item => item.label),
                datasets: [{
                    data: statusData.map(item => item.value),
                    backgroundColor: ['#4CAF50', '#F44336', '#FFC107', '#2196F3'], // Adicionando uma nova cor para o total
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Inicializar gráfico de barra
        const ctxUsuarios = document.getElementById('usuariosChart').getContext('2d');
        const usuariosChart = new Chart(ctxUsuarios, {
            type: 'bar',
            data: {
                labels: usuariosData.map(item => item.usuario),
                datasets: [{
                    label: 'Cadastros',
                    data: usuariosData.map(item => item.quantidade),
                    backgroundColor: ['#4CAF50', '#2196F3', '#FFC107'],
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Usuários',
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Quantidade',
                        },
                        beginAtZero: true,
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
// Fecha a conexão ao final do script
$con->close();
?>
