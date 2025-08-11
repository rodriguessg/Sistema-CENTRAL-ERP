let opportunities = [];
let currentTab = "dashboard";
let performanceChart, distributionChart, evolutionChart;
let sectorCharts = {};
let filteredSector = null;

// Sector Goals
const sectorGoals = {
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
    bondes: {
        title: "Projetos de Bondes",
         macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}
        ]
    },
    ferrovia: {
        title: "Projetos de Ferrovia",
         macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}]
    },
    teleferico: {
        title: "Projetos de Teleférico",
        macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}]
    },
    ti: {
        title: "Projetos de TI",
         macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}
        ]
    },
    capacitacao: {
        title: "Projetos de Capacitação",
        macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}
        ]
    },
    patrimonio: {
        title: "Projetos de Patrimônio",
        macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}
        ]
    },
    pca: {
        title: "Plano de Contratação Anual",
        macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}]
    },
    gestao_pessoas: {
        title: "Gestão de Pessoas",
         macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}]
    },
    
    
    solucoes_tecnologicas: {
        title: "Soluções Tecnológicas",
        macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}
    ]
    },
    recuperacao_bonde: {
        title: "OPERACIONALIZAÇÃO DO SISTEMA DE BONDES DE SANTA TERESA ",
         macroetapas: [{ name: "PLANEJAMENTO PCA", etapas: ["Desenvolvimento de novas soluções tecnológicas"] },
        {name: "FASE PREPARATÓRIA", etapas:["Estudo Técnico Preliminar / Matriz de Risco",
            "Termo de Referência ",
        "Pesquisa de Preços / Relatório Analítico",
        "Autorização do Ordenador de Despesa",
        "DDO - Declaração de Dotação Orçamentária",
        "Minuta Edital/Contrato",
        "Parecer Jurídico (ASSJUR)",
        "Lançamento do Edital no SIGA"
    ]},
        {name: "FASE EXTERNA", etapas: ["Recibo de envio de Edital ao TCE / Lançamento SIGA",
            "Abertura do Pregão / Dispensa de Licitação",
            "Adjudicação (Declaração do Vencedor)",
            "Compliance (Due Diligence - ASSGER)",
            "Homologação e Publicação no D.O"
        ]},
        {name: "FASE DE CONTRATAÇÃO", etapas: ["Contratação no SIGA",
            "NAD / Nota de Empenho",
            "Contrato (Assinatura)",
            "Gestores e Fiscais",
            "Garantia Contratual"

        ]}
    ]
    }
};

// Initialization
document.addEventListener("DOMContentLoaded", async () => {
    try {
        await loadChartJs();
        await carregarDados();
        await atualizarMacroetapas();
    } catch (error) {
        console.error("Erro na inicialização:", error);
        alert("Erro ao carregar os dados iniciais. Tente novamente.");
    }
});

// Utility Functions
const loadChartJs = () => {
    return new Promise((resolve, reject) => {
        const script = document.createElement("script");
        script.src = "https://cdn.jsdelivr.net/npm/chart.js";
        script.onload = resolve;
        script.onerror = () => {
            alert("Falha ao carregar Chart.js. Algumas visualizações podem não funcionar.");
            reject(new Error("Falha ao carregar Chart.js"));
        };
        document.head.appendChild(script);
    });
};

// const formatarMoeda = (valor) => {
//     return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(valor);
// };

const lightenColor = (color, amount) => {
    const hex = color.replace('#', '');
    const r = Math.min(255, parseInt(hex.substr(0, 2), 16) + Math.round(255 * amount));
    const g = Math.min(255, parseInt(hex.substr(2, 2), 16) + Math.round(255 * amount));
    const b = Math.min(255, parseInt(hex.substr(4, 2), 16) + Math.round(255 * amount));
    return `rgba(${r}, ${g}, ${b}, 0.5)`;
};

const darkenColor = (color, amount) => {
    const hex = color.replace('#', '');
    const r = Math.max(0, parseInt(hex.substr(0, 2), 16) - Math.round(255 * amount));
    const g = Math.max(0, parseInt(hex.substr(2, 2), 16) - Math.round(255 * amount));
    const b = Math.max(0, parseInt(hex.substr(4, 2), 16) - Math.round(255 * amount));
    return `rgba(${r}, ${g}, ${b}, 1)`;
};

// Tab Management
const exibirAba = (tabName) => {
    document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
    document.querySelectorAll(".tab-content").forEach(content => content.classList.remove("active"));

    const tabBtn = document.querySelector(`[onclick="exibirAba('${tabName}')"]`);
    const tabContent = document.getElementById(tabName);

    if (tabBtn) tabBtn.classList.add("active");
    if (tabContent) tabContent.classList.add("active");

    currentTab = tabName;
    if (tabName === "dashboard") {
        atualizarPainel();
    } else {
        atualizarSetor(tabName);
    }
};

// Modal Management
const abrirModal = () => {
    const modal = document.getElementById("opportunityModal");
    const form = document.getElementById("opportunityForm");
    if (!modal || !form) return;

    modal.style.display = "block";
    modal.setAttribute("aria-hidden", "false");
    form.reset();
    document.getElementById("projectPlan").innerHTML = "";
};

const fecharModal = () => {
    const modal = document.getElementById("opportunityModal");
    if (modal) {
        modal.style.display = "none";
        modal.setAttribute("aria-hidden", "true");
    }
};

