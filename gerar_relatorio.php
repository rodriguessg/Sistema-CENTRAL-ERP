<?php
// Gerar o relatório de pagamentos ou outros dados conforme necessário
$contrato_id = $_GET['contrato_id'];
$pdo = new PDO("mysql:host=localhost;dbname=gm_sicbd", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Gerar o relatório de pagamentos (ou outros dados)
$stmt = $pdo->prepare("SELECT * FROM pagamentos WHERE contrato_id = ?");
$stmt->execute([$contrato_id]);
$pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gerar uma tabela de resultados (apenas exemplo simples)
$table = "<table border='1'>
             <tr>
                 <th>ID</th>
                 <th>Valor</th>
                 <th>Data</th>
                 <th>Forma de Pagamento</th>
             </tr>";

foreach ($pagamentos as $pagamento) {
    $table .= "<tr>
                  <td>{$pagamento['id']}</td>
                  <td>{$pagamento['valor']}</td>
                  <td>{$pagamento['data_pagamento']}</td>
                  <td>{$pagamento['forma_pagamento']}</td>
               </tr>";
}

$table .= "</table>";

echo $table;
?>
