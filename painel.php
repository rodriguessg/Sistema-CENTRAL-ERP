
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gm_sicbd";

try {
    // Conexão com PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Conexão com MySQLi para compatibilidade
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // **Estoque Module Queries**
    $sql_total_produtos = "SELECT COUNT(*) AS total FROM produtos";
    $stmt_total_produtos = $pdo->query($sql_total_produtos);
    $total_produtos = $stmt_total_produtos->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $sql_valor_estoque = "SELECT SUM(custo * quantidade) AS valor_total_estoque FROM produtos";
    $stmt_valor_estoque = $pdo->query($sql_valor_estoque);
    $valor_total_estoque = $stmt_valor_estoque->fetch(PDO::FETCH_ASSOC)['valor_total_estoque'] ?? 0;

    $sql_alertas = "SELECT produto, quantidade, estoque_minimo 
                    FROM produtos 
                    WHERE quantidade <= estoque_minimo 
                    ORDER BY quantidade ASC 
                    LIMIT 3";
    $stmt_alertas = $pdo->query($sql_alertas);
    $alertas_estoque = $stmt_alertas->fetchAll(PDO::FETCH_ASSOC);

    $sql_ultimos_produtos = "SELECT produto, quantidade, descricao, data_cadastro 
                             FROM produtos 
                             ORDER BY data_cadastro DESC 
                             LIMIT 5";
    $stmt_ultimos_produtos = $pdo->query($sql_ultimos_produtos);
    $ultimos_produtos = $stmt_ultimos_produtos->fetchAll(PDO::FETCH_ASSOC);

    $sql_movimentacoes = "SELECT matricula, COUNT(*) AS total_movimentacoes 
                          FROM log_eventos 
                          WHERE tipo_operacao IN ('cadastrou no estoque', 'retirou do estoque') 
                          GROUP BY matricula 
                          ORDER BY total_movimentacoes DESC 
                          LIMIT 5";
    $result_movimentacoes = $conn->query($sql_movimentacoes);
    $movimentacoes = $result_movimentacoes->fetch_all(MYSQLI_ASSOC);

    // **Contratos Module Queries**
    $sql_processos = "SELECT COUNT(*) AS total_processos FROM gestao_contratos";
    $total_processos = $conn->query($sql_processos)->fetch_assoc()['total_processos'] ?? 0;

    $sql_ativos = "SELECT COUNT(*) AS total_ativos FROM gestao_contratos WHERE validade >= CURDATE()";
    $total_ativos = $conn->query($sql_ativos)->fetch_assoc()['total_ativos'] ?? 0;

    $sql_expirados = "SELECT COUNT(*) AS total_expirados FROM gestao_contratos WHERE validade < CURDATE()";
    $total_expirados = $conn->query($sql_expirados)->fetch_assoc()['total_expirados'] ?? 0;

    $sql_vencendo = "SELECT COUNT(*) AS total_vencendo FROM gestao_contratos WHERE validade BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
    $total_vencendo = $conn->query($sql_vencendo)->fetch_assoc()['total_vencendo'] ?? 0;

    $sql_agendamentos = "SELECT COUNT(*) AS total_agendamentos FROM eventos WHERE DATE(data) = CURDATE()";
    $total_agendamentos = $conn->query($sql_agendamentos)->fetch_assoc()['total_agendamentos'] ?? 0;

    $sql_lista_expirando = "SELECT id, titulo, validade, valor_contrato, 
                            CASE WHEN validade >= CURDATE() THEN 'Ativo' ELSE 'Expirado' END AS status
                            FROM gestao_contratos 
                            WHERE validade <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH) AND validade >= CURDATE()";
    $result_lista_expirando = $conn->query($sql_lista_expirando);
    $contratos_expirando = $result_lista_expirando->fetch_all(MYSQLI_ASSOC);

    $sql_eventos = "SELECT id, titulo, descricao, data, hora, categoria 
                    FROM eventos 
                    WHERE data = CURDATE()";
    $stmt_eventos = $pdo->prepare($sql_eventos);
    $stmt_eventos->execute();
    $eventos = $stmt_eventos->fetchAll(PDO::FETCH_ASSOC);

    $sql_contratos_por_mes = "
        SELECT 
            MONTH(validade) AS mes,
            YEAR(validade) AS ano,
            SUM(CASE WHEN validade >= CURDATE() THEN 1 ELSE 0 END) AS ativos,
            SUM(CASE WHEN validade < CURDATE() THEN 1 ELSE 0 END) AS expirados,
            SUM(CASE WHEN data_cadastro >= CURDATE() - INTERVAL 1 MONTH THEN 1 ELSE 0 END) AS novos
        FROM gestao_contratos
        WHERE validade >= CURDATE() - INTERVAL 6 MONTH
        GROUP BY ano, mes
        ORDER BY ano, mes";
    $result_contratos_por_mes = $conn->query($sql_contratos_por_mes);
    $meses_contratos = [];
    $contratos_ativos_por_mes = [];
    $contratos_expirados_por_mes = [];
    $contratos_novos_por_mes = [];
    while ($row = $result_contratos_por_mes->fetch_assoc()) {
        $meses_contratos[] = date('M', mktime(0, 0, 0, $row['mes'], 1));
        $contratos_ativos_por_mes[] = $row['ativos'] ?? 0;
        $contratos_expirados_por_mes[] = $row['expirados'] ?? 0;
        $contratos_novos_por_mes[] = $row['novos'] ?? 0;
    }

    // **Patrimônio Module Queries**
    $totalBensQuery = "SELECT COUNT(*) AS total FROM patrimonio";
    $ativosQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'ativo'";
    $emBaixaQuery = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'EmBaixa'";
    $mortos_query = "SELECT COUNT(*) AS total FROM patrimonio WHERE situacao = 'inativo'";
    $totalBens = $pdo->query($totalBensQuery)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $bensAtivos = $pdo->query($ativosQuery)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $bensEmBaixa = $pdo->query($emBaixaQuery)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $bensMortos = $pdo->query($mortos_query)->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $ultimosPatrimoniosQuery = "SELECT id, codigo, nome, descricao, data_registro, situacao AS status 
                                FROM patrimonio ORDER BY data_registro DESC LIMIT 5";
    $stmt_ultimos_patrimonios = $pdo->query($ultimosPatrimoniosQuery);
    $ultimosPatrimonios = $stmt_ultimos_patrimonios->fetchAll(PDO::FETCH_ASSOC);

    $query_usuarios_mes = "
        SELECT p.cadastrado_por AS usuario, u.setor, COUNT(*) AS quantidade_cadastros
        FROM patrimonio p
        INNER JOIN usuario u ON p.cadastrado_por = u.username
        WHERE MONTH(p.data_registro) = MONTH(CURRENT_DATE()) 
          AND YEAR(p.data_registro) = YEAR(CURRENT_DATE())
          AND p.cadastrado_por IS NOT NULL
        GROUP BY p.cadastrado_por, u.setor
        ORDER BY quantidade_cadastros DESC
        LIMIT 5";
    $stmt_usuarios_mes = $pdo->query($query_usuarios_mes);
    $usuarios_patrimonio = $stmt_usuarios_mes->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "Erro ao consultar o banco de dados: " . $e->getMessage();
    exit();
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Gestão</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        th, td {
            padding: 1rem;
            text-align: left;
        }
        th {
            background-color: #1f2937;
            color: white;
            position: sticky;
            top: 0;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status-ativo, .status-estoque-baixo {
            background-color: #10b981;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        .status-inativo, .status-esgotado {
            background-color: #ef4444;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        .status-em-processo-de-baixa, .status-em-baixa {
            background-color: #f59e0b;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        .status-expirado {
            background-color: #dc2626;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        select {
            transition: all 0.3s ease;
        }
        select:focus {
            outline: none;
            ring: 2px solid #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Painel de Gestão</h1>
        <div class="mb-6">
            <label for="moduleSelect" class="block text-lg font-semibold text-gray-700 mb-2">Selecionar Módulo:</label>
            <select id="moduleSelect" class="w-full sm:w-64 bg-white border border-gray-300 rounded-lg p-3 text-gray-700 focus:ring-2 focus:ring-blue-500 hover:border-blue-500 transition duration-200">
                <option value="all">Todos os Módulos</option>
                <option value="estoque">Estoque</option>
                <option value="contratos">Contratos</option>
                <option value="patrimonio">Patrimônio</option>
            </select>
        </div>
        <div class="dashboard bg-white rounded-xl shadow-lg p-6">
            <!-- Estoque Module -->
            <div id="estoqueModule" class="mb-12">
                <h2 class="text-2xl font-semibold mb-4">Estoque</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div class="card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Total de Produtos</h3>
                        <p class="text-3xl font-bold"><?php echo $total_produtos; ?></p>
                    </div>
                    <div class="card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Valor Total no Estoque</h3>
                        <p class="text-3xl font-bold">R$ <?php echo number_format($valor_total_estoque, 2, ',', '.'); ?></p>
                    </div>
                    <div class="card bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Produtos com Estoque Baixo</h3>
                        <p class="text-3xl font-bold"><?php echo count($alertas_estoque); ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="table-container bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">Alertas de Estoque</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alertas_estoque as $alerta): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($alerta['produto']); ?></td>
                                        <td><?php echo htmlspecialchars($alerta['quantidade']); ?> unidades (mínimo: <?php echo htmlspecialchars($alerta['estoque_minimo']); ?>)</td>
                                        <td>
                                            <span class="<?php echo $alerta['quantidade'] == 0 ? 'status-esgotado' : 'status-estoque-baixo'; ?>">
                                                <?php echo $alerta['quantidade'] == 0 ? 'Esgotado' : 'Estoque Baixo'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($alertas_estoque)): ?>
                                    <tr><td colspan="3" class="text-center">Nenhum alerta de estoque encontrado.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="text-right mt-4">
                            <a href="todos_alertas.php" class="text-blue-600 hover:underline">Ver todos os alertas <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="table-container bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">Últimos Produtos Cadastrados</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Código do Material</th>
                                    <th>Quantidade</th>
                                    <th>Descrição</th>
                                    <th>Data de Cadastro</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimos_produtos as $produto): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($produto['produto']); ?></td>
                                        <td><?php echo htmlspecialchars($produto['quantidade']); ?></td>
                                        <td><?php echo htmlspecialchars($produto['descricao']); ?></td>
                                        <td><?php echo htmlspecialchars($produto['data_cadastro']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-container bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-semibold mb-4">Usuários com Mais Movimentações</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Usuário (Matrícula)</th>
                                <th>Total de Movimentações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movimentacoes as $mov): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($mov['matricula']); ?></td>
                                    <td><?php echo htmlspecialchars($mov['total_movimentacoes']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($movimentacoes)): ?>
                                <tr><td colspan="2" class="text-center">Nenhuma movimentação encontrada.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Contratos Module -->
            <div id="contratosModule" class="mb-12">
                <h2 class="text-2xl font-semibold mb-4">Contratos</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <div class="card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Total de Contratos</h3>
                        <p class="text-3xl font-bold"><?php echo $total_processos; ?></p>
                    </div>
                    <div class="card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Contratos Ativos</h3>
                        <p class="text-3xl font-bold"><?php echo $total_ativos; ?></p>
                    </div>
                    <div class="card bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Contratos Expirados</h3>
                        <p class="text-3xl font-bold"><?php echo $total_expirados; ?></p>
                    </div>
                    <div class="card bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Contratos Vencendo em 30 Dias</h3>
                        <p class="text-3xl font-bold"><?php echo $total_vencendo; ?></p>
                    </div>
                    <div class="card bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Eventos de Hoje</h3>
                        <p class="text-3xl font-bold"><?php echo $total_agendamentos; ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">Contratos por Mês</h3>
                        <canvas id="contratosPorMesChart"></canvas>
                    </div>
                    <div class="table-container bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">Contratos por Vencer <i class="fas fa-exclamation-triangle text-yellow-500"></i></h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Contrato</th>
                                    <th>Fornecedor</th>
                                    <th>Término</th>
                                    <th>Valência</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contratos_expirando as $contrato): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($contrato['id']); ?></td>
                                        <td><?php echo htmlspecialchars($contrato['titulo']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($contrato['validade'])); ?></td>
                                        <td>R$ <?php echo number_format($contrato['valor_contrato'], 2, ',', '.'); ?></td>
                                        <td><span class="<?php echo $contrato['status'] == 'Ativo' ? 'status-ativo' : 'status-expirado'; ?>"><?php echo $contrato['status']; ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($contratos_expirando)): ?>
                                    <tr><td colspan="5" class="text-center">Nenhum contrato encontrado.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-container bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">Eventos de Hoje</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Descrição</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Categoria</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($eventos as $evento): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($evento['id']); ?></td>
                                        <td><?php echo htmlspecialchars($evento['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($evento['descricao']); ?></td>
                                        <td><?php echo htmlspecialchars($evento['data']); ?></td>
                                        <td><?php echo htmlspecialchars($evento['hora']); ?></td>
                                        <td><?php echo htmlspecialchars($evento['categoria']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($eventos)): ?>
                                    <tr><td colspan="6" class="text-center">Nenhum evento encontrado.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Patrimônio Module -->
            <div id="patrimonioModule" class="mb-12">
                <h2 class="text-2xl font-semibold mb-4">Patrimônio</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="card bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Total de Bens Cadastrados</h3>
                        <p class="text-3xl font-bold"><?php echo $totalBens; ?></p>
                    </div>
                    <div class="card bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Bens Ativos</h3>
                        <p class="text-3xl font-bold"><?php echo $bensAtivos; ?></p>
                    </div>
                    <div class="card bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Bens em Processo de Baixa</h3>
                        <p class="text-3xl font-bold"><?php echo $bensEmBaixa; ?></p>
                    </div>
                    <div class="card bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg p-6">
                        <h3 class="text-lg font-semibold">Bens Mortos</h3>
                        <p class="text-3xl font-bold"><?php echo $bensMortos; ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="table-container bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">Últimos Patrimônios Cadastrados</h3>
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
                                            <span class="<?php 
                                                echo $patrimonio['status'] == 'ativo' ? 'status-ativo' : 
                                                      ($patrimonio['status'] == 'inativo' ? 'status-inativo' : 'status-em-baixa'); ?>">
                                                <?php echo htmlspecialchars($patrimonio['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-container bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">Usuários que Mais Cadastraram Bens no Mês</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Setor</th>
                                    <th>Quantidade de Cadastros</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios_patrimonio as $usuario): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($usuario['usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['setor']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['quantidade_cadastros']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($usuarios_patrimonio)): ?>
                                    <tr><td colspan="3" class="text-center">Nenhum cadastro encontrado.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle module visibility based on select
        const moduleSelect = document.getElementById('moduleSelect');
        const estoqueModule = document.getElementById('estoqueModule');
        const contratosModule = document.getElementById('contratosModule');
        const patrimonioModule = document.getElementById('patrimonioModule');

        moduleSelect.addEventListener('change', () => {
            const selectedModule = moduleSelect.value;
            estoqueModule.style.display = selectedModule === 'all' || selectedModule === 'estoque' ? 'block' : 'none';
            contratosModule.style.display = selectedModule === 'all' || selectedModule === 'contratos' ? 'block' : 'none';
            patrimonioModule.style.display = selectedModule === 'all' || selectedModule === 'patrimonio' ? 'block' : 'none';
        });

        // Initialize with all modules visible
        moduleSelect.value = 'all';
        estoqueModule.style.display = 'block';
        contratosModule.style.display = 'block';
        patrimonioModule.style.display = 'block';

        // Gráfico de Contratos por Mês
        const contratosPorMesData = {
            labels: <?php echo json_encode($meses_contratos); ?>,
            datasets: [
                {
                    label: 'Ativos',
                    data: <?php echo json_encode($contratos_ativos_por_mes); ?>,
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1
                },
                {
                    label: 'Expirados',
                    data: <?php echo json_encode($contratos_expirados_por_mes); ?>,
                    backgroundColor: '#ef4444',
                    borderColor: '#dc2626',
                    borderWidth: 1
                },
                {
                    label: 'Novos',
                    data: <?php echo json_encode($contratos_novos_por_mes); ?>,
                    backgroundColor: '#3b82f6',
                    borderColor: '#2563eb',
                    borderWidth: 1
                }
            ]
        };

        const ctxContratosPorMes = document.getElementById('contratosPorMesChart').getContext('2d');
        new Chart(ctxContratosPorMes, {
            type: 'bar',
            data: contratosPorMesData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw + ' contratos';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Meses',
                            font: {
                                size: 14
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Contratos',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
include 'footer.php';
?>
