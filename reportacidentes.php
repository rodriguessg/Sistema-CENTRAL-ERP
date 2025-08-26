<?php
session_start();

// Configuração do banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gm_sicbd';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    die("Erro: Usuário não autenticado ou sessão expirada!");
}
$username = $_SESSION['username'];

$erro = '';
$sucesso = '';

// Manipular registro de novo acidente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update_status'])) {
    $descricao = $_POST['descricao'] ?? '';
    $localizacao = $_POST['localizacao'] ?? '';
    $severidade = $_POST['severidade'] ?? '';
    $categoria = $_POST['subcategoria'] ?? '';
    $cor = $_POST['cor'] ?? '';

    if (empty($descricao) || empty($severidade) || empty($categoria) || empty($cor)) {
        $erro = "Todos os campos obrigatórios devem ser preenchidos!";
    } elseif (!in_array($severidade, ['Leve', 'Moderado', 'Grave'])) {
        $erro = "Severidade inválida!";
    } else {
        $sql = "INSERT INTO acidentes (descricao, localizacao, usuario, severidade, categoria, cor, data_registro, status) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), 'em andamento')";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $erro = "Erro na preparação da query: " . $conn->error;
        } else {
            $stmt->bind_param("ssssss", $descricao, $localizacao, $username, $severidade, $categoria, $cor);
            if (!$stmt->execute()) {
                $erro = "Erro ao registrar o acidente: " . $stmt->error;
            } else {
                $sucesso = "Acidente registrado com sucesso!";
                header('Location: /Sistema-CENTRAL-ERP/views/mensagem.php?mensagem=acidente&pagina=/Sistema-CENTRAL-ERP/reportacidentes.php');
                exit();
            }
            $stmt->close();
        }
    }
}

// Manipular atualização de status e órgãos de emergência
if (isset($_POST['update_status']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $policia = isset($_POST['policia'][$id]) ? 1 : 0;
    $bombeiros = isset($_POST['bombeiros'][$id]) ? 1 : 0;
    $samu = isset($_POST['samu'][$id]) ? 1 : 0;

    $sql = "UPDATE acidentes SET status = 'resolvido', policia = ?, bombeiros = ?, samu = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iiii", $policia, $bombeiros, $samu, $id);
        if ($stmt->execute()) {
            $sucesso = "Status do acidente atualizado para resolvido!";
        } else {
            $erro = "Erro ao atualizar o status: " . $stmt->error;
        }
        $stmt->close();
        header("Location: reportacidentes.php?success=2");
        exit();
    }
}

// Buscar todos os registros
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "SELECT id, descricao, localizacao, usuario, severidade, categoria, cor, data_registro, status, policia, bombeiros, samu 
        FROM acidentes 
        WHERE descricao LIKE ? OR localizacao LIKE ? OR severidade LIKE ? OR categoria LIKE ? 
        ORDER BY data_registro";
$params = ["%$search%", "%$search%", "%$search%", "%$search%"];

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Erro na preparação da query: " . $conn->error);
}
$stmt->bind_param("ssss", ...$params);
if (!$stmt->execute()) {
    die("Erro na execução da query: " . $stmt->error);
}

// Buscar resultados
$result = [];
$queryResult = $stmt->get_result();
if ($queryResult) {
    while ($row = $queryResult->fetch_assoc()) {
        $result[] = $row;
    }
    $fetchSuccess = !empty($result);
} else {
    $erro = "Erro ao obter resultados: " . $conn->error;
}
$stmt->close();

if (!$fetchSuccess) {
    $erro = "Nenhum dado foi recuperado. Verifique a query ou os dados na tabela 'acidentes'.";
}

// Converter $result para JSON para uso no JavaScript
$resultJson = json_encode($result);

// Mapear cores para classes CSS
$colorClasses = [
    'Verde' => 'severity-green',
    'Amarelo' => 'severity-yellow',
    'Vermelho' => 'severity-red',
    'Amarelo/Vermelho' => 'severity-yellow-red'
];

// Função para obter a classe CSS com base na cor
function getSeverityClass($cor, $colorClasses) {
    return isset($colorClasses[$cor]) ? $colorClasses[$cor] : '';
}

// Include header.php only after all header() calls
include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Acidente</title>
    <link rel="stylesheet" href="./src/bonde/style/report.css">
