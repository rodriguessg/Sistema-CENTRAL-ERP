<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recursos Humanos</title>
    <link rel="stylesheet" href="./src/style/rh.css">
</head>
<body>
    <div class="container">
        <h1 class="rh">Recursos Humanos</h1>
        <div class="tabs">
            <button class="tab-button active" onclick="openTab(event, 'gestaoPessoas')">Gestão de Pessoas</button>
            <button class="tab-button" onclick="openTab(event, 'departamentoPessoal')">Departamento Pessoal</button>
            <button class="tab-button" onclick="openTab(event, 'relatorios')">Relatórios</button>
        </div>
        <div class="tab-content">
        <div id="gestaoPessoas" class="tab-panel active">
    <h2>Gestão de Pessoas</h2>
    <div class="steps">
        <div class="step">
            <h3>Controle de Colaboradores</h3>
            <p>Gerencie o cadastro e os dados dos colaboradores.</p>
            <button onclick="openSection('colaboradores')">Acessar</button>
            
        </div>
        
        <div class="step">
            <h3>Gerenciamento de Treinamentos</h3>
            <p>Organize treinamentos e acompanhe a participação.</p>
            <button onclick="openSection('treinamentos')">Acessar</button>
        </div>
        <div class="step">
            <h3>Desempenho</h3>
            <p>Monitore e avalie o desempenho dos colaboradores.</p>
            <button onclick="openSection('desempenho')">Acessar</button>
        </div>
    </div>
    <!-- Seções detalhadas -->
    <div id="colaboradores" class="details-section">
        <h4>Controle de Colaboradores</h4>
        <p>Aqui você pode adicionar, editar ou remover informações sobre os colaboradores.</p>
      <!-- Botão para abrir o modal -->
<button onclick="openModal()">Adicionar Colaborador</button>
<button onclick="visualizarLista()">Visualizar Lista</button>
   

<!-- Área para exibir a tabela -->
<div id="listaFuncionarios" style="margin-top: 20px; display: none;">
    <h3>Lista de Colaboradores</h3>
    <table border="1" id="tabelaFuncionarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Cargo</th>
                <th>Data de Admissão</th>
            </tr>
        </thead>
        <tbody>
            <!-- Dados serão inseridos aqui -->
        </tbody>
    </table>
</div>
    </div>
    <!-- Botão para visualizar lista -->
 

    <!-- Modal para cadastro de colaborador -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Adicionar Colaborador</h3>
        <form id="addColaboradorForm">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
            <label for="cargo">Cargo:</label>
            <input type="text" id="cargo" name="cargo" required>
            <label for="dataAdmissao">Data de Admissão:</label>
            <input type="date" id="dataAdmissao" name="dataAdmissao" required>
            <button type="submit">Salvar</button>
        </form>
    </div>
</div>
    <div id="treinamentos" class="details-section">
        <h4>Gerenciamento de Treinamentos</h4>
        <p>Planeje novos treinamentos e acompanhe os concluídos.</p>
        <button onclick="alert('Adicionar Treinamento')">Adicionar Treinamento</button>
        <button onclick="alert('Acompanhar Treinamentos')">Acompanhar Treinamentos</button>
    </div>
    <div id="desempenho" class="details-section">
        <h4>Desempenho</h4>
        <p>Avalie o progresso e desempenho individual ou por equipe.</p>
        <button onclick="alert('Criar Avaliação')">Criar Avaliação</button>
        <button onclick="alert('Ver Relatórios')">Ver Relatórios</button>
    </div>
</div>
            <div id="departamentoPessoal" class="tab-panel">
                <h2>Departamento Pessoal</h2>
                <p>Recursos para folha de pagamento, admissões e desligamentos.</p>
            </div>
            <div id="relatorios" class="tab-panel">
                <h2>Relatórios</h2>
                <p>Visualize e exporte relatórios de desempenho e gestão.</p>
            </div>
        </div>
    </div>
    <script src="./src/js/rh.js"></script>
</body>
</html>
