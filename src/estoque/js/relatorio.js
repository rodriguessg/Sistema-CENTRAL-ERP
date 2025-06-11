
    // Função para exibir o seletor de exercício ou mês dependendo da opção selecionada
    function toggleExercicioSelector(periodo) {
        const mesGroup = document.getElementById('mes-group');
        const exercicioGroup = document.getElementById('exercicio-group');
        
        if (periodo === 'mensal') {
            // Exibe o seletor de meses
            mesGroup.style.display = 'block';
            exercicioGroup.style.display = 'none';

            // Preencher os meses
            const meses = [
                "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", 
                "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
            ];
            
            const mesSelect = document.getElementById('mes');
            mesSelect.innerHTML = '<option value="" disabled selected>Escolha um mês</option>'; // Limpar o conteúdo existente
            
            meses.forEach((mes, index) => {
                const option = document.createElement('option');
                option.value = index + 1;  // O valor será o número do mês (1-12)
                option.textContent = mes;
                mesSelect.appendChild(option);
            });
        } else if (periodo === 'anual') {
            // Exibe o seletor de exercício (ano)
            mesGroup.style.display = 'none';
            exercicioGroup.style.display = 'block';
            fetchExercicios(); // Carregar exercícios via AJAX
        } else {
            mesGroup.style.display = 'none';
            exercicioGroup.style.display = 'none';
        }
    }


    // Função para carregar os exercícios (anos) disponíveis
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
        }
    }
 // Verificar sessão
    if (!isset($_SESSION['username']) || !isset($_SESSION['setor'])) {
        setMessageAndRedirect('error', 'Sessão inválida. Faça login.', 'index.php');
    }
    // Preencher o campo de usuário logado dinamicamente
    document.addEventListener("DOMContentLoaded", () => {
        const usuario = "<?php echo $_SESSION['username'] ?? 'Desconhecido'; ?>";
        document.getElementById("usuario").value = usuario;
    });

    // Função para gerar o relatório
    async function gerarRelatorio() {
        const periodo = document.getElementById('periodo').value;
        const exercicio = document.getElementById('exercicio').value;
        const mes = document.getElementById('mes').value;
        const usuario = document.getElementById('usuario').value;

        if (!periodo) {
            alert('Por favor, selecione o período.');
            return;
        }

        if (periodo === 'anual' && !exercicio) {
            alert('Por favor, selecione um exercício para o relatório anual.');
            return;
        }

        if (periodo === 'mensal' && !mes) {
            alert('Por favor, selecione um mês para o relatório mensal.');
            return;
        }

        try {
            const response = await fetch('./almoxarifado/gerar_relatorioestoque.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    periodo,
                    exercicio,
                    mes,
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
            console.error('Erro ao gerar relatório:', error);
            alert('Erro ao gerar o relatório. Tente novamente.');
        }
    }

    // Função para imprimir a tabela
    function imprimirTabela() {
        const conteudo = document.getElementById('resultadoRelatorio').innerHTML;
        const janelaImpressao = window.open('', '', 'width=800,height=600');
        janelaImpressao.document.write(`
            <html>
            <head>
                <title>Impressão de Relatório</title>
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
        link.download = 'relatorio.xls';
        link.click();

        URL.revokeObjectURL(url);
    }