</head>
<body>
    <div class="header">
        <h1>Registrar Acidente</h1>
    </div>
    <div class="container">
        <?php if ($erro): ?>
            <p class="error"><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>
        <?php if ($sucesso || isset($_GET['success'])): ?>
            <!-- <p class="success"><?php echo htmlspecialchars($sucesso ?: ($_GET['success'] == 1 ? "Acidente registrado com sucesso!" : "Status do acidente atualizado para resolvido!")); ?></p> -->
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="categoria">Categoria:</label>
                <select id="categoria" name="categoria" required onchange="updateSubcategorias()">
                    <option value="">Selecione a categoria</option>
                    <option value="Operacionais">Operacionais</option>
                    <option value="Via permanente / infraestrutura">Via permanente / infraestrutura</option>
                    <option value="Relacionadas a terceiros">Relacionadas a terceiros</option>
                    <option value="Emergências médicas">Emergências médicas</option>
                    <option value="Segurança">Segurança</option>
                    <option value="Eventos externos">Eventos externos</option>
                </select>
            </div>
            <div class="form-group">
                <label for="subcategoria">Subcategoria:</label>
                <select id="subcategoria" name="subcategoria" required onchange="updateSeveridadeECor()">
                    <option value="">Selecione a subcategoria</option>
                </select>
            </div>
            <div class="form-group">
                <label for="severidade">Severidade:</label>
                <select id="severidade" name="severidade">
                    <option value="">Selecione a severidade</option>
                    <option value="Leve">Leve</option>
                    <option value="Moderado">Moderado</option>
                    <option value="Grave">Grave</option>
                </select>
                <input type="hidden" id="cor" name="cor">
            </div>
            <div class="form-group">
                <label for="localizacao">Localização:</label>
                <input type="text" id="localizacao" name="localizacao" placeholder="Ex: Largo do Curvel, Copacabana, Carioca, próximo ao poste 13 ...">
            </div>
            <div class="form-group">
                <label for="data">Data do Acidente:</label>
                <input type="date" id="data" name="data" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição do Acidente:</label>
                <textarea id="descricao" name="descricao" rows="4" required placeholder="Descreva o acidente, danos, envolvidos, e ações tomadas"></textarea>
            </div>
            <button type="submit">Salvar Registro</button>
        </form>

        <div class="search-section">
            <div class="form-group">
                <label for="search">Pesquisar:</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Pesquisar por descrição, localização, severidade ou categoria">
            </div>
            <div class="form-group">
                <label for="severityFilter">Filtrar por Severidade:</label>
                <select id="severityFilter" name="severityFilter">
                    <option value="">Todas</option>
                    <option value="Leve">Leve</option>
                    <option value="Moderado">Moderado</option>
                    <option value="Grave">Grave</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dateStart">Data de Registro (Início):</label>
                <input type="date" id="dateStart" name="dateStart">
            </div>
            <div class="form-group">
                <label for="dateEnd">Data de Registro (Fim):</label>
                <input type="date" id="dateEnd" name="dateEnd">
            </div>
        </div>

        <div class="accidents-table">
            <h2>Histórico de Acidentes</h2>
            <h3 id="totalAccidents">Total de Acidentes: <?php echo count($result); ?></h3>
            <?php if (!is_array($result)): ?>
                <p class="error">Erro: Dados inválidos. Valor: <?php echo htmlspecialchars(var_export($result, true)); ?></p>
                <?php $result = []; ?>
            <?php endif; ?>
            <?php if (empty($result)): ?>
                <div class="no-data">Nenhum acidente registrado.</div>
            <?php else: ?>
                <table id="accidentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Localização</th>
                            <th>Usuário</th>
                            <th>Severidade</th>
                            <th>Categoria</th>
                            <th>Cor</th>
                            <th>Data de Registro</th>
                            <th>Polícia</th>
                            <th>Bombeiros</th>
                            <th>SAMU</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="accidentsTableBody">
                        <!-- Conteúdo gerado por JavaScript -->
                    </tbody>
                </table>
                <div class="pagination" id="pagination">
                    <!-- Botões de paginação gerados por JavaScript -->
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
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

        // Função para determinar quais órgãos de emergência devem ser pré-marcados
        function getEmergencyServices(categoria) {
            const emergencyServices = {
                "Atropelamento de pedestre": { policia: true, bombeiros: false, samu: true },
                "Colisão com veículo": { policia: true, bombeiros: false, samu: true },
                "Colisão com motocicleta/bicicleta": { policia: true, bombeiros: false, samu: true },
                "Manifestação/bloqueio proposital na via": { policia: true, bombeiros: false, samu: false },
                "Ato de vandalismo no bonde": { policia: true, bombeiros: false, samu: false },
                "Agressão entre passageiros": { policia: true, bombeiros: false, samu: false },
                "Roubo ou tentativa de assalto": { policia: true, bombeiros: false, samu: false },
                "Ameaça de bomba / suspeita de artefato": { policia: true, bombeiros: false, samu: false },
                "Descarrilamento com vítimas": { policia: false, bombeiros: true, samu: true },
                "Alagamento de via": { policia: false, bombeiros: true, samu: false },
                "Deslizamento de encosta": { policia: false, bombeiros: true, samu: false },
                "Rompimento de trilho / falha estrutural": { policia: false, bombeiros: true, samu: false },
                "Incêndio em área próxima à via": { policia: false, bombeiros: true, samu: false },
                "Queda de árvore sobre a rede elétrica": { policia: false, bombeiros: true, samu: false },
                "Passageiro passando mal (grave)": { policia: false, bombeiros: false, samu: true },
                "Acidente interno com vítima grave": { policia: false, bombeiros: false, samu: true }
            };
            return emergencyServices[categoria] || { policia: false, bombeiros: false, samu: false };
        }

        // Função para renderizar a tabela
        function renderTable(page, data) {
            const start = (page - 1) * perPage;
            const end = start + perPage;
            const pageData = data.slice(start, end);
            const tbody = document.getElementById('accidentsTableBody');
            tbody.innerHTML = '';

            if (pageData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="13" class="no-data">Nenhum acidente encontrado.</td></tr>';
                return;
            }

            pageData.forEach(row => {
                if (typeof row !== 'object' || row === null) {
                    console.error('Dados inválidos:', row);
                    return;
                }
                const emergencyServices = getEmergencyServices(row.categoria);
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.id || ''}</td>
                    <td>${row.descricao || ''}</td>
                    <td>${row.localizacao || ''}</td>
                    <td>${row.usuario || ''}</td>
                    <td class="${colorClasses[row.cor] || ''}">${row.severidade || ''}</td>
                    <td>${row.categoria || ''}</td>
                    <td>${row.cor || ''}</td>
                    <td>${row.data_registro || ''}</td>
                    <td>
                        ${row.status === 'em andamento' ? 
                            `<form method="POST" action="" id="form-${row.id}">
                                <input type="checkbox" name="policia[${row.id}]" ${emergencyServices.policia ? 'checked' : ''}>
                                <input type="hidden" name="id" value="${row.id}">
                                <input type="hidden" name="update_status" value="1">
                            </form>` : 
                            (row.policia == 1 ? '✔' : '')}
                    </td>
                    <td>
                        ${row.status === 'em andamento' ? 
                            `<input type="checkbox" name="bombeiros[${row.id}]" form="form-${row.id}" ${emergencyServices.bombeiros ? 'checked' : ''}>` : 
                            (row.bombeiros == 1 ? '✔' : '')}
                    </td>
                    <td>
                        ${row.status === 'em andamento' ? 
                            `<input type="checkbox" name="samu[${row.id}]" form="form-${row.id}" ${emergencyServices.samu ? 'checked' : ''}>` : 
                            (row.samu == 1 ? '✔' : '')}
                    </td>
                    <td>${row.status || ''}</td>
                    <td>
                        ${row.status === 'em andamento' ? 
                            `<button type="submit" form="form-${row.id}" class="status-btn">Marcar como Resolvido</button>` : 
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
                const matchesSearch = !searchTerm || 
                    (row.descricao?.toLowerCase().includes(searchTerm) || false) ||
                    (row.localizacao?.toLowerCase().includes(searchTerm) || false) ||
                    (row.severidade?.toLowerCase().includes(searchTerm) || false) ||
                    (row.categoria?.toLowerCase().includes(searchTerm) || false);

                const matchesSeverity = !severityFilter || row.severidade === severityFilter;

                let matchesDate = true;
                if (dateStart) {
                    const rowDate = new Date(row.data_registro.split(' ')[0]);
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
    </script>
</body>
</html>