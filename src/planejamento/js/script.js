// Global Data
let opportunities = [];
let currentTab = "dashboard";
let performanceChart, distributionChart, evolutionChart;
let sectorCharts = {};
let filteredSector = null; // Track the currently filtered sector

// Sector Goals (dynamically updated based on opportunities)
let sectorGoals = {
    bondes: 0,
    ferrovia: 0,
    teleferico: 0,
    ti: 0,
    capacitacao: 0,
    patrimonio: 0,
    pca: 0,
    gestao_pessoas: 0,
    solucoes_tecnologicas: 0,
};

// Sector Project Templates
const sectorTemplates = {
    bondes: { title: "Projetos de Bondes", macroetapas: [{ name: "Operacionalização do Sistema de Bondes", etapas: ["Reativação de Linhas"] }, { name: "Recuperação do Sistema de Bondes", etapas: ["Operação do Bonde"] }] },
    ferrovia: { title: "Projetos de Ferrovia", macroetapas: [{ name: "Projetos de Engenharia", etapas: ["Planejamento e Execução"] }] },
    teleferico: { title: "Projetos de Teleférico", macroetapas: [{ name: "Reativação e Operacionalização", etapas: ["Reativação do Sistema de Transporte do Teleférico do Alemão"] }] },
    ti: { title: "Projetos de TI", macroetapas: [
        { name: "Infraestrutura de TI", etapas: ["Estruturar o banco de dados dos projetos de engenharia", "Aquisição de equipamentos e softwares", "Implantação da tecnologia BIM na CENTRAL e capacitar seus usuários", "Reestruturação de toda Infraestrutura de Rede com Segurança"] },
        { name: "Modernização", etapas: ["Modernização do parque computacional (desktops e notebooks)"] },
        { name: "Soluções de Atendimento", etapas: ["Solução de Atendimento ao Usuário – ITSM", "Wi-Fi corporativo com segurança e gerenciamento"] },
        { name: "Monitoramento", etapas: ["Monitoramento e Observabilidade da Infraestrutura"] },
        { name: "Conectividade", etapas: ["Contratação de link de internet dedicado", "Aquisição de softwares diversos", "Serviço de cabeamento estruturado"] }
    ]},
    capacitacao: { title: "Projetos de Capacitação", macroetapas: [
        { name: "Capacitação Geral", etapas: ["Capacitação dos empregados da GERGEP nos softwares de edição de texto e planilhas", "Capacitação dos empregados da GERGEP no tratamento do arquivamento de documentos", "Formação e capacitação do corpo técnico em processos de TIC"] },
        { name: "Treinamentos Específicos", etapas: ["Treinamentos no Sistema e-Social", "Treinamentos para área de Segurança do Trabalho", "Treinamentos visando à Educação Ambiental"] }
    ]},
    patrimonio: { title: "Projetos de Patrimônio", macroetapas: [
        { name: "Gestão Documental", etapas: ["Tratamento e digitalização de documentação administrativa e histórica"] },
        { name: "Controle de Bens", etapas: ["Controle de todos os bens móveis e imóveis", "Identificação dos bens que integram o acervo patrimonial"] }
    ]},
    pca: { title: "Plano de Contratação Anual", macroetapas: [{ name: "Planejamento", etapas: ["Plano de Contratação Anual (PCA) da CENTRAL"] }] },
    gestao_pessoas: { title: "Gestão de Pessoas", macroetapas: [{ name: "Programas", etapas: ["Planejamento do Programa de Desligamento Voluntário Incentivado - PDVI"] }] },
    solucoes_tecnologicas: { title: "Soluções Tecnológicas", macroetapas: [{ name: "Inovações", etapas: ["Desenvolvimento de novas soluções tecnológicas"] }] },
};

// Initialization
document.addEventListener("DOMContentLoaded", () => {
    carregarDados();
    atualizarMetasSetores();
    atualizarPainel();
    atualizarTodosSetores();
});

// Tab Management
const exibirAba = (tabName) => {
    document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
    document.querySelectorAll(".tab-content").forEach(content => content.classList.remove("active"));
    document.querySelector(`[onclick="exibirAba('${tabName}')"]`).classList.add("active");
    document.getElementById(tabName).classList.add("active");
    currentTab = tabName;
    tabName === "dashboard" ? atualizarPainel() : atualizarSetor(tabName);
};

// Modal Management
const abrirModal = () => {
    const modal = document.getElementById("opportunityModal");
    const form = document.getElementById("opportunityForm");
    modal.style.display = "block";
    form.reset();
    document.getElementById("projectPlan").innerHTML = "";
};

const fecharModal = () => document.getElementById("opportunityModal").style.display = "none";