const abrirEditModal = (opportunityId) => {
    console.log("Tentando abrir modal para ID:", opportunityId, " (tipo:", typeof opportunityId, ")");
    console.log("Opportunities atuais:", opportunities);
    const opportunity = opportunities.find(opp => Number(opp.id) === Number(opportunityId));
    if (!opportunity) {
        console.error("Oportunidade não encontrada para ID:", opportunityId, "Lista de IDs disponíveis:", opportunities.map(opp => opp.id));
        alert("Oportunidade não encontrada. Verifique os dados ou recarregue a página.");
        return;
    }

    const modal = document.getElementById("editModal");
    if (!modal) {
        console.error("Modal de edição não encontrado no DOM.");
        return;
    }

    document.getElementById("editId").value = opportunity.id || "";
    document.getElementById("editTitle").value = opportunity.title || "";
    document.getElementById("editSector").value = opportunity.sector || "";
    document.getElementById("editValue").value = opportunity.value ? Number(opportunity.value).toFixed(2) : "";
    document.getElementById("editDeadline").value = opportunity.deadline || "";
    document.getElementById("editStatus").value = opportunity.status || "planejamento";
    document.getElementById("editDescription").value = opportunity.description || "";

    const editProjectPlanDiv = document.getElementById("editProjectPlan");
    if (!editProjectPlanDiv) {
        console.error("Div editProjectPlan não encontrada.");
        return;
    }

    editProjectPlanDiv.innerHTML = "";
    opportunity.projectPlan.forEach((macro, macroIndex) => {
        const html = `
            <div class="macroetapa-form" data-macro="${macroIndex}">
                <div class="macroetapa-title">${macro.name || `Macroetapa ${macroIndex + 1}`}</div>
                <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                    <input type="text" id="responsible-edit-${macroIndex}" name="responsible" value="${macro.responsible || ''}" placeholder="Responsável" style="flex: 1;">
                    <label for="responsible-edit-${macroIndex}" style="margin: 0;">Responsável</label>
                </div>
                <div class="etapas-container" data-macro="${macroIndex}">
                    ${macro.etapas.map((etapa, etapaIndex) => `
                        <div class="etapa-form">
                            <input type="text" value="${etapa.name || ''}" data-macro="${macroIndex}" data-etapa="${etapaIndex}">
                            <button type="button" class="btn btn-small btn-remove" onclick="removerEtapaEdit(${macroIndex}, ${etapaIndex})" aria-label="Remover etapa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `).join('')}
                    <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapaEdit(${macroIndex})" aria-label="Adicionar etapa">
                        <i class="fas fa-plus"></i> Adicionar Etapa
                    </button>
                    <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapaEdit(${macroIndex})" aria-label="Excluir macroetapa">
                        <i class="fas fa-trash"></i> Excluir Macroetapa
                    </button>
                </div>
            </div>
        `;
        editProjectPlanDiv.innerHTML += html;
    });

    modal.style.display = "block";
    modal.setAttribute("aria-hidden", "false");
};

const fecharEditModal = () => {
    const modal = document.getElementById("editModal");
    if (modal) {
        modal.style.display = "none";
        modal.setAttribute("aria-hidden", "true");
    }
};

// Load Sector Template
const carregarTemplateSetor = () => {
    const sector = document.getElementById("sector")?.value || "";
    const projectPlanDiv = document.getElementById("projectPlan");
    if (!projectPlanDiv || !sectorTemplates[sector]) return;

    projectPlanDiv.innerHTML = "";
    const template = sectorTemplates[sector] || { macroetapas: [] };
    template.macroetapas.forEach((macroetapa, macroIndex) => {
        const html = `
            <div class="macroetapa-form" data-macro="${macroIndex}">
                <div class="macroetapa-title">${macroetapa.name || `Macroetapa ${macroIndex + 1}`}</div>
                <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                    <input type="text" id="responsible-${macroIndex}" name="responsible" placeholder="Responsável" style="flex: 1;">
                    <label for="responsible-${macroIndex}" style="margin: 0;">Responsável</label>
                </div>
                <div class="etapas-container" data-macro="${macroIndex}">
                    ${macroetapa.etapas.map((etapa, etapaIndex) => `
                        <div class="etapa-form">
                            <input type="text" value="${etapa || ''}" data-macro="${macroIndex}" data-etapa="${etapaIndex}">
                            <button type="button" class="btn btn-small btn-remove" onclick="removerEtapa(${macroIndex}, ${etapaIndex})" aria-label="Remover etapa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `).join('')}
                    <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapa(${macroIndex})" aria-label="Adicionar etapa">
                        <i class="fas fa-plus"></i> Adicionar Etapa
                    </button>
                    <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapa(${macroIndex})" aria-label="Excluir macroetapa">
                        <i class="fas fa-trash"></i> Excluir Macroetapa
                    </button>
                </div>
            </div>
        `;
        projectPlanDiv.innerHTML += html;
    });
};

const carregarTemplateSetorEdit = () => {
    const sector = document.getElementById("editSector")?.value || "";
    const projectPlanDiv = document.getElementById("editProjectPlan");
    if (!projectPlanDiv || !sectorTemplates[sector]) return;

    projectPlanDiv.innerHTML = "";
    const template = sectorTemplates[sector] || { macroetapas: [] };
    template.macroetapas.forEach((macroetapa, macroIndex) => {
        const html = `
            <div class="macroetapa-form" data-macro="${macroIndex}">
                <div class="macroetapa-title">${macroetapa.name || `Macroetapa ${macroIndex + 1}`}</div>
                <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                    <input type="text" id="responsible-edit-${macroIndex}" name="responsible" placeholder="Responsável" style="flex: 1;">
                    <label for="responsible-edit-${macroIndex}" style="margin: 0;">Responsável</label>
                </div>
                <div class="etapas-container" data-macro="${macroIndex}">
                    ${macroetapa.etapas.map((etapa, etapaIndex) => `
                        <div class="etapa-form">
                            <input type="text" value="${etapa || ''}" data-macro="${macroIndex}" data-etapa="${etapaIndex}">
                            <button type="button" class="btn btn-small btn-remove" onclick="removerEtapaEdit(${macroIndex}, ${etapaIndex})" aria-label="Remover etapa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `).join('')}
                    <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapaEdit(${macroIndex})" aria-label="Adicionar etapa">
                        <i class="fas fa-plus"></i> Adicionar Etapa
                    </button>
                    <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapaEdit(${macroIndex})" aria-label="Excluir macroetapa">
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
    if (!projectPlanDiv) return;
    const macroIndex = projectPlanDiv.querySelectorAll(".macroetapa-form").length;
    const html = `
        <div class="macroetapa-form" data-macro="${macroIndex}">
            <div class="macroetapa-title"><input type="text" placeholder="Novo nome da macroetapa" class="macro-title-input" data-macro="${macroIndex}"></div>
            <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                <input type="text" id="responsible-${macroIndex}" name="responsible" placeholder="Responsável" style="flex: 1;">
                <label for="responsible-${macroIndex}" style="margin: 0;">Responsável</label>
            </div>
            <div class="etapas-container" data-macro="${macroIndex}">
                <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapa(${macroIndex})" aria-label="Adicionar etapa">
                    <i class="fas fa-plus"></i> Adicionar Etapa
                </button>
                <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapa(${macroIndex})" aria-label="Excluir macroetapa">
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
    if (!projectPlanDiv) return;
    const macroIndex = projectPlanDiv.querySelectorAll(".macroetapa-form").length;
    const html = `
        <div class="macroetapa-form" data-macro="${macroIndex}">
            <div class="macroetapa-title"><input type="text" placeholder="Novo nome da macroetapa" class="macro-title-input" data-macro="${macroIndex}"></div>
            <div class="responsible-field" style="display: flex; align-items: center; gap: 10px;">
                <input type="text" id="responsible-edit-${macroIndex}" name="responsible" placeholder="Responsável" style="flex: 1;">
                <label for="responsible-edit-${macroIndex}" style="margin: 0;">Responsável</label>
            </div>
            <div class="etapas-container" data-macro="${macroIndex}">
                <button type="button" class="btn btn-small btn-add" onclick="adicionarEtapaEdit(${macroIndex})" aria-label="Adicionar etapa">
                    <i class="fas fa-plus"></i> Adicionar Etapa
                </button>
                <button type="button" class="btn btn-delete-macro" onclick="removerMacroetapaEdit(${macroIndex})" aria-label="Excluir macroetapa">
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
    const container = document.querySelector(`#projectPlan .etapas-container[data-macro="${macroIndex}"]`);
    if (!container) return;
    const etapas = container.querySelectorAll(".etapa-form");
    const html = `
        <div class="etapa-form">
            <input type="text" placeholder="Nova etapa" data-macro="${macroIndex}" data-etapa="${etapas.length}">
            <button type="button" class="btn btn-small btn-remove" onclick="removerEtapa(${macroIndex}, ${etapas.length})" aria-label="Remover etapa">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.querySelector(".btn-add").insertAdjacentHTML("beforebegin", html);
};

const adicionarEtapaEdit = (macroIndex) => {
    const container = document.querySelector(`#editProjectPlan .etapas-container[data-macro="${macroIndex}"]`);
    if (!container) return;
    const etapas = container.querySelectorAll(".etapa-form");
    const html = `
        <div class="etapa-form">
            <input type="text" placeholder="Nova etapa" data-macro="${macroIndex}" data-etapa="${etapas.length}">
            <button type="button" class="btn btn-small btn-remove" onclick="removerEtapaEdit(${macroIndex}, ${etapas.length})" aria-label="Remover etapa">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.querySelector(".btn-add").insertAdjacentHTML("beforebegin", html);
};

const removerEtapa = (macroIndex, etapaIndex) => {
    const etapaForm = document.querySelector(`#projectPlan input[data-macro="${macroIndex}"][data-etapa="${etapaIndex}"]`)?.closest(".etapa-form");
    if (etapaForm) etapaForm.remove();
};

const removerEtapaEdit = (macroIndex, etapaIndex) => {
    const etapaForm = document.querySelector(`#editProjectPlan input[data-macro="${macroIndex}"][data-etapa="${etapaIndex}"]`)?.closest(".etapa-form");
    if (etapaForm) etapaForm.remove();
};

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
    if (!projectPlanDiv) return;
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
const excluirOportunidade = async (opportunityId) => {
    console.log("Tentando excluir ID:", opportunityId, " (tipo:", typeof opportunityId, ")");
    console.log("Opportunities atuais:", opportunities.map(opp => ({ id: opp.id, title: opp.title, type: typeof opp.id })));

    const opportunity = opportunities.find(opp => Number(opp.id) === Number(opportunityId));
    console.log("Oportunidade encontrada:", opportunity);

    if (!opportunity) {
        console.error("Oportunidade não encontrada para ID:", opportunityId);
        alert("Oportunidade não encontrada. Verifique os dados ou recarregue a página.");
        return;
    }
    if (!confirm(`Tem certeza que deseja excluir "${opportunity.title}"?`)) {
        console.log("Exclusão cancelada pelo usuário.");
        return;
    }

    try {
        console.log("Enviando requisição para excluir ID:", opportunityId);
        const response = await fetch('./excluir_planejamento.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: opportunityId })
        });

        const text = await response.text();
        console.log("Resposta recebida:", text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Resposta não é JSON:', text);
            throw new Error('Resposta inválida do servidor');
        }

        if (data.success) {
            opportunities = opportunities.filter(opp => Number(opp.id) !== Number(opportunityId));
            atualizarMetasSetores();
            atualizarPainel();
            atualizarSetor(opportunity.sector);
            alert("Oportunidade excluída com sucesso!");
        } else {
            alert(data.message || "Erro ao excluir oportunidade.");
        }
    } catch (error) {
        console.error("Erro ao excluir oportunidade:", error);
        alert("Falha ao excluir oportunidade. Tente novamente.");
    }
};

// const atualizarStatusEtapa = async (opportunityId, macroIndex, etapaIndex) => {
//     const opportunity = opportunities.find(opp => Number(opp.id) === Number(opportunityId));
//     if (!opportunity) {
//         console.error("Oportunidade não encontrada para o ID:", opportunityId);
//         alert("Oportunidade não encontrada. Recarregue a página.");
//         return;
//     }

//     const macro = opportunity.projectPlan[macroIndex];
//     const etapa = macro.etapas[etapaIndex];
//     if (!etapa) {
//         console.error("Etapa não encontrada para os índices:", { opportunityId, macroIndex, etapaIndex });
//         return;
//     }

//     const newCompleted = !etapa.completed;
//     etapa.completed = newCompleted;

//     console.log("Atualizando etapa - Antes da requisição:", {
//         opportunityId,
//         macroIndex,
//         etapaIndex,
//         etapaNome: etapa.name,
//         completed: newCompleted,
//         setor: opportunity.sector
//     });

//     try {
//         const response = await fetch('./atualizar_planejamento.php', {
//             method: 'POST',
//             headers: { 'Content-Type': 'application/json' },
//             body: JSON.stringify({
//                 planejamento_id: opportunityId,
//                 setor: opportunity.sector || '',
//                 nome_macroetapa: macro.name || `Macroetapa ${macroIndex + 1}`,
//                 responsavel: macro.responsible || '',
//                 etapa_nome: etapa.name,
//                 etapa_concluida: newCompleted ? 'sim' : 'nao',
//                 data_conclusao: newCompleted ? new Date().toISOString() : null
//             })
//         });

//         if (!response.ok) {
//             throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`);
//         }

//         const text = await response.text();
//         let data;
//         try {
//             data = JSON.parse(text);
//         } catch (parseError) {
//             console.error('Resposta do backend não é JSON válido:', text);
//             throw new Error('Resposta inválida do servidor');
//         }

//         if (!data.success) {
//             throw new Error(data.message || "Falha ao atualizar etapa no servidor.");
//         }

//         console.log("Resposta do backend:", data);

//         if (data.updated_etapa) {
//             etapa.completed = data.updated_etapa.etapa_concluida === 'sim';
//             etapa.data_conclusao = data.updated_etapa.data_conclusao;
//             console.log("Etapa sincronizada com backend:", {
//                 completed: etapa.completed,
//                 data_conclusao: etapa.data_conclusao
//             });
//         } else {
//             console.warn("Nenhum updated_etapa retornado pelo backend.");
//         }

//         if (typeof atualizarSetor === 'function') {
//             await atualizarSetor(opportunity.sector);
//         }
//         if (typeof atualizarPainel === 'function') {
//             await atualizarPainel();
//         }

//         console.log("Etapa atualizada com sucesso:", { opportunityId, etapaNome: etapa.name, completed: etapa.completed });
//         alert("Etapa atualizada com sucesso!");
//     } catch (error) {
//         console.error("Erro ao atualizar etapa:", error.message);
//         etapa.completed = !newCompleted;
//         if (typeof atualizarSetor === 'function') {
//             await atualizarSetor(opportunity.sector);
//         }
//         alert(`Falha ao atualizar etapa: ${error.message}. Tente novamente.`);
//     }
// };

// const atualizarStatusOportunidade = async (opportunityId, newStatus) => {
//     const opportunity = opportunities.find(opp => Number(opp.id) === Number(opportunityId));
//     if (!opportunity) {
//         console.error("Oportunidade não encontrada para ID:", opportunityId);
//         alert("Oportunidade não encontrada.");
//         return;
//     }

//     const previousStatus = opportunity.status;
//     opportunity.status = newStatus;

//     try {
//         const response = await fetch('./atualizar_planejamento.php', {
//             method: 'PUT',
//             headers: { 'Content-Type': 'application/json' },
//             body: JSON.stringify({ id: opportunityId, status: newStatus })
//         });

//         const text = await response.text();
//         let data;
//         try {
//             data = JSON.parse(text);
//         } catch (e) {
//             console.error('Resposta não é JSON:', text);
//             throw new Error('Resposta inválida do servidor');
//         }

//         if (data.success) {
//             atualizarMetasSetores();
//             atualizarPainel();
//             atualizarSetor(opportunity.sector);
//             console.log(`Status da oportunidade ${opportunityId} atualizado para ${newStatus}`);
//         } else {
//             opportunity.status = previousStatus;
//             alert(data.message || "Erro ao atualizar status.");
//             atualizarSetor(opportunity.sector);
//         }
//     } catch (error) {
//         console.error("Erro ao atualizar status:", error);
//         opportunity.status = previousStatus;
//         alert("Falha ao atualizar status. Tente novamente.");
//         atualizarSetor(opportunity.sector);
//     }
// };

// Form Handling


document.getElementById("opportunityForm")?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const projectPlan = [];

    document.querySelectorAll("#projectPlan .macroetapa-form").forEach((macroDiv, macroIndex) => {
        const macroTitleInput = macroDiv.querySelector(".macro-title-input");
        const macroTitle = macroTitleInput ? macroTitleInput.value.trim() : macroDiv.querySelector(".macroetapa-title")?.textContent?.trim() || `Macroetapa ${macroIndex + 1}`;
        const responsibleInput = macroDiv.querySelector(`#responsible-${macroIndex}`);
        const responsible = responsibleInput ? responsibleInput.value.trim() : "";
        const etapas = Array.from(macroDiv.querySelectorAll(".etapa-form input"))
            .filter(input => input.value.trim())
            .map(input => ({ name: input.value.trim(), completed: false }));
        if (etapas.length > 0 || macroTitle) {
            projectPlan.push({ name: macroTitle, responsible, etapas, expanded: true });
            etapas.forEach(etapa => {
                enviarNotificacao(responsible, formData.get("sector"), etapa.name);
            });
        }
    });

    let valueInput = formData.get("value")?.trim() || "";
    let value = 0;
    if (valueInput) {
        valueInput = valueInput.replace(/[^\d,.]/g, '').replace(/\.(?=.*\.)/g, '').replace(',', '.');
        value = parseFloat(valueInput) || 0;
        if (isNaN(value)) {
            alert("Valor inválido. Insira um número válido (ex.: 1000,00 ou 1000.00).");
            return;
        }
    }

    const opportunity = {
        id: null,
        title: formData.get("title")?.trim() || "",
        sector: formData.get("sector")?.trim() || "",
        value: value,
        deadline: formData.get("deadline") || null,
        status: formData.get("status") || "planejamento",
        description: formData.get("description")?.trim() || "",
        projectPlan,
        createdAt: new Date().toISOString(),
    };

    console.log("Dados enviados para o backend:", opportunity);

    try {
        const response = await fetch('./cadastrar_planejamento.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(opportunity)
        });
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Resposta não é JSON:', text);
            throw new Error('Resposta inválida do servidor: ' + text);
        }

        if (data.success) {
            opportunities.push({ ...opportunity, id: data.id, value: value });
            atualizarMetasSetores();
            atualizarPainel();
            atualizarTodosSetores();
            fecharModal();
            if (opportunity.sector && opportunity.sector !== "dashboard") exibirAba(opportunity.sector);
            alert("Oportunidade criada com sucesso!");
        } else {
            console.error("Erro retornado pelo servidor:", data.message);
            alert(data.message || "Erro ao salvar oportunidade.");
        }
    } catch (error) {
        console.error("Erro ao salvar oportunidade:", error);
        alert("Falha ao salvar oportunidade: " + error.message);
    }
});

