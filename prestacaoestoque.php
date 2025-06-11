<?php
// Verificar sessão no início do arquivo
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
    header('Location: index.php');
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? 'Desconhecido', ENT_QUOTES, 'UTF-8');

include 'header.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestação de Contas de Estoque</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }
        .relatorio-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .relatorio-group {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        select, input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="text"][readonly] {
            background-color: #f4f4f4;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        #resultadoRelatorio table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        #resultadoRelatorio th, #resultadoRelatorio td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        #resultadoRelatorio th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="form-container" id="prestacao">
        <h3>Prestação de Contas de Estoque</h3>
        <form id="form-prestacao" class="relatorio-form">
            <!-- Seletor de Mês -->
            <label for="mes">Selecione o Mês:</label>
            <div class="relatorio-group">
                <select id="mes" name="mes" required>
                    <option value="" disabled selected>Escolha um mês</option>
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                </select>
            </div>

            <!-- Seletor de Exercício (Ano) -->
            <label for="exercicio">Selecione o Exercício:</label>
            <div class="relatorio-group">
                <select id="exercicio" name="exercicio" required>
                    <option value="" disabled selected>Carregando...</option>
                </select>
            </div>

            <!-- Campo de Usuário Logado -->
            <div class="relatorio-group">
                <label for="usuario">Usuário Logado:</label>
                <input type="text" id="usuario" name="usuario" value="<?php echo $username; ?>" readonly>
            </div>

            <!-- Botão de Submissão -->
            <div class="relatorio-group">
                <button type="button" id="gerarPrestacao" onclick="gerarPrestacao()">Gerar Prestação de Contas</button>
            </div>
        </form>

        <!-- Área para exibição do relatório gerado -->
        <div id="resultadoRelatorio" style="margin-top: 20px;"></div>

        <!-- Botão de Impressão -->
        <button id="imprimirBtn" onclick="imprimirTabela()" style="display: none; margin-top: 10px;">Imprimir Tabela</button>

        <!-- Botão de Exportação para Excel -->
        <button id="exportarExcelBtn" onclick="exportarParaExcel()" style="display: none; margin-top: 10px;">Exportar para Excel</button>
    </div>

    <script>
        // Carregar exercícios (anos) disponíveis
        async function fetchExercicios() {
            try {
                const response = await fetch('./almoxarifado/buscar_exercicios.php');
                const exercicios = await response.json();
                const exercicioSelect = document.getElementById('exercicio');

                exercicioSelect.innerHTML = '<option value="" disabled selected>Selecione o ano</option>';
                exercicios.forEach(ano => {
                    const option = document.createElement('option');
                    option.value = ano;
                    option.textContent = ano;
                    exercicioSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Erro ao carregar exercícios:', error);
                alert('Erro ao carregar os anos disponíveis. Tente novamente.');
            }
        }

        // Preencher o campo de usuário logado e carregar exercícios ao carregar a página
        document.addEventListener("DOMContentLoaded", () => {
            const usuarioInput = document.getElementById("usuario");
            if (!usuarioInput.value) {
                usuarioInput.value = "Desconhecido";
            }
            fetchExercicios(); // Carregar exercícios
        });

        // Função para gerar a prestação de contas
        async function gerarPrestacao() {
            const mes = document.getElementById('mes').value;
            const exercicio = document.getElementById('exercicio').value;
            const usuario = document.getElementById('usuario').value;

            if (!mes) {
                alert('Por favor, selecione o mês.');
                return;
            }

            if (!exercicio) {
                alert('Por favor, selecione o exercício.');
                return;
            }

            try {
                const response = await fetch('./almoxarifado/gerar_prestacaoestoque.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        mes,
                        exercicio,
                        usuario
                    })
                });

                const data = await response.text();
                const resultadoDiv = document.getElementById('resultadoRelatorio');
                resultadoDiv.innerHTML = data;

                // Exibe os botões de impressão e exportação se houver tabela no relatório
                const imprimirBtn = document.getElementById('imprimirBtn');
                const exportarExcelBtn = document.getElementById('exportarExcelBtn');
                if (data.includes('<table')) {
                    imprimirBtn.style.display = 'block';
                    exportarExcelBtn.style.display = 'block';
                } else {
                    imprimirBtn.style.display = 'none';
                    exportarExcelBtn.style.display = 'none';
                }
            } catch (error) {
                console.error('Erro ao gerar prestação de contas:', error);
                alert('Erro ao gerar a prestação de contas. Tente novamente.');
            }
        }

        // Função para imprimir a tabela
        function imprimirTabela() {
            const conteudo = document.getElementById('resultadoRelatorio').innerHTML;
            const janelaImpressao = window.open('', '', 'width=800,height=600');
            janelaImpressao.document.write(`
                <html>
                <head>
                    <title>Impressão de Prestação de Contas</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f4f4f4; }
                    </style>
                </head>
                <body>
                    ${conteudo}
                </body>
                </html>
            `);
            janelaImpressao.document.close();
            janelaImpressao.print();
        }

        // Função para exportar o relatório para Excel
        function exportarParaExcel() {
            const conteudo = document.getElementById('resultadoRelatorio').innerHTML;
            const blob = new Blob([conteudo], { type: 'application/vnd.ms-excel' });
            const url = URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.download = `prestacao_estoque_${new Date().toISOString().slice(0, 10)}.xls`;
            link.click();

            URL.revokeObjectURL(url);
        }
    </script>
    <?php

    include 'footer.php';
    ?>
</body>
</html>