const abrirEditModal = (opportunityId) => {
    const opportunity = opportunities.find(opp => opp.id === opportunityId);
    if (!opportunity) return;

    document.getElementById("editId").value = opportunity.id;
    document.getElementById("editTitle").value = opportunity.title;
    document.getElementById("editSector").value = opportunity.sector;
    document.getElementById("editValue").value = opportunity.value;
    document.getElementById("editDeadline").value = opportunity.deadline;
    document.getElementById("editStatus").value = opportunity.status;
    document.getElementById("editDescription").value = opportunity.description;

    const editProjectPlanDiv = document.getElementById("editProjectPlan");
    editProjectPlanDiv.innerHTML = "";
    opportunity.projectPlan.forEach((macro, macroIndex) => {
        let html = `
            <div class="macroetapa-form" data-macro="${macroIndex}">
                <div class="macroetapa-title">${macro.name}</div>
                <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                    <input type="text" id="responsible-edit-${macroIndex}" name="responsible" value="${macro.responsible || ''}" placeholder="Responsável" style="flex: 1;">
                    <label for="responsible-edit-${macroIndex}" style="margin: 0;">Responsável</label>
                </div>
                <div class="etapas-container" data-macro="${macroIndex}">
        `;
        macro.etapas.forEach((etapa, etapaIndex) => {
            html += `
                <div class="etapa-form">
                    <input type="text" value="${etapa.name}" data-macro="${macroIndex}" data-etapa="${etapaIndex}">
                    <button type="button" class="btn btn-small btn-remove" onclick="removerEtapaEdit(${macroIndex}, ${etapaIndex})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        });
        html += `
                <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapaEdit(${macroIndex})">
                    <i class="fas fa-plus"></i> Adicionar Etapa
                </button>
                <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapaEdit(${macroIndex})">
                    <i class="fas fa-trash"></i> Excluir Macroetapa
                </button>
                </div>
            </div>
        `;
        editProjectPlanDiv.innerHTML += html;
    });

    document.getElementById("editModal").style.display = "block";
};

const fecharEditModal = () => document.getElementById("editModal").style.display = "none";

// Load Sector Template
const carregarTemplateSetor = () => {
    const sector = document.getElementById("sector").value;
    const projectPlanDiv = document.getElementById("projectPlan");
    projectPlanDiv.innerHTML = "";

    if (!sector || !sectorTemplates[sector]) return;

    const template = sectorTemplates[sector];
    template.macroetapas.forEach((macroetapa, macroIndex) => {
        let html = `
            <div class="macroetapa-form" data-macro="${macroIndex}">
                <div class="macroetapa-title">${macroetapa.name}</div>
                <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                    <input type="text" id="responsible-${macroIndex}" name="responsible" placeholder="Responsável" style="flex: 1;">
                    <label for="responsible-${macroIndex}" style="margin: 0;">Responsável</label>
                </div>
                <div class="etapas-container" data-macro="${macroIndex}">
        `;
        macroetapa.etapas.forEach((etapa, etapaIndex) => {
            html += `
                <div class="etapa-form">
                    <input type="text" value="${etapa}" data-macro="${macroIndex}" data-etapa="${etapaIndex}">
                    <button type="button" class="btn btn-small btn-remove" onclick="removerEtapa(${macroIndex}, ${etapaIndex})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        });
        html += `
                <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapa(${macroIndex})">
                    <i class="fas fa-plus"></i> Adicionar Etapa
                </button>
                <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapa(${macroIndex})">
                    <i class="fas fa-trash"></i> Excluir Macroetapa
                </button>
                </div>
            </div>
        `;
        projectPlanDiv.innerHTML += html;
    });
};

const carregarTemplateSetorEdit = () => {
    const sector = document.getElementById("editSector").value;
    const projectPlanDiv = document.getElementById("editProjectPlan");
    projectPlanDiv.innerHTML = "";

    if (!sector || !sectorTemplates[sector]) return;

    const template = sectorTemplates[sector];
    template.macroetapas.forEach((macroetapa, macroIndex) => {
        let html = `
            <div class="macroetapa-form" data-macro="${macroIndex}">
                <div class="macroetapa-title">${macroetapa.name}</div>
                <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                    <input type="text" id="responsible-edit-${macroIndex}" name="responsible" placeholder="Responsável" style="flex: 1;">
                    <label for="responsible-edit-${macroIndex}" style="margin: 0;">Responsável</label>
                </div>
                <div class="etapas-container" data-macro="${macroIndex}">
        `;
        macroetapa.etapas.forEach((etapa, etapaIndex) => {
            html += `
                <div class="etapa-form">
                    <input type="text" value="${etapa}" data-macro="${macroIndex}" data-etapa="${etapaIndex}">
                    <button type="button" class="btn btn-small btn-remove" onclick="removerEtapaEdit(${macroIndex}, ${etapaIndex})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        });
        html += `
                <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapaEdit(${macroIndex})">
                    <i class="fas fa-plus"></i> Adicionar Etapa
                </button>
                <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapaEdit(${macroIndex})">
                    <i class="fas fa-trash"></i> Excluir Macroetapa
                </button>
                </div>
            </div>
        `;
        projectPlanDiv.innerHTML += html;
    });
};

// Macroetapa Management
const adicionarMacroetapa = () => {
    const projectPlanDiv = document.getElementById("projectPlan");
    const macroIndex = projectPlanDiv.querySelectorAll(".macroetapa-form").length;
    const html = `
        <div class="macroetapa-form" data-macro="${macroIndex}">
            <div class="macroetapa-title"><input type="text" placeholder="Novo nome da macroetapa" class="macro-title-input" data-macro="${macroIndex}"></div>
            <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                <input type="text" id="responsible-${macroIndex}" name="responsible" placeholder="Responsável" style="flex: 1;">
                <label for="responsible-${macroIndex}" style="margin: 0;">Responsável</label>
            </div>
            <div class="etapas-container" data-macro="${macroIndex}">
                <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapa(${macroIndex})">
                    <i class="fas fa-plus"></i> Adicionar Etapa
                </button>
                <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapa(${macroIndex})">
                    <i class="fas fa-trash"></i> Excluir Macroetapa
                </button>
            </div>
        </div>
    `;
    projectPlanDiv.insertAdjacentHTML("beforeend", html);
    projectPlanDiv.scrollTop = projectPlanDiv.scrollHeight;
};

const adicionarMacroetapaEdit = () => {
    const projectPlanDiv = document.getElementById("editProjectPlan");
    const macroIndex = projectPlanDiv.querySelectorAll(".macroetapa-form").length;
    const html = `
        <div class="macroetapa-form" data-macro="${macroIndex}">
            <div class="macroetapa-title"><input type="text" placeholder="Novo nome da macroetapa" class="macro-title-input" data-macro="${macroIndex}"></div>
            <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                <input type="text" id="responsible-edit-${macroIndex}" name="responsible" placeholder="Responsável" style="flex: 1;">
                <label for="responsible-edit-${macroIndex}" style="margin: 0;">Responsável</label>
            </div>
            <div class="etapas-container" data-macro="${macroIndex}">
                <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapaEdit(${macroIndex})">
                    <i class="fas fa-plus"></i> Adicionar Etapa
                </button>
                <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapaEdit(${macroIndex})">
                    <i class="fas fa-trash"></i> Excluir Macroetapa
                </button>
            </div>
        </div>
    `;
    projectPlanDiv.insertAdjacentHTML("beforeend", html);
    projectPlanDiv.scrollTop = projectPlanDiv.scrollHeight;
};

// Etapa Management
const adicionarEtapa = (macroIndex) => {
    const container = document.querySelector(`[data-macro="${macroIndex}"]`);
    if (!container) return;
    const etapas = container.querySelectorAll(".etapa-form");
    const html = `
        <div class="etapa-form">
            <input type="text" placeholder="Nova etapa" data-macro="${macroIndex}" data-etapa="${etapas.length}">
            <button type="button" class="btn btn-small btn-remove" onclick="removerEtapa(${macroIndex}, ${etapas.length})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.querySelector(".btn-add").insertAdjacentHTML("beforebegin", html);
};

const adicionarEtapaEdit = (macroIndex) => {
    const container = document.querySelector(`#editProjectPlan [data-macro="${macroIndex}"]`);
    if (!container) return;
    const etapas = container.querySelectorAll(".etapa-form");
    const html = `
        <div class="etapa-form">
            <input type="text" placeholder="Nova etapa" data-macro="${macroIndex}" data-etapa="${etapas.length}">
            <button type="button" class="btn btn-small btn-remove" onclick="removerEtapaEdit(${macroIndex}, ${etapas.length})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.querySelector(".btn-add").insertAdjacentHTML("beforebegin", html);
};

const removerEtapa = (macroIndex, etapaIndex) => {
    const etapaForm = document.querySelector(`input[data-macro="${macroIndex}"][data-etapa="${etapaIndex}"]`)?.closest(".etapa-form");
    if (etapaForm) etapaForm.remove();
};

const removerEtapaEdit = (macroIndex, etapaIndex) => {
    const etapaForm = document.querySelector(`#editProjectPlan input[data-macro="${macroIndex}"][data-etapa="${etapaIndex}"]`)?.closest(".etapa-form");
    if (etapaForm) etapaForm.remove();
};

// Macroetapa Removal and Reindexing
const removerMacroetapa = (macroIndex) => {
    const macroForm = document.querySelector(`#projectPlan .macroetapa-form[data-macro="${macroIndex}"]`);
    if (macroForm && confirm("Tem certeza que deseja excluir esta macroetapa?")) {
        macroForm.remove();
        reindexMacroetapas("projectPlan");
    }
};

const removerMacroetapaEdit = (macroIndex) => {
    const macroForm = document.querySelector(`#editProjectPlan .macroetapa-form[data-macro="${macroIndex}"]`);
    if (macroForm && confirm("Tem certeza que deseja excluir esta macroetapa?")) {
        macroForm.remove();
        reindexMacroetapas("editProjectPlan");
    }
};

const reindexMacroetapas = (planId) => {
    const projectPlanDiv = document.getElementById(planId);
    projectPlanDiv.querySelectorAll(".macroetapa-form").forEach((form, index) => {
        form.setAttribute("data-macro", index);
        const titleInput = form.querySelector(".macro-title-input");
        if (titleInput) titleInput.setAttribute("data-macro", index);
        form.querySelector(".etapas-container").setAttribute("data-macro", index);
        form.querySelectorAll(".etapa-form input").forEach(input => input.setAttribute("data-macro", index));
        const responsibleInput = form.querySelector(".responsible-field input");
        const responsibleLabel = form.querySelector(".responsible-field label");
        if (responsibleInput && responsibleLabel) {
            const newId = `${planId === "projectPlan" ? "" : "edit-"}${index}`;
            responsibleInput.id = `responsible-${newId}`;
            responsibleLabel.setAttribute("for", `responsible-${newId}`);
        }
    });
};

// Opportunity Management
const excluirOportunidade = (opportunityId) => {
    const opportunity = opportunities.find(opp => opp.id === opportunityId);
    if (opportunity && confirm(`Tem certeza que deseja excluir "${opportunity.title}"?`)) {
        opportunities = opportunities.filter(opp => opp.id !== opportunityId);
        atualizarMetasSetores();
        salvarDados();
        atualizarPainel();
        atualizarSetor(opportunity.sector);
    }
};

const atualizarStatusEtapa = (opportunityId, macroIndex, etapaIndex) => {
    const opportunity = opportunities.find(opp => opp.id === opportunityId);
    if (!opportunity) return;
    const etapa = opportunity.projectPlan[macroIndex]?.etapas[etapaIndex];
    if (etapa) {
        etapa.completed = !etapa.completed;
        salvarDados();
        atualizarSetor(opportunity.sector);
        atualizarPainel();
    }
};

// Form Handling
document.getElementById("opportunityForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const projectPlan = [];

    document.querySelectorAll("#projectPlan .macroetapa-form").forEach((macroDiv, macroIndex) => {
        const macroTitleInput = macroDiv.querySelector(".macro-title-input");
        const macroTitle = macroTitleInput ? macroTitleInput.value.trim() : macroDiv.querySelector(".macroetapa-title").textContent;
        const responsibleInput = macroDiv.querySelector(`#responsible-${macroIndex}`);
        const responsible = responsibleInput ? responsibleInput.value.trim() : "";
        const etapas = Array.from(macroDiv.querySelectorAll(".etapa-form input"))
            .filter(input => input.value.trim())
            .map(input => ({ name: input.value.trim(), completed: false }));
        if (etapas.length > 0 || macroTitle) {
            projectPlan.push({ name: macroTitle || `Macroetapa ${macroIndex + 1}`, responsible, etapas, expanded: true });
            // Send notification for each etapa and responsible
            etapas.forEach(etapa => {
                enviarNotificacao(responsible, formData.get("sector"), etapa.name);
            });
        }
    });

    const opportunity = {
        id: Date.now(),
        title: formData.get("title"),
        sector: formData.get("sector"),
        value: parseFloat(formData.get("value")) || 0,
        deadline: formData.get("deadline"),
        status: formData.get("status"),
        description: formData.get("description") || "",
        projectPlan,
        createdAt: new Date().toISOString(),
    };

    opportunities.push(opportunity);
    atualizarMetasSetores();
    salvarDados();
    atualizarPainel();
    atualizarTodosSetores();
    fecharModal();
    if (opportunity.sector && opportunity.sector !== "dashboard") exibirAba(opportunity.sector);
});

document.getElementById("editForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const id = document.getElementById("editId").value;
    const opportunity = opportunities.find(opp => opp.id == id);
    if (!opportunity) return;

    opportunity.title = document.getElementById("editTitle").value;
    opportunity.sector = document.getElementById("editSector").value;
    opportunity.value = parseFloat(document.getElementById("editValue").value) || 0;
    opportunity.deadline = document.getElementById("editDeadline").value;
    opportunity.status = document.getElementById("editStatus").value;
    opportunity.description = document.getElementById("editDescription").value;

    const projectPlan = [];
    document.querySelectorAll("#editProjectPlan .macroetapa-form").forEach((macroDiv, macroIndex) => {
        const macroTitleInput = macroDiv.querySelector(".macro-title-input");
        const macroTitle = macroTitleInput ? macroTitleInput.value.trim() : macroDiv.querySelector(".macroetapa-title").textContent;
        const responsibleInput = macroDiv.querySelector(`#responsible-edit-${macroIndex}`);
        const responsible = responsibleInput ? responsibleInput.value.trim() : "";
        const etapas = Array.from(macroDiv.querySelectorAll(".etapa-form input"))
            .filter(input => input.value.trim())
            .map(input => ({ name: input.value.trim(), completed: false }));
        if (etapas.length > 0 || macroTitle) {
            projectPlan.push({ name: macroTitle || `Macroetapa ${macroIndex + 1}`, responsible, etapas, expanded: true });
        }
    });
    opportunity.projectPlan = projectPlan;

    atualizarMetasSetores();
    salvarDados();
    atualizarPainel();
    atualizarTodosSetores();
    fecharEditModal();
    exibirAba(opportunity.sector);
});

function enviarNotificacao(username, setor, mensagem) {
    const notification = {
        id: Date.now(),
        username: username || "N/A",
        setor: setor,
        mensagem: mensagem,
        situacao: "nao lida",
        data_criacao: new Date("2025-07-17T10:41:00-03:00").toISOString(),
        certidao_id: null
    };
    fetch('./save_notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(notification)
    })
    .then(response => response.json())
    .then(data => console.log("Notificação salva:", data))
    .catch(error => console.error("Erro ao salvar notificação:", error));
}

// Dashboard and Visualization
const atualizarPainel = () => {
    const filteredOpps = filteredSector ? opportunities.filter(opp => opp.sector === filteredSector) : opportunities;
    const totalValue = filteredOpps.reduce((sum, opp) => opp.status !== "finalizado" ? sum + opp.value : sum, 0);
    const totalOpportunities = filteredOpps.length;
    const completedOpportunities = filteredOpps.filter(opp => opp.status === "finalizado").length;
    const conversionRate = totalOpportunities > 0 ? (completedOpportunities / totalOpportunities) * 100 : 0;
    const totalGoal = filteredSector ? sectorGoals[filteredSector] : Object.values(sectorGoals).reduce((sum, goal) => sum + goal, 0);
    const completedValue = filteredOpps.filter(opp => opp.status === "finalizado").reduce((sum, opp) => sum + opp.value, 0);
    const goalProgress = totalGoal > 0 ? (completedValue / totalGoal) * 100 : 0;

    document.getElementById("total-value").textContent = formatarMoeda(totalValue || 0);
    document.getElementById("total-opportunities").textContent = totalOpportunities;
    document.getElementById("conversion-rate").textContent = conversionRate.toFixed(1) + "%";
    document.getElementById("goal-progress").textContent = goalProgress.toFixed(1) + "%";

    atualizarGraficos();
};

const atualizarGraficos = () => {
    if (performanceChart) performanceChart.destroy();
    if (distributionChart) distributionChart.destroy();
    if (evolutionChart) evolutionChart.destroy();

    atualizarGraficoPerformance();
    atualizarGraficoDistribuicao();
    atualizarGraficoEvolucao();
    atualizarAlertas();
};

const atualizarGraficoPerformance = () => {
    const ctx = document.getElementById("performanceChart")?.getContext("2d");
    if (!ctx) return;

    const sectorData = Object.keys(sectorGoals).map(sector => {
        const sectorOpportunities = opportunities.filter(opp => opp.sector === sector && opp.status === "finalizado");
        const achieved = sectorOpportunities.reduce((sum, opp) => sum + opp.value, 0);
        return { sector: sector.charAt(0).toUpperCase() + sector.slice(1).replace('_', ' '), achieved, goal: sectorGoals[sector] };
    });

    performanceChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: sectorData.map(d => d.sector),
            datasets: [
                { label: "Realizado", data: sectorData.map(d => d.achieved), backgroundColor: "rgba(76, 175, 80, 0.8)", borderColor: "rgba(76, 175, 80, 1)", borderWidth: 1 },
                { label: "Meta", data: sectorData.map(d => d.goal), backgroundColor: "rgba(102, 126, 234, 0.8)", borderColor: "rgba(102, 126, 234, 1)", borderWidth: 1 },
            ],
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { callback: value => "R$ " + value / 1000 + "k" } } },
            plugins: { tooltip: { callbacks: { label: context => context.dataset.label + ": " + formatarMoeda(context.parsed.y) } } },
        },
    });
};


