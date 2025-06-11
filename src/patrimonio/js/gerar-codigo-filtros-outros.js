 // Função para gerar o código automaticamente via AJAX
    function gerarCodigo(categoria) {
        if (categoria) {
            fetch(`./patrimonio/gerar_codigo.php?categoria=${categoria}`)
                .then(response => response.text())
                .then(data => {
                    // Preencher o campo de código com o valor retornado
                    document.getElementById("codigo").value = data;
                })
                .catch(error => console.error('Erro ao gerar o código:', error));
        }
    }

    // Função para alternar entre as abas
    function showTab(tabName) {
        // Esconder todas as abas do tipo form-container e form-container2
        const tabs = document.querySelectorAll('.form-container, .form-container2');
        tabs.forEach(tab => tab.style.display = 'none');

        // Exibir a aba selecionada (form-container ou form-container2)
        const selectedTab = document.getElementById(tabName);
        if (selectedTab) {
            selectedTab.style.display = 'block';
        }

        // Atualizar o estilo das abas para mostrar qual está ativa
        const tabLinks = document.querySelectorAll('.tab');
        tabLinks.forEach(tab => tab.classList.remove('active'));
        const activeTabLink = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeTabLink) {
            activeTabLink.classList.add('active');
        }
    }


    // Mostrar a aba 'cadastrar' como padrão quando a página for carregada
    window.onload = function() {
        showTab('cadastrar');
    };


    // Função de filtro da tabela - Exemplo para ilustrar o funcionamento
    function filtrarTabela() {
        // Obter os valores dos filtros
        const identificacao = document.getElementById('filtro-identificacao').value.toLowerCase().trim();
        const situacao = document.getElementById('filtro-situacao').value.toLowerCase().trim();
        const operacao = document.getElementById('filtro-operacao').value.toLowerCase().trim();

        // Selecionar todas as linhas do corpo da tabela
        const linhas = document.querySelectorAll('#tabela-levantamento tbody tr');

        linhas.forEach(linha => {
            // Obter os valores das colunas relevantes
            const colunaIdentificacao = linha.cells[1]?.textContent.toLowerCase().trim() || '';
            const colunaSituacao = linha.cells[5]?.textContent.toLowerCase().trim() || '';
            const colunaOperacao = linha.cells[2]?.textContent.toLowerCase().trim() || '';

            // Comparar os valores das colunas com os filtros
            const matchIdentificacao = !identificacao || colunaIdentificacao.includes(identificacao);
            const matchSituacao = !situacao || colunaSituacao.includes(situacao);
            const matchOperacao = !operacao || colunaOperacao.includes(operacao);

            // Exibir ou ocultar a linha com base nos critérios
            linha.style.display = matchIdentificacao && matchSituacao && matchOperacao ? '' : 'none';
        });
    }


        // Função para abrir o modal e carregar conteúdo de modaldetalhes.php
        document.querySelectorAll('.detalhes-btn').forEach(button => {
            button.addEventListener('click', function() {
                const patrimonioId = this.getAttribute('data-id');
                abrirModal(patrimonioId);
            });
        });

    // Função para abrir o modal e carregar os detalhes
    function abrirModalDetalhes(id) {
        // Encontrar a linha da tabela que corresponde ao id do patrimônio
        const linhas = document.querySelectorAll('tbody tr');
        let patrimonio = {};

        linhas.forEach(linha => {
            const tdId = linha.querySelector('td:first-child').textContent; // ID da linha
            if (tdId == id) {
                patrimonio = {
                    id: tdId,
                    nome: linha.cells[1].textContent, // Nome
                    descricao: linha.cells[2].textContent, // Descrição
                    valor: linha.cells[3].textContent, // Valor
                    localizacao: linha.cells[4].textContent, // Localização
                    situacao: linha.cells[5].textContent, // Situação
                    cadastrado_por: linha.cells[6].textContent, // Cadastrado Por
                    categoria: linha.cells[7].textContent, // Categoria
                    codigo: linha.cells[8].textContent // Código
                };
            }
        });

        // Preencher as informações no modal com os dados do patrimônio
        const modalConteudo = document.getElementById('modal-informacoes');
        modalConteudo.innerHTML = `
            <h3>Detalhes do Patrimônio</h3>
            <p><strong>ID:</strong> ${patrimonio.id}</p>
            <p><strong>Nome:</strong> ${patrimonio.nome}</p>
            <p><strong>Descrição:</strong> ${patrimonio.descricao}</p>
            <p><strong>Valor:</strong> ${patrimonio.valor}</p>
            <p><strong>Localização:</strong> ${patrimonio.localizacao}</p>
            <p><strong>Situação:</strong> ${patrimonio.situacao}</p>
            <p><strong>Cadastrado Por:</strong> ${patrimonio.cadastrado_por}</p>
            <p><strong>Categoria:</strong> ${patrimonio.categoria}</p>
            <p><strong>Código:</strong> ${patrimonio.codigo}</p>
        `;

        // Exibir o modal
        const modal = document.getElementById('modal-detalhes');
        modal.style.display = 'block';
    }

    // Função para fechar o modal
    function fecharModal() {
        const modal = document.getElementById('modal-detalhes');
        modal.style.display = 'none';
    }


    // Função para abrir o modal de atualização
    function abrirModalAtualizar(id) {
        // Encontrar a linha correspondente ao ID
        const linhas = document.querySelectorAll('tbody tr');
        let patrimonio = {};

        linhas.forEach(linha => {
            const tdId = linha.querySelector('td:first-child').textContent; // ID da linha
            if (tdId == id) {
                patrimonio = {
                    id: tdId,
                    nome: linha.cells[1].textContent, // Nome
                    descricao: linha.cells[2].textContent, // Descrição
                    valor: linha.cells[3].textContent.replace(/[^\d,.-]/g, ''), // Remover símbolos de moeda
                    localizacao: linha.cells[4].textContent, // Localização
                    situacao: linha.cells[5].textContent, // Situação
                    cadastrado_por: linha.cells[6].textContent, // Cadastrado Por
                    categoria: linha.cells[7].textContent, // Categoria
                    codigo: linha.cells[8].textContent // Código
                };
            }
        });

        // Preencher os campos do formulário com os dados do patrimônio
        document.getElementById('atualizar-id').value = patrimonio.id;
        document.getElementById('atualizar-nome').value = patrimonio.nome;
        document.getElementById('atualizar-descricao').value = patrimonio.descricao;
        document.getElementById('atualizar-valor').value = patrimonio.valor;
        document.getElementById('atualizar-localizacao').value = patrimonio.localizacao;
        document.getElementById('atualizar-situacao').value = patrimonio.situacao.toLowerCase();
        document.getElementById('atualizar-cadastrado-por').value = patrimonio.cadastrado_por;
        document.getElementById('atualizar-categoria').value = patrimonio.categoria;
        document.getElementById('atualizar-codigo').value = patrimonio.codigo;

        // Exibir o modal de atualização
        document.getElementById('modal-atualizar').style.display = 'block';
    }

    // Função para fechar o modal de atualização
    function fecharModalAtualizar() {
        document.getElementById('modal-atualizar').style.display = 'none';
    }

    document.getElementById('form-atualizar').addEventListener('submit', function (event) {
        event.preventDefault(); // Evita o comportamento padrão de envio do formulário

        // Coleta os dados do formulário
        const formData = new FormData(this);

        // Envia os dados via fetch para o script de atualização
        fetch('./patrimonio/modalatualizabp.php', {
            method: 'POST',
            body: formData,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    alert('Dados atualizados com sucesso!');
                    fecharModalAtualizar(); // Fecha o modal
                    location.reload(); // Atualiza a tabela ou página
                } else {
                    alert('Erro ao atualizar os dados: ' + data.message);
                }
            })
            .catch((error) => {
                console.error('Erro ao enviar os dados:', error);
                alert('Erro ao atualizar os dados. Tente novamente.');
            });
    });


    // Função para imprimir os detalhes do patrimônio
    function imprimirDetalhes() {
        const conteudo = document.getElementById('modal-informacoes').innerHTML;
        const logoURL = './src/img/Logo CENTRAL (colorida).png'; // Substitua pelo caminho do logotipo
        const janelaImpressao = window.open('', '_blank');

        janelaImpressao.document.open();
        janelaImpressao.document.write(`
            <html>
                <head>
                    
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            margin: 20px;
                        }
                        .header2 {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        .header2 img {
                            max-height: 80px;
                        }
                    </style>
                </head>
                <body>
                    <div class="header2">
                        <img src="${logoURL}" alt="Logotipo da Empresa">
                    </div>
                    <div>${conteudo}</div>
                </body>
            </html>
        `);

        janelaImpressao.document.close();
        janelaImpressao.print();
    }


    let paginaAtual = 1; // Página inicial
    const itensPorPagina = 3;

    // Função para carregar dados do servidor
    async function carregarDados(pagina) {
        try {
            const response = await fetch(`./patrimonio/paginasTabela.php?pagina=${pagina}`); // Substitua pelo caminho correto do PHP
            const resultado = await response.json();

            atualizarTabela(resultado.dados);
            atualizarBotoes(resultado.total_paginas);
        } catch (error) {
            console.error('Erro ao carregar dados:', error);
        }
    }

    // Atualizar a tabela com os dados recebidos
    function atualizarTabela(dados) {
        const tbody = document.querySelector('tbody');
        tbody.innerHTML = ''; // Limpar a tabela

        dados.forEach(dado => {
            const row = document.createElement('tr');
            row.innerHTML = `
                    <td>${dado.id}</td>
                      <td>${dado.codigo}</td>
                    <td>${dado.nome}</td>
                    <td>${dado.descricao}</td>
                    <td>${parseFloat(dado.valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</td>
                    <td>${dado.localizacao}</td>
                    <td>${dado.situacao}</td>
                    <td>${dado.cadastrado_por}</td>
                    <td>${dado.categoria}</td>
                  
                    <td class="actions">
                        <button class="btn1" onclick="abrirModalDetalhes('${dado.id}')">+ Detalhes</button>
                        <button class="btn2" onclick="abrirModalAtualizar('${dado.id}')">Atualizar</button>
                    </td>
                `;

            tbody.appendChild(row);
        });
    }

    // Atualizar os botões de paginação
    function atualizarBotoes(totalPaginas) {
        const paginacao = document.querySelector('.pagination');
        paginacao.innerHTML = ''; // Limpar os botões

        const maxBotoes = 5; // Número máximo de botões a exibir de uma vez
        let inicio = Math.max(1, paginaAtual - Math.floor(maxBotoes / 2));
        let fim = Math.min(totalPaginas, inicio + maxBotoes - 1);

        // Ajusta a exibição de botões se houver menos de 5
        if (fim - inicio + 1 < maxBotoes) {
            inicio = Math.max(1, fim - maxBotoes + 1);
        }

        // Criar o botão de "<<"
        if (inicio > 1) {
            const primeiro = document.createElement('button');
            primeiro.textContent = '<<';
            primeiro.onclick = () => {
                paginaAtual = 1;
                carregarDados(paginaAtual);
            };
            paginacao.appendChild(primeiro);
        }

        // Criar os botões de páginas
        for (let i = inicio; i <= fim; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.classList.toggle('active', i === paginaAtual);
            button.onclick = () => {
                paginaAtual = i;
                carregarDados(paginaAtual);
            };
            paginacao.appendChild(button);
        }

        // Criar o botão de ">>"
        if (fim < totalPaginas) {
            const ultimo = document.createElement('button');
            ultimo.textContent = '>>';
            ultimo.onclick = () => {
                paginaAtual = fim + 1;
                carregarDados(paginaAtual);
            };
            paginacao.appendChild(ultimo);
        }
    }

    // Inicializar a página
    window.onload = () => {
        carregarDados(paginaAtual);
    };
