<?php
include 'header.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
      <link rel="stylesheet" href="./src/planejamento/style/planejamento.css">
          <!-- <link rel="stylesheet" href="./src/estoque/style/estoque2.css"> -->
</head>
<body class="cardeno">
    <div class="container">
        <!-- Cabeçalho -->
        <header class="header">
       <div class="header-content">
    <h1><i class="fas fa-chart-line"></i> Planejamento de Negócios</h1>
    <input type="text" id="filterInput" class="filter-input" placeholder="Filtrar por título..." onkeyup="filtrarOportunidades()">
    <!-- <button class="btn btn-primary" onclick="abrirModal()">
        <i class="fas fa-plus"></i> Nova Oportunidade
    </button> -->
</div>
        </header>

        <!-- Abas de Navegação -->
        <nav class="nav-tabs">
            <button class="tab-btn active" onclick="exibirAba('dashboard')">
                <i class="fas fa-tachometer-alt"></i> Painel Geral
            </button>
            <button class="tab-btn" onclick="exibirAba('bondes')">
                <i class="fas fa-train"></i> Bondes de Santa Teresa
            </button>
            <button class="tab-btn" onclick="exibirAba('ferrovia')">
                <i class="fas fa-road"></i> Ferrovia
            </button>
            <button class="tab-btn" onclick="exibirAba('teleferico')">
                <i class="fas fa-parachute-box"></i> Teleférico
            </button>
            <button class="tab-btn" onclick="exibirAba('ti')">
                <i class="fas fa-laptop-code"></i> Hardwares, Softwares e Sistemas (TI)
            </button>
            <button class="tab-btn" onclick="exibirAba('capacitacao')">
                <i class="fas fa-graduation-cap"></i> Capacitação
            </button>
            <button class="tab-btn" onclick="exibirAba('patrimonio')">
                <i class="fas fa-landmark"></i> Patrimônio
            </button>
            <button class="tab-btn" onclick="exibirAba('pca')">
                <i class="fas fa-file-contract"></i> Plano de Contratação Anual - PCA
            </button>
            <button class="tab-btn" onclick="exibirAba('gestao_pessoas')">
                <i class="fas fa-users"></i> Gestão de Pessoas
            </button>
            <button class="tab-btn" onclick="exibirAba('solucoes_tecnologicas')">
                <i class="fas fa-cogs"></i> Soluções Tecnológicas
            </button>
        </nav>

       <!-- Painel Geral -->
<!-- Painel Geral -->
<div id="dashboard" class="tab-content active">
    <div class="dashboard-grid">
        <!-- Indicadores-Chave -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="kpi-content">
                    <h3 id="total-value">R$ 0,00</h3>
                    <p>Valor Total em Aberto</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="kpi-content">
                    <h3 id="total-opportunities">0</h3>
                    <p>Oportunidades Ativas</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="kpi-content">
                    <h3 id="conversion-rate">0%</h3>
                    <p>Taxa de Conversão</p>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="kpi-content">
                    <h3 id="goal-progress">0%</h3>
                    <p>Progresso da Meta</p>
                </div>
            </div>
        </div>
        <!-- Gráficos -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3>Desempenho por Setor</h3>
                <canvas id="performanceChart"></canvas>
            </div>
          
            <div class="chart-card">
                <h3>Distribuição de Oportunidades</h3>
                <canvas id="distributionChart"></canvas>
            </div>
              <div class="chart-card">
                <h3>Evolução das Oportunidades</h3>
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
        <!-- Alertas Inteligentes -->
        <div class="alerts-section" style="margin-top: 20px;">
            <h3>Alertas Inteligentes</h3>
            <div id="alerts-container" style="display: flex; flex-direction: column; gap: 10px;"></div>
        </div>
    </div>