const atualizarGraficoDistribuicao = () => {
    const ctx = document.getElementById("distributionChart")?.getContext("2d");
    if (!ctx) return;

    const sectors = Object.keys(sectorGoals);
    const sectorCounts = sectors.map(sector => opportunities.filter(opp => opp.sector === sector).length);
    const totalOpportunities = opportunities.length;
    const percentages = totalOpportunities > 0 ? sectorCounts.map(count => ((count / totalOpportunities) * 100).toFixed(1)) : sectorCounts.map(() => "0.0");
    const colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF", "#FF9F40", "#4BC0C0", "#9966FF", "#FF9F40"];
    const backgroundColors = sectors.map((sector, index) => filteredSector === sector ? darkenColor(colors[index], 0.2) : lightenColor(colors[index], 0.5));
    const borderColors = sectors.map((sector, index) => filteredSector === sector ? darkenColor(colors[index], 0.4) : colors[index]);

    distributionChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: sectors.map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' ')),
            datasets: [{ data: sectorCounts, backgroundColor: backgroundColors, borderColor: borderColors, borderWidth: 2 }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "bottom",
                    onClick: (e, legendItem) => {
                        const sectorIndex = legendItem.index;
                        const sector = sectors[sectorIndex].toLowerCase();
                        filteredSector = filteredSector === sector ? null : sector;
                        atualizarPainel();
                        atualizarGraficoDistribuicao(); // Re-render to update colors
                    }
                },
                tooltip: { callbacks: { label: context => `${context.label || ''}: ${context.parsed} (${percentages[context.dataIndex]}%)` } }
            },
        },
    });
};

