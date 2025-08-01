<?php
// Configuração inicial
header('Content-Type: text/html; charset=utf-8');

// Verifica se é uma requisição AJAX (fetch)
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Definições de conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro ao conectar ao banco de dados: " . $e->getMessage());
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Erro de conexão: ' . $e->getMessage()]);
        exit;
    }
    $records = [];
}

// Obtém a data e hora atual no fuso horário de São Paulo
$dateTimeNow = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
$currentDateTime = $dateTimeNow->format('Y-m-d H:i:s');

// Sincroniza project_plan com macroetapas (se necessário)
try {
    $sqlPlanejamento = "SELECT id, project_plan FROM planejamento WHERE status = :status";
    $stmtPlanejamento = $pdo->prepare($sqlPlanejamento);
    $stmtPlanejamento->execute([':status' => 'andamento']);
    $planejamentos = $stmtPlanejamento->fetchAll(PDO::FETCH_ASSOC);

    foreach ($planejamentos as $planejamento) {
        $id = $planejamento['id'];
        $projectPlan = json_decode($planejamento['project_plan'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Erro ao decodificar JSON em project_plan (ID $id): " . json_last_error_msg());
            continue;
        }
        $macroetapasExistentes = $pdo->prepare("SELECT id, planejamento_id, nome_macroetapa FROM macroetapas WHERE planejamento_id = :id");
        $macroetapasExistentes->execute([':id' => $id]);
        $macroetapasDb = $macroetapasExistentes->fetchAll(PDO::FETCH_ASSOC);
        $nomesExistentes = array_column($macroetapasDb, 'nome_macroetapa');

        $macroetapasPlan = $projectPlan; // Ajuste para o formato correto de projectPlan
        foreach ($macroetapasPlan as $macro) {
            if (!in_array($macro['name'] ?? $macro['nome_macroetapa'], $nomesExistentes)) {
                $sqlInsert = "INSERT INTO macroetapas (planejamento_id, setor, nome_macroetapa, responsavel, etapa_nome, etapa_concluida, data_conclusao) VALUES (:id, (SELECT setor FROM planejamento WHERE id = :id), :nome, :responsavel, :etapa, :concluida, :data)";
                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':id' => $id,
                    ':nome' => $macro['name'] ?? $macro['nome_macroetapa'] ?? 'Sem Nome',
                    ':responsavel' => $macro['responsible'] ?? 'N/A',
                    ':etapa' => isset($macro['etapas'][0]['name']) ? $macro['etapas'][0]['name'] : 'Sem Etapa',
                    ':concluida' => isset($macro['etapas'][0]['completed']) && $macro['etapas'][0]['completed'] ? 'sim' : 'não',
                    ':data' => isset($macro['etapas'][0]['completed']) && $macro['etapas'][0]['completed'] ? $currentDateTime : null
                ]);
                error_log("Nova etapa inserida em macroetapas para planejamento ID $id: " . ($macro['name'] ?? $macro['nome_macroetapa']));
            }
        }
    }
} catch (PDOException $e) {
    error_log("Erro ao sincronizar project_plan: " . $e->getMessage());
}

// Consulta às oportunidades em andamento com todas as macroetapas
$sql = "
    SELECT 
        p.id AS planejamento_id, 
        p.titulo_oportunidade, 
        p.setor, 
        p.valor_estimado, 
        p.prazo, 
        p.status, 
        p.descricao, 
        p.created_at,
        m.id AS macro_id, 
        m.nome_macroetapa, 
        m.responsavel, 
        m.etapa_nome, 
        m.etapa_concluida, 
        m.data_conclusao
    FROM planejamento p
    LEFT JOIN macroetapas m ON p.id = m.planejamento_id
    WHERE p.status = :status
    ORDER BY p.created_at DESC, m.nome_macroetapa
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':status' => 'andamento']);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular médias por setor
$mediaPorSetor = [];
foreach ($records as $record) {
    $setor = $record['setor'] ?? 'Sem Setor';
    if (!isset($mediaPorSetor[$setor])) {
        $mediaPorSetor[$setor] = [
            'totalMacroetapas' => 0,
            'macroetapasConcluidas' => 0
        ];
    }
    if ($record['macro_id']) {
        $mediaPorSetor[$setor]['totalMacroetapas']++;
        if ($record['etapa_concluida'] === 'sim') {
            $mediaPorSetor[$setor]['macroetapasConcluidas']++;
        }
    }
}

