  // Array para armazenar certidões
        let certidoes = [];

        // Função para calcular dias restantes até a data de vencimento
        function calcularDiasRestantes(dataVencimento) {
            const hoje = new Date();
            hoje.setHours(0, 0, 0, 0);
            const vencimento = new Date(dataVencimento);
            vencimento.setHours(0, 0, 0, 0);
            const diffTime = vencimento - hoje;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            let texto = '';
            let classe = '';

            if (diffDays < 0) {
                texto = `Vencida há ${Math.abs(diffDays)} dias`;
                classe = 'text-danger';
            } else if (diffDays === 0) {
                texto = 'Vence hoje';
                classe = 'text-danger';
            } else {
                texto = `${diffDays} dias restantes`;
                classe = diffDays <= 40 ? 'text-danger' : diffDays <= 60 ? 'text-warning' : 'text-success';
            }

            return { texto, classe };
        }

        // Função para exibir feedback
        function showFeedback(message, className) {
            const feedback = document.getElementById('feedback');
            feedback.textContent = message;
            feedback.classList.remove('hidden', 'alert-success', 'alert-danger', 'alert-info');
            feedback.classList.add(className);
            setTimeout(() => feedback.classList.add('hidden'), 3000);
        }

        // Função para carregar contratos no select
        function loadContratos(selectElement) {
            fetch('./get_contratos.php')
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        selectElement.innerHTML = '<option value="">Selecione um contrato</option>';
                        data.contratos.forEach(contrato => {
                            const option = document.createElement('option');
                            option.value = contrato.id;
                            option.textContent = contrato.titulo;
                            selectElement.appendChild(option);
                        });
                    } else {
                        showFeedback('Erro ao carregar contratos: ' + data.message, 'alert-danger');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar contratos:', error);
                    showFeedback('Erro ao carregar contratos: ' + error.message, 'alert-danger');
                });
        }

        // Função para adicionar ou atualizar um card de certidão
        function updateCertidaoCard(certidao) {
            const certidoesList = document.getElementById('certidoesList');
            let card = document.querySelector(`.certidao-card[data-certidao-id="${certidao.id}"]`);

            if (!card) {
                card = document.createElement('div');
                card.className = 'certidao-card card';
                card.setAttribute('data-certidao-id', certidao.id);
                certidoesList.appendChild(card);
            }

            const { texto, classe } = calcularDiasRestantes(certidao.data_vencimento);
            const arquivoLink = certidao.arquivo ? `<a href="uploads/${certidao.arquivo}" target="_blank">Visualizar Arquivo</a>` : 'Nenhum arquivo';
            const contratoInfo = certidao.contrato_titulo ? certidao.contrato_titulo : 'Nenhum contrato vinculado';

            card.innerHTML = `
                <div class="card-header">
                    <h5 class="mb-0">${certidao.nome}</h5>
                    <div>
                        <button class="btn btn-primary btn-action me-1" onclick='editCertidao("${certidao.id}")'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-action" onclick='deleteCertidao("${certidao.id}")'>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
              <div class="card-body">
  <p><i class="fas fa-file-alt"></i><strong> Documento:</strong> ${certidao.documento}</p>
  <p><i class="fas fa-calendar-alt"></i><strong> Data de Vencimento:</strong> ${new Date(certidao.data_vencimento).toLocaleDateString('pt-BR')}</p>
  <p class="dias-restantes ${classe}">
    <i class="fas fa-hourglass-half"></i><strong> Dias Restantes:</strong> ${texto}
  </p>
  <p><i class="fas fa-building"></i><strong> Fornecedor:</strong> ${certidao.fornecedor}</p>
  <p><i class="fas fa-user"></i><strong> Responsável:</strong> ${certidao.responsavel}</p>
  <p><i class="fas fa-file-upload"></i><strong> Arquivo:</strong> ${arquivoLink}</p>
  <p><i class="fas fa-link"></i><strong> Contrato Vinculado:</strong> ${contratoInfo}</p>
</div>

            `;
        }

        // Função para filtrar certidões
        function filterCertidoes(searchTerm) {
            const certidoesList = document.getElementById('certidoesList');
            certidoesList.innerHTML = '';

            const filteredCertidoes = certidoes.filter(certidao => {
                const diasRestantes = calcularDiasRestantes(certidao.data_vencimento).texto;
                const searchFields = [
                    certidao.documento,
                    new Date(certidao.data_vencimento).toLocaleDateString('pt-BR'),
                    certidao.nome,
                    certidao.fornecedor,
                    certidao.responsavel,
                    diasRestantes,
                    certidao.contrato_titulo || ''
                ].map(field => field.toLowerCase());

                return searchFields.some(field => field.includes(searchTerm.toLowerCase()));
            });

            if (filteredCertidoes.length === 0) {
                showFeedback('Nenhuma certidão encontrada.', 'alert-info');
            } else {
                filteredCertidoes.forEach(updateCertidaoCard);
            }
        }

        // Evento de pesquisa
        document.getElementById('searchCertidao').addEventListener('input', function (e) {
            filterCertidoes(e.target.value);
        });

        // Evento para mostrar/esconder select de contratos
        document.getElementById('vincularContrato').addEventListener('click', function () {
            const container = document.querySelector('#certidaoForm .contrato-select-container');
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
            if (container.style.display === 'block') {
                loadContratos(document.getElementById('contrato_id'));
            }
        });

        document.getElementById('edit_vincularContrato').addEventListener('click', function () {
            const container = document.querySelector('#editCertidaoForm .contrato-select-container');
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
            if (container.style.display === 'block') {
                loadContratos(document.getElementById('edit_contrato_id'));
            }
        });

        // Função para carregar certidões do banco
        function loadCertidoes() {
            console.log('Carregando certidões...');
            fetch('./get_certidao.php')
                .then(response => {
                    console.log('Resposta recebida:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dados recebidos:', data);
                    if (data.success) {
                        certidoes = data.certidoes.map(certidao => ({
                            ...certidao,
                            id: certidao.id.toString()
                        }));
                        console.log('Certidões atualizadas:', certidoes);
                        filterCertidoes('');
                    } else {
                        showFeedback('Erro ao carregar certidões: ' + data.message, 'alert-danger');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar certidões:', error);
                    showFeedback('Erro ao carregar certidões: ' + error.message, 'alert-danger');
                });
        }

        // Função para cadastrar nova certidão
        document.getElementById('certidaoForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = e.target;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData();
            formData.append('documento', form.documento.value);
            formData.append('data_vencimento', form.data_vencimento.value);
            formData.append('nome', form.nome.value);
            formData.append('fornecedor', form.fornecedor.value);
            formData.append('responsavel', form.responsavel.value);
            if (form.arquivo.files[0]) {
                formData.append('arquivo', form.arquivo.files[0]);
            }
            const contratoId = form.contrato_id.value;
            if (contratoId) {
                formData.append('contrato_id', contratoId);
            }

            console.log('Enviando certidão:', formData);
            fetch('./salvar_certidao.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    console.log('Resposta de salvar:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dados de salvar:', data);
                    if (data.success) {
                        const certidao = {
                            id: data.id.toString(),
                            documento: form.documento.value,
                            data_vencimento: form.data_vencimento.value,
                            nome: form.nome.value,
                            fornecedor: form.fornecedor.value,
                            responsavel: form.responsavel.value,
                            arquivo: data.arquivo || null,
                            contrato_id: data.contrato_id || null,
                            contrato_titulo: data.contrato_titulo || null
                        };
                        certidoes.push(certidao);
                        updateCertidaoCard(certidao);
                        form.reset();
                        document.querySelector('#certidaoForm .contrato-select-container').style.display = 'none';
                        showFeedback('Certidão cadastrada com sucesso!', 'alert-success');
                        filterCertidoes(document.getElementById('searchCertidao').value);
                    } else {
                        showFeedback('Erro ao cadastrar certidão: ' + data.message, 'alert-danger');
                    }
                })
                .catch(error => {
                    console.error('Erro ao cadastrar:', error);
                    showFeedback('Erro: ' + error.message, 'alert-danger');
                });
        });

        // Função para editar certidão
        function editCertidao(certidaoId) {
            const certidao = certidoes.find(c => c.id === certidaoId);
            if (!certidao) {
                showFeedback('Certidão não encontrada.', 'alert-danger');
                return;
            }

            document.getElementById('edit_certidao_id').value = certidao.id;
            document.getElementById('edit_documento').value = certidao.documento;
            document.getElementById('edit_data_vencimento').value = certidao.data_vencimento;
            document.getElementById('edit_nome').value = certidao.nome;
            document.getElementById('edit_fornecedor').value = certidao.fornecedor;
            document.getElementById('edit_responsavel').value = certidao.responsavel;
            document.getElementById('edit_contrato_id').value = certidao.contrato_id || '';

            const modal = new bootstrap.Modal(document.getElementById('editCertidaoModal'));
            modal.show();
        }

        // Função para salvar certidão editada
        function saveEditedCertidao() {
            const form = document.getElementById('editCertidaoForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData();
            formData.append('id', document.getElementById('edit_certidao_id').value);
            formData.append('documento', document.getElementById('edit_documento').value);
            formData.append('data_vencimento', document.getElementById('edit_data_vencimento').value);
            formData.append('nome', document.getElementById('edit_nome').value);
            formData.append('fornecedor', document.getElementById('edit_fornecedor').value);
            formData.append('responsavel', document.getElementById('edit_responsavel').value);
            if (document.getElementById('edit_arquivo').files[0]) {
                formData.append('arquivo', document.getElementById('edit_arquivo').files[0]);
            }
            const contratoId = document.getElementById('edit_contrato_id').value;
            if (contratoId) {
                formData.append('contrato_id', contratoId);
            }

            console.log('Editando certidão:', formData);
            fetch('./salvar_certidao.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    console.log('Resposta de edição:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dados de edição:', data);
                    if (data.success) {
                        const certidao = {
                            id: document.getElementById('edit_certidao_id').value,
                            documento: document.getElementById('edit_documento').value,
                            data_vencimento: document.getElementById('edit_data_vencimento').value,
                            nome: document.getElementById('edit_nome').value,
                            fornecedor: document.getElementById('edit_fornecedor').value,
                            responsavel: document.getElementById('edit_responsavel').value,
                            arquivo: data.arquivo || null,
                            contrato_id: data.contrato_id || null,
                            contrato_titulo: data.contrato_titulo || null
                        };
                        const certidaoIndex = certidoes.findIndex(c => c.id === certidao.id);
                        if (certidaoIndex !== -1) {
                            certidoes[certidaoIndex] = certidao;
                            updateCertidaoCard(certidao);
                            bootstrap.Modal.getInstance(document.getElementById('editCertidaoModal')).hide();
                            document.querySelector('#editCertidaoForm .contrato-select-container').style.display = 'none';
                            showFeedback('Certidão atualizada com sucesso!', 'alert-success');
                            filterCertidoes(document.getElementById('searchCertidao').value);
                        }
                    } else {
                        showFeedback('Erro ao atualizar certidão: ' + data.message, 'alert-danger');
                    }
                })
                .catch(error => {
                    console.error('Erro ao editar:', error);
                    showFeedback('Erro: ' + error.message, 'alert-danger');
                });
        }

        // Função para excluir certidão
        function deleteCertidao(certidaoId) {
            console.log('Excluindo certidão ID:', certidaoId);
            fetch('./deletar_certidao.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: certidaoId })
            })
                .then(response => {
                    console.log('Resposta de exclusão:', response);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Dados de exclusão:', data);
                    if (data.success) {
                        certidoes = certidoes.filter(c => c.id !== certidaoId);
                        filterCertidoes(document.getElementById('searchCertidao').value);
                        showFeedback('Certidão excluída com sucesso!', 'alert-success');
                    } else {
                        showFeedback('Erro ao excluir certidão: ' + data.message, 'alert-danger');
                    }
                })
                .catch(error => {
                    console.error('Erro ao excluir:', error);
                    showFeedback('Erro: ' + error.message, 'alert-danger');
                });
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', loadCertidoes);








        