const atualizarGraficoEvolucao = () => {
    const ctx = document.getElementById("evolutionChart")?.getContext("2d");
    if (!ctx) return;

    const months = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
    const currentDate = new Date("2025-07-15T16:32:00-03:00");

    const monthlyData = months.reduce((acc, month, index) => {
        const monthKey = `2025-${String(index + 1).padStart(2, '0')}`;
        acc[monthKey] = { total: 0, completed: 0 };
        return acc;
    }, {});

    opportunities.forEach(opp => {
        const createdDate = new Date(opp.createdAt);
        if (createdDate.getFullYear() === 2025) {
            const monthKey = `2025-${String(createdDate.getMonth() + 1).padStart(2, '0')}`;
            monthlyData[monthKey].total++;
            if (opp.status === "finalizado") monthlyData[monthKey].completed++;
        }
    });

    const totalData = months.map((_, index) => monthlyData[`2025-${String(index + 1).padStart(2, '0')}`].total);
    const completedData = months.map((_, index) => monthlyData[`2025-${String(index + 1).padStart(2, '0')}`].completed);

    evolutionChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: months,
            datasets: [
                { label: "Total de Oportunidades", data: totalData, borderColor: "rgba(54, 162, 235, 1)", backgroundColor: "rgba(54, 162, 235, 0.2)", fill: false, tension: 0.1, borderWidth: 2, pointRadius: 4 },
                { label: "Oportunidades Concluídas", data: completedData, borderColor: "rgba(75, 192, 192, 1)", backgroundColor: "rgba(75, 192, 192, 0.2)", fill: false, tension: 0.1, borderWidth: 2, pointRadius: 4 },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { x: { title: { display: true, text: "Mês" }, position: "bottom" }, y: { beginAtZero: true, title: { display: true, text: "Quantidade" } } },
            plugins: { legend: { position: "top" }, tooltip: { callbacks: { label: context => `${context.dataset.label}: ${context.parsed.y}` } } },
        },
    });
};

