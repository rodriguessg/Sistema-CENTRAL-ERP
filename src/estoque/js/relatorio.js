 // Exibir o seletor de exercício apenas se a opção anual for selecionada
 function toggleExercicioSelector(periodo) {
    const exercicioGroup = document.getElementById('exercicio-group');
    if (periodo === 'anual') {
        exercicioGroup.style.display = 'block';
        fetchExercicios(); // Carregar exercícios via AJAX
    } else {
        exercicioGroup.style.display = 'none';
    }
}

// Função para carregar os exercícios disponíveis
async function fetchExercicios() {
    try {
        const response = await fetch('buscar_exercicios.php');
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

// Preencher o campo de usuário logado dinamicamente
document.addEventListener("DOMContentLoaded", () => {
    const usuario = "<?php echo $_SESSION['username'] ?? 'Desconhecido'; ?>";
    document.getElementById("usuario").value = usuario;
});

// Função para gerar o relatório
async function gerarRelatorio() {
    const periodo = document.getElementById('periodo').value;
    const exercicio = document.getElementById('exercicio').value;
    const incluirQuantidade = document.getElementById('incluir_quantidade').checked;
    const usuario = document.getElementById('usuario').value;

    if (!periodo) {
        alert('Por favor, selecione o período.');
        return;
    }

    if (periodo === 'anual' && !exercicio) {
        alert('Por favor, selecione um exercício para o relatório anual.');
        return;
    }

    try {
        const response = await fetch('gerar_relatorioestoque.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                periodo,
                exercicio,
                incluir_quantidade: incluirQuantidade ? '1' : '',
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