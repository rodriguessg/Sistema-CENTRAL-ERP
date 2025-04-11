<?php
require_once __DIR__ . '/vendor/autoload.php'; // Certifique-se de que o caminho para o autoload está correto

// Conectar ao banco de dados
$conn = new mysqli('localhost', 'root', '', 'gm_sicbd');

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Defina a consulta para buscar os dados
$query = "
    SELECT t.id, t.material_id, t.quantidade, t.data, t.tipo, 
        p.produto, p.classificacao, p.natureza, p.contabil, p.descricao, p.unidade, 
        p.localizacao, p.custo, p.quantidade AS produto_quantidade, p.nf, p.preco_medio, p.tipo_operacao
    FROM transicao t
    LEFT JOIN produtos p ON t.material_id = p.id
";

// Execute a consulta
$resultado_transicao = $conn->query($query);

// Crie o objeto TCPDF
$pdf = new TCPDF('L', 'mm', 'A4'); // L = paisagem, mm = milímetros, A4 = tamanho do papel

// Adicione uma página
$pdf->AddPage();

// Defina a fonte com tamanho reduzido
$pdf->SetFont('helvetica', '', 8); // Reduzindo a fonte para 8

// Adicione um título
$pdf->Cell(0, 10, 'Relatorio de Transicao de Material', 0, 1, 'C');

// Adicionar uma linha de separação
$pdf->Ln(10);

// Adicionar os cabeçalhos da tabela com fonte menor
$pdf->SetFont('helvetica', 'B', 8); // Usando o mesmo tamanho de fonte para os cabeçalhos
// $pdf->Cell(20, 10, 'ID', 1);
$pdf->Cell(40, 10, 'Material', 1);
$pdf->Cell(30, 10, 'Classificacao', 1);
$pdf->Cell(30, 10, 'Localizacao', 1);
$pdf->Cell(50, 10, 'Descricao', 1);
$pdf->Cell(30, 10, 'Natureza', 1);
$pdf->Cell(30, 10, 'Quantidade', 1);
$pdf->Cell(30, 10, 'Custo', 1);
$pdf->Cell(30, 10, 'Data', 1);
$pdf->Cell(30, 10, 'Entrada/Saida', 1);
$pdf->Ln();

// Adicionar os dados da tabela com fonte reduzida
$pdf->SetFont('helvetica', '', 8); // Tamanho de fonte reduzido para dados
while ($row = $resultado_transicao->fetch_assoc()) {
    // $pdf->Cell(20, 10, $row['id'], 1);
    $pdf->Cell(40, 10, $row['produto'], 1);
    // Usar MultiCell para Classificação para evitar ultrapassar a largura
    $pdf->Cell(30, 10, $row['classificacao'], 1);
    // Usar MultiCell para Localização para evitar ultrapassar a largura
    $pdf->Cell(30, 10, $row['localizacao'], 1);
    // Usar MultiCell para Descrição para permitir quebras de linha
    $pdf->Cell(50, 10, $row['descricao'], 1);
    $pdf->Cell(30, 10, $row['natureza'], 1);
    $pdf->Cell(30, 10, $row['quantidade'], 1);
    $pdf->Cell(30, 10, number_format($row['preco_medio'], 2, ',', '.'), 1);
    $pdf->Cell(30, 10, $row['data'], 1);
    $pdf->Cell(30, 10, $row['tipo'], 1);
    $pdf->Ln();
}

// Gerar o PDF e forçar o download
$pdf->Output('relatorio_material.pdf', 'D');
?>