const atualizarAlertas = () => {
    const alertsContainer = document.getElementById("alerts-container");
    if (!alertsContainer) return;

    const currentDate = new Date("2025-07-15T16:32:00-03:00");
    alertsContainer.innerHTML = "";

    const expiredOpps = opportunities.filter(opp => {
        const deadline = new Date(opp.deadline);
        return opp.status !== "finalizado" && deadline < currentDate;
    });

    expiredOpps.forEach((opp, index) => {
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert-card alert-danger-${index}`;
        alertDiv.style.cssText = `
            padding: 15px; border-radius: 12px; color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 10px; transition: all 0.3s ease; background: linear-gradient(135deg, #e74c3c, #c0392b);
            position: relative; overflow: hidden;
        `;
        alertDiv.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between; z-index: 1;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 20px; animation: pulse 1.5s infinite;"></i>
                    <span style="font-weight: 600; font-size: 16px;">${opp.title} - Vencida</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 12px; opacity: 0.8;">${currentDate.toLocaleTimeString("pt-BR", { hour: '2-digit', minute: '2-digit' })} - ${currentDate.toLocaleDateString("pt-BR")}</span>
                    <button onclick="this.closest('.alert-card').style.opacity='0';setTimeout(() => this.closest('.alert-card').remove(),300)" style="background:none;border:none;color:#fff;cursor:pointer;font-size:16px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div style="position:absolute;top:0;left:-100%;width:100%;height:100%;background:rgba(255,255,255,0.1);animation:slide 4s infinite;"></div>
        `;
        alertsContainer.appendChild(alertDiv);
    });

    const style = document.createElement("style");
    style.textContent = `
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
        @keyframes slide { 0% { left: -100%; } 100% { left: 100%; } }
        .alert-card:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.2); }
        .alert-card button:hover { transform: rotate(90deg); }
        .alert-card button:active { transform: scale(0.9); }
    `;
    document.head.appendChild(style);
};

// Sector Updates
const atualizarSetor = (setor) => {
    atualizarPainelEstimativa(setor);
    renderizarOportunidades(setor);
};

const atualizarTodosSetores = () => Object.keys(sectorGoals).forEach(atualizarSetor);

const atualizarPainelEstimativa = (setor) => {
    const sectorOpportunities = opportunities.filter(opp => opp.sector === setor);
    const totalEstimatedValue = sectorOpportunities.reduce((sum, opp) => opp.status !== "finalizado" ? sum + opp.value : sum, 0);
    const completedValue = sectorOpportunities.filter(opp => opp.status === "finalizado").reduce((sum, opp) => sum + opp.value, 0);
    const goal = sectorGoals[setor];
    const goalProgress = goal > 0 ? (completedValue / goal) * 100 : 0;

    document.getElementById(`estimated-value-${setor}`).textContent = formatarMoeda(totalEstimatedValue);
    document.getElementById(`goal-progress-${setor}`).textContent = goalProgress.toFixed(1) + "%";
};

const renderizarOportunidades = (setor) => {
    const opportunitiesGrid = document.getElementById(`opportunities-${setor}`);
    if (!opportunitiesGrid) return;

    opportunitiesGrid.innerHTML = "";
    const sectorOpportunities = opportunities.filter(opp => opp.sector === setor);

    sectorOpportunities.forEach(opp => {
        const progress = opp.projectPlan.reduce((total, macro) => total + (macro.etapas.filter(e => e.completed).length / (macro.etapas.length || 1)), 0) / (opp.projectPlan.length || 1) * 100;
        const deadlineDate = new Date(opp.deadline);
        const currentDate = new Date("2025-07-15T16:32:00-03:00");
        const isExpired = deadlineDate < currentDate;
        const expiredMessage = isExpired ? `Vencido em: ${deadlineDate.toLocaleDateString("pt-BR")}` : "";

        const html = `
            <div class="opportunity-card" style="border:1px solid #ddd;border-radius:8px;padding:15px;background:#fff;width:113%;margin-bottom:15px;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                <div class="opportunity-header" style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #eee;padding-bottom:10px;">
                    <div>
                        <div class="opportunity-title" style="font-size:18px;font-weight:bold;color:#2c3e50;margin-bottom:5px;">${opp.title}</div>
                        <div class="opportunity-meta" style="font-size:14px;color:#7f8c8d;display:flex;gap:10px;flex-wrap:wrap;">
                            <span>R$ ${formatarMoeda(opp.value)}</span><span>|</span>
                            <span>${new Date(opp.deadline).toLocaleDateString("pt-BR")}</span><span>|</span>
                            <span class="opportunity-status status-${opp.status}" style="padding:2px 8px;border-radius:3px;${
                                opp.status === 'planejamento' ? 'background-color:#f1c40f;' : opp.status === 'andamento' ? 'background-color:#3498db;' : 'background-color:#2ecc71;'
                            }color:white;">${
                                opp.status === "planejamento" ? "Em Planejamento" : opp.status === "andamento" ? "Em Andamento" : "Finalizado"
                            }</span>
                            ${isExpired ? `<span style="color:#e74c3c;font-weight:bold;">${expiredMessage}</span>` : ""}
                            <select class="status-dropdown" onchange="atualizarStatusOportunidade(${opp.id},this.value)" style="padding:2px 8px;border:1px solid #ddd;border-radius:4px;font-size:14px;">
                                <option value="planejamento" ${opp.status === "planejamento" ? "selected" : ""}>Em Planejamento</option>
                                <option value="andamento" ${opp.status === "andamento" ? "selected" : ""}>Em Andamento</option>
                                <option value="finalizado" ${opp.status === "finalizado" ? "selected" : ""}>Finalizado</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;gap:5px;">
                        <button class="btn btn-edit" onclick="abrirEditModal(${opp.id})" style="background:#e67e22;color:#fff;display:flex;justify-content:center;align-items:center;padding:5px 10px;border:none;border-radius:4px;width:65px;height:29px;">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-delete" onclick="excluirOportunidade(${opp.id})" style="background:#e74c3c;color:#fff;display:flex;justify-content:center;align-items:center;padding:5px 10px;border:none;border-radius:4px;width:65px;height:29px;">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </div>
                </div>
                <div class="opportunity-progress" style="margin-top:10px;">
                    <div class="progress-bar-small" style="width:100%;height:6px;background:#ecf0f1;border-radius:3px;">
                        <div class="progress-fill-small" style="height:100%;width:${progress}%;background:#3498db;border-radius:3px;"></div>
                    </div>
                    <span style="display:block;font-size:12px;color:#7f8c8d;margin-top:5px;">Progresso: ${progress.toFixed(1)}%</span>
                </div>
                ${opp.projectPlan.map((macro, macroIndex) => `
                    <div class="macroetapa-header ${macro.expanded ? '' : 'collapsed'}" style="display:flex;align-items:center;gap:5px;cursor:pointer;font-weight:bold;color:#2c3e50;margin-top:10px;padding:5px;border-bottom:1px solid #eee;" onclick="toggleMacroetapa(${opp.id},${macroIndex})">
                        <i class="fas fa-chevron-down" style="transition:transform 0.3s;"></i> ${macro.name} (Responsável: ${macro.responsible || "N/A"})
                    </div>
                    <div class="etapas-list" style="display:${macro.expanded ? 'block' : 'none'};margin-left:20px;margin-top:5px;">
                        ${macro.etapas.map((etapa, etapaIndex) => `
                            <div class="etapa-item" style="display:flex;align-items:center;gap:5px;margin-top:5px;">
                                <input type="checkbox" ${etapa.completed ? "checked" : ""} onchange="atualizarStatusEtapa(${opp.id},${macroIndex},${etapaIndex})">
                                <span style="flex:1;">${etapa.name}</span>
                            </div>
                        `).join('')}
                    </div>
                `).join('')}
            </div>
        `;
        opportunitiesGrid.innerHTML += html;
    });
};

// Toggle and Status Updates
const toggleMacroetapa = (opportunityId, macroIndex) => {
    const opportunity = opportunities.find(opp => opp.id === opportunityId);
    if (!opportunity) return;
    opportunity.projectPlan[macroIndex].expanded = !opportunity.projectPlan[macroIndex].expanded;
    salvarDados();
    atualizarSetor(opportunity.sector);
};

const atualizarStatusOportunidade = (opportunityId, newStatus) => {
    const opportunity = opportunities.find(opp => opp.id === opportunityId);
    if (!opportunity) return;
    opportunity.status = newStatus;
    atualizarMetasSetores();
    salvarDados();
    atualizarPainel();
    atualizarSetor(opportunity.sector);
};

// Utility Functions
const formatarMoeda = (valor) => new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(valor);

const salvarDados = () => {
    const data = { opportunities, sectorGoals };
    localStorage.setItem("opportunities", JSON.stringify(data));
};

const carregarDados = () => {
    const saved = localStorage.getItem("opportunities");
    if (saved) {
        const data = JSON.parse(saved);
        opportunities = data.opportunities.map(opp => ({
            ...opp,
            projectPlan: opp.projectPlan.map(macro => ({ ...macro, expanded: macro.expanded !== undefined ? macro.expanded : true })),
        }));
        sectorGoals = data.sectorGoals || {
            bondes: 0,
            ferrovia: 0,
            teleferico: 0,
            ti: 0,
            capacitacao: 0,
            patrimonio: 0,
            pca: 0,
            gestao_pessoas: 0,
            solucoes_tecnologicas: 0,
        };
    }
};

const atualizarMetasSetores = () => {
    const newSectorGoals = {
        bondes: 0,
        ferrovia: 0,
        teleferico: 0,
        ti: 0,
        capacitacao: 0,
        patrimonio: 0,
        pca: 0,
        gestao_pessoas: 0,
        solucoes_tecnologicas: 0,
    };
    opportunities.forEach(opp => {
        if (newSectorGoals[opp.sector] !== undefined) newSectorGoals[opp.sector] += opp.value;
    });
    sectorGoals = newSectorGoals;
};

// Color Utility Functions
function lightenColor(color, amount) {
    const hex = color.replace('#', '');
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    const newR = Math.min(255, r + Math.round(255 * amount));
    const newG = Math.min(255, g + Math.round(255 * amount));
    const newB = Math.min(255, b + Math.round(255 * amount));
    return `rgba(${newR}, ${newG}, ${newB}, 0.5)`;
}

function darkenColor(color, amount) {
    const hex = color.replace('#', '');
    const r = parseInt(hex.substr(0, 2), 16);
    const g = parseInt(hex.substr(2, 2), 16);
    const b = parseInt(hex.substr(4, 2), 16);
    const newR = Math.max(0, r - Math.round(255 * amount));
    const newG = Math.max(0, g - Math.round(255 * amount));
    const newB = Math.max(0, b - Math.round(255 * amount));
    return `rgba(${newR}, ${newG}, ${newB}, 1)`;
}