const carregarDados = async () => {
    try {
        const response = await fetch('./cadastrar_planejamento.php', {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        });
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const text = await response.text();
        console.log("Resposta bruta do backend (carregarDados):", text);
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Resposta não é JSON:', text);
            throw new Error('Resposta inválida do servidor');
        }

        if (data.success && Array.isArray(data.opportunities)) {
            opportunities = await Promise.all(data.opportunities.map(async opp => {
                let parsedValue = 0;
                if (opp.value !== undefined && opp.value !== null) {
                    if (typeof opp.value === 'string' && opp.value.trim()) {
                        parsedValue = parseFloat(opp.value.replace(/\./g, '').replace(',', '.')) || 0;
                    } else if (typeof opp.value === 'number') {
                        parsedValue = opp.value;
                    }
                }

                // Fetch macroetapas for this opportunity
                const macroResponse = await fetch(`./get_macroetapas_by_opportunity.php?planejamento_id=${opp.id}&setor=${encodeURIComponent(opp.sector)}`);
                const macroText = await macroResponse.text();
                let macroData;
                try {
                    macroData = JSON.parse(macroText);
                } catch (e) {
                    console.error(`Resposta não é JSON para oportunidade ID ${opp.id}:`, macroText);
                    macroData = { success: false, macroetapas: [] };
                }

         let projectPlan = [];
                if (macroData.success && Array.isArray(macroData.macroetapas) && macroData.macroetapas.length > 0) {
                    projectPlan = macroData.macroetapas.map(macro => ({
                        name: macro.nome_macroetapa || `Macroetapa`,
                        responsible: macro.responsavel || '',
                        etapas: Array.isArray(macro.etapas) ? macro.etapas.map(e => ({
                            name: e.etapa_nome || '',
                            completed: e.etapa_concluida === 'sim',
                            data_conclusao: e.data_conclusao || null
                        })) : [],
                        expanded: true
                    }));
                } else {
                    let fallbackPlan = opp.projectPlan;
                    if (typeof fallbackPlan === 'string') {
                        try {
                            fallbackPlan = JSON.parse(fallbackPlan);
                        } catch (parseError) {
                            console.error(`Erro ao parsear projectPlan para oportunidade ID ${opp.id}:`, parseError);
                            fallbackPlan = []; // Fallback to empty array on parse failure
                        }
                    }
                    projectPlan = Array.isArray(fallbackPlan) ? fallbackPlan.map(macro => ({
                        ...macro,
                        etapas: Array.isArray(macro.etapas) ? macro.etapas.map(e => ({
                            name: e.name || '',
                            completed: e.completed || false, // Adjust based on your data (was e.etapa_concluida in original)
                            data_conclusao: e.data_conclusao || null
                        })) : [],
                        expanded: macro.expanded !== undefined ? macro.expanded : true
                    })) : [];
                }

                console.log(`Oportunidade ID ${opp.id} mapeada:`, { ...opp, value: parsedValue, projectPlan });
                return {
                    ...opp,
                    value: parsedValue,
                    projectPlan
                };
            }));

            console.log("Opportunities carregadas após mapeamento:", opportunities);
            atualizarMetasSetores();
            atualizarPainel();
            atualizarTodosSetores();
        } else {
            throw new Error(data.message || "Resposta inválida do backend");
        }
    } catch (error) {
        console.error("Erro ao carregar dados:", error);
        alert(`Falha ao carregar dados: ${error.message}. Verifique a conexão com o servidor ou tente novamente.`);
    }
};

