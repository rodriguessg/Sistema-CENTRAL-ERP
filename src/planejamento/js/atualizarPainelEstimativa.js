const atualizarPainelEstimativa = (setor) => {
    const sectorOpportunities = opportunities.filter(opp => opp.sector === setor);
    const totalEstimatedValue = sectorOpportunities.reduce((sum, opp) => opp.status !== "finalizado" ? sum + (parseFloat(opp.value) || 0) : sum, 0);

    // Contar total de etapas e etapas concluídas no setor
    let totalEtapas = 0;
    let completedEtapas = 0;
    sectorOpportunities.forEach(opp => {
        if (opp.projectPlan) {
            opp.projectPlan.forEach(macro => {
                if (macro.etapas) {
                    totalEtapas += macro.etapas.length;
                    completedEtapas += macro.etapas.filter(e => e.completed).length;
                }
            });
        }
    });

    // Calcular a porcentagem de progresso com base nas etapas concluídas
    const goalProgress = totalEtapas > 0 ? (completedEtapas / totalEtapas) * 100 : 0;

    const updateElement = (id, value) => {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    };

    updateElement(`estimated-value-${setor}`, formatarMoeda(totalEstimatedValue));
    updateElement(`goal-progress-${setor}`, goalProgress.toFixed(1) + "%");
};

// Função para atualizar o status de uma oportunidade e disparar a atualização do painel
const atualizarStatusOportunidade = async (opportunityId, newStatus) => {
    const opportunity = opportunities.find(opp => Number(opp.id) === Number(opportunityId));
    if (!opportunity) {
        console.error("Oportunidade não encontrada para ID:", opportunityId);
        alert("Oportunidade não encontrada.");
        return;
    }

    const previousStatus = opportunity.status;
    opportunity.status = newStatus;

    try {
        const response = await fetch('./atualizar_planejamento.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: opportunityId, status: newStatus })
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
            atualizarMetasSetores();
            atualizarPainel();
            atualizarPainelEstimativa(opportunity.sector);
            atualizarSetor(opportunity.sector);
            console.log(`Status da oportunidade ${opportunityId} atualizado para ${newStatus}`);
        } else {
            opportunity.status = previousStatus;
            alert(data.message || "Erro ao atualizar status.");
            atualizarSetor(opportunity.sector);
        }
    } catch (error) {
        console.error("Erro ao atualizar status:", error);
        opportunity.status = previousStatus;
        alert("Falha ao atualizar status. Tente novamente.");
        atualizarSetor(opportunity.sector);
    }
};

// Função para atualizar o status de uma etapa
const atualizarStatusEtapa = async (opportunityId, macroIndex, etapaIndex) => {
    const opportunity = opportunities.find(opp => Number(opp.id) === Number(opportunityId));
    if (!opportunity) {
        console.error("Oportunidade não encontrada para o ID:", opportunityId);
        alert("Oportunidade não encontrada. Recarregue a página.");
        return;
    }

    const macro = opportunity.projectPlan[macroIndex];
    const etapa = macro.etapas[etapaIndex];
    if (!etapa) {
        console.error("Etapa não encontrada para os índices:", { opportunityId, macroIndex, etapaIndex });
        return;
    }

    const newCompleted = !etapa.completed;
    etapa.completed = newCompleted;

    console.log("Atualizando etapa - Antes da requisição:", {
        opportunityId,
        macroIndex,
        etapaIndex,
        etapaNome: etapa.name,
        completed: newCompleted,
        setor: opportunity.sector
    });

    try {
        const response = await fetch('./atualizar_planejamento.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                planejamento_id: opportunityId,
                setor: opportunity.sector || '',
                nome_macroetapa: macro.name || `Macroetapa ${macroIndex + 1}`,
                responsavel: macro.responsible || '',
                etapa_nome: etapa.name,
                etapa_concluida: newCompleted ? 'sim' : 'nao',
                data_conclusao: newCompleted ? new Date().toISOString() : null
            })
        });

        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status} - ${response.statusText}`);
        }

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('Resposta do backend não é JSON válido:', text);
            throw new Error('Resposta inválida do servidor');
        }

        if (!data.success) {
            throw new Error(data.message || "Falha ao atualizar etapa no servidor.");
        }

        if (data.updated_etapa) {
            etapa.completed = data.updated_etapa.etapa_concluida === 'sim';
            etapa.data_conclusao = data.updated_etapa.data_conclusao;
            console.log("Etapa sincronizada com backend:", {
                completed: etapa.completed,
                data_conclusao: etapa.data_conclusao
            });
        } else {
            console.warn("Nenhum updated_etapa retornado pelo backend.");
        }

        // Verificar se todas as etapas estão concluídas
        const allEtapasCompleted = opportunity.projectPlan.every(macro =>
            macro.etapas.every(e => e.completed)
        );

        if (allEtapasCompleted && opportunity.status !== "finalizado") {
            await atualizarStatusOportunidade(opportunityId, "finalizado");
        } else if (!allEtapasCompleted && opportunity.status === "finalizado") {
            await atualizarStatusOportunidade(opportunityId, "andamento");
        }

        if (typeof atualizarSetor === 'function') {
            await atualizarSetor(opportunity.sector);
        }
        if (typeof atualizarPainel === 'function') {
            await atualizarPainel();
        }
        atualizarPainelEstimativa(opportunity.sector);

        console.log("Etapa atualizada com sucesso:", { opportunityId, etapaNome: etapa.name, completed: etapa.completed });
        alert("Etapa atualizada com sucesso!");
    } catch (error) {
        console.error("Erro ao atualizar etapa:", error.message);
        etapa.completed = !newCompleted;
        if (typeof atualizarSetor === 'function') {
            await atualizarSetor(opportunity.sector);
        }
        alert(`Falha ao atualizar etapa: ${error.message}. Tente novamente.`);
    }
};

// Função auxiliar para formatar moeda
const formatarMoeda = (valor) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
};