let currentContractId = null;
let steps = [];

// Função para exibir o fluxo de contratos
function exibirFluxoContratos() {
    const contractId = document.getElementById('contractSelect').value || '';
    if (!contractId) {
        document.getElementById('timeline').innerHTML = '<p class="text-center">Selecione um contrato para visualizar o fluxo.</p>';
        return;
    }

    currentContractId = contractId;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', `./get_fluxo_contrato.php?contract_id=${contractId}`, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                try {
                    const fluxos = JSON.parse(xhr.responseText);
                    const timeline = document.getElementById('timeline');
                    timeline.innerHTML = '';

                    if (fluxos.length === 0 || !fluxos[0]) {
                        timeline.innerHTML = '<p class="text-center">Nenhum contrato encontrado.</p>';
                        return;
                    }

                    const fluxo = fluxos[0];
                    
                    // CORREÇÃO: Remover duplicatas baseado no título da etapa
                    const uniqueSteps = [];
                    const seenTitles = new Set();
                    
                    fluxo.forEach((etapa, index) => {
                        const title = etapa.etapa.trim();
                        if (!seenTitles.has(title)) {
                            seenTitles.add(title);
                            uniqueSteps.push(etapa);
                        }
                    });

                    steps = uniqueSteps.map((etapa, index) => ({
                        id: index + 1,
                        contract_id: etapa.contract_id,
                        title: etapa.etapa,
                        description: etapa.descricao,
                        date: etapa.data,
                        time: etapa.hora,
                        status: etapa.status,
                        completed: etapa.status === 'Completo'
                    }));

                    renderTimeline();
                } catch (error) {
                    console.error('Erro ao processar resposta:', error);
                    document.getElementById('timeline').innerHTML = '<p class="text-center">Erro ao carregar fluxo.</p>';
                }
            } else {
                console.error('Erro na requisição AJAX:', xhr.status, xhr.statusText);
                document.getElementById('timeline').innerHTML = '<p class="text-center">Erro ao carregar fluxo. Status: ' + xhr.status + '</p>';
            }
        }
    };
    xhr.onerror = function() {
        console.error('Erro de rede na requisição AJAX');
        document.getElementById('timeline').innerHTML = '<p class="text-center">Erro de rede ao carregar fluxo.</p>';
    };
    xhr.send();
}