document.getElementById("editForm")?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const id = document.getElementById("editId").value;
    const opportunity = opportunities.find(opp => opp.id == id);
    if (!opportunity) return;

    opportunity.title = document.getElementById("editTitle").value.trim() || "";
    opportunity.sector = document.getElementById("editSector").value.trim() || "";
    let valueInput = document.getElementById("editValue").value.trim() || "";
    opportunity.value = valueInput ? parseFloat(valueInput.replace(/[^\d,.]/g, '').replace(/\.(?=.*\.)/g, '').replace(',', '.')) || 0 : 0;
    opportunity.deadline = document.getElementById("editDeadline").value || null;
    opportunity.status = document.getElementById("editStatus").value || "planejamento";
    opportunity.description = document.getElementById("editDescription").value.trim() || "";

    const projectPlan = [];
    document.querySelectorAll("#editProjectPlan .macroetapa-form").forEach((macroDiv, macroIndex) => {
        const macroTitleInput = macroDiv.querySelector(".macro-title-input");
        const macroTitle = macroTitleInput ? macroTitleInput.value.trim() : macroDiv.querySelector(".macroetapa-title")?.textContent?.trim() || `Macroetapa ${macroIndex + 1}`;
        const responsibleInput = macroDiv.querySelector(`#responsible-edit-${macroIndex}`);
        const responsible = responsibleInput ? responsibleInput.value.trim() : "";
        const etapas = Array.from(macroDiv.querySelectorAll(".etapa-form input"))
            .filter(input => input.value.trim())
            .map(input => ({ name: input.value.trim(), completed: false }));
        if (etapas.length > 0 || macroTitle) {
            projectPlan.push({ name: macroTitle, responsible, etapas, expanded: true });
        }
    });
    opportunity.projectPlan = projectPlan;

    try {
        const response = await fetch('./cadastrar_planejamento.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(opportunity)
        });
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Resposta não é JSON:', text);
            throw new Error('Resposta inválida do servidor');
        }

        if (data.success) {
            const index = opportunities.findIndex(opp => opp.id === opportunity.id);
            if (index !== -1) opportunities[index] = { ...opportunity };
            atualizarMetasSetores();
            atualizarPainel();
            atualizarTodosSetores();
            fecharEditModal();
            exibirAba(opportunity.sector);
            alert("Oportunidade atualizada com sucesso!");
        } else {
            alert(data.message || "Erro ao atualizar oportunidade.");
        }
    } catch (error) {
        console.error("Erro ao atualizar oportunidade:", error);
        alert("Falha ao atualizar oportunidade. Tente novamente.");
    }
});