$cardsData = [];
foreach ($mediaPorSetor as $setor => $dados) {
    $progresso = $dados['totalMacroetapas'] > 0 ? ($dados['macroetapasConcluidas'] / $dados['totalMacroetapas']) * 100 : 0;
    $cardsData[] = [
        'setor' => $setor,
        'progresso' => $progresso,
        'totalMacroetapas' => $dados['totalMacroetapas'],
        'macroetapasConcluidas' => $dados['macroetapasConcluidas']
    ];
}

// Organiza os dados por oportunidade e setor para a tabela
$oportunidadesPorSetor = [];
foreach ($records as $record) {
    $setor = $record['setor'] ?? 'Sem Setor';
    $oportunidadeId = $record['planejamento_id'];

    if (!isset($oportunidadesPorSetor[$setor][$oportunidadeId])) {
        $oportunidadesPorSetor[$setor][$oportunidadeId] = [
            'titulo_oportunidade' => $record['titulo_oportunidade'],
            'setor' => $record['setor'],
            'valor_estimado' => $record['valor_estimado'],
         
            'status' => $record['status'],
            'descricao' => $record['descricao'],
            'created_at' => $record['created_at'],
            'macroetapas' => []
        ];
    }

    if ($record['macro_id']) {
        $oportunidadesPorSetor[$setor][$oportunidadeId]['macroetapas'][] = [
            'macro_id' => $record['macro_id'],
            'nome_macroetapa' => $record['nome_macroetapa'],
            'responsavel' => $record['responsavel'],
            'etapa_nome' => $record['etapa_nome'],
            'etapa_concluida' => $record['etapa_concluida'],
            'data_conclusao' => $record['data_conclusao']
        ];
    }
}

// Se for requisição AJAX, retorna JSON
if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'oportunidades_por_setor' => $oportunidadesPorSetor,
        'cards_data' => $cardsData,
        'last_updated' => $currentDateTime
    ]);
    exit;
}