// Função para renderizar a timeline no novo formato
function renderTimeline() {
    const timeline = document.getElementById('timeline');
    timeline.innerHTML = '';

    if (!steps || steps.length === 0) {
        timeline.innerHTML = '<p class="text-center">Nenhuma etapa disponível.</p>';
        return;
    }

    // Criar o container da timeline
    const timelineContainer = document.createElement('div');
    timelineContainer.className = 'timeline-container';

    steps.forEach((step, index) => {
        if (!step || !step.id) return;

        // CORREÇÃO: Lógica mais rigorosa para determinar o status
        let iconStatus = 'pending';
        let iconClass = 'bi-hourglass';
        let displayStatus = step.status;
        
        // Verificar se realmente está completo baseado em critérios mais rigorosos
        if (step.status === 'Completo' && step.completed) {
            iconStatus = 'completed';
            iconClass = 'bi-check-lg';
            displayStatus = 'Completo';
        } else if (step.status === 'Em Andamento' || step.status === 'Em Processo de Pagamento') {
            iconStatus = 'in-progress';
            iconClass = 'bi-clock';
            displayStatus = 'Em Andamento';
        } else {
            iconStatus = 'pending';
            iconClass = 'bi-hourglass';
            displayStatus = 'Pendente';
        }

        // Criar o item da timeline
        const timelineItem = document.createElement('div');
        timelineItem.className = 'timeline-item';
        timelineItem.dataset.stepId = step.id;
        
        // Montar o HTML do item
        timelineItem.innerHTML = `
            <div class="timeline-icon ${iconStatus}">
                <i class="bi ${iconClass}"></i>
            </div>
            <div class="timeline-content">
                <div class="timeline-header" onclick="toggleExpand(this)">
                    <h3 class="timeline-title">${step.title}</h3>
                    <div class="timeline-date">
                        <span class="status-badge ${iconStatus} me-2">${displayStatus}</span>
                        <i class="bi bi-calendar me-1"></i> ${step.date}
                    </div>
                </div>
                <div class="timeline-body">
                    <div class="timeline-body-content">
                        <p class="timeline-description">${step.description}</p>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <small class="text-muted">Data</small>
                                    <div>${step.date}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <small class="text-muted">Hora</small>
                                    <div>${step.time}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-details">
                            <h6 class="mb-2">Ações:</h6>
                            <div class="checklist-item">
                                <input class="step-checkbox" type="checkbox" id="step-${step.id}" 
                                    data-step-id="${step.id}" ${step.completed ? 'checked' : ''}>
                                <label for="step-${step.id}">Marcar como concluído</label>
                            </div>
                        </div>
                        
                        <div class="timeline-actions">
                            <div>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-text me-1"></i> Ver Documentos
                                </button>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-secondary move-up-btn" 
                                    onclick="moveStepUp(${step.id})" ${index === 0 ? 'disabled' : ''}>
                                    <i class="bi bi-arrow-up"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary move-down-btn" 
                                    onclick="moveStepDown(${step.id})" ${index === steps.length - 1 ? 'disabled' : ''}>
                                    <i class="bi bi-arrow-down"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="removeStep(${step.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        timelineContainer.appendChild(timelineItem);
    });

    timeline.appendChild(timelineContainer);

    // Adicionar event listeners para os checkboxes
    document.querySelectorAll('.step-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function(e) {
            const stepId = parseInt(this.dataset.stepId);
            const step = steps.find(s => s.id === stepId);
            if (step) {
                step.completed = this.checked;
                step.status = step.completed ? 'Completo' : 'Em Andamento';
                
                // CORREÇÃO: Salvar no servidor e depois atualizar o visual
                saveStepStatus(step).then(() => {
                    updateStepVisual(stepId, step);
                    updateProgress();
                });
            }
        });
    });

    // CORREÇÃO: Verificar etapas de pagamentos de forma mais específica
    steps.forEach(step => {
        if (step.title.toLowerCase().includes('pagamento')) {
            addPaymentDetails(step);
        }
    });

    // Atualizar a barra de progresso
    updateProgress();
}

// NOVA FUNÇÃO: Atualizar visual de uma etapa específica
function updateStepVisual(stepId, step) {
    const timelineItem = document.querySelector(`.timeline-item[data-step-id="${stepId}"]`);
    if (!timelineItem) return;
    
    const icon = timelineItem.querySelector('.timeline-icon');
    const iconI = icon.querySelector('i');
    const statusBadge = timelineItem.querySelector('.status-badge');
    
    if (step.completed) {
        icon.className = 'timeline-icon completed';
        iconI.className = 'bi bi-check-lg';
        statusBadge.className = 'status-badge completed me-2';
        statusBadge.textContent = 'Completo';
    } else {
        icon.className = 'timeline-icon in-progress';
        iconI.className = 'bi bi-clock';
        statusBadge.className = 'status-badge in-progress me-2';
        statusBadge.textContent = 'Em Andamento';
    }
}

// CORREÇÃO: Função para adicionar detalhes de pagamento
function addPaymentDetails(step) {
    const timelineItem = document.querySelector(`.timeline-item[data-step-id="${step.id}"]`);
    if (!timelineItem) return;
    
    const timelineDetails = timelineItem.querySelector('.timeline-details');
    if (!timelineDetails) return;
    
    // Buscar os dados de pagamento
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `./get_pagamentos.php?contract_id=${step.contract_id}`, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                const pagamentos = JSON.parse(xhr.responseText);
                if (pagamentos.length > 0) {
                    const pagamento = pagamentos[0];
                    
                    // Limpar conteúdo anterior e adicionar título
                    timelineDetails.innerHTML = '<h6 class="mb-3">Detalhes do Processo:</h6>';
                    
                    // Adicionar os detalhes de pagamento
                    const detailsContent = document.createElement('div');
                    detailsContent.className = 'row mb-3';
                    detailsContent.innerHTML = `
                        <div class="col-md-6">
                            <div class="mb-2">
                                <small class="text-muted">Empenho</small>
                                <div>${pagamento.empenho || 'Não informado'}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Nota de Empenho</small>
                                <div>${pagamento.nota_empenho || 'Não informado'}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Valor Liquidado</small>
                                <div>${pagamento.valor_liquidado || '0.00'}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <small class="text-muted">Ordem Bancária</small>
                                <div>${pagamento.ordem_bancaria || 'Não informado'}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Nota Fiscal</small>
                                <div>${pagamento.nota_fiscal || 'Não informado'}</div>
                            </div>
                        </div>
                    `;
                    
                    timelineDetails.appendChild(detailsContent);
                    
                    // Adicionar o checklist de pagamentos
                    const checklistTitle = document.createElement('h6');
                    checklistTitle.className = 'mb-2 mt-3';
                    checklistTitle.textContent = 'Checklist de Pagamentos:';
                    timelineDetails.appendChild(checklistTitle);
                    
                    const checklistItems = [
                        { id: 'empenho', label: 'Empenho registrado', checked: !!pagamento.empenho },
                        { id: 'nota', label: 'Nota fiscal recebida', checked: !!pagamento.nota_fiscal },
                        { id: 'pagamento', label: 'Pagamento efetuado', checked: !!pagamento.ordem_bancaria }
                    ];
                    
                    let allPaymentItemsCompleted = true;
                    
                    checklistItems.forEach((item, idx) => {
                        if (!item.checked) allPaymentItemsCompleted = false;
                        
                        const checklistItem = document.createElement('div');
                        checklistItem.className = 'checklist-item';
                        checklistItem.innerHTML = `
                            <input type="checkbox" id="payment-check-${step.id}-${idx}" 
                                ${item.checked ? 'checked' : ''} 
                                onchange="checkPaymentCompletion(${step.id})">
                            <label for="payment-check-${step.id}-${idx}">${item.label}</label>
                        `;
                        timelineDetails.appendChild(checklistItem);
                    });
                    
                    // CORREÇÃO: Atualizar status baseado na conclusão dos pagamentos
                    if (allPaymentItemsCompleted && !step.completed) {
                        step.completed = true;
                        step.status = 'Completo';
                        saveStepStatus(step).then(() => {
                            updateStepVisual(step.id, step);
                            updateProgress();
                        });
                    }
                }
            } catch (error) {
                console.error('Erro ao processar dados de pagamento:', error);
            }
        }
    };
    xhr.send();
}

// NOVA FUNÇÃO: Verificar conclusão dos pagamentos
function checkPaymentCompletion(stepId) {
    const step = steps.find(s => s.id === stepId);
    if (!step) return;
    
    const paymentCheckboxes = document.querySelectorAll(`input[id^="payment-check-${stepId}-"]`);
    const allChecked = Array.from(paymentCheckboxes).every(cb => cb.checked);
    
    if (allChecked && !step.completed) {
        step.completed = true;
        step.status = 'Completo';
        
        // Atualizar checkbox principal
        const mainCheckbox = document.getElementById(`step-${stepId}`);
        if (mainCheckbox) mainCheckbox.checked = true;
        
        saveStepStatus(step).then(() => {
            updateStepVisual(stepId, step);
            updateProgress();
        });
    } else if (!allChecked && step.completed) {
        step.completed = false;
        step.status = 'Em Andamento';
        
        // Atualizar checkbox principal
        const mainCheckbox = document.getElementById(`step-${stepId}`);
        if (mainCheckbox) mainCheckbox.checked = false;
        
        saveStepStatus(step).then(() => {
            updateStepVisual(stepId, step);
            updateProgress();
        });
    }
}

// Função para alternar a expansão de um item da timeline
function toggleExpand(header) {
    const body = header.nextElementSibling;
    body.classList.toggle('expanded');
}

// Função para atualizar a barra de progresso
function updateProgress() {
    const totalSteps = steps.length;
    const completedSteps = steps.filter(step => step.completed).length;
    
    if (totalSteps === 0) return;
    
    const percentage = Math.round((completedSteps / totalSteps) * 100);
    
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress-label span:first-child');
    const progressPercentage = document.querySelector('.progress-label span:last-child');
    
    if (progressBar && progressText && progressPercentage) {
        progressBar.style.width = `${percentage}%`;
        progressBar.setAttribute('aria-valuenow', percentage);
        
        progressText.textContent = `${completedSteps} de ${totalSteps} etapas concluídas`;
        progressPercentage.textContent = `${percentage}%`;
    }
}

// CORREÇÃO: Função para salvar o status de uma etapa com Promise
function saveStepStatus(step) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './update_etapa_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    resolve();
                } else {
                    console.error('Erro ao salvar status da etapa:', xhr.statusText);
                    reject(xhr.statusText);
                }
            }
        };
        xhr.send(JSON.stringify({
            contract_id: step.contract_id,
            etapa: step.title,
            status: step.status
        }));
    });
}

// Função para mover uma etapa para cima
function moveStepUp(id) {
    const index = steps.findIndex(step => step.id === id);
    if (index <= 0) return;

    // Trocar a etapa com a anterior
    const temp = steps[index];
    steps[index] = steps[index - 1];
    steps[index - 1] = temp;

    // Atualizar a ordem no servidor
    updateStepOrder();
    renderTimeline();
}

// Função para mover uma etapa para baixo
function moveStepDown(id) {
    const index = steps.findIndex(step => step.id === id);
    if (index >= steps.length - 1) return;

    // Trocar a etapa com a próxima
    const temp = steps[index];
    steps[index] = steps[index + 1];
    steps[index + 1] = temp;

    // Atualizar a ordem no servidor
    updateStepOrder();
    renderTimeline();
}

// Função para atualizar a ordem das etapas no servidor
function updateStepOrder() {
    const order = steps.map((step, index) => ({
        contract_id: step.contract_id,
        etapa: step.title,
        order: index
    }));

    const xhr = new XMLHttpRequest();
    xhr.open('POST', './update_etapa_order.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status != 200) {
            console.error('Erro ao atualizar ordem das etapas:', xhr.statusText);
        }
    };
    xhr.send(JSON.stringify(order));
}

// Função para remover uma etapa
function removeStep(id) {
    const step = steps.find(s => s.id === id);
    if (!step) return;
    
    if (!confirm(`Tem certeza que deseja remover a etapa "${step.title}"?`)) {
        return;
    }
    
    steps = steps.filter(s => s.id !== id);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', './remove_etapa.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status != 200) {
            console.error('Erro ao remover etapa:', xhr.statusText);
        }
    };
    xhr.send(JSON.stringify({ contract_id: step.contract_id, etapa: step.title }));

    renderTimeline();
}

// Adicionar event listener para o botão de adicionar etapa
document.addEventListener('DOMContentLoaded', function() {
    const addStepBtn = document.getElementById('addStepBtn');
    if (addStepBtn) {
        addStepBtn.addEventListener('click', function() {
            const newStepInput = document.getElementById('newStepInput');
            const stepTitle = newStepInput.value.trim();

            if (stepTitle === '') {
                alert('Por favor, digite o nome da etapa.');
                return;
            }

            // CORREÇÃO: Verificar se já existe uma etapa com o mesmo nome
            const existingStep = steps.find(s => s.title.toLowerCase() === stepTitle.toLowerCase());
            if (existingStep) {
                alert('Já existe uma etapa com este nome.');
                return;
            }

            const newStep = {
                id: steps.length > 0 ? Math.max(...steps.map(s => s.id)) + 1 : 1,
                contract_id: currentContractId,
                title: stepTitle,
                description: `Etapa adicionada manualmente.`,
                date: new Date().toISOString().split('T')[0],
                time: new Date().toTimeString().split(' ')[0].slice(0, 5),
                status: 'Pendente',
                completed: false
            };

            steps.push(newStep);
            newStepInput.value = '';

            const xhr = new XMLHttpRequest();
            xhr.open('POST', './add_etapa.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status != 200) {
                    console.error('Erro ao adicionar etapa:', xhr.statusText);
                }
            };
            xhr.send(JSON.stringify(newStep));

            renderTimeline();
        });
    }
    
    // Inicializar a timeline ao carregar a página
    exibirFluxoContratos();
});