const enviarNotificacao = async (username, setor, mensagem) => {
    const notification = {
        id: Date.now(),
        username: username || "N/A",
        setor: setor || "",
        mensagem: mensagem || "",
        situacao: "nao lida",
        data_criacao: new Date().toISOString(),
        certidao_id: null
    };
    try {
        const response = await fetch('./save_notification.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(notification)
        });
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Resposta não é JSON:', text);
            throw new Error('Resposta inválida do servidor');
        }
        console.log("Notificação salva:", data);
    } catch (error) {
        console.error("Erro ao salvar notificação:", error);
    }
};

// Dashboard and Visualization
const atualizarPainel = () => {
    const filteredOpps = filteredSector ? opportunities.filter(opp => opp.sector === filteredSector) : opportunities;
    const totalValue = filteredOpps.reduce((sum, opp) => opp.status !== "finalizado" ? sum + (parseFloat(opp.value) || 0) : sum, 0);
    const totalOpportunities = filteredOpps.length;
    const completedOpportunities = filteredOpps.filter(opp => opp.status === "finalizado").length;
    const conversionRate = totalOpportunities > 0 ? (completedOpportunities / totalOpportunities) * 100 : 0;
    const totalGoal = filteredSector ? sectorGoals[filteredSector] : Object.values(sectorGoals).reduce((sum, goal) => sum + (parseFloat(goal) || 0), 0);
    const completedValue = filteredOpps.filter(opp => opp.status === "finalizado").reduce((sum, opp) => sum + (parseFloat(opp.value) || 0), 0);
    const goalProgress = totalGoal > 0 ? (completedValue / totalGoal) * 100 : 0;

    const updateElement = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    };

    updateElement("total-value", formatarMoeda(totalValue || 0));
    updateElement("total-opportunities", totalOpportunities || 0);
    updateElement("conversion-rate", conversionRate.toFixed(1) + "%");
    updateElement("goal-progress", goalProgress.toFixed(1) + "%");

    atualizarGraficos();
};

