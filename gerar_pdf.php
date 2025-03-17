<?php
require('PDF/fpdf.php'); // Incluindo a versão correta da FPDF

// Função para gerar relatório completo do estoque
function gerarRelatorioEstoquePDF($conn) {
    // Consulta para pegar os dados de estoque
    $sql = "SELECT produto, quantidade, custo, (quantidade * custo) AS valor_total FROM produtos";
    $result = $conn->query($sql);

    if (!$result) {
        return "Erro ao gerar o relatório de estoque: " . $conn->error;
    }

    // Instância do FPDF
    $pdf = new FPDF('P', 'mm', 'A4'); // P = retrato, mm = milímetros, A4 = tamanho do papel
    $pdf->AddPage(); // Adiciona uma página ao PDF
    $pdf->SetFont('Arial', 'B', 16); // Define a fonte do título
    $pdf->Cell(190, 10, 'Relatorio de Estoque', 0, 1, 'C'); // Título centralizado
    $pdf->Ln(10); // Quebra de linha

   
    // Definir fonte para a tabela (em negrito para o cabeçalho)
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, 'Produto', 1, 0, 'C', true);  // A célula será preenchida com a cor definida
    $pdf->Cell(40, 10, 'Quantidade', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Valor Unitario (R$)', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'Valor Total (R$)', 1, 1, 'C', true);

    // Configura a fonte para os dados
    $pdf->SetFont('Arial', '', 12);

    // Preencher a tabela com os dados do estoque
    while ($row = $result->fetch_assoc()) {
        // Preenchendo a tabela
        $pdf->Cell(50, 10, $row['produto'], 1, 0, 'C');
        $pdf->Cell(40, 10, $row['quantidade'], 1, 0, 'C');
        $pdf->Cell(50, 10, number_format($row['custo'], 2, ',', '.'), 1, 0, 'C');
        $pdf->Cell(50, 10, number_format($row['valor_total'], 2, ',', '.'), 1, 1, 'C');
    }

    // Adicionar link para download
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(190, 10, 'Clique aqui para baixar o relatório completo.', 0, 1, 'C');

    // Gerar o PDF
    $pdf->Output('I', 'relatorio_estoque.pdf'); // 'I' para exibir no navegador, 'relatorio_estoque.pdf' é o nome do arquivo gerado
}

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', '', 'supat');

// Verificar se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Gerar o relatório PDF
gerarRelatorioEstoquePDF($conn);

// Fecha a conexão com o banco de dados
$conn->close();
?>