$pdo = null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhamento de Etapas por Setor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h1 { font-size: 24px; font-weight: 600; color: #1a73e8; margin: 0; }
        .cards-container { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 15px; width: 200px; text-align: center; }
        .card h3 { margin: 0 0 10px; font-size: 16px; color: #1a73e8; }
        .card .progress { font-size: 24px; font-weight: bold; color: #34a853; }
        .card .progress-bar { width: 100%; height: 8px; background: #e0e0e0; border-radius: 4px; margin-top: 10px; }
        .card .progress-fill { height: 100%; background: #34a853; border-radius: 4px; }
        .section { margin-bottom: 20px; }
        .section h2 { color: #1a73e8; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #1a73e8; color: #fff; font-weight: 500; }
        td { background-color: #fff; }
        .progress-bar { width: 100%; height: 8px; background: #e0e0e0; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; background: #34a853; border-radius: 4px; }
        .status-completed { color: #34a853; font-weight: bold; }
        .status-pending { color: #ea4335; }
        .actions { display: flex; gap: 10px; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-edit { background-color: #fbbc04; color: #fff; }
        .btn-delete { background-color: #ea4335; color: #fff; }
        #last-updated { font-size: 12px; color: #757575; text-align: right; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Acompanhamento de Etapas em Andamento</h1>
        </div>
        <!-- Seção de Cards -->
        <div class="cards-container">
            <?php foreach ($cardsData as $card): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($card['setor']); ?></h3>
                    <div class="progress"><?php echo number_format($card['progresso'], 1); ?>%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $card['progresso']; ?>%;"></div>
                    </div>
                    <p>Total Macroetapas: <?php echo $card['totalMacroetapas']; ?>, Concluídas: <?php echo $card['macroetapasConcluidas']; ?></p>
                </div>
            <?php endforeach; ?>
            <?php if (empty($cardsData)): ?>
                <p style="text-align: center; color: #ea4335;">Nenhum setor com dados disponíveis.</p>
            <?php endif; ?>
        </div>
        <!-- Restante do conteúdo (tabela) -->
        <div id="content">
            <?php foreach ($oportunidadesPorSetor as $setor => $oportunidades): ?>
                <div class="section">
                    <h2><?php echo htmlspecialchars($setor); ?> (Total Etapas: <?php echo array_reduce($oportunidades, fn($sum, $op) => $sum + count($op['macroetapas']), 0); ?>, Concluídas: <?php echo array_reduce($oportunidades, fn($sum, $op) => $sum + array_reduce($op['macroetapas'], fn($carry, $macro) => $carry + ($macro['etapa_concluida'] === 'sim' ? 1 : 0), 0), 0); ?> - <?php
                        $total = array_reduce($oportunidades, fn($sum, $op) => $sum + count($op['macroetapas']), 0);
                        $concluidas = array_reduce($oportunidades, fn($sum, $op) => $sum + array_reduce($op['macroetapas'], fn($carry, $macro) => $carry + ($macro['etapa_concluida'] === 'sim' ? 1 : 0), 0), 0);
                        echo number_format($total > 0 ? ($concluidas / $total) * 100 : 0, 1);
                    ?>%)</h2>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $total > 0 ? ($concluidas / $total) * 100 : 0; ?>%;"></div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Oportunidade</th>
                         
                                <th>Macroetapa</th>
                                <th>Etapa</th>
                                <th>Responsável</th>
                                <th>Progresso</th>
                                <th>Status</th>
                         
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($oportunidades as $oportunidadeId => $op): ?>
                                <?php foreach ($op['macroetapas'] as $macro): ?>
                                    <?php
                                     
                                        $status = $macro['etapa_concluida'] === 'sim' ? 'Concluído' : 'Em Andamento';
                                        $progresso = $macro['etapa_concluida'] === 'sim' ? 100 : 0;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($op['titulo_oportunidade']); ?></td>
                                     
                                        <td><?php echo htmlspecialchars($macro['nome_macroetapa'] ?? 'Sem Nome'); ?></td>
                                        <td><?php echo htmlspecialchars($macro['etapa_nome'] ?? 'Sem Etapa'); ?></td>
                                        <td><?php echo htmlspecialchars($macro['responsavel'] ?? 'N/A'); ?></td>
                                        <td>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?php echo $progresso; ?>%;"></div>
                                            </div>
                                            <span style="display: block; text-align: center; font-size: 12px;"><?php echo number_format($progresso, 1); ?>%</span>
                                        </td>
                                        <td class="<?php echo $status === 'Concluído' ? 'status-completed' : 'status-pending'; ?>">
                                            <?php echo $status; ?>
                                        </td>
                                        <!-- <td class="actions">
                                            <button class="btn btn-edit" onclick="editarOportunidade(<?php echo $op['planejamento_id']; ?>, '<?php echo isset($macro['macro_id']) ? htmlspecialchars($macro['macro_id']) : '0'; ?>')">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <button class="btn btn-delete" onclick="excluirOportunidade(<?php echo $op['planejamento_id']; ?>, '<?php echo isset($macro['macro_id']) ? htmlspecialchars($macro['macro_id']) : '0'; ?>')">
                                                <i class="fas fa-trash"></i> Excluir
                                            </button>
                                        </td> -->
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($op['macroetapas'])): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($op['titulo_oportunidade']); ?></td>
                                      
                                        <td colspan="6" style="text-align: center; color: #ea4335;">Nenhuma macroetapa cadastrada</td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
            <?php if (empty($oportunidadesPorSetor)): ?>
                <p style="text-align: center; color: #ea4335;">Nenhum dado disponível para status 'andamento'.</p>
            <?php endif; ?>
        </div>
        <p id="last-updated">Última atualização: <?php echo $dateTimeNow->format('h:i A -03, d/m/Y'); ?></p>
    </div>

    <script>
        function editarOportunidade(id, macroId) {
            alert(`Editar oportunidade ID: ${id}, Macroetapa ID: ${macroId || 'não especificada'}`);
        }
        function excluirOportunidade(id, macroId) {
            if (confirm(`Deseja excluir a macroetapa ID: ${macroId || 'não especificada'} da oportunidade ID: ${id}?`)) {
                alert(`Excluindo macroetapa ID: ${macroId || 'não especificada'} da oportunidade ID: ${id}`);
            }
        }

        function atualizarTabela() {
            fetch('./?ajax=true', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const content = document.getElementById('content');
                    const cardsContainer = document.querySelector('.cards-container');
                    let cardsHtml = '';
                    data.cards_data.forEach(card => {
                        cardsHtml += `<div class="card"><h3>${card.setor}</h3><div class="progress">${card.progresso.toFixed(1)}%</div><div class="progress-bar"><div class="progress-fill" style="width: ${card.progresso}%;"></div></div><p>Total Macroetapas: ${card.totalMacroetapas}, Concluídas: ${card.macroetapasConcluidas}</p></div>`;
                    });
                    if (data.cards_data.length === 0) {
                        cardsHtml = '<p style="text-align: center; color: #ea4335;">Nenhum setor com dados disponíveis.</p>';
                    }
                    cardsContainer.innerHTML = cardsHtml;

                    let tableHtml = '';
                    for (const [setor, oportunidades] of Object.entries(data.oportunidades_por_setor)) {
                        tableHtml += `<div class="section"><h2>${setor} (Total Etapas: ${Object.values(oportunidades).reduce((sum, op) => sum + op.macroetapas.length, 0)}, Concluídas: ${Object.values(oportunidades).reduce((sum, op) => sum + op.macroetapas.filter(m => m.etapa_concluida === 'sim').length, 0)} - ${data.cards_data.find(c => c.setor === setor)?.progresso.toFixed(1) || 0}%)</h2>`;
                        tableHtml += `<div class="progress-bar"><div class="progress-fill" style="width: ${data.cards_data.find(c => c.setor === setor)?.progresso || 0}%;"></div></div>`;
                        tableHtml += '<table><thead><tr><th>Oportunidade</th><th>Prazo</th><th>Macroetapa</th><th>Etapa</th><th>Responsável</th><th>Progresso</th><th>Status</th><th>Ações</th></tr></thead><tbody>';
                        for (const [oportunidadeId, op] of Object.entries(oportunidades)) {
                            op.macroetapas.forEach(macro => {
                                const prazoDate = new Date(op.prazo).toLocaleDateString('pt-BR');
                                const status = macro.etapa_concluida === 'sim' ? 'Concluído' : 'Em Andamento';
                                const progresso = macro.etapa_concluida === 'sim' ? 100 : 0;
                                tableHtml += `<tr><td>${op.titulo_oportunidade}</td><td>${prazoDate}</td><td>${macro.nome_macroetapa || 'Sem Nome'}</td><td>${macro.etapa_nome || 'Sem Etapa'}</td><td>${macro.responsavel || 'N/A'}</td><td><div class="progress-bar"><div class="progress-fill" style="width: ${progresso}%;"></div><span style="display: block; text-align: center; font-size: 12px;">${progresso.toFixed(1)}%</span></div></td><td class="${status === 'Concluído' ? 'status-completed' : 'status-pending'}">${status}</td><td class="actions"><button class="btn btn-edit" onclick="editarOportunidade(${op.planejamento_id}, '${macro.macro_id || '0'}')"><i class="fas fa-edit"></i> Editar</button><button class="btn btn-delete" onclick="excluirOportunidade(${op.planejamento_id}, '${macro.macro_id || '0'}')"><i class="fas fa-trash"></i> Excluir</button></td></tr>`;
                            });
                            if (op.macroetapas.length === 0) {
                                const prazoDate = new Date(op.prazo).toLocaleDateString('pt-BR');
                                tableHtml += `<tr><td>${op.titulo_oportunidade}</td><td>${prazoDate}</td><td colspan="6" style="text-align: center; color: #ea4335;">Nenhuma macroetapa cadastrada</td></tr>`;
                            }
                        }
                        tableHtml += '</tbody></table></div>';
                    }
                    if (Object.keys(data.oportunidades_por_setor).length === 0) {
                        tableHtml = '<p style="text-align: center; color: #ea4335;">Nenhum dado disponível para status \'andamento\'.</p>';
                    }
                    content.innerHTML = tableHtml;
                    document.getElementById('last-updated').textContent = `Última atualização: ${new Date(data.last_updated).toLocaleString('pt-BR', { hour: '2-digit', minute: '2-digit', timeZone: 'America/Sao_Paulo', hour12: true }).replace(' ', ' -03, ') + ', ' + new Date(data.last_updated).toLocaleDateString('pt-BR')}`;
                }
            })
            .catch(error => console.error('Erro ao atualizar tabela:', error));
        }

        // Atualiza a tabela a cada 30 segundos
        setInterval(atualizarTabela, 30000);
        // Atualiza imediatamente ao carregar a página
        document.addEventListener('DOMContentLoaded', atualizarTabela);
    </script>
</body>
</html>