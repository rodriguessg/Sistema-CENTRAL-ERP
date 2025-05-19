let currentContractId = null;
let steps = [];

function showTab(tabName) {
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.form-container');
    tabs.forEach(tab => {
        tab.classList.toggle('active', tab.dataset.tab === tabName);
    });
    contents.forEach(content => {
        content.style.display = content.id === tabName ? 'block' : 'none';
    });
}

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
                    steps = fluxo.map((etapa, index) => ({
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

function renderTimeline() {
    const timeline = document.getElementById('timeline');
    timeline.innerHTML = '';

    if (!steps || steps.length === 0) {
        timeline.innerHTML = '<p class="text-center">Nenhuma etapa disponível.</p>';
        return;
    }

    const ul = document.createElement('ul');
    ul.className = 'timeline';

    steps.forEach((step, index) => {
        if (!step || !step.id) return;

        const isFirst = index === 0;
        const isLast = index === steps.length - 1;

        const li = document.createElement('li');
        li.className = step.completed ? 'completed' : '';
        li.innerHTML = `
            <div class="timeline-icon">
                <i class="${step.completed ? 'fas fa-check' : 'fas fa-circle'}"></i>
            </div>
            <div class="timeline-content">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="step-${step.id}" ${step.completed ? 'checked' : ''}>
                    <label class="form-check-label" for="step-${step.id}">${step.title}</label>
                </div>
                <p>${step.description}</p>
                <p><strong>Data:</strong> ${step.date} <br> <strong>Hora:</strong> ${step.time}</p>
                <span class="badge ${step.status === 'Completo' ? 'bg-success' : step.status === 'Em Andamento' ? 'bg-warning text-dark' : 'bg-secondary'}">${step.status}</span>
                <div class="timeline-actions">
                    <button class="move-btn" onclick="moveStepUp(${step.id})" ${isFirst ? 'disabled' : ''}>
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <button class="move-btn" onclick="moveStepDown(${step.id})" ${isLast ? 'disabled' : ''}>
                        <i class="fas fa-arrow-down"></i>
                    </button>
                    <button class="btn btn-danger btn-sm remove-step-btn" onclick="removeStep(${step.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        ul.appendChild(li);
    });

    timeline.appendChild(ul);

    steps.forEach(step => {
        if (!step || !step.id) return;
        const checkbox = document.getElementById(`step-${step.id}`);
        if (checkbox) {
            checkbox.addEventListener('change', (e) => {
                step.completed = e.target.checked;
                step.status = step.completed ? 'Completo' : 'Em Andamento';
                saveStepStatus(step);
                renderTimeline();
            });
        } else {
            console.warn(`Checkbox com ID step-${step.id} não encontrado.`);
        }
    });

    steps.forEach(step => {
        if (step.title === 'Pagamentos') {
            const li = ul.querySelector(`input[id="step-${step.id}"]`)?.closest('li');
            if (!li) return;

            const checklistDiv = document.createElement('div');
            checklistDiv.className = 'checklist';
            checklistDiv.innerHTML = '<h6>Checklist de Pagamentos:</h6>';

            const xhr = new XMLHttpRequest();
            xhr.open('GET', `./get_pagamentos.php?contract_id=${step.contract_id}`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const pagamentos = JSON.parse(xhr.responseText);
                    if (pagamentos.length > 0) {
                        const pagamento = pagamentos[0];
                        const checklistItems = [
                            { label: "Empenho", campo: pagamento.empenho },
                            { label: "Nota de Empenho", campo: pagamento.nota_empenho },
                            { label: "Valor Liquidado", campo: pagamento.valor_liquidado },
                            { label: "Ordem Bancária", campo: pagamento.ordem_bancaria },
                            { label: "Nota Fiscal", campo: pagamento.nota_fiscal }
                        ];
                        checklistItems.forEach(item => {
                            const checked = item.campo ? 'checked' : '';
                            checklistDiv.innerHTML += `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="${item.campo || ''}" id="check-${item.label}-${step.id}" ${checked} disabled>
                                    <label class="form-check-label" for="check-${item.label}-${step.id}">
                                        ${item.label}: ${item.campo || 'Não informado'}
                                    </label>
                                </div>
                            `;
                        });
                    }
                    li.querySelector('.timeline-content').appendChild(checklistDiv);
                }
            };
            xhr.send();
        }
    });
}

function saveStepStatus(step) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './update_etapa_status.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status != 200) {
            console.error('Erro ao salvar status da etapa:', xhr.statusText);
        }
    };
    xhr.send(JSON.stringify({
        contract_id: step.contract_id,
        etapa: step.title,
        status: step.status
    }));
}

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

document.getElementById('addStepBtn').addEventListener('click', () => {
    const newStepInput = document.getElementById('newStepInput');
    const stepTitle = newStepInput.value.trim();

    if (stepTitle === '') {
        alert('Por favor, digite o nome da etapa.');
        return;
    }

    const newStep = {
        id: steps.length > 0 ? Math.max(...steps.map(s => s.id)) + 1 : 1,
        contract_id: currentContractId,
        title: stepTitle,
        description: `Etapa adicionada manualmente.`,
        date: new Date().toISOString().split('T')[0],
        time: new Date().toTimeString().split(' ')[0].slice(0, 5),
        status: 'Em Andamento',
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

function removeStep(id) {
    const step = steps.find(s => s.id === id);
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

window.onload = exibirFluxoContratos;