const atualizarGraficos = () => {
    if (typeof Chart === 'undefined') {
        console.error("Chart.js não está carregado.");
        return;
    }
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
        const achieved = sectorOpportunities.reduce((sum, opp) => sum + (parseFloat(opp.value) || 0), 0);
        return { sector: sector.charAt(0).toUpperCase() + sector.slice(1).replace('_', ' '), achieved, goal: sectorGoals[sector] };
    });

    performanceChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: sectorData.map(d => d.sector),
            datasets: [
                { label: "Realizado", data: sectorData.map(d => d.achieved), backgroundColor: "rgba(76, 175, 80, 0.8)", borderColor: "rgba(76, 175, 80, 1)", borderWidth: 1 },
                { label: "Meta", data: sectorData.map(d => parseFloat(d.goal) || 0), backgroundColor: "rgba(102, 126, 234, 0.8)", borderColor: "rgba(102, 126, 234, 1)", borderWidth: 1 },
            ],
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { callback: value => "R$ " + (value / 1000) + "k" } } },
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
                        atualizarGraficoDistribuicao();
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
    const currentYear = new Date().getFullYear(); // 2025

    const monthlyData = months.reduce((acc, month, index) => {
        const monthKey = `${currentYear}-${String(index + 1).padStart(2, '0')}`;
        acc[monthKey] = { total: 0, completed: 0 };
        return acc;
    }, {});

    opportunities.forEach(opp => {
        if (!opp.createdAt) {
            console.warn(`Oportunidade sem created_at:`, opp);
            return;
        }
        const createdDate = new Date(opp.createdAt);
        if (isNaN(createdDate.getTime())) {
            console.warn(`Data inválida em created_at para oportunidade ID ${opp.id}: ${opp.createdAt}`);
            return;
        }
        if (createdDate.getFullYear() === currentYear) {
            const monthKey = `${currentYear}-${String(createdDate.getMonth() + 1).padStart(2, '0')}`;
            monthlyData[monthKey].total++;
            if (opp.status === "finalizado") {
                monthlyData[monthKey].completed++;
            }
        }
    });

    const totalData = months.map((_, index) => monthlyData[`${currentYear}-${String(index + 1).padStart(2, '0')}`].total);
    const completedData = months.map((_, index) => monthlyData[`${currentYear}-${String(index + 1).padStart(2, '0')}`].completed);

    if (evolutionChart) evolutionChart.destroy();
    evolutionChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: months,
            datasets: [
                { 
                    label: "Total de Oportunidades", 
                    data: totalData, 
                    borderColor: "rgba(54, 162, 235, 1)", 
                    backgroundColor: "rgba(54, 162, 235, 0.2)", 
                    fill: false, 
                    tension: 0.1, 
                    borderWidth: 2, 
                    pointRadius: 4 
                },
                { 
                    label: "Oportunidades Concluídas", 
                    data: completedData, 
                    borderColor: "rgba(75, 192, 192, 1)", 
                    backgroundColor: "rgba(75, 192, 192, 0.2)", 
                    fill: false, 
                    tension: 0.1, 
                    borderWidth: 2, 
                    pointRadius: 4 
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { 
                x: { title: { display: true, text: "Mês" }, position: "bottom" }, 
                y: { beginAtZero: true, title: { display: true, text: "Quantidade" } } 
            },
            plugins: { 
                legend: { position: "top" }, 
                tooltip: { callbacks: { label: context => `${context.dataset.label}: ${context.parsed.y}` } } 
            },
        },
    });
};

