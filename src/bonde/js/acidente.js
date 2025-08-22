
  // Dados do PHP convertidos para JavaScript
        const acidentes = <?php echo $resultJson; ?>;
        const colorClasses = <?php echo json_encode($colorClasses); ?>;
        const perPage = 5;
        let currentPage = 1;
        let filteredData = acidentes;

        // Função para atualizar o total de acidentes
        function updateTotalAccidents(data) {
            const totalAccidents = document.getElementById('totalAccidents');
            totalAccidents.textContent = `Acidentes Registrados: ${data.length}`;
        }

        // Função para renderizar a tabela
        function renderTable(page, data) {
            const start = (page - 1) * perPage;
            const end = start + perPage;
            const pageData = data.slice(start, end);
            const tbody = document.getElementById('accidentsTableBody');
            tbody.innerHTML = '';

            if (pageData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="11" class="no-data">Nenhum acidente encontrado.</td></tr>';
                return;
            }

            pageData.forEach(row => {
                if (typeof row !== 'object' || row === null) {
                    console.error('Dados inválidos:', row);
                    return;
                }
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.id || ''}</td>
                    <td>${row.data || ''}</td>
                    <td>${row.descricao || ''}</td>
                    <td>${row.localizacao || ''}</td>
                    <td>${row.usuario || ''}</td>
                    <td class="${colorClasses[row.cor] || ''}">${row.severidade || ''}</td>
                    <td>${row.categoria || ''}</td>
                    <td>${row.cor || ''}</td>
                    <td>${row.data_registro || ''}</td>
                    <td>${row.status || ''}</td>
                    <td>
                        ${row.status === 'em andamento' ? 
                            `<form method="POST" action="">
                                <input type="hidden" name="id" value="${row.id}">
                                <input type="hidden" name="update_status" value="1">
                                <button type="submit" class="status-btn">Marcar como Resolvido</button>
                            </form>` : 
                            `<button class="status-btn resolved" disabled>Resolvido</button>`}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Função para renderizar botões de paginação
        function renderPagination(data) {
            const totalPages = Math.ceil(data.length / perPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = '';

            // Atualizar total de acidentes
            updateTotalAccidents(data);

            // Botão "Anterior"
            const prevButton = document.createElement('button');
            prevButton.textContent = 'Anterior';
            prevButton.disabled = currentPage === 1;
            prevButton.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderTable(currentPage, filteredData);
                    renderPagination(filteredData);
                }
            };
            pagination.appendChild(prevButton);

            // Botões de página
            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.textContent = i;
                pageButton.className = i === currentPage ? 'active' : '';
                pageButton.onclick = () => {
                    currentPage = i;
                    renderTable(currentPage, filteredData);
                    renderPagination(filteredData);
                };
                pagination.appendChild(pageButton);
            }

            // Botão "Próximo"
            const nextButton = document.createElement('button');
            nextButton.textContent = 'Próximo';
            nextButton.disabled = currentPage === totalPages || totalPages === 0;
            nextButton.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderTable(currentPage, filteredData);
                    renderPagination(filteredData);
                }
            };
            pagination.appendChild(nextButton);
        }

        // Função para filtrar dados com base nos filtros
        function filterData() {
            const searchTerm = document.getElementById('search').value.toLowerCase().trim();
            const severityFilter = document.getElementById('severityFilter').value;
            const dateStart = document.getElementById('dateStart').value;
            const dateEnd = document.getElementById('dateEnd').value;

            return acidentes.filter(row => {
                // Filtro de texto
                const matchesSearch = !searchTerm || 
                    (row.descricao?.toLowerCase().includes(searchTerm) || false) ||
                    (row.localizacao?.toLowerCase().includes(searchTerm) || false) ||
                    (row.severidade?.toLowerCase().includes(searchTerm) || false) ||
                    (row.categoria?.toLowerCase().includes(searchTerm) || false);

                // Filtro de severidade
                const matchesSeverity = !severityFilter || row.severidade === severityFilter;

                // Filtro de data
                let matchesDate = true;
                if (dateStart) {
                    const rowDate = new Date(row.data_registro.split(' ')[0]); // Extrai apenas a data (YYYY-MM-DD)
                    const startDate = new Date(dateStart);
                    matchesDate = rowDate >= startDate;
                    if (dateEnd) {
                        const endDate = new Date(dateEnd);
                        matchesDate = matchesDate && rowDate <= endDate;
                    }
                }

                return matchesSearch && matchesSeverity && matchesDate;
            });
        }

        // Manipular filtros
        function applyFilters() {
            currentPage = 1;
            filteredData = filterData();
            renderTable(currentPage, filteredData);
            renderPagination(filteredData);
        }

        // Adicionar eventos aos filtros
        document.getElementById('search').addEventListener('input', applyFilters);
        document.getElementById('severityFilter').addEventListener('change', applyFilters);
        document.getElementById('dateStart').addEventListener('change', applyFilters);
        document.getElementById('dateEnd').addEventListener('change', applyFilters);

        // Inicializar tabela e paginação
        renderTable(currentPage, filteredData);
        renderPagination(filteredData);

        // Funções para formulário
        const subcategorias = {
            "Operacionais": [
                { value: "Pane elétrica", text: "Pane elétrica", severidade: "Moderado", cor: "Amarelo" },
                { value: "Falha mecânica", text: "Falha mecânica (freios, motor de tração)", severidade: "Moderado a Grave", cor: "Amarelo/Vermelho" },
                { value: "Descarrilamento sem vítimas", text: "Descarrilamento sem vítimas", severidade: "Grave", cor: "Vermelho" },
                { value: "Descarrilamento com vítimas", text: "Descarrilamento com vítimas", severidade: "Grave", cor: "Vermelho" },
                { value: "Problema de sinalização", text: "Problema de sinalização", severidade: "Moderado", cor: "Amarelo" },
                { value: "Falha no sistema de bilhetagem", text: "Falha no sistema de bilhetagem", severidade: "Leve", cor: "Verde" }
            ],
            "Via permanente / infraestrutura": [
                { value: "Obstrução na via", text: "Obstrução na via (galho, objeto)", severidade: "Leve", cor: "Verde" },
                { value: "Carro estacionado no trilho", text: "Carro estacionado no trilho", severidade: "Moderado", cor: "Amarelo" },
                { value: "Alagamento de via", text: "Alagamento de via", severidade: "Grave", cor: "Vermelho" },
                { value: "Deslizamento de encosta", text: "Deslizamento de encosta", severidade: "Grave", cor: "Vermelho" },
                { value: "Rompimento de trilho / falha estrutural", text: "Rompimento de trilho / falha estrutural", severidade: "Grave", cor: "Vermelho" }
            ],
            "Relacionadas a terceiros": [
                { value: "Atropelamento de pedestre", text: "Atropelamento de pedestre", severidade: "Grave", cor: "Vermelho" },
                { value: "Colisão com veículo", text: "Colisão com veículo", severidade: "Grave", cor: "Vermelho" },
                { value: "Colisão com motocicleta/bicicleta", text: "Colisão com motocicleta/bicicleta", severidade: "Grave", cor: "Vermelho" },
                { value: "Manifestação/bloqueio proposital na via", text: "Manifestação/bloqueio proposital na via", severidade: "Moderado", cor: "Amarelo" }
            ],
            "Emergências médicas": [
                { value: "Passageiro passando mal (sem gravidade)", text: "Passageiro passando mal (sem gravidade)", severidade: "Moderado", cor: "Amarelo" },
                { value: "Passageiro passando mal (grave)", text: "Passageiro passando mal (grave, ex.: infarto)", severidade: "Grave", cor: "Vermelho" },
                { value: "Acidente interno sem vítima grave", text: "Acidente interno sem vítima grave", severidade: "Moderado", cor: "Amarelo" },
                { value: "Acidente interno com vítima grave", text: "Acidente interno com vítima grave", severidade: "Grave", cor: "Vermelho" }
            ],
            "Segurança": [
                { value: "Ato de vandalismo no bonde", text: "Ato de vandalismo no bonde", severidade: "Moderado", cor: "Amarelo" },
                { value: "Agressão entre passageiros", text: "Agressão entre passageiros", severidade: "Moderado a Grave", cor: "Amarelo/Vermelho" },
                { value: "Roubo ou tentativa de assalto", text: "Roubo ou tentativa de assalto", severidade: "Grave", cor: "Vermelho" },
                { value: "Ameaça de bomba / suspeita de artefato", text: "Ameaça de bomba / suspeita de artefato", severidade: "Grave", cor: "Vermelho" }
            ],
            "Eventos externos": [
                { value: "Incêndio em área próxima à via", text: "Incêndio em área próxima à via", severidade: "Grave", cor: "Vermelho" },
                { value: "Queda de árvore sobre a rede elétrica", text: "Queda de árvore sobre a rede elétrica", severidade: "Grave", cor: "Vermelho" },
                { value: "Falta geral de energia elétrica", text: "Falta geral de energia elétrica (rede pública)", severidade: "Moderado", cor: "Amarelo" }
            ]
        };

        function updateSubcategorias() {
            const categoriaSelect = document.getElementById('categoria');
            const subcategoriaSelect = document.getElementById('subcategoria');
            const selectedCategoria = categoriaSelect.value;

            subcategoriaSelect.innerHTML = '<option value="">Selecione a subcategoria</option>';
            document.getElementById('severidade').value = '';
            document.getElementById('cor').value = '';

            if (selectedCategoria && subcategorias[selectedCategoria]) {
                subcategorias[selectedCategoria].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.value;
                    option.textContent = sub.text;
                    subcategoriaSelect.appendChild(option);
                });
            }
        }

        function updateSeveridadeECor() {
            const categoriaSelect = document.getElementById('categoria');
            const subcategoriaSelect = document.getElementById('subcategoria');
            const severidadeSelect = document.getElementById('severidade');
            const corInput = document.getElementById('cor');
            const selectedCategoria = categoriaSelect.value;
            const selectedSubcategoria = subcategoriaSelect.value;

            severidadeSelect.value = '';
            corInput.value = '';

            if (selectedCategoria && selectedSubcategoria && subcategorias[selectedCategoria]) {
                const subcategoria = subcategorias[selectedCategoria].find(sub => sub.value === selectedSubcategoria);
                if (subcategoria) {
                    severidadeSelect.value = subcategoria.severidade;
                    corInput.value = subcategoria.cor;
                }
            }
        }