</div>

        <!-- Abas de Setores -->
        <div id="bondes" class="tab-content">
            <div class="sector-header">
                <h2><i class="fas fa-train"></i> Bondes de Santa Teresa</h2>
                <div class="sector-meta">
                    <span class="meta-label">Meta PE:</span>
                    <span class="meta-value">R$ 200.000,00</span>
                </div>
            </div>
            <div class="estimation-panel">
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="estimated-value-bondes">R$ 0,00</h3>
                        <p>Valor Total Estimado</p>
                    </div>
                </div>
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="goal-progress-bondes">0%</h3>
                        <p>% da Meta Alcançada</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fas fa-plus"></i> Nova Oportunidade
            </button>
            <div class="opportunities-grid" id="opportunities-bondes"></div>
        </div>

        <div id="ferrovia" class="tab-content">
            <div class="sector-header">
                <h2><i class="fas fa-road"></i> Ferrovia</h2>
                <div class="sector-meta">
                    <span class="meta-label">Meta PE:</span>
                    <span class="meta-value">R$ 300.000,00</span>
                </div>
            </div>
            <div class="estimation-panel">
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="estimated-value-ferrovia">R$ 0,00</h3>
                        <p>Valor Total Estimado</p>
                    </div>
                </div>
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="goal-progress-ferrovia">0%</h3>
                        <p>% da Meta Alcançada</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fas fa-plus"></i> Nova Oportunidade
            </button>
            <div class="opportunities-grid" id="opportunities-ferrovia"></div>
        </div>

        <div id="teleferico" class="tab-content">
            <div class="sector-header">
                <h2><i class="fas fa-parachute-box"></i> Teleférico</h2>
                <div class="sector-meta">
                    <span class="meta-label">Meta PE:</span>
                    <span class="meta-value">R$ 250.000,00</span>
                </div>
            </div>
            <div class="estimation-panel">
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="estimated-value-teleferico">R$ 0,00</h3>
                        <p>Valor Total Estimado</p>
                    </div>
                </div>
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="goal-progress-teleferico">0%</h3>
                        <p>% da Meta Alcançada</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fas fa-plus"></i> Nova Oportunidade
            </button>
            <div class="opportunities-grid" id="opportunities-teleferico"></div>
        </div>

        <div id="ti" class="tab-content">
            <div class="sector-header">
                <h2><i class="fas fa-laptop-code"></i> Hardwares, Softwares e Sistemas (TI)</h2>
                <!-- <div class="sector-meta">
                    <span class="meta-label">Meta PE:</span>
                    <span class="meta-value">R$ 400.000,00</span>
                </div> -->
            </div>
            <div class="estimation-panel">
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="estimated-value-ti">R$ 0,00</h3>
                        <p>Valor Total Estimado</p>
                    </div>
                </div>
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="goal-progress-ti">0%</h3>
                        <p>% da Meta Alcançada</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fas fa-plus"></i> Nova Oportunidade
            </button>
            <div class="opportunities-grid" id="opportunities-ti"></div>
        </div>

        <div id="capacitacao" class="tab-content">
            <div class="sector-header">
                <h2><i class="fas fa-graduation-cap"></i> Capacitação</h2>
                <div class="sector-meta">
                    <span class="meta-label">Meta PE:</span>
                    <span class="meta-value">R$ 150.000,00</span>
                </div>
            </div>
            <div class="estimation-panel">
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="estimated-value-capacitacao">R$ 0,00</h3>
                        <p>Valor Total Estimado</p>
                    </div>
                </div>
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="goal-progress-capacitacao">0%</h3>
                        <p>% da Meta Alcançada</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fas fa-plus"></i> Nova Oportunidade
            </button>
            <div class="opportunities-grid" id="opportunities-capacitacao"></div>
        </div>

        <div id="patrimonio" class="tab-content">
            <div class="sector-header">
                <h2><i class="fas fa-landmark"></i> Patrimônio</h2>
                <div class="sector-meta">
                    <span class="meta-label">Meta PE:</span>
                    <span class="meta-value">R$ 200.000,00</span>
                </div>
            </div>
            <div class="estimation-panel">
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="estimated-value-patrimonio">R$ 0,00</h3>
                        <p>Valor Total Estimado</p>
                    </div>
                </div>
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="goal-progress-patrimonio">0%</h3>
                        <p>% da Meta Alcançada</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fas fa-plus"></i> Nova Oportunidade
            </button>
            <div class="opportunities-grid" id="opportunities-patrimonio"></div>
        </div>

        <div id="pca" class="tab-content">
            <div class="sector-header">
                <h2><i class="fas fa-file-contract"></i> Plano de Contratação Anual - PCA</h2>
                <div class="sector-meta">
                    <span class="meta-label">Meta PE:</span>
                    <span class="meta-value">R$ 100.000,00</span>
                </div>
            </div>
            <div class="estimation-panel">
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="estimated-value-pca">R$ 0,00</h3>
                        <p>Valor Total Estimado</p>
                    </div>
                </div>
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="goal-progress-pca">0%</h3>
                        <p>% da Meta Alcançada</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fas fa-plus"></i> Nova Oportunidade
            </button>
            <div class="opportunities-grid" id="opportunities-pca"></div>
        </div>

        <div id="gestao_pessoas" class="tab-content">
            <div class="sector-header">
                <h2><i class="fas fa-users"></i> Gestão de Pessoas</h2>
                <div class="sector-meta">
                    <span class="meta-label">Meta PE:</span>
                    <span class="meta-value">R$ 150.000,00</span>
                </div>
            </div>
            <div class="estimation-panel">
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="estimated-value-gestao_pessoas">R$ 0,00</h3>
                        <p>Valor Total Estimado</p>
                    </div>
                </div>
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="goal-progress-gestao_pessoas">0%</h3>
                        <p>% da Meta Alcançada</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fas fa-plus"></i> Nova Oportunidade
            </button>
            <div class="opportunities-grid" id="opportunities-gestao_pessoas"></div>
        </div>

        <div id="solucoes_tecnologicas" class="tab-content">
            <div class="sector-header">
                <h2><i class="fas fa-cogs"></i> Soluções Tecnológicas</h2>
                <div class="sector-meta">
                    <span class="meta-label">Meta PE:</span>
                    <span class="meta-value">R$ 300.000,00</span>
                </div>
            </div>
            <div class="estimation-panel">
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="estimated-value-solucoes_tecnologicas">R$ 0,00</h3>
                        <p>Valor Total Estimado</p>
                    </div>
                </div>
                <div class="estimation-card">
                    <div class="estimation-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="estimation-content">
                        <h3 id="goal-progress-solucoes_tecnologicas">0%</h3>
                        <p>% da Meta Alcançada</p>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" onclick="abrirModal()">
                <i class="fas fa-plus"></i> Nova Oportunidade
            </button>
            <div class="opportunities-grid" id="opportunities-solucoes_tecnologicas"></div>
        </div>
    </div>

 <!-- Modal para Nova Oportunidade -->