const atualizarAlertas = () => {
    const alertsContainer = document.getElementById("alerts-container");
    if (!alertsContainer) return;

    const currentDate = new Date("2025-07-29T17:23:00-03:00"); // 02:23 PM -03
    alertsContainer.innerHTML = "";

    const expiredOpps = opportunities.filter(opp => {
        const deadline = new Date(opp.deadline || "");
        return opp.status !== "finalizado" && !isNaN(deadline.getTime()) && deadline < currentDate && opp.deadline;
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
                    <button onclick="this.closest('.alert-card').style.opacity='0';setTimeout(() => this.closest('.alert-card').remove(),300)" style="background:none;border:none;color:#fff;cursor:pointer;font-size:16px;" aria-label="Fechar alerta">
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

const atualizarTodosSetores = () => Object.keys(sectorGoals).forEach(atualizarSetor);

// const atualizarPainelEstimativa = (setor) => {
//     const sectorOpportunities = opportunities.filter(opp => opp.sector === setor);
//     const totalEstimatedValue = sectorOpportunities.reduce((sum, opp) => opp.status !== "finalizado" ? sum + (parseFloat(opp.value) || 0) : sum, 0);
//     const completedValue = sectorOpportunities.filter(opp => opp.status === "finalizado").reduce((sum, opp) => sum + (parseFloat(opp.value) || 0), 0);
//     const goal = sectorGoals[setor] || 0;
//     const goalProgress = goal > 0 ? (completedValue / goal) * 100 : 0;

//     const updateElement = (id, value) => {
//         const element = document.getElementById(id);
//         if (element) element.textContent = value;
//     };

//     updateElement(`estimated-value-${setor}`, formatarMoeda(totalEstimatedValue));
//     updateElement(`goal-progress-${setor}`, goalProgress.toFixed(1) + "%");
// };

// Search and Sector Update
const buscarOportunidades = async (setor, termoPesquisa) => {
    try {
        const response = await fetch('./buscar_planejamento.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ setor, termo: termoPesquisa.trim() })
        });
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Resposta não é JSON:', text);
            throw new Error('Resposta inválida do servidor');
        }

        if (data.success && Array.isArray(data.opportunities)) {
            return data.opportunities.map(opp => {
                let parsedValue = 0;
                if (opp.value !== undefined && opp.value !== null) {
                    if (typeof opp.value === 'string' && opp.value.trim()) {
                        parsedValue = parseFloat(opp.value.replace(/\./g, '').replace(',', '.')) || 0;
                    } else if (typeof opp.value === 'number') {
                        parsedValue = opp.value;
                    }
                }
                return {
                    ...opp,
                    value: parsedValue,
                    projectPlan: opp.projectPlan ? opp.projectPlan.map(macro => ({
                        ...macro,
                        etapas: macro.etapas ? macro.etapas.map(e => ({
                            name: e.name || '',
                            completed: e.etapa_concluida === 'sim',
                            data_conclusao: e.data_conclusao || null
                        })) : [],
                        expanded: macro.expanded !== undefined ? macro.expanded : true
                    })) : []
                };
            });
        } else {
            throw new Error(data.message || 'Erro ao buscar oportunidades');
        }
    } catch (error) {
        console.error('Erro ao buscar oportunidades:', error);
        alert('Falha ao buscar oportunidades: ' + error.message);
        return [];
    }
};

const atualizarSetor = async (setor) => {
    const searchInput = document.getElementById(`search-${setor}`);
    const termoPesquisa = searchInput ? searchInput.value.trim() : '';

    let sectorOpportunities = opportunities.filter(opp => opp.sector === setor);
    if (termoPesquisa) {
        sectorOpportunities = await buscarOportunidades(setor, termoPesquisa);
    }

    atualizarPainelEstimativa(setor);
    renderizarOportunidades(setor, sectorOpportunities);
};

document.addEventListener("DOMContentLoaded", () => {
    Object.keys(sectorGoals).forEach(setor => {
        const searchInput = document.getElementById(`search-${setor}`);
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                atualizarSetor(setor);
            });
        }
    });
});

const toggleMacroetapa = (opportunityId, macroIndex) => {
    const opportunity = opportunities.find(opp => opp.id === opportunityId);
    if (!opportunity) return;
    opportunity.projectPlan[macroIndex].expanded = !opportunity.projectPlan[macroIndex].expanded;
    atualizarSetor(opportunity.sector);
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
        if (newSectorGoals[opp.sector] !== undefined) {
            newSectorGoals[opp.sector] += parseFloat(opp.value) || 0;
        }
    });
    Object.assign(sectorGoals, newSectorGoals);
};

