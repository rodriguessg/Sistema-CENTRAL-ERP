document.addEventListener('DOMContentLoaded', function() {
    // Função para formatar valores monetários
    function formatCurrency(value) {
        const num = value ? parseFloat(value) : 0;
        return `R$ ${num.toFixed(2)}`;
    }

    // Função para formatar datas no formato DD/MM/YYYY
    function formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        const date = new Date(dateStr);
        return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', timeZone: 'UTC' });
    }

    // Função para formatar horas no formato HH:MM
    function formatTime(timeStr) {
        if (!timeStr) return 'N/A';
        return timeStr;
    }

    // Função para atualizar o campo de e-mail com base no seletor
    function atualizarCampoEmail() {
        const emailSelect = document.getElementById('email_destinatario_select');
        const emailInput = document.getElementById('email_destinatario');
        const salvarEmailLabel = document.getElementById('salvar_email_label');

        if (emailSelect.value === 'novo') {
            emailInput.value = '';
            emailInput.disabled = false;
            salvarEmailLabel.style.display = 'block';
        } else if (emailSelect.value) {
            emailInput.value = emailSelect.value;
            emailInput.disabled = true;
            salvarEmailLabel.style.display = 'none';
        } else {
            emailInput.value = '';
            emailInput.disabled = false;
            salvarEmailLabel.style.display = 'block';
        }
    }

    // Função para mostrar ou esconder o campo de tipo de relatório
    function mostrarTipoRelatorio() {
        const contratoSelect = document.getElementById('tipo_relatorio');
        const relatorioTodosSelect = document.getElementById('relatorio_todos');
        const tipoRelatorioContainer = document.getElementById('tipo-relatorio-container');
        const mesContainer = document.getElementById('mes-container');
        const anoContainer = document.getElementById('ano-container');

        tipoRelatorioContainer.style.display = 'none';
        mesContainer.style.display = 'none';
        anoContainer.style.display = 'none';
        relatorioTodosSelect.value = '';
        document.getElementById('relatorio_tipo').value = 'completo';

        if (contratoSelect.value) {
            tipoRelatorioContainer.style.display = 'block';
        }
    }

    // Função para mostrar os campos específicos de cada tipo de relatório (todos os contratos)
    function mostrarCamposRelatorioTodos() {
        const relatorioTodos = document.getElementById('relatorio_todos').value;
        const tipoRelatorioContainer = document.getElementById('tipo-relatorio-container');
        const mesContainer = document.getElementById('mes-container');
        const anoContainer = document.getElementById('ano-container');
        const contratoSelect = document.getElementById('tipo_relatorio');

        tipoRelatorioContainer.style.display = 'none';
        mesContainer.style.display = 'none';
        anoContainer.style.display = 'none';
        contratoSelect.value = '';
        document.getElementById('relatorio_tipo').value = 'completo';

        if (relatorioTodos === 'mensal_todos') {
            mesContainer.style.display = 'block';
            carregarMeses();
        } else if (relatorioTodos === 'anual_todos') {
            anoContainer.style.display = 'block';
            carregarAnos();
        }
    }

    // Função para mostrar os campos específicos de cada tipo de relatório (contratos individuais)
    function mostrarCamposRelatorio() {
        const relatorioTipo = document.getElementById('relatorio_tipo').value;
        const mesContainer = document.getElementById('mes-container');
        const anoContainer = document.getElementById('ano-container');

        mesContainer.style.display = 'none';
        anoContainer.style.display = 'none';

        if (relatorioTipo === 'mensal') {
            mesContainer.style.display = 'block';
            carregarMeses();
        } else if (relatorioTipo === 'anual') {
            anoContainer.style.display = 'block';
            carregarAnos();
        }
    }

    // Função para preencher o seletor de meses
    function carregarMeses() {
        const mesSelect = document.getElementById('mes');
        mesSelect.innerHTML = '<option value="">Selecione o Mês</option>';
        const meses = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
        
        meses.forEach((mes, index) => {
            const option = document.createElement('option');
            option.value = index + 1;
            option.textContent = mes;
            mesSelect.appendChild(option);
        });
    }

    // Função para preencher o seletor de anos
    function carregarAnos() {
        const anoSelect = document.getElementById('ano');
        anoSelect.innerHTML = '<option value="">Selecione o Ano</option>';
        const anoAtual = new Date().getFullYear();
        const anos = [anoAtual - 2, anoAtual - 1, anoAtual, anoAtual + 1, anoAtual + 2];
        
        anos.forEach(ano => {
            const option = document.createElement('option');
            option.value = ano;
            option.textContent = ano;
            anoSelect.appendChild(option);
        });
    }

    // Função para gerar o relatório
    function gerarRelatorio() {
        const contrato = document.getElementById('tipo_relatorio').value;
        const relatorioTodos = document.getElementById('relatorio_todos').value;

        if (relatorioTodos) {
            gerarRelatorioTodos();
            return;
        }

        if (!contrato) {
            alert('Por favor, selecione o contrato.');
            return;
        }

        const tipoRelatorio = document.getElementById('relatorio_tipo').value;
        if (!tipoRelatorio) {
            alert('Por favor, selecione o tipo de relatório.');
            return;
        }

        let params = 'contrato=' + encodeURIComponent(contrato) + '&relatorio_tipo=' + encodeURIComponent(tipoRelatorio);
        if (tipoRelatorio === 'mensal') {
            const mes = document.getElementById('mes').value;
            if (!mes) {
                alert('Por favor, selecione o mês.');
                return;
            }
            params += '&mes=' + encodeURIComponent(mes);
        } else if (tipoRelatorio === 'anual') {
            const ano = document.getElementById('ano').value;
            if (!ano) {
                alert('Por favor, selecione o ano.');
                return;
            }
            params += '&ano=' + encodeURIComponent(ano);
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'processar_relatorio.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(params);

        xhr.onload = function () {
            console.log('Status HTTP:', xhr.status);
            console.log('Resposta do servidor:', xhr.responseText);
            if (xhr.status === 200) {
                try {
                    const resposta = JSON.parse(xhr.responseText);
                    if (resposta.sucesso) {
                        resetTabelas();
                        const dados = Array.isArray(resposta.dados) ? resposta.dados : [resposta.dados];
                        if (tipoRelatorio === 'completo') {
                            preencherTabelaCompleta(dados);
                        } else if (tipoRelatorio === 'compromissos_futuros') {
                            preencherCompromissosFuturos(dados);
                        } else if (tipoRelatorio === 'pagamentos') {
                            preencherTabelaPagamentos(dados);
                        } else if (tipoRelatorio === 'mensal') {
                            preencherTabelaMensal(dados);
                        } else if (tipoRelatorio === 'anual') {
                            preencherTabelaAnual(dados);
                        }
                    } else {
                        alert(resposta.mensagem || 'Erro ao gerar o relatório.');
                    }
                } catch (e) {
                    console.error('Erro ao processar a resposta JSON:', e);
                    alert('Erro ao processar os dados. Resposta inesperada: ' + xhr.responseText);
                }
            } else {
                alert('Erro ao gerar relatório. Status HTTP: ' + xhr.status + '\nResposta: ' + xhr.responseText);
            }
        };
    }

    // Função para gerar relatório de todos os contratos
    function gerarRelatorioTodos() {
        const relatorioTodos = document.getElementById('relatorio_todos').value;
        if (!relatorioTodos) {
            alert('Por favor, selecione o tipo de relatório.');
            return;
        }

        let params = 'relatorio_tipo=' + encodeURIComponent(relatorioTodos);
        if (relatorioTodos === 'mensal_todos') {
            const mes = document.getElementById('mes').value;
            if (!mes) {
                alert('Por favor, selecione o mês.');
                return;
            }
            params += '&mes=' + encodeURIComponent(mes);
        } else if (relatorioTodos === 'anual_todos') {
            const ano = document.getElementById('ano').value;
            if (!ano) {
                alert('Por favor, selecione o ano.');
                return;
            }
            params += '&ano=' + encodeURIComponent(ano);
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'processar_relatorio_todos.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(params);

        xhr.onload = function () {
            console.log('Status HTTP:', xhr.status);
            console.log('Resposta do servidor:', xhr.responseText);
            if (xhr.status === 200) {
                try {
                    const resposta = JSON.parse(xhr.responseText);
                    if (resposta.sucesso) {
                        resetTabelas();
                        const dados = Array.isArray(resposta.dados) ? resposta.dados : [resposta.dados];
                        if (relatorioTodos === 'mensal_todos') {
                            preencherTabelaMensalTodos(dados);
                        } else if (relatorioTodos === 'anual_todos') {
                            preencherTabelaAnualTodos(dados);
                        }
                    } else {
                        alert(resposta.mensagem || 'Erro ao gerar o relatório.');
                    }
                } catch (e) {
                    console.error('Erro ao processar a resposta JSON:', e);
                    alert('Erro ao processar os dados. Resposta inesperada: ' + xhr.responseText);
                }
            } else {
                alert('Erro ao gerar relatório. Status HTTP: ' + xhr.status + '\nResposta: ' + xhr.responseText);
            }
        };
    }

    // Função para agendar relatório
    function agendarRelatorio() {
        const contrato = document.getElementById('tipo_relatorio').value;
        const relatorioTodos = document.getElementById('relatorio_todos').value;
        const tipoRelatorio = document.getElementById('relatorio_tipo').value;
        const mes = document.getElementById('mes').value;
        const ano = document.getElementById('ano').value;
        const email = document.getElementById('email_destinatario').value;
        const salvarEmail = document.getElementById('salvar_email').checked;
        const periodicidade = document.getElementById('periodicidade').value;

        if (!email) {
            alert('Por favor, informe o e-mail do destinatário.');
            return;
        }
        if (!periodicidade) {
            alert('Por favor, selecione a periodicidade.');
            return;
        }

        let params = 'email=' + encodeURIComponent(email) + '&periodicidade=' + encodeURIComponent(periodicidade);
        if (salvarEmail) {
            params += '&salvar_email=1';
        }
        if (relatorioTodos) {
            params += '&relatorio_todos=' + encodeURIComponent(relatorioTodos);
            if (relatorioTodos === 'mensal_todos' && mes) {
                params += '&mes=' + encodeURIComponent(mes);
            } else if (relatorioTodos === 'anual_todos' && ano) {
                params += '&ano=' + encodeURIComponent(ano);
            }
        } else {
            if (!contrato || !tipoRelatorio) {
                alert('Por favor, selecione o contrato e o tipo de relatório.');
                return;
            }
            params += '&contrato=' + encodeURIComponent(contrato) + '&relatorio_tipo=' + encodeURIComponent(tipoRelatorio);
            if (tipoRelatorio === 'mensal' && mes) {
                params += '&mes=' + encodeURIComponent(mes);
            } else if (tipoRelatorio === 'anual' && ano) {
                params += '&ano=' + encodeURIComponent(ano);
            }
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'agendar_relatorio.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(params);

        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    const resposta = JSON.parse(xhr.responseText);
                    if (resposta.sucesso) {
                        alert('Relatório agendado com sucesso!');
                        if (salvarEmail && resposta.email_salvo) {
                            // Adicionar o e-mail ao seletor de e-mails salvos
                            const emailSelect = document.getElementById('email_destinatario_select');
                            const option = document.createElement('option');
                            option.value = email;
                            option.textContent = email;
                            emailSelect.insertBefore(option, emailSelect.lastElementChild);
                        }
                    } else {
                        alert(resposta.mensagem || 'Erro ao agendar o relatório.');
                    }
                } catch (e) {
                    alert('Erro ao processar a resposta: ' + xhr.responseText);
                }
            } else {
                alert('Erro ao agendar relatório. Status HTTP: ' + xhr.status);
            }
        };
    }

    // Função para exportar para PDF
    function exportarPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const tabelaVisivel = document.querySelector('div[id$="-tabela"][style*="block"] table');
        if (!tabelaVisivel) {
            alert('Nenhum relatório gerado para exportar.');
            return;
        }

        const tipoRelatorio = tabelaVisivel.id;
        doc.text(`Relatório ${tipoRelatorio.replace('-', ' ').toUpperCase()}`, 10, 10);
        doc.autoTable({ html: tabelaVisivel });
        doc.save(`relatorio_${tipoRelatorio}.pdf`);
    }

    // Função para exportar para CSV
    function exportarCSV() {
        const tabelaVisivel = document.querySelector('div[id$="-tabela"][style*="block"] table');
        if (!tabelaVisivel) {
            alert('Nenhum relatório gerado para exportar.');
            return;
        }

        const tipoRelatorio = tabelaVisivel.id;
        const rows = tabelaVisivel.querySelectorAll('tr');
        const data = [];

        rows.forEach(row => {
            const rowData = [];
            row.querySelectorAll('th, td').forEach(cell => {
                rowData.push(cell.innerText);
            });
            data.push(rowData);
        });

        const csv = Papa.unparse(data);
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `relatorio_${tipoRelatorio}.csv`;
        link.click();
    }

    // Funções auxiliares para preencher as tabelas específicas
    function preencherTabelaCompleta(dados) {
        const tabela = document.querySelector('#relatorio-completo tbody');
        tabela.innerHTML = '';

        const contratos = Array.isArray(dados) ? dados : [dados];
        contratos.forEach(contrato => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${contrato.titulo || 'N/A'}</td>
                <td>${formatDate(contrato.validade)}</td>
                <td>${contrato.gestor || 'N/A'}</td>
                <td>${contrato.gestorsb || 'N/A'}</td>
                <td>${contrato.situacao || 'N/A'}</td>
                <td>${contrato.num_parcelas ?? 'N/A'}</td>
                <td>${formatDate(contrato.data_cadastro)}</td>
            `;
            tabela.appendChild(tr);
        });
        document.getElementById('relatorio-completo-tabela').style.display = 'block';
    }

    // function preencherCompromissosFuturos(dados) {
    //     const tabela = document.querySelector('#compromissos-futuros tbody');
    //     tabela.innerHTML = '';

    //     const contratos = Array.isArray(dados) ? dados : [dados];
    //     contratos.forEach(contrato => {
    //         if (contrato.proximos_eventos && Array.isArray(contrato.proximos_eventos) && contrato.proximos_eventos.length > 0) {
    //             contrato.proximos_eventos.forEach(evento => {
    //                 const tr = document.createElement('tr');
    //                 tr.innerHTML = `
    //                     <td>${contrato.titulo || 'N/A'}</td>
    //                     <td>${formatDate(contrato.validade)}</td>
    //                     <td>${contrato.gestor || 'N/A'}</td>
    //                     <td>${contrato.gestorsb || 'N/A'}</td>
    //                     <td>${contrato.situacao || 'N/A'}</td>
    //                     <td>${contrato.num_parcelas ?? 'N/A'}</td>
    //                     <td>${evento.titulo || 'N/A'}</td>
    //                     <td>${evento.descricao || 'N/A'}</td>
    //                     <td>${formatDate(evento.data)}</td>
    //                     <td>${formatTime(evento.hora)}</td>
    //                     <td>${evento.categoria || 'N/A'}</td>
    //                 `;
    //                 tabela.appendChild(tr);
    //             });
    //         } else {
    //             const tr = document.createElement('tr');
    //             tr.innerHTML = `
    //                 <td>${contrato.titulo || 'N/A'}</td>
    //                 <td>${formatDate(contrato.validade)}</td>
    //                 <td>${contrato.gestor || 'N/A'}</td>
    //                 <td>${contrato.gestorsb || 'N/A'}</td>
    //                 <td>${contrato.situacao || 'N/A'}</td>
    //                 <td>${contrato.num_parcelas ?? 'N/A'}</td>
    //                 <td colspan="5">Nenhum evento futuro</td>
    //             `;
    //             tabela.appendChild(tr);
    //         }
    //     });
    //     document.getElementById('compromissos-futuros-tabela').style.display = 'block';
    // }

    function preencherTabelaPagamentos(dados) {
    const tabela = document.querySelector('#relatorio-pagamentos tbody');
    tabela.innerHTML = ''; // Limpar a tabela antes de adicionar os novos dados

    const contratos = Array.isArray(dados) ? dados : [dados]; // Garantir que 'dados' seja um array

    contratos.forEach(contrato => {
        // Verifica se o contrato tem pagamentos
        if (contrato.pagamentos && Array.isArray(contrato.pagamentos) && contrato.pagamentos.length > 0) {
            contrato.pagamentos.forEach(pagamento => {
                // Cria uma linha na tabela para cada pagamento
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${contrato.contrato_titulo || 'N/A'}</td>
                    <td>${pagamento.mes || 'N/A'}</td>
                    <td>${formatDate(pagamento.data_pagamento)}</td>
                    <td>R$ ${parseFloat(pagamento.valor_liquidado).toFixed(2) }</td>
                `;
                tabela.appendChild(tr);
            });
        } else {
            // Se não houver pagamentos, cria uma linha indicando "Nenhum pagamento"
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${contrato.contrato_titulo || 'N/A'}</td>
                <td>Nenhum pagamento</td>
                <td></td>
                <td></td>
            `;
            tabela.appendChild(tr);
        }
    });

    // Exibe a tabela depois de preenchê-la com os dados
    document.getElementById('relatorio-pagamentos-tabela').style.display = 'block';
}

// Função auxiliar para formatar a data de pagamento
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', options);
}


    function preencherTabelaMensal(dados) {
        const tabela = document.querySelector('#relatorio-mensal tbody');
        tabela.innerHTML = '';

        const contratos = Array.isArray(dados) ? dados : [dados];
        contratos.forEach(contrato => {
            const numPagamentos = contrato.pagamentos && Array.isArray(contrato.pagamentos) ? contrato.pagamentos.length : 0;
            const parcelasRestantes = (contrato.num_parcelas ?? 0) - numPagamentos;

            if (contrato.pagamentos && Array.isArray(contrato.pagamentos) && contrato.pagamentos.length > 0) {
                contrato.pagamentos.forEach(pagamento => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${contrato.titulo || 'N/A'}</td>
                        <td>${parcelasRestantes >= 0 ? parcelasRestantes : 'N/A'}</td>
                        <td>${formatDate(pagamento.data_pagamento)}</td>
                        <td>${formatCurrency(pagamento.valor)}</td>
                    `;
                    tabela.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${contrato.titulo || 'N/A'}</td>
                    <td>${contrato.num_parcelas ?? 'N/A'}</td>
                    <td colspan="2">Nenhum pagamento</td>
                `;
                tabela.appendChild(tr);
            }
        });
        document.getElementById('relatorio-mensal-tabela').style.display = 'block';
    }

    function preencherTabelaAnual(dados) {
        const tabela = document.querySelector('#relatorio-anual tbody');
        tabela.innerHTML = '';

        const contratos = Array.isArray(dados) ? dados : [dados];
        contratos.forEach(contrato => {
            if (contrato.anos && Array.isArray(contrato.anos)) {
                contrato.anos.forEach(ano => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${contrato.titulo || 'N/A'}</td>
                        <td>${ano.ano || 'N/A'}</td>
                        <td>${contrato.num_parcelas ?? 'N/A'}</td>
                        <td>${formatCurrency(ano.total_pago)}</td>
                        <td>${ano.quantidade_pagamentos ?? '0'}</td>
                    `;
                    tabela.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${contrato.titulo || 'N/A'}</td>
                    <td colspan="4">Nenhum dado disponível para este ano.</td>
                `;
                tabela.appendChild(tr);
            }
        });
        document.getElementById('relatorio-anual-tabela').style.display = 'block';
    }

    function preencherTabelaMensalTodos(dados) {
        const tabela = document.querySelector('#relatorio-mensal-todos tbody');
        tabela.innerHTML = '';

        const contratos = Array.isArray(dados) ? dados : [dados];
        contratos.forEach(contrato => {
            const numPagamentos = contrato.pagamentos && Array.isArray(contrato.pagamentos) ? contrato.pagamentos.length : 0;
            const parcelasRestantes = (contrato.num_parcelas ?? 0) - numPagamentos;

            if (contrato.pagamentos && Array.isArray(contrato.pagamentos) && contrato.pagamentos.length > 0) {
                contrato.pagamentos.forEach(pagamento => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${contrato.titulo || 'N/A'}</td>
                        <td>${parcelasRestantes >= 0 ? parcelasRestantes : 'N/A'}</td>
                        <td>${formatDate(pagamento.data_pagamento)}</td>
                        <td>${formatCurrency(pagamento.valor_liquidado)}</td>
                    `;
                    tabela.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${contrato.titulo || 'N/A'}</td>
                    <td>${contrato.num_parcelas ?? 'N/A'}</td>
                    <td colspan="2">Nenhum pagamento</td>
                `;
                tabela.appendChild(tr);
            }
        });
        document.getElementById('relatorio-mensal-todos-tabela').style.display = 'block';
    }

    function preencherTabelaAnualTodos(dados) {
        const tabela = document.querySelector('#relatorio-anual-todos tbody');
        tabela.innerHTML = '';

        const contratos = Array.isArray(dados) ? dados : [dados];
        contratos.forEach(contrato => {
            if (contrato.anos && Array.isArray(contrato.anos)) {
                contrato.anos.forEach(ano => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${contrato.titulo || 'N/A'}</td>
                        <td>${ano.ano || 'N/A'}</td>
                        <td>${contrato.num_parcelas ?? 'N/A'}</td>
                        <td>${formatCurrency(ano.total_pago)}</td>
                        <td>${ano.quantidade_pagamentos ?? '0'}</td>
                    `;
                    tabela.appendChild(tr);
                });
            } else {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${contrato.titulo || 'N/A'}</td>
                    <td colspan="4">Nenhum dado disponível para este ano.</td>
                `;
                tabela.appendChild(tr);
            }
        });
        document.getElementById('relatorio-anual-todos-tabela').style.display = 'block';
    }

    // Função para resetar as tabelas
    function resetTabelas() {
        const tabelasIds = [
            'relatorio-completo',
            'compromissos-futuros',
            'relatorio-pagamentos',
            'relatorio-mensal',
            'relatorio-anual',
            'relatorio-mensal-todos',
            'relatorio-anual-todos'
        ];
        tabelasIds.forEach(id => {
            document.getElementById(`${id}-tabela`).style.display = 'none';
            document.querySelector(`#${id} tbody`).innerHTML = '';
        });
    }

    // Expor funções globais para serem chamadas pelo HTML
    window.mostrarTipoRelatorio = mostrarTipoRelatorio;
    window.mostrarCamposRelatorioTodos = mostrarCamposRelatorioTodos;
    window.mostrarCamposRelatorio = mostrarCamposRelatorio;
    window.gerarRelatorio = gerarRelatorio;
    window.agendarRelatorio = agendarRelatorio;
    window.exportarPDF = exportarPDF;
    window.exportarCSV = exportarCSV;
    window.atualizarCampoEmail = atualizarCampoEmail;
});