function openTab(event, tabId) {
    // Remove active class from all tab buttons and panels
    const buttons = document.querySelectorAll('.tab-button');
    const panels = document.querySelectorAll('.tab-panel');
    buttons.forEach(button => button.classList.remove('active'));
    panels.forEach(panel => panel.classList.remove('active'));

    // Add active class to the clicked button and corresponding panel
    event.currentTarget.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}
function openSection(sectionId) {
    // Esconde todas as seções de detalhes
    const sections = document.querySelectorAll('.details-section');
    sections.forEach(section => section.style.display = 'none');

    // Exibe a seção correspondente
    document.getElementById(sectionId).style.display = 'block';
}
function openModal() {
    document.getElementById('modal').style.display = 'block';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

// Função para salvar os dados no banco
document.getElementById('addColaboradorForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Evita o reload da página

    const nome = document.getElementById('nome').value;
    const email = document.getElementById('email').value;
    const cargo = document.getElementById('cargo').value;
    const dataAdmissao = document.getElementById('dataAdmissao').value;

    // Enviar os dados via AJAX para salvar no banco
    fetch('salvar_colaborador.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ nome, email, cargo, dataAdmissao }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Colaborador cadastrado com sucesso!');
                closeModal();
            } else {
                alert('Erro ao cadastrar colaborador: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
});
function visualizarLista() {
    // Faz a requisição para o servidor
    fetch('listar_funcionarios.php')
        .then(response => response.json())
        .then(data => {
            const tabela = document.querySelector('#tabelaFuncionarios tbody');
            tabela.innerHTML = ''; // Limpa a tabela antes de preencher

            if (data.length > 0) {
                data.forEach(funcionario => {
                    const row = `
                        <tr>
                            <td>${funcionario.id}</td>
                            <td>${funcionario.nome}</td>
                            <td>${funcionario.email}</td>
                            <td>${funcionario.cargo}</td>
                            <td>${funcionario.data_admissao}</td>
                        </tr>
                    `;
                    tabela.innerHTML += row;
                });
            } else {
                tabela.innerHTML = '<tr><td colspan="5">Nenhum colaborador encontrado.</td></tr>';
            }

            // Exibe a área da tabela
            document.getElementById('listaFuncionarios').style.display = 'block';
        })
        .catch(error => {
            console.error('Erro ao buscar dados:', error);
            alert('Não foi possível carregar a lista de colaboradores.');
        });
}