// Rendering and Macroetapas
const atualizarMacroetapas = async () => {
    const macroetapasContainer = document.getElementById("macroetapas-container");
    if (!macroetapasContainer) return;

    macroetapasContainer.innerHTML = "<p style='color: #7f8c8d;'>Carregando macroetapas...</p>";

    try {
        const response = await fetch('./get_macroetapas.php');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();
        if (!data.success) throw new Error(data.message || "Falha ao carregar macroetapas.");

        macroetapasContainer.innerHTML = "";

        data.macroetapas.forEach(macro => {
            const totalEtapas = macro.etapas.length;
            const etapasConcluidas = macro.etapas.filter(e => e.etapa_concluida === "sim").length;
            const progresso = totalEtapas > 0 ? (etapasConcluidas / totalEtapas) * 100 : 0;

            const macroCard = document.createElement("div");
            macroCard.className = "macroetapa-card";
            macroCard.style.cssText = `
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 15px;
                background: #fff;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                width: 100%;
            `;

            macroCard.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                    <div>
                        <h4 style="font-size: 16px; font-weight: bold; color: #2c3e50; margin: 0;">${macro.nome_macroetapa}</h4>
                        <p style="font-size: 12px; color: #7f8c8d; margin: 5px 0;">Responsável: ${macro.responsavel || "N/A"}</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 12px; color: #7f8c8d;">Progresso: ${progresso.toFixed(1)}%</span>
                    </div>
                </div>
                <div style="margin-top: 10px;">
                    <div class="progress-bar-small" style="width: 100%; height: 6px; background: #ecf0f1; border-radius: 3px;">
                        <div class="progress-fill-small" style="height: 100%; width: ${progresso}%; background: #3498db; border-radius: 3px;"></div>
                    </div>
                    <div style="margin-top: 10px;">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            ${macro.etapas.map(etapa => `
                                <li style="display: flex; align-items: center; gap: 10px; margin-top: 5px; padding: 5px; background: ${etapa.etapa_concluida === 'sim' ? '#e8f5e9' : '#fffef7'}; border-radius: 4px;">
                                    <input type="checkbox" ${etapa.etapa_concluida === 'sim' ? 'checked' : ''} disabled style="margin: 0;">
                                    <span style="flex: 1;">${etapa.etapa_nome}</span>
                                  
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                </div>
            `;

            macroetapasContainer.appendChild(macroCard);
        });
    } catch (error) {
        console.error("Erro ao atualizar macroetapas:", error);
        macroetapasContainer.innerHTML = `<p style="color: #e74c3c;">Erro ao carregar macroetapas: ${error.message}</p>`;
    }
};

const renderizarOportunidades = (setor, filteredOpportunities = null) => {
    const opportunitiesGrid = document.getElementById(`opportunities-${setor}`);
    if (!opportunitiesGrid) {
        console.warn(`Elemento opportunities-${setor} não encontrado no DOM.`);
        return;
    }

    opportunitiesGrid.innerHTML = "";
    const sectorOpportunities = filteredOpportunities || opportunities.filter(opp => opp.sector === setor);

    if (sectorOpportunities.length === 0) {
        opportunitiesGrid.innerHTML = `<p style="color: #7f8c8d;">Nenhuma oportunidade encontrada para o setor ${setor}.</p>`;
        return;
    }

    const style = document.createElement("style");
    style.textContent = `
        .status-planejamento { background-color: #f39c12; color: white; }
        .status-andamento { background-color: #f1c40f; color: white; }
        .status-finalizado { background-color: #2ecc71; color: white; }
        .status-dropdown { 
            padding: 2px 8px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            font-size: 14px; 
            appearance: none; 
            -webkit-appearance: none; 
            -moz-appearance: none; 
            background-color: white; 
            cursor: pointer; 
            transition: all 0.2s ease;
        }
        .progress-bar-small, .macro-progress-bar { 
            width: 100%; 
            height: 6px; 
            background: #ecf0f1; 
            border-radius: 3px; 
        }
        .progress-fill-small, .macro-progress-fill { 
            height: 100%; 
            border-radius: 3px; 
            background: #3498db; 
        }
    `;
    document.head.appendChild(style);

    sectorOpportunities.forEach(opp => {
        if (!opp.id) {
            console.warn("Oportunidade sem ID encontrado:", opp);
            return;
        }
        const progress = opp.projectPlan.reduce((total, macro) => total + (macro.etapas.filter(e => e.completed).length / (macro.etapas.length || 1)), 0) / (opp.projectPlan.length || 1) * 100;
        const deadlineDate = new Date(opp.deadline || "");
        const currentDate = new Date("2025-07-29T17:23:00-03:00"); // 02:23 PM -03
        const isExpired = deadlineDate && deadlineDate < currentDate && opp.status !== "finalizado";
        const expiredMessage = isExpired ? `Vencido em: ${deadlineDate.toLocaleDateString("pt-BR")}` : "";

        const html = `
            <div class="opportunity-card" style="border:1px solid #ddd;border-radius:8px;padding:15px;background:#fff;width:100%;margin-bottom:15px;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
                <div class="opportunity-header" style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #eee;padding-bottom:10px;">
                    <div>
                        <div class="opportunity-title" style="font-size:18px;font-weight:bold;color:#2c3e50;margin-bottom:5px;">${opp.title || "Sem Título"}</div>
                        <div class="opportunity-meta" style="font-size:14px;color:#7f8c8d;display:flex;gap:10px;flex-wrap:wrap;">
                            <span>R$ ${formatarMoeda(opp.value || 0)}</span><span>|</span>
                            <span>${opp.deadline ? new Date(opp.deadline).toLocaleDateString("pt-BR") : "Sem Prazo"}</span><span>|</span>
                            <span class="opportunity-status status-${opp.status}" style="padding:2px 8px;border-radius:3px;">
                                ${opp.status === "planejamento" ? "Em Planejamento" : opp.status === "andamento" ? "Em Andamento" : "Finalizado"}
                            </span>
                            ${isExpired ? `<span style="color:#e74c3c;font-weight:bold;">${expiredMessage}</span>` : ""}
                            <select class="status-dropdown status-${opp.status}" onchange="atualizarStatusOportunidade(${opp.id}, this.value)" onblur="this.size = 1;" onfocus="this.size = 3;" aria-label="Alterar status">
                                <option value="planejamento" ${opp.status === "planejamento" ? "selected" : ""}>Em Planejamento</option>
                                <option value="andamento" ${opp.status === "andamento" ? "selected" : ""}>Em Andamento</option>
                                <option value="finalizado" ${opp.status === "finalizado" ? "selected" : ""}>Finalizado</option>
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;gap:5px;">
                        <button class="btn btn-edit" onclick="abrirEditModal(${opp.id})" style="background:#e67e22;color:#fff;display:flex;justify-content:center;align-items:center;padding:5px 10px;border:none;border-radius:4px;width:65px;height:29px;" aria-label="Editar oportunidade">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-delete" onclick="excluirOportunidade(${Number(opp.id)})" style="background:#e74c3c;color:#fff;display:flex;justify-content:center;align-items:center;padding:5px 10px;border:none;border-radius:4px;width:65px;height:29px;" aria-label="Excluir oportunidade">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </div>
                </div>
                <div class="opportunity-progress" style="margin-top:10px;">
                    <div class="progress-bar-small">
                        <div class="progress-fill-small" style="width:${progress}%;"></div>
                    </div>
                    <span style="display:block;font-size:12px;color:#7f8c8d;margin-top:5px;">Progresso: ${progress.toFixed(1)}%</span>
                </div>
                ${opp.projectPlan.map((macro, macroIndex) => {
                    const macroProgress = macro.etapas.length > 0 ? (macro.etapas.filter(e => e.completed).length / macro.etapas.length) * 100 : 0;
                    return `
                        <div class="macroetapa-header ${macro.expanded ? '' : 'collapsed'}" style="display:flex;align-items:center;gap:5px;cursor:pointer;font-weight:bold;color:#2c3e50;margin-top:10px;padding:5px;border-bottom:1px solid #eee;" onclick="toggleMacroetapa(${opp.id},${macroIndex})">
                            <i class="fas fa-chevron-down" style="transition:transform 0.3s;"></i> ${macro.name || `Macroetapa ${macroIndex + 1}`} (Responsável: ${macro.responsible || "N/A"})
                        </div>
                        <div class="macro-progress" style="margin-left:20px;margin-bottom:5px;">
                            <div class="macro-progress-bar">
                                <div class="macro-progress-fill" style="width:${macroProgress}%;"></div>
                            </div>
                            <span style="display:block;font-size:12px;color:#7f8c8d;">Progresso: ${macroProgress.toFixed(1)}%</span>
                        </div>
                        <div class="etapas-list" style="display:${macro.expanded ? 'block' : 'none'};margin-left:20px;margin-top:5px;">
                            ${macro.etapas.map((etapa, etapaIndex) => `
                                <div class="etapa-item" style="display:flex;align-items:center;gap:5px;margin-top:5px;">
                                    <input type="checkbox" ${etapa.completed ? "checked" : ""} onchange="atualizarStatusEtapa(${opp.id},${macroIndex},${etapaIndex})" aria-label="Marcar etapa como concluída">
                                    <span style="flex:1;">${etapa.name || ''}</span>
                                  
                                </div>
                            `).join('')}
                        </div>
                    `;
                }).join('')}
            </div>
        `;
        opportunitiesGrid.innerHTML += html;
    });
};