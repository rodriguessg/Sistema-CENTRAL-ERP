<?php
include 'header.php';

// Configuração da conexão com o banco de dados
$host = 'localhost';
$dbname = 'gm_sicbd'; // Substitua pelo nome do seu banco de dados
$username = 'root'; // Substitua pelo seu usuário do banco
$password = ''; // Substitua pela sua senha do banco

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}


// Consulta para obter os projetos e suas macroetapas
$query = "
    SELECT p.id, p.titulo_oportunidade, m.planejamento_id, m.etapa_concluida
    FROM planejamento p
    LEFT JOIN macroetapas m ON p.id = m.planejamento_id
    ORDER BY p.id
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar etapas por projeto
$projects = [];
foreach ($rows as $row) {
    $projectId = $row['id'];
    if (!isset($projects[$projectId])) {
        $projects[$projectId] = [
            'titulo_oportunidade' => $row['titulo_oportunidade'],
            'etapas' => []
        ];
    }
    if ($row['planejamento_id'] !== null) {
        $projects[$projectId]['etapas'][] = [
            'etapa_concluida' => $row['etapa_concluida']
        ];
    }
}

// Função para calcular o progresso
function calculateProgress($etapas) {
    if (empty($etapas)) {
        return 0;
    }
    $totalSteps = count($etapas);
    $completedSteps = count(array_filter($etapas, function($etapa) {
        return strtolower($etapa['etapa_concluida']) === 'sim';
    }));
    return $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planejamento de Negócios</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./src/planejamento/style/planejamento.css">
</head>
<body class="cardeno">
    <div class="container">
        <header class="header">
            <div class="header-content">
                <h1><i class="fas fa-chart-line"></i> Planejamento de Negócios</h1>
            </div>
        </header>

        <nav class="nav-tabs">
            <button class="tab-btn active" onclick="exibirAba('dashboard')"> <i class="fas fa-tachometer-alt"></i> Painel Geral</button>
            <!-- <button class="tab-btn" onclick="exibirAba('macroetapas')"><i class="fas fa-tachometer-alt"></i> Andamento Etapas</button> -->
            <button class="tab-btn" onclick="exibirAba('operacionalizacao_bonde')"><i class="fas fa-train"></i> PE1</button>
            <button class="tab-btn" onclick="exibirAba('bondes')"><i class="fas fa-train"></i> PE2</button>
            <button class="tab-btn" onclick="exibirAba('ferrovia')"><i class="fas fa-road"></i> PE3</button>
            <button class="tab-btn" onclick="exibirAba('teleferico')"><i class="fas fa-parachute-box"></i> PE4</button>
            <button class="tab-btn" onclick="exibirAba('ti')"><i class="fas fa-laptop-code"></i> PE5</button>
            <button class="tab-btn" onclick="exibirAba('capacitacao')"><i class="fas fa-graduation-cap"></i> PE6</button>
            <button class="tab-btn" onclick="exibirAba('patrimonio')"><i class="fas fa-landmark"></i> PE7</button>
            <button class="tab-btn" onclick="exibirAba('pca')"><i class="fas fa-file-contract"></i> PE8</button>
            <button class="tab-btn" onclick="exibirAba('gestao_pessoas')"><i class="fas fa-users"></i> PE9</button>
            <button class="tab-btn" onclick="exibirAba('solucoes_tecnologicas')"><i class="fas fa-cogs"></i> PE10</button>
            
        </nav>


<div id="dashboard" class="tab-content active">
    <div class="dashboard-grid">
        <div class="kpi-grid">
            <div class="kpi-card"><div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div><div class="kpi-content"><h3 id="total-value">R$ 0,00</h3><p>Valor Total em Aberto</p></div></div>
            <div class="kpi-card"><div class="kpi-icon"><i class="fas fa-bullseye"></i></div><div class="kpi-content"><h3 id="total-opportunities">0</h3><p>Oportunidades Ativas</p></div></div>
            <div class="kpi-card"><div class="kpi-icon"><i class="fas fa-percentage"></i></div><div class="kpi-content"><h3 id="conversion-rate">0%</h3><p>Taxa de Conversão</p></div></div>
            <div class="kpi-card"><div class="kpi-icon"><i class="fas fa-chart-bar"></i></div><div class="kpi-content"><h3 id="goal-progress">0%</h3><p>Progresso da Meta</p></div></div>
        </div>
        <div class="charts-grid">
            <div class="chart-card"><h3>Desempenho por Setor</h3><canvas id="performanceChart"></canvas></div>
            <div class="chart-card"><h3>Distribuição de Oportunidades</h3><canvas id="distributionChart"></canvas></div>
        </div>
        <!-- Seção de barras de progresso -->
        <div class="progress-section" style="margin-top: 20px;">
            <h3>Andamento dos Projetos</h3>
            <div id="progress-container" style="display: flex; flex-direction: column; gap: 15px;">
                <?php
                // Cores para as barras de progresso
                $colors = ['#4caf50', '#2196f3', '#ff9800', '#f44336', '#673ab7'];
                $index = 0;
                foreach ($projects as $project) {
                    $progress = calculateProgress($project['etapas']);
                    $color = $colors[$index % count($colors)];
                    // Refatorado para evitar ternários aninhados
                    if (isset($progress) && is_numeric($progress)) {
                        $width = $progress;
                    } else {
                        $width = 0;
                    }
                    echo '
                    <div class="progress-item">
                        <span>' . htmlspecialchars($project['titulo_oportunidade']) . '</span>
                        <div class="progress-bar" style="width: 100%; background-color: #e0e0e0; border-radius: 5px; overflow: hidden;">
                            <div style="width: ' . $width . '%; background-color: ' . $color . '; height: 20px; transition: width 0.5s;"></div>
                        </div>
                        <span>' . $progress . '%</span>
                    </div>';
                    $index++;
                }
                ?>
            </div>
        </div>
        <div class="alerts-section" style="margin-top: 20px;"><h3>Alertas Inteligentes</h3><div id="alerts-container" style="display: flex; flex-direction: column; gap: 10px;"></div></div>
    </div>
</div>

<style>
.progress-section {
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.progress-item {
    display: flex;
    align-items: center;
    gap: 10px;
}
.progress-item span:first-child {
    width: 150px;
    font-weight: bold;
}
.progress-item span:last-child {
    width: 50px;
    text-align: right;
}
.progress-bar {
    width: 100%;
    background-color: #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
}
.progress-bar div {
    height: 20px;
    transition: width 0.5s;
}
</style>

<!-- 
        <div id="macroetapas" class="tab-content">
            <div class="macroetapas-section" style="margin-top: 20px;">
                <h3>Progresso das Macroetapas</h3>
                <div id="macroetapas-container" style="display: flex; flex-direction: column; gap: 10px;">
                    <?php
                    foreach ($oportunidades as $oportunidade) {
                        $checked = isset($checkedEtapas[$oportunidade['macroetapa_id']]) ? 'checked' : '';
                        ?>
                        <div>
                            <input type="checkbox" name="etapa_<?php echo htmlspecialchars($oportunidade['id']); ?>" <?php echo $checked; ?>>
                            <label><?php echo htmlspecialchars($oportunidade['nome']); ?></label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div> -->

        <div id="operacionalizacao_bonde" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-train"></i> OPERACIONALIZAÇÃO DO SISTEMA DE BONDES DE SANTA TERESA</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-operacionalizacao_bonde">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-bondes-operacionalizacao_bonde">0%</h3><p>% da Meta Alcançada</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-bondes" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-operacionalizacao_bonde"></div>
        </div>

        <div id="bondes" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-train"></i> 2. RECUPERAÇÃO DO SISTEMA DE BONDES DE SANTA TERESA (OPERAÇÃO DO BONDE)</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-bondes">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-bondes">0%</h3><p>% da Meta Alcançada</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-bondes" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-bondes"></div>
        </div>

        <div id="ferrovia" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-road"></i> Ferrovia</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-ferrovia">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-ferrovia">0%</h3><p>% da Meta Alcançada</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-ferrovia" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-ferrovia"></div>
        </div>

        <div id="teleferico" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-parachute-box"></i> Teleférico</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-teleferico">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-teleferico">0%</h3><p>% da Meta Alcançada</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-teleferico" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-teleferico"></div>
        </div>

        <div id="ti" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-laptop-code"></i> Hardwares, Softwares e Sistemas (TI)</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-ti">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-ti">0%</h3><p>% da Evolução das Etapas</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-ti" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-ti"></div>
        </div>

        <div id="capacitacao" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-graduation-cap"></i> Capacitação</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-capacitacao">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-capacitacao">0%</h3><p>% da Meta Alcançada</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-capacitacao" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-capacitacao"></div>
        </div>

        <div id="patrimonio" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-landmark"></i> Patrimônio</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-patrimonio">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-patrimonio">0%</h3><p>% da Meta Alcançada</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-patrimonio" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-patrimonio"></div>
        </div>

        <div id="pca" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-file-contract"></i> Plano de Contratação Anual - PCA</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-pca">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-pca">0%</h3><p>% da Meta Alcançada</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-pca" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-pca"></div>
        </div>

        <div id="gestao_pessoas" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-users"></i> Gestão de Pessoas</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-gestao_pessoas">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-gestao_pessoas">0%</h3><p>% da Meta Alcançada</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-gestao_pessoas" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-gestao_pessoas"></div>
        </div>

        <div id="solucoes_tecnologicas" class="tab-content">
            <div class="sector-header"><h2><i class="fas fa-cogs"></i> Soluções Tecnológicas</h2></div>
            <div class="estimation-panel">
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-money-bill-wave"></i></div><div class="estimation-content"><h3 id="estimated-value-solucoes_tecnologicas">R$ 0,00</h3><p>Valor Total Estimado</p></div></div>
                <div class="estimation-card"><div class="estimation-icon"><i class="fas fa-percentage"></i></div><div class="estimation-content"><h3 id="goal-progress-solucoes_tecnologicas">0%</h3><p>% da Meta Alcançada</p></div></div>
            </div>
            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn btn-primary" onclick="abrirModal()"><i class="fas fa-plus"></i> Nova Oportunidade</button>
                <input type="text" id="search-solucoes_tecnologicas" placeholder="Pesquisar por nome da oportunidade" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px; width: 100%; max-width: 300px;">
            </div>
            <div class="opportunities-grid" id="opportunities-solucoes_tecnologicas"></div>
        </div>

        <div id="opportunityModal" class="modal" style="max-height: 80vh;
            overflow-y: auto;">
            <div class="modal-content" style="max-height: 80vh;
            overflow-y: auto;">
                <div class="modal-header"><h2><i class="fas fa-plus"></i> Nova Oportunidade</h2><span class="close" onclick="fecharModal()">×</span></div>
                <form id="opportunityForm" class="modal-body">
                    <div class="form-group"><label for="title">Título da Oportunidade</label><input type="text" id="title" name="title" required></div>
                    <div class="form-row">
                        <div class="form-group"><label for="sector">Setor</label><select id="sector" name="sector" required onchange="carregarTemplateSetor()"><option value="">Selecione um setor</option><option value="bondes">Bondes de Santa Teresa</option><option value="operacionalizacao_bonde">OPERACIONALIZAÇÃO DO SISTEMA DE BONDES DE SANTA TERESA</option><option value="ferrovia">Ferrovia</option><option value="teleferico">Teleférico</option><option value="ti">Hardwares, Softwares e Sistemas (TI)</option><option value="capacitacao">Capacitação</option><option value="patrimonio">Patrimônio</option><option value="pca">Plano de Contratação Anual - PCA</option><option value="gestao_pessoas">Gestão de Pessoas</option><option value="solucoes_tecnologicas">Soluções Tecnológicas</option></select></div>
                        <div class="form-group"><label for="value">Valor Estimado (R$)</label><input type="number" id="value" name="value" step="0.01" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label for="deadline">Prazo</label><input type="date" id="deadline" name="deadline" required></div>
                        <div class="form-group"><label for="status">Status</label><select id="status" name="status" required><option value="planejamento">Em Planejamento</option><option value="andamento">Em Andamento</option><option value="finalizado">Finalizado</option></select></div>
                    </div>
                    <div class="form-group"><label for="description">Descrição</label><textarea id="description" name="description" rows="3"></textarea></div>
                    <div class="form-group"><label>Plano do Projeto</label><div id="projectPlan" class="project-plan"></div><button type="button" class="btn btn-add" onclick="adicionarMacroetapa()"><i class="fas fa-plus"></i> Adicionar Macroetapa</button></div>
                    <div class="modal-footer-plan"><button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar Oportunidade</button>
            </div>
        </form>
        </div>
</div>

<!-- Modal para Edição -->
<div id="editModal" class="modal">
        <div class="modal-content" style="    position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        width: 61%;
        max-height: 80vh;
        overflow-y: scroll;
    ">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Editar Oportunidade</h2>
            <span class="close" onclick="fecharEditModal()">×</span>
        </div>
        <form id="editForm" class="modal-body">
            <input type="hidden" id="editId">
            <div class="form-group">
                <label for="editTitle">Título da Oportunidade</label>
                <input type="text" id="editTitle" name="title" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="editSector">Setor</label>
                    <select id="editSector" name="sector" required onchange="carregarTemplateSetorEdit()">
                        <option value="bondes">Bondes de Santa Teresa</option>
                        <option value="operacionalizacao_bonde">OPERACIONALIZAÇÃO DO SISTEMA DE BONDES DE SANTA TERESA</option>
                        <option value="ferrovia">Ferrovia</option>
                        <option value="teleferico">Teleférico</option>
                        <option value="ti">Hardwares, Softwares e Sistemas (TI)</option>
                        <option value="capacitacao">Capacitação</option>
                        <option value="patrimonio">Patrimônio</option>
                        <option value="pca">Plano de Contratação Anual - PCA</option>
                        <option value="gestao_pessoas">Gestão de Pessoas</option>
                        <option value="solucoes_tecnologicas">Soluções Tecnológicas</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editValue">Valor Estimado (R$)</label>
                    <input type="number" id="editValue" name="value" step="0.01" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="editDeadline">Prazo</label>
                    <input type="date" id="editDeadline" name="deadline" required>
                </div>
                <div class="form-group">
                    <label for="editStatus">Status</label>
                    <select id="editStatus" name="status" required>
                        <option value="planejamento">Em Planejamento</option>
                        <option value="andamento">Em Andamento</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="editDescription">Descrição</label>
                <textarea id="editDescription" name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Plano do Projeto</label>
                <div id="editProjectPlan" class="project-plan">
                    <!-- Macroetapas serão carregadas dinamicamente -->
                </div>
                <button type="button" class="btn btn-add" onclick="adicionarMacroetapaEdit()">
                    <i class="fas fa-plus"></i> Adicionar Macroetapa
                </button>
            </div>
            <div class="modal-footer-plan">
                <button type="button" class="btn btn-secondary" onclick="fecharEditModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./src/planejamento/js/script.js"></script>
    <script src="./src/planejamento/js/atualizarPainelEstimativa.js"></script>
    <?php
    include 'footer.php';?>
</body>
</html>