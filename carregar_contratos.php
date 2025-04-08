<?php
// Conexão com o banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=gm_sicbd', 'root', '');

// Verificar se foi enviado o tipo de relatório
$tipoRelatorio = $_GET['tipo_relatorio'] ?? '';

// Consultar os contratos se o tipo for "pagamentos"
if ($tipoRelatorio === 'pagamentos') {
    $stmt = $pdo->query("SELECT id, titulo FROM gestao_contratos");
    $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($contratos);
}
?>
