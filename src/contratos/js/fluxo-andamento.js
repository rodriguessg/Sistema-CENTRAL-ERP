 function exibirFluxoContratos() {
            const contractId = document.getElementById('contractSelect').value || '';
            var xhr = new XMLHttpRequest();
            xhr.open('GET', `./get_fluxo_contrato.php?contract_id=${contractId}`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        try {
                            const fluxos = JSON.parse(xhr.responseText);
                            const timeline = document.getElementById('timeline');
                            timeline.innerHTML = '';

                            if (fluxos.length === 0) {
                                timeline.innerHTML = '<p class="text-center">Nenhum contrato encontrado.</p>';
                                return;
                            }

                            fluxos.forEach((fluxo, contractIndex) => {
                                const timelineDiv = document.createElement('div');
                                timelineDiv.className = 'timeline';
                                timelineDiv.innerHTML = `<h5 class="text-center">${fluxo[0].titulo}</h5>`;
                                fluxo.forEach((etapa, index) => {
                                    const isCurrent = new Date(etapa.data) <= new Date('2025-05-16T10:54:00-03:00') && 
                                                     (!fluxo[index + 1] || new Date(fluxo[index + 1].data) > new Date('2025-05-16T10:54:00-03:00'));
                                    const statusClass = `timeline-item status-${etapa.status.toLowerCase().replace(' ', '-')}`;
                                    const item = `
                                        <div class="${statusClass} ${isCurrent ? 'current' : ''}" data-contract-id="${etapa.contract_id}" data-etapa="${etapa.etapa}">
                                            <h6>${etapa.etapa}</h6>
                                            <p>${etapa.descricao}</p>
                                            <p><strong>${etapa.status === 'Previsto' ? 'Previsão' : 'Data'}:</strong> ${etapa.data} <br> <strong>Hora:</strong> ${etapa.hora}</p>
                                            <span class="badge ${etapa.status === 'Completo' ? 'bg-success' : etapa.status === 'Em Andamento' ? 'bg-warning text-dark' : 'bg-secondary'}">${etapa.status}</span>
                                        </div>
                                    `;
                                    timelineDiv.innerHTML += item;
                                });
                                timeline.appendChild(timelineDiv);

                                const items = timelineDiv.querySelectorAll('.timeline-item');
                                items.forEach((item, idx) => {
                                    setTimeout(() => item.classList.add('active'), idx * 200);
                                });
                            });
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

        function carregarDetalhesRastreamento() {
            const selectedItem = document.querySelector('.timeline-item.current');
            const contractId = selectedItem ? selectedItem.getAttribute('data-contract-id') : null;
            if (!contractId) {
                alert('Selecione um contrato clicando em uma etapa atual.');
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('GET', `./get_detalhe_rastreamento.php?contract_id=${contractId}`, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    try {
                        const detalhes = JSON.parse(xhr.responseText);
                        const modalContent = document.getElementById('modalContent');
                        modalContent.innerHTML = `
                            <h6>Histórico Completo - Contrato ID: ${contractId}</h6>
                            <ul class="list-group">
                                ${detalhes.map(d => `<li class="list-group-item">${d.etapa}: ${d.data} ${d.hora} - ${d.status}</li>`).join('')}
                            </ul>
                        `;
                    } catch (error) {
                        console.error('Erro ao carregar detalhes:', error);
                        document.getElementById('modalContent').innerHTML = '<p>Erro ao carregar detalhes.</p>';
                    }
                }
            };
            xhr.send();
        }

        document.querySelector('.btn-rastrear').addEventListener('click', carregarDetalhesRastreamento);
        window.onload = exibirFluxoContratos;