<div id="opportunityModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-plus"></i> Nova Oportunidade</h2>
            <span class="close" onclick="fecharModal()">×</span>
        </div>
        <form id="opportunityForm" class="modal-body">
            <div class="form-group">
                <label for="title">Título da Oportunidade</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="sector">Setor</label>
                    <select id="sector" name="sector" required onchange="carregarTemplateSetor()">
                        <option value="">Selecione um setor</option>
                        <option value="bondes">Bondes de Santa Teresa</option>
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
                    <label for="value">Valor Estimado (R$)</label>
                    <input type="number" id="value" name="value" step="0.01" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="deadline">Prazo</label>
                    <input type="date" id="deadline" name="deadline" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="planejamento">Em Planejamento</option>
                        <option value="andamento">Em Andamento</option>
                        <option value="finalizado">Finalizado</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Plano do Projeto</label>
                <div id="projectPlan" class="project-plan">
                    <!-- Macroetapas serão carregadas dinamicamente -->
                </div>
                <button type="button" class="btn btn-add" onclick="adicionarMacroetapa()">
                    <i class="fas fa-plus"></i> Adicionar Macroetapa
                </button>
            </div>
            <div class="modal-footer-plan">
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Criar Oportunidade</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para Edição -->
<div id="editModal" class="modal">
    <div class="modal-content">
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

<!-- JavaScript for Updated Functionality -->
<script>
 
</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./src/planejamento/js/script.js"></script>
    <?php
    include 'footer.php';?>
</